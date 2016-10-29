<?php namespace tsamsiyu\tests\yii\rest\mocks\app\controllers\user;

use yii\web\Controller;

class PostController extends Controller
{
    public function actionIndex()
    {
        return 'actionResponse:user/post/index';
    }

    public function actionView($id)
    {
        return 'actionResponse:user/post/view/' . $id;
    }

    public function actionCreate()
    {
        return 'actionResponse:user/post/create';
    }

    public function actionUpdate($id)
    {
        return 'actionResponse:user/post/update/' . $id;
    }

    public function actionDelete($id)
    {
        return 'actionResponse:user/post/delete/' . $id;
    }
}