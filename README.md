# PHP клиент для API автоматической фискализации чеков интернет-магазинов Модуль.Кассы
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/bigperson/modulpos-php-api-client/master/LICENSE) [![Build Status](https://travis-ci.org/bigperson/modulpos-php-api-client.svg?branch=master)](https://travis-ci.org/bigperson/modulpos-php-api-client) [![codecov](https://codecov.io/gh/bigperson/modulpos-php-api-client/branch/master/graph/badge.svg)](https://codecov.io/gh/bigperson/modulpos-php-api-client)

Пакет предоставляет удобный интерфейс для общения с API Модуль.Кассы для отправки данных чеков в сервис фискализации. Пакет может быть и упрощает разработку модулей интеграции интернет-магазина с севисом фискализации Модуль.Кассы.

Часть описания дублирует оригинал [документации по API Модуль.Кассы](http://modulkassa.ru/upload/medialibrary/abb/api-avtomaticheskoy-fiskalizatsii-chekov-internet_magazinov-_ver.1.2_.pdf)


## Требования
* php 5.4, 5.6

## Установка
Вы можете установить данный пакет с помощью сomposer:

```
composer require bigperson/modulpos-php-api-client
```

## Использование
Схема процесса фискализации подробна описана в [документации к API](http://modulkassa.ru/upload/medialibrary/abb/api-avtomaticheskoy-fiskalizatsii-chekov-internet_magazinov-_ver.1.2_.pdf). В кратце необходимо связать точку продаж с интернет магазином, настроить отправку данных чеков и проверить статус отправленного чека.

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
Для начала необходимо сформировать данные самого чека:
```php
$checkData = [
            'id'               => 'efbafcdd-113a-45db-8fb9-718b1fdc3524', // id документа
            'checkoutDateTime' => '"2016-12-23T06:06:58+00:00', // Дата документа
            'docNum'           => '12345', // Номер заказа в магазине
            'docType'          => 'SALE', // Тип документа: SALE - продажа, RETURN - возврат
            'printReceipt'     => false, // Печатать ли бумажный чек на кассе
            'email'            => 'test@example.com', // Электронная почта покупателя
            'inventPositions'  => // Массив номенклатуры товаров в чеке
                [
                    0 => [
                            'barcode'  => '', // Штрихкод товара
                            'discSum'  => 0, // Сумма скидки, примененной на позицию
                            'name'     => 'Подажа по свободной цене', // Название товара
                            'price'    => 100, // Цена товара
                            'quantity' => 1, // Количество товара
                            'vatTag'   => 1107, // Тег НДС согласно ФЗ-54
                        ],
                ],
            'moneyPositions'   =>
                [
                    0 => [
                            'paymentType' => 'CARD', // Тип оплаты: CARD - безналичная оплата, CASH - оплата наличными
                            'sum'         => 100, // Сумма выбранного типа оплаты
                        ],
                ],
            'responseURL'      => 'https://internet.shop.ru/order/982340931/checkout?completed=1', // URL куда будет сообщен результат фискализации по конкретному документу
        ];
```

Далее этот чек необходимо передать клиенту:
```php
$login = 'test@test.ru'; // Логин полученный на первом шаге
$password = 'password'; // Пароль полученный на первом шаге
$testMode = true; // Тестовый режим
$client = new \Bigperson\ModulposApiClient\Client($login, $password, true);
$result = $client->sendCheck($checkData);
```

В ответ придет массив со статусом обработки документа и фискального накопителя.

### Проверка статуса документа
Если при передаче данных чека был передан `responseURL`, то на него придет результат фискализации, если параметр задан не был, то вы можете самостоятельно проверить статус документа:
```php
$login = 'test@test.ru'; // Логин полученный на первом шаге
$password = 'password'; // Пароль полученный на первом шаге
$testMode = true; // Тестовый режим
$documentId = 'efbafcdd-113a-45db-8fb9-718b1fdc3524'; // id документа
$client = new \Bigperson\ModulposApiClient\Client($login, $password, true);
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
$client = new \Bigperson\ModulposApiClient\Client($login, $password, true);
$result = $client->getStatusFiscalService();
```


## Лицензия
[MIT](https://raw.githubusercontent.com/bigperson/modulpos-php-api-client/master/LICENSE)
