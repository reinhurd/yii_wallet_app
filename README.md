<p align="center">
    <h1 align="center">TeleBudget educational project</h1>
    <br>
</p>

Training project YII. 

An attempt to implement maintaining your own budget by sending and receiving data from a telegram bot.

Setting for telegram bot

`https://api.telegram.org/botTOKEN/getWebhookInfo` - отладка
`https://api.telegram.org/botTOKEN/setWebhook?url=https://DOMEN/wapi/telegram` - запись вебхука

Отправка сообщений в бот - через sendmessage и ПОСТ-параметры

###Conception
Есть **Wallet** - текущий "слепок" кошелька на данный момент. Это вся доступная сумма, разбитая по фондам - каким то статьям расходов (например, медицина\депозит\кредитные обязательства\вклады\на мелочи и т.д.)

Каждый раз, когда деньги на что-то тратятся (или вносятся), создается **WalletChange** - "лог" изменений, указывающий статью (фонд, например, положил в депозит 100 рублей), и каммент с датой. Одновременно с созданием такого лога происходит обновление Wallet - создается новый слепок с учетом внесенных изменений.

Есть сервисы:
- TelegramService, отвечающий за распознавание команд с бота
- WalletService, куда вынес логику обновлений Wallet и WalletChange
- BudgetService, который отвечает за финансовую математику - распределение зарплаты, сколько денег есть на день и т.д.

###RoadMap
~~1. Make reset method to clear wallet and set new param~~
2. More funds fields
3. Make Services in controller
~~4. Automatically count available money for day~~
5. More user friendly interface in bot
6. Delete trash files from Yii template
7. Menu on the main page
8. Rename wallet to dayWallets

###Wishes
1. Dynamically set funds fields for wallet model
2. Email notifications
3. More financial magic?