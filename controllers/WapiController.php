<?php
namespace app\controllers;

use app\components\BudgetService;
use app\components\WalletService;
use app\models\repository\WalletRepository;
use app\models\Wallet;
use app\models\WalletChange;
use app\components\TelegramService;
use Exception;
use yii\base\InvalidArgumentException;
use yii\rest\ActiveController;
use yii\web\Response;
use Yii;

class WapiController extends ActiveController
{
    public $modelClass = 'app\models\Wallet';
    private $budgetService;
    private $telegramService;
    private $walletRepository;
    private $walletService;

    private const COMMAND_SHOW_ALL_COMMAND = '/show_all';
    private const COMMAND_HELP = '/help';
    private const COMMAND_GET_INFO_ABOUT_WALLET = '/info';
    private const COMMAND_RESET = '/reset';
    private const COMMAND_RESET_NEW = '/reset_new';
    private const COMMAND_DEFAULT = '/default';
    private const COMMAND_GET_REMAINING_MONTH_EVERYDAY_MONEY = '/remainM';
    private const COMMAND_SALARY = '/salary';

    public function __construct(
        $id,
        $module,
        BudgetService $budgetService,
        TelegramService $telegramService,
        WalletRepository $walletRepository,
        WalletService $walletService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);

        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->budgetService = $budgetService;
        $this->telegramService = $telegramService;
        $this->walletRepository = $walletRepository;
        $this->walletService = $walletService;
    }

    //todo think about interface and classes for all command below
    public static function getAllCommand()
    {
        return [
            self::COMMAND_SHOW_ALL_COMMAND => 'Show all bot command',
            self::COMMAND_HELP => 'Help create wallet change entry',
            self::COMMAND_GET_INFO_ABOUT_WALLET => 'Info about last wallet moneys',
            self::COMMAND_RESET => 'Reset all wallets data',
            self::COMMAND_RESET_NEW => 'Reset all wallets data and add new values',
            self::COMMAND_GET_REMAINING_MONTH_EVERYDAY_MONEY => 'How much I can spend everyday in current month daily',
            self::COMMAND_SALARY => 'distribute salary according to distribution rules ' . json_encode(BudgetService::FUNDS_SALARY_WEIGHTS_RULES),
        ];
    }

    //todo make new endpoint access through telegram webhooks
    public function actionGetLastWalletInfo()
    {
        return $this->walletService->getLastWalletInfo();
    }

    public function actionTelegram()
    {
        $message = Yii::$app->request->get('message');
        try {
            $messageText = $message;

            if ($this->handleSpecialCommand($messageText)) {
                return true;
            }

            $newWalletChange = $this->handleCommonChangeWalletFundCommand($messageText);
        } catch (Exception $exception) {
            $message = 'Error!' . $exception->getMessage() . $exception->getTraceAsString();
            $this->telegramService->sendMessage($message);

            return true;
        }

        return $newWalletChange;
    }

    private function handleCommonChangeWalletFundCommand(string $messageText): WalletChange
    {
        $params = $this->parseCommand($messageText);
        $changeValue = (int)$params[0];
        $entityCode = $params[1];
        $comment = $params[2];

        if (!isset($entityCode) || !isset($changeValue)) {
            throw new InvalidArgumentException();
        }

        $entityName = Wallet::getFieldByCode()[(int)$entityCode] ?? null;
        if (empty($entityName)) {
            throw new InvalidArgumentException();
        }

        $newWalletChange = $this->walletService->createWalletChange($entityName, $changeValue, $comment);
        if (!$newWalletChange instanceof WalletChange) {
            throw new Exception();
        }

        $lastLastWallet = $this->walletRepository->getById($newWalletChange->wallet_id);
        $message = "Success {$newWalletChange->id} {$newWalletChange->entity_name} New total sum: {$lastLastWallet->money_all}";

        $this->telegramService->sendMessage($message);

        return $newWalletChange;
    }

    private function handleSpecialCommand(string $messageText): bool
    {
        switch ($messageText) {
            case self::COMMAND_SHOW_ALL_COMMAND:
                $message = 'Доступные команды для бота:' . json_encode(self::getAllCommand());
                $this->telegramService->sendMessage($message);

                return true;
            case self::COMMAND_HELP:
                $message = 'Первое слово - сумма с плюсом или минусом, второе - код денежного фонда, третье - коммент (не обязателен). Разделять пробелами';
                $message .= PHP_EOL . 'Актуальные коды фондов' . json_encode(Wallet::getFieldByCode());
                $this->telegramService->sendMessage($message);

                return true;
            case self::COMMAND_GET_INFO_ABOUT_WALLET:
                $message = 'Остаток денег на счете = ' . $this->walletService->getLastWalletInfo();
                $this->telegramService->sendMessage($message);

                return true;
            case self::COMMAND_GET_REMAINING_MONTH_EVERYDAY_MONEY:
                $message = 'Денег каждый день на текущий месяц = ' . $this->budgetService->getMoneyForCurrentMonth();
                $this->telegramService->sendMessage($message);

                return true;
            case self::COMMAND_RESET:
                $this->walletService->resetWallets();
                $message = 'Все кошельки очищены';
                $this->telegramService->sendMessage($message);

                return true;
            case strpos($messageText, self::COMMAND_SALARY) !== false:
                $params = $this->parseCommand($messageText, self::COMMAND_SALARY);
                $salary = $params[1];
                $this->budgetService->setSalary($salary);
                $message = 'Зарплата распределена по фонтам.';
                $message .= 'Остаток денег на счете = ' . $this->walletService->getLastWalletInfo();
                $this->telegramService->sendMessage($message);

                return true;
            case strpos($messageText, self::COMMAND_RESET_NEW) !== false:
                $params = $this->parseCommand($messageText, self::COMMAND_RESET_NEW);
                $this->walletService->setNewWalletToEmptyBase($params);
                $message = 'Все кошельки очищены и заданы новые значения';
                $this->telegramService->sendMessage($message);

                return true;
        }

        return false;
    }

    private function parseCommand(string $text, string $operationType = self::COMMAND_DEFAULT): array
    {
        $arrayValidCount = 0;
        $array = explode(' ', trim($text));
        switch ($operationType) {
            case self::COMMAND_DEFAULT:
                $arrayValidCount = 3;
                break;
            case self::COMMAND_RESET_NEW:
                $arrayValidCount = 8;
                break;
            case self::COMMAND_SALARY:
                $arrayValidCount = 2;
                break;
        }
        $currentCount = count($array);
        if ($currentCount !== $arrayValidCount) {
            $this->telegramService->sendMessage("Нужно точное количество ($arrayValidCount) слов. Прислано: $currentCount");
            throw new InvalidArgumentException();
        }

        return $array;
    }
}
