# PHP клиент для API автоматической фискализации чеков интернет-магазинов Модуль.Кассы
[![](https://img.shields.io/packagist/l/bigperson/modulpos-php-api-client.svg?style=flat-square)](https://github.com/bigperson/modulpos-php-api-client/blob/master/LICENSE) 
[![](https://img.shields.io/packagist/dt/bigperson/modulpos-php-api-client.svg?style=flat-square)](https://packagist.org/packages/bigperson/modulpos-php-api-client)
[![](https://img.shields.io/packagist/v/bigperson/modulpos-php-api-client.svg?style=flat-square)](https://packagist.org/packages/bigperson/modulpos-php-api-client)
[![](https://img.shields.io/travis/bigperson/modulpos-php-api-client.svg?style=flat-square)](https://travis-ci.org/bigperson/modulpos-php-api-client)
[![](https://img.shields.io/codecov/c/github/bigperson/modulpos-php-api-client.svg?style=flat-square)](https://codecov.io/gh/bigperson/modulpos-php-api-client)
[![StyleCI](https://styleci.io/repos/98306851/shield?branch=master)](https://styleci.io/repos/98306851)

Пакет предоставляет удобный интерфейс для общения с API Модуль.Кассы для отправки данных чеков в сервис фискализации. 
Пакет упрощает разработку модулей интеграции интернет-магазина с севисом фискализации Модуль.Кассы.

Часть описания дублирует оригинал [документации по API Модуль.Кассы](http://modulkassa.ru/upload/medialibrary/abb/api-avtomaticheskoy-fiskalizatsii-chekov-internet_magazinov-_ver.1.2_.pdf)


## Требования
* php 5.4, 5.6, 7.0, 7.1, 7.2, 7.3
* curl

## Установка
Вы можете установить данный пакет с помощью сomposer:

```
composer require bigperson/modulpos-php-api-client
```

## Использование
Схема процесса фискализации подробна описана в [документации к API](https://support.modulkassa.ru/upload/medialibrary/abb/API%20%D0%B0%D0%B2%D1%82%D0%BE%D0%BC%D0%B0%D1%82%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%BE%D0%B9%20%D1%84%D0%B8%D1%81%D0%BA%D0%B0%D0%BB%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D0%B8%20%D1%87%D0%B5%D0%BA%D0%BE%D0%B2%20%D0%B8%D0%BD%D1%82%D0%B5%D1%80%D0%BD%D0%B5%D1%82-%D0%BC%D0%B0%D0%B3%D0%B0%D0%B7%D0%B8%D0%BD%D0%BE%D0%B2%20(ver.1.4).pdf). В кратце необходимо связать точку продаж с интернет магазином, настроить отправку данных чеков и проверить статус отправленного чека.

### Создания связки аккаунта и розничной точки
Для начала необходимо в личном кабинете Модуль.Кассы создать розничную точку продаж, активировать у неё функцию `Использовать для печати документов интернет-магазина` и получить идентификатор `uuid`. Далее вызываем связку

```php
$login = 'test@test.ru'; // Логин от аккаунта Модуль.Кассы
$password = 'password'; // Пароль от аккаунта Модуль.Кассы
$retailPointUuid = 'uuid'; // Идентификатор розничной точки
$testMode = true; // Тестовый режим
$associate = new \Bigperson\ModulposApiClient\Associate($login, $password, $retailPointUuid, $testMode);
$result = $associate->init();
```

В `$result` получим массив с данным `userName` и `password` которые будут использоватся для дальнейших обращений к API. Их нужно где-нибудь сохранить, например в базе данных.

### Отправка данных чека на сервер фискализации (создание документа)
Для начала необходимо сформировать данные самого чека. Для этого достаточно для ваших моделей инплементировать интерфейсы ModulposOrderInterface для заказа, ModulposOrderItemInterface для товара в заказе, ModulposPaymentItemInterface для способа оплаты. Также вы можете использовать entity из пакета, или отнаследовать от них собственные классы переопределив методы на собственные.
```php
use Bigperson\ModulposApiClient\Entity\Order;
use Bigperson\ModulposApiClient\Entity\Cashier;
use Bigperson\ModulposApiClient\Entity\OrderItem;
use Bigperson\ModulposApiClient\Entity\PaymentItem;

$dateTime =  new \DateTime('NOW');
// Создаем заказ
$order = Order::create([
    'documentUuid'     => uniqid(),
    'checkoutDateTime' => $dateTime->format(DATE_RFC3339),
    'orderId'          => rand(100000, 999999),
    'typeOperation'    => 'SALE',
    'customerContact'  => 'test@example.com',
]);

// Созадем товары
$orderItem1 = OrderItem::create([
    'price' => 100,
    'quantity' => 1,
    'vatTag' => OrderItem::VAT_NO,
    'name' => 'Test Product1'
]);

$orderItem2 = OrderItem::create([
    'price' => 200,
    'quantity' => 1,
    'vatTag' => OrderItem::VAT_NO,
    'name' => 'Test Product2'
]);

//Создаем способ оплаты
$paymentItem = PaymentItem::create([
    'type' => 'CARD',
    'sum' => 300
]);

// Добавляем товары и способ оплаты к заказу
$order->addItem($orderItem1);
$order->addItem($orderItem2);
$order->addPaymentItem($paymentItem);

//Создаем кассира
$cashier = Cashier::create([
    'name' => 'Test Cashier',
    'inn' => '123456789012',
    'position' => 'salesman',
]);
```

Далее объект заказа необходимо передать клиенту, также вы можете передать `responseURL` и печатать ли чек на кассе :
```php
$login = 'test@test.ru'; // Логин полученный на первом шаге
$password = 'password'; // Пароль полученный на первом шаге
$testMode = true; // Тестовый режим
$client = new \Bigperson\ModulposApiClient\Client($login, $password, $testMode);
$responseUrl =  'https://internet.shop.ru/order/982340931/checkout?completed=1';
$printReceipt = true; // Печатать ли чек на кассе
$result = $client->sendCheck($order, $responseUrl, $printReceipt);
```

В ответ придет массив со статусом обработки документа и фискального накопителя.

### Проверка статуса документа
Если при передаче данных чека был передан `responseURL`, то на него придет результат фискализации, если параметр задан не был, то вы можете самостоятельно проверить статус документа:
```php
$login = 'test@test.ru'; // Логин полученный на первом шаге
$password = 'password'; // Пароль полученный на первом шаге
$testMode = true; // Тестовый режим
$documentId = 'efbafcdd-113a-45db-8fb9-718b1fdc3524'; // id документа
$client = new \Bigperson\ModulposApiClient\Client($login, $password, $testMode);
$result = $client->getStatusDocumentById($documentId);
```
В ответ придет массив со статусом `status`, который может принимать значения:
* QUEUED - документ принят в очередь на обработку;
* PENDING - документ получен кассой для печати;
* PRINTED - фискализирован успешно;
* COMPLETED - результат фискализации отправлен (если было заполнено поле responseURL) в сервис источник;
* FAILED - ошибка при фискализации.


Также в массив придет `fnState` - статус фискального накопителя, может принимать значения:

* ready ​- соединение с фискальным накопителем установлено, состояние позволяет фискализировать чеки
* associated​ - клиент успешно связан с розничной точкой, но касса еще ни разу не вышла на связь и не сообщила свое состояние
* failed ​- Проблемы получения статуса фискального накопителя. Этот статус не препятствует добавлению документов для фискализации. Все документы будут добавлены в очередь на сервере и дождутся момента когда касса будет в состоянии их фискализировать

Кроме того вы можете вызвать отдельно метод проверки статуса фискального накопителя (сервиса фискализации):
```php
$client = new \Bigperson\ModulposApiClient\Client($login, $password, $testMode);
$result = $client->getStatusFiscalService();
```


## Лицензия
[MIT](https://raw.githubusercontent.com/bigperson/modulpos-php-api-client/master/LICENSE)
