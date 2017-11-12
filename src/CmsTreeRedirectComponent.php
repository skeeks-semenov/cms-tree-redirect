<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 15.04.2016
 */
namespace skeeks\cms\treeredirect;
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
                    $modelEvent->isValid = false;
                    //throw new \yii\base\Exception('Нет');
                    //return false;
                }
            }
         });
    }
}