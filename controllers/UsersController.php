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

    public function actionData() //��� - ������ �����, �.�. Data
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
        $data = Yii::$app->request->getBodyParams(); //����� ������ �� ����������� ������ �� JSON --- ������ �������
        $users = new Users(); //������� ������ ������� ������������ --- ������
        $users->id=$id; //������������ id ���������� ������������
        $users->setAttributes($data,false); //�������� ������ �� JSON
        $users->save(); //��������� ���������
        return $users->attributes;
    }

    public function actionPut($id) // ���������� POST, �� + �������� �� ������������ � ����������
    {
        $users = Users::findOne($id);
        if($users === null)
        {
            //return("404"); //������������ �� ������ - �������� ��������
            $data = Yii::$app->request->getBodyParams(); //����� ������ �� ����������� ������ �� JSON --- ������ �������
            $users = new Users(); //������� ������ ������� ������������ --- ������
            $users->id = $id; //������������ id ���������� ������������
            $users->setAttributes($data,false); //�������� ������ �� JSON --- �������� �������
            $users->save(); //��������� ���������
            return $users->attributes;
        }
        else
        {
            //return("EXSIST"); // ������������ ���������� - �������� ��������
            $data = Yii::$app->request->getBodyParams(); //����� ������ �� ����������� ������ �� JSON --- ������ �������
            $users->setAttributes($data,false); //�������� ������ �� JSON --- �������� �������
            $users->update(); //��������� ��������� ���������
            return $users->attributes;
        }
    }

    public function actionPutfriends($user_id)
    {
        $data = Yii::$app->request->getBodyParams(); // id ������ �������������
        $users = Users::findOne($user_id); // ����� ������������ ����� ��� id
        if($users === null)
        {
            throw new NotFoundHttpException(); // 404
        }
        $users->setfriends($data); // �������� ����� �� ������ ����� setfriends � �������� � ���� ������ id ���� ������
        return("PUT FRIENDS");
    }
}
