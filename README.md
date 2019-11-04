[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

# CakePdf PDFreactor engine plugin for CakePHP

This plugin contains a wrapper engine for the PDFreactor Web Service
Client to use with the CakePHP 3 [CakePdf plugin](https://github.com/FriendsOfCake/CakePdf).

This Plugin is not affiliated with RealObjects.com and does not contain
the Webservice client. You will need to import it, e.g. in your applications
`bootstrap.php`.

``` php
require_once '/path/to/PDFreactor/wrappers/php/lib/PDFreactor.class.php';

```

## Installation

Install using [Composer](https://getcomposer.org):

``` bash
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
