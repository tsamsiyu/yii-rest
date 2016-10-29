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

It will compose the next routes:

```
'GET        companies/<companyId:\d+>/banks/<bankId:\d+>/accounts'          => 'company/bank/account/index',
'GET        companies/<companyId:\d+>/banks/<bankId:\d+>/accounts/<id:\d+>' => 'company/bank/account/view',
'POST       companies/<companyId:\d+>/banks/<bankId:\d+>/accounts'          => 'company/bank/account/create',
'PATCH,PUT  companies/<companyId:\d+>/banks/<bankId:\d+>/accounts/<id:\d+>' => 'company/bank/account/update',
'DELETE     companies/<companyId:\d+>/banks/<bankId:\d+>/accounts/<id:\d+>' => 'company/bank/account/delete',
'OPTIONS    companies/<companyId:\d+>/banks/<bankId:\d+>/accounts/<id:\d+>' => 'company/bank/account/options',
'OPTIONS    companies/<companyId:\d+>/banks/<bankId:\d+>/accounts'          => 'company/bank/account/options',
```

You can override the controller actions and specify the prefix:

```
    'class' => 'tsamsiyu\yii\rest',
    'resources' => ['company', 'bank', 'account'],
    'prefix' => 'v2/operator',
    'routesMap' => [
        'index' => '{controller}/all',
        'view' => '{controller}/one',
        'downloadIndex' => '{controller}/downloadAll',
        'downloadView' => '{controller}/downloadOne'
    ],
    'routesDescription' => [
        'GET {permanentUrl}/download' => 'downloadIndex',
        'GET {permanentUrl}/{resourceId}/download' => 'downloadView',
        'HEAD {permanentUrl}/files' => 'my/custom/action/without/routes/map',
    ]
```