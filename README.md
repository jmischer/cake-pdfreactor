# CakePdf PDFreactor engine plugin for CakePHP

This Plugin contains a wrapper engine for the PDFreactor Webservice
Client to use with the CakePHP 
[CakePdf plugin](https://github.com/FriendsOfCake/CakePdf).

This Plugin is not affiliated with RealObjects.com and does not contain
the Webservice client. You will need to import it, e.g. in your applications
`bootstrap.php`.

``` php
require_once '/path/to/PDFreactor/wrappers/php/lib/PDFreactor.class.php';

```


## Installation

Install using [Composer](https://getcomposer.org):

```
composer require jmischer/cake-pdfreactor
```

## Configuration

`config/cakepdf.php`:

``` php
return [
    'CakePdf' => [
        'engine' => [
            'className' => 'JMischer/CakePDFreactor.PDFreactor',
            'client' => [
                'className' => '\com\realobjects\pdfreactor\webservice\client\PDFreactor',
                'serviceUrl' => 'http://localhost:9423/service/rest',
            ],
            'options' => [
                // PDFreactor configuration ...
            ]
        ]
    ]
];
```
