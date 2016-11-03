<?php
/**
 * Created by PhpStorm.
 * User: serge_000
 * Date: 02/11/2016
 * Time: 22:22
 */

namespace app\controllers;

use yii\rest\Controller;

class UsersController extends Controller
{
    public function actionData()
    {
        return ['name' => 'John', 'last_name' => 'Snow', 'pets' => ['12', 234, 34.54]];
    }
}