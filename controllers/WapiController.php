<?php
namespace app\controllers;

use app\components\BudgetService;
use app\components\exceptions\CommandCountWordException;
use app\components\helpers\DateHelper;
use app\components\helpers\DescriptionHelper;
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
    private $budgetService;
    private $dateHelper;
    private $descriptionHelper;
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
        DateHelper $dateHelper,
        DescriptionHelper $descriptionHelper,
        TelegramService $telegramService,
        WalletRepository $walletRepository,
        WalletService $walletService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);

        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->budgetService = $budgetService;
        $this->dateHelper = $dateHelper;
        $this->descriptionHelper = $descriptionHelper;
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
            self::COMMAND_SALARY => 'distribute salary according to distribution rules ' . BudgetService::getFundsWeightDescription()
        ];
    }

    //todo make new endpoint access through telegram webhooks

    /**
     * GET /wapi
     */
    public function actionGetLastWalletInfo()
    {
        return $this->walletService->getLastWalletInfo();
    }

    /**
     * POST /wapi/telegram
     * message{string}
     */
    public function actionTelegram()
    {
        $message = Yii::$app->request->post('message');
        try {
            $messageText = $message['text'];

            $resultMessage = $this->handleSpecialCommand($messageText);
            if ($resultMessage) {
                return $this->telegramService->sendMessage($resultMessage);
            }

            $newWalletChangeMessage = $this->handleCommonChangeWalletFundCommand($messageText);

            return $this->telegramService->sendMessage($newWalletChangeMessage);
        } catch (CommandCountWordException $commandCountWordException) {
            return $this->telegramService->sendMessage($commandCountWordException->getMessage());
        } catch (Exception $exception) {
            $message = 'Error!' . $exception->getMessage() . $exception->getTraceAsString();

            return $this->telegramService->sendMessage($message);
        }
    }

    /**
     * GET wapi/browse
     * message{string}
     */
    public function actionWebBrowser()
    {
        $message = Yii::$app->request->get('message');
        try {
            $resultMessage = $this->handleSpecialCommand($message);
            if ($resultMessage) {
                return $resultMessage;
            }

            return $this->handleCommonChangeWalletFundCommand($message);
        } catch (CommandCountWordException $commandCountWordException) {
            return $commandCountWordException->getMessage();
        } catch (Exception $exception) {
            $message = 'Error!' . $exception->getMessage() . $exception->getTraceAsString();

            return $message;
        }
    }

    private function handleCommonChangeWalletFundCommand(string $messageText): string
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

        return "Success {$newWalletChange->id} {$newWalletChange->entity_name} New total sum: {$lastLastWallet->money_all}";
    }

    private function handleSpecialCommand(string $messageText): ?string
    {
        switch ($messageText) {
            case self::COMMAND_SHOW_ALL_COMMAND:
                return 'Доступные команды для бота:' . $this->descriptionHelper->getDescriptionFromArray(self::getAllCommand());
            case self::COMMAND_HELP:
                $message = 'Первое слово - сумма с плюсом или минусом, второе - код денежного фонда, третье - коммент (не обязателен). Разделять пробелами';
                $message .= PHP_EOL . 'Актуальные коды фондов' . $this->descriptionHelper->getDescriptionFromArray(Wallet::getFieldByCode());

                return $message;
            case self::COMMAND_GET_INFO_ABOUT_WALLET:
                $message = 'Остаток денег на счете = ' . $this->walletService->getLastWalletInfo();

                return $message;
            case self::COMMAND_GET_REMAINING_MONTH_EVERYDAY_MONEY:
                return 'Денег каждый день на текущий месяц = ' . $this->budgetService->getMoneyForCurrentMonth(
                    $this->dateHelper->getRemainingDaysOfMonth()
                    );
            case self::COMMAND_RESET:
                $this->walletService->resetWallets();

                return 'Все кошельки очищены';
            case strpos($messageText, self::COMMAND_SALARY) !== false:
                $params = $this->parseCommand($messageText, self::COMMAND_SALARY);
                $salary = $params[1];
                $this->budgetService->setSalary($salary);
                $message = 'Зарплата распределена по фонтам.';
                $message .= 'Остаток денег на счете = ' . $this->walletService->getLastWalletInfo();

                return $message;
            case strpos($messageText, self::COMMAND_RESET_NEW) !== false:
                $params = $this->parseCommand($messageText, self::COMMAND_RESET_NEW);
                $this->walletService->setNewWalletToEmptyBase($params);
                $message = 'Все кошельки очищены и заданы новые значения';

                return $message;
        }

        return null;
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
            throw new CommandCountWordException("Нужно точное количество ($arrayValidCount) слов. Прислано: $currentCount");
        }

        return $array;
    }
}
