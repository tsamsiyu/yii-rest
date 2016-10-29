<?php namespace tsamsiyu\tests\yii\rest;

require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');

use Codeception\Test\Unit;
use tsamsiyu\yii\rest\NestedUrlRule;
use yii\web\Application;

class RuleTest extends Unit
{
    public $patterns = [

    ];

    public function testSimple()
    {
        $app = $this->appFactory('/index.php/users/10/posts');
        $app->run();
        expect($app->response->data)->equals('actionResponse:user/post/index');

        $app = $this->appFactory('/index.php/users/10/posts/7');
        $app->run();
        expect($app->response->data)->equals('actionResponse:user/post/view/7');
    }

    protected function appFactory($uri, $method = 'GET')
    {
        $_SERVER['REQUEST_URI'] = $uri;
        $_SERVER['REQUEST_METHOD'] = $method;
        return new Application([
            'id' => 'yiiRest',
            'basePath' => __DIR__ . '/../mocks/app',
            'controllerNamespace' => 'tsamsiyu\tests\yii\rest\mocks\app\controllers',
            'components' => [
                'request' => [
                    'scriptUrl' => '/index.php'
                ],
                'urlManager' => [
                    'enablePrettyUrl' => true,
                    'enableStrictParsing' => true,
                    'showScriptName' => false,
                    'rules' => [
                        [
                            'class' => NestedUrlRule::class,
                            'resources' => ['user', 'post']
                        ]
                    ]
                ]
            ]
        ]);
    }
}