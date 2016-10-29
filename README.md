To install this package add the composer dependency:
```
    tsamsiyu/yii-rest 1.0.0-beta1
```

The features to help working with REST api in Yii:

1) Nested Routes
It's easy to use:
put it in your urlManager.rules:

```php
[
    'class' => 'tsamsiyu\yii\rest\NestedUrlRule',
    'resources' => ['company', 'bank', 'account']
]
```

It will generate the next routes:

```php
'GET        companies/<companyId:\d+>/banks/<bankId:\d+>/accounts'          => 'company/bank/account/index',
'GET        companies/<companyId:\d+>/banks/<bankId:\d+>/accounts/<id:\d+>' => 'company/bank/account/view',
'POST       companies/<companyId:\d+>/banks/<bankId:\d+>/accounts'          => 'company/bank/account/create',
'PATCH,PUT  companies/<companyId:\d+>/banks/<bankId:\d+>/accounts/<id:\d+>' => 'company/bank/account/update',
'DELETE     companies/<companyId:\d+>/banks/<bankId:\d+>/accounts/<id:\d+>' => 'company/bank/account/delete',
'OPTIONS    companies/<companyId:\d+>/banks/<bankId:\d+>/accounts/<id:\d+>' => 'company/bank/account/options',
'OPTIONS    companies/<companyId:\d+>/banks/<bankId:\d+>/accounts'          => 'company/bank/account/options',
```

You can override the controller actions and specify the prefix:

```php
    'class' => 'tsamsiyu\yii\rest\NestedUrlRule',
    'resources' => ['company', 'bank', 'account'],
    'prefix' => 'v2/operator',
    'routesMap' => [
        'index' => '{controller}/all', // rewrite base index action
        'view' => '{controller}/one', // rewrite base view action
        'downloadIndex' => '{controller}/downloadAll', // define new action
        'downloadView' => '{controller}/downloadOne' // define new action
    ],
    'routesDescription' => [
        'GET {permanentUrl}/download' => 'downloadIndex', // use the defined action
        'GET {permanentUrl}/{resourceId}/download' => 'downloadView', // use the defined action
        'HEAD {permanentUrl}/files' => 'my/custom/action/without/routes/map', // use the non previously defined action
    ]
```

Also you can define the controller namespace (concatenation of resources with backslash will be used by default).
Just specify the 'controller' key in your rule config:

```php
    'class' => 'tsamsiyu\yii\rest\NestedUrlRule',
    'resources' => ['company', 'bank', 'account'],
    'prefix' => 'operator',
    'controller' => 'operator/{controller}' // {controller} placeholder is "company/bank/account"
```