<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 13.11.2017
 */
namespace skeeks\cms\treeredirect\console\controllers;

use skeeks\cms\models\CmsTree;
use skeeks\cms\treeredirect\models\CmsTreeRedirect;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Class UpdateController
 * @package skeeks\cms\treeredirect\console\controllers
 */
class UpdateController extends Controller
{
    /**
     * Init agents from config files
     */
    public function actionTree()
    {
        foreach (CmsTree::find()->each(10) as $cmsTree) {
            $this->stdout('Tree id: ' . $cmsTree->id . " - ");
            
            $redirect = CmsTreeRedirect::find()->where([
                'cms_tree_id' => $cmsTree->id
            ])->andWhere(['slug' => $cmsTree->code])->one();
            
            if ($redirect) {
                $this->stdout("exist \n", Console::FG_YELLOW);
            } else {
                $redirect = new CmsTreeRedirect();
                $redirect->cms_tree_id = $cmsTree->id;
                $redirect->slug = $cmsTree->code;

                if (!$redirect->save()) {
                    $this->stdout("Not saved redirect: {$redirect->slug} for id={$cmsTree->id}. " . print_r($redirect->errors, true) . "\n", Console::FG_RED);
                } else {
                    $this->stdout("created \n", Console::FG_GREEN);
                }
            }
        }
    }


}