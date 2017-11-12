<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 24.05.2015
 */
namespace skeeks\cms\treeredirect;
use skeeks\cms\models\CmsContentElement;
use skeeks\cms\models\Tree;
use \yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Class UrlRuleContentElement
 * @package skeeks\cms\components\urlRules
 */
class CmsTreeRedirectUrlRule
    extends \yii\web\UrlRule
{

    public function init()
    {
        if ($this->name === null)
        {
            $this->name = __CLASS__;
        }
    }

    /**
     * @param \yii\web\UrlManager $manager
     * @param string $route
     * @param array $params
     * @return bool|string
     */
    public function createUrl($manager, $route, $params)
    {
        return false;
    }

    /**
     * @param \yii\web\UrlManager $manager
     * @param \yii\web\Request $request
     * @return array|bool
     */
    public function parseRequest($manager, $request)
    {
        if ($this->mode === self::CREATION_ONLY) {
            return false;
        }

        if (!empty($this->verb) && !in_array($request->getMethod(), $this->verb, true)) {
            return false;
        }

        $pathInfo           = $request->getPathInfo();
        if ($this->host !== null) {
            $pathInfo = strtolower($request->getHostInfo()) . ($pathInfo === '' ? '' : '/' . $pathInfo);
        }


        $params             = $request->getQueryParams();
        $suffix             = (string)($this->suffix === null ? $manager->suffix : $this->suffix);
        $treeNode           = null;

        if (!$pathInfo)
        {
            return false;
        }

        if (!preg_match('/\/(?<id>\d+)\-(?<code>\S+)$/i', "/" . $pathInfo, $matches))
        {
            return false;
        }


        return ['cms/content-element/view', [
            'id'    => $matches['id'],
            'code'  => $matches['code']
        ]];
    }


}
