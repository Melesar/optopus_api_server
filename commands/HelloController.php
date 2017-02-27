<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\AppUser;
use yii\console\Controller;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HelloController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex($message = 'hello world')
    {
        echo $message . "\n";
    }

    /**
     * This command is testing now...
     */

    const DATE_FORMAT = "Y-m-d H:i:s";

    public function actionUpdateLives()
    {
        $currentDate = new \DateTime();
        $interval = new \DateInterval('PT5M');
        AppUser::updateAll(['SERVER_TIMESTAMP' => $currentDate->format(self::DATE_FORMAT)]);
        //AppUser::updateAll(['LIVES' => 0]);
        $ob = AppUser::find()->where('LIVES < :LIVES',[':LIVES' => 5])->all();
        if($ob)
        {
            foreach($ob as $obj)
            {
                if ($obj->SERVER_TIMESTAMP >= $obj->NEXT_UPDATE)
                {
                    $obj->updateCounters(['LIVES' => 1]);
                    if($obj->LIVES < 5)
                        $obj->NEXT_UPDATE = $currentDate->add($interval)->format(self::DATE_FORMAT);
                    $obj->save();
                }
            }
        }
    }
}
