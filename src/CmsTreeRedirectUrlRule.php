<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 15.04.2016
 */
namespace skeeks\cms\treeredirect;
use skeeks\cms\treeredirect\models\CmsTreeRedirect;
use yii\base\BootstrapInterface;
use yii\base\Component;
use yii\web\Application;

/**
 * Class CmsTreeRedirectComponent
 * @package skeeks\cms\treeredirect
 */
class CmsTreeRedirectComponent extends Component implements BootstrapInterface
{
    /**
     * @var bool
     */
    public $is_enabled = true;

    public function bootstrap($application)
    {
        \yii\base\Event::on(\yii\db\ActiveRecord::class, \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE, function(\yii\base\ModelEvent $modelEvent) {

            if (!$this->is_enabled) {
                return false;
            }
            /**
            * @var $model \skeeks\cms\models\Tree
            */
            $model = $modelEvent->sender;
            if ($modelEvent->sender instanceof \skeeks\cms\models\Tree) {
                if ($model->isAttributeChanged('code')) {

                    //Если старое значение еще не сохраняли, сохраним сразу
                    $oldCode = $model->getOldAttribute('code');
                    if (!CmsTreeRedirect::find()->where([
                        'cms_tree_id' => $model->id
                    ])->andWhere(['slug' => $oldCode])->one()) {

                        $redirect = new CmsTreeRedirect();
                        $redirect->cms_tree_id = $model->id;
                        $redirect->slug = $oldCode;

                        if (!$redirect->save()) {
                            $modelEvent->isValid = false;
                            \Yii::error("Not save old redirect: {$redirect->slug} for id={$model->id}. " . print_r($redirect->errors, true), self::class);
                        }

                    }

                    //Если такой slug для данного раздела создан
                    if (CmsTreeRedirect::find()->where([
                        'cms_tree_id' => $model->id
                    ])->andWhere(['slug' => $model->code])->one()) {
                        return true;
                    }


                    $redirect = new CmsTreeRedirect();
                    $redirect->cms_tree_id = $model->id;
                    $redirect->slug = $model->code;

                    if (!$redirect->save()) {
                        $modelEvent->isValid = false;
                        \Yii::error("Not saved redirect: {$redirect->slug} for id={$model->id}. " . print_r($redirect->errors, true), self::class);
                    }

                    //throw new \yii\base\Exception('Нет');
                    //return false;
                }
            }
         });
    }
}