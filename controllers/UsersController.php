<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02.11.2016
 * Time: 22:31
 */

namespace app\controllers;

use app\models\Users;
use yii\rest\Controller;
use yii;
use yii\web\NotFoundHttpException;

class UsersController extends Controller
{

    public function actionData() //им€ - втора€ часть, т.е. Data
    {
       return("HELLO!");
    }

    public function actionGet()
    {
        //$users = new Users();
        //$users->name = "Oleg";
        //$users->last_name = "Konm";
        //$users->save();
        $id = Yii::$app->request->get('id');
        $users = Users::findOne($id);
        return $users->attributes;
    }

    public function actionPost($id)
    {
        $data = Yii::$app->request->getBodyParams(); //берем данные из пересланных данных по JSON --- данные считаны
        $users = new Users(); //создаем нового ѕ”—“ќ√ќ пользовател€ --- создан
        $users->id=$id; //переписываем id созданного пользовател€
        $users->setAttributes($data,false); //загрузка данных из JSON
        $users->save(); //сохран€ем изменени€
        return $users->attributes;
    }

    public function actionPut($id) // аналогично POST, но + проверка на существующие и добавление
    {
        $users = Users::findOne($id);
        if($users === null)
        {
            //return("404"); //пользователь не найден - проверка пройдена
            $data = Yii::$app->request->getBodyParams(); //берем данные из пересланных данных по JSON --- данные считаны
            $users = new Users(); //создаем нового ѕ”—“ќ√ќ пользовател€ --- создан
            $users->id = $id; //переписываем id созданного пользовател€
            $users->setAttributes($data,false); //загрузка данных из JSON --- загрузка успешна
            $users->save(); //сохран€ем изменени€
            return $users->attributes;
        }
        else
        {
            //return("EXSIST"); // пользователь существует - проверка пройдена
            $data = Yii::$app->request->getBodyParams(); //берем данные из пересланных данных по JSON --- данные считаны
            $users->setAttributes($data,false); //загрузка данных из JSON --- загрузка успешна
            $users->update(); //обновл€ем внесенные изменени€
            return $users->attributes;
        }
    }

    public function actionPutfriends($user_id)
    {
        $data = Yii::$app->request->getBodyParams(); // id друзей пользоваетел€
        $users = Users::findOne($user_id); // найти позьзовател€ через его id
        if($users === null)
        {
            throw new NotFoundHttpException(); // костыль 404
        }
        $users->setfriends($data); // вызываем метод из модели метод setfriends и передаем в него массив id всех друзей
        return("PUT FRIENDS"); //заглушка
    }
}
