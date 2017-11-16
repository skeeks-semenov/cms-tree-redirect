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
use yii\db\AfterSaveEvent;
use yii\helpers\ArrayHelper;
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
    
    protected $_slugRedurect = null;

    public function bootstrap($application)
    {
        \yii\base\Event::on(\yii\db\ActiveRecord::class, \yii\db\ActiveRecord::EVENT_AFTER_INSERT, function(AfterSaveEvent $modelEvent)  {
            if (!$this->is_enabled) {
                return false;
            }


            /**
            * @var $model \skeeks\cms\models\Tree
            */
            $model = $modelEvent->sender;
            if ($modelEvent->sender instanceof \skeeks\cms\models\Tree) {

                $this->_slugRedurect->cms_tree_id = $model->id;
                if (!$this->_slugRedurect->save()) {
                    //$modelEvent->isValid = false;
                    \Yii::error("Not saved redirect: {$this->_slugRedurect->slug} for id={$model->id}. " . print_r($this->_slugRedurect->errors, true), self::class);
                }

            }

        });

        \yii\base\Event::on(\yii\db\ActiveRecord::class, \yii\db\ActiveRecord::EVENT_BEFORE_INSERT, function(\yii\base\ModelEvent $modelEvent) {
            if (!$this->is_enabled) {
                return false;
            }

            /**
            * @var $model \skeeks\cms\models\Tree
            */
            $model = $modelEvent->sender;
            if ($modelEvent->sender instanceof \skeeks\cms\models\Tree) {


                if ($redirect = CmsTreeRedirect::find()->andWhere(['slug' => $model->code])->one()) {

                    return false;
                    $modelEvent->isValid = false;
                    \Yii::warning("Exist slug: {$redirect->slug}", self::class);
                }

                $redirect = new CmsTreeRedirect();
                $redirect->slug = $model->code;

                $this->_slugRedurect = $redirect;
            }

        });

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

        if (\Yii::$app instanceof Application) {
            \Yii::$app->on(Application::EVENT_AFTER_ACTION, function ($e) {
                if (\Yii::$app->requestedAction->controller->uniqueId != 'cms/error') {
                    return true;
                }

                if (!$this->is_enabled) {
                    return false;
                }


                $current_url = \Yii::$app->getRequest()->getAbsoluteUrl();
                $parsed_current_url = parse_url($current_url);
                $current_path = ArrayHelper::getValue($parsed_current_url, 'path', '');


                $findedRedirect = null;
                $parts = preg_split('/[^\-\_a-zA-Z0-9]+/iu', $current_path, -1, PREG_SPLIT_NO_EMPTY);
                $parts = array_reverse($parts);

                foreach ($parts as $part) {

                    $treeRedirect = CmsTreeRedirect::find()->andWhere(['slug' => $part])->one();
                    if ($treeRedirect) {
                        $findedRedirect = $treeRedirect;
                        break;
                    }
                }

                /**
                 * @var $findedRedirect CmsTreeRedirect
                 */
                if ($findedRedirect) {
                    \Yii::$app->response->redirect($findedRedirect->cmsTree->url, 301);
                };


            });
        }
    }
}