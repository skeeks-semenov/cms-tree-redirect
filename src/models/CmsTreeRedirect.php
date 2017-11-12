<?php

namespace skeeks\cms\treeredirect\models;

use skeeks\cms\models\CmsTree;
use Yii;

/**
 * This is the model class for table "{{%cms_tree_redirect}}".
 *
 * @property integer $id
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $cms_tree_id
 * @property string $slug
 *
 * @property CmsTree $cmsTree
 * @property CmsUser $createdBy
 * @property CmsUser $updatedBy
 */
class CmsTreeRedirect extends \skeeks\cms\models\Core
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cms_tree_redirect}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_by', 'updated_by', 'created_at', 'updated_at', 'cms_tree_id'], 'integer'],
            [['cms_tree_id', 'slug'], 'required'],
            [['slug'], 'string', 'max' => 255],
            [['slug'], 'unique'],
            [['cms_tree_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmsTree::className(), 'targetAttribute' => ['cms_tree_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cms_tree_id' => 'Cms Tree ID',
            'slug' => 'Slug',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCmsTree()
    {
        return $this->hasOne(CmsTree::className(), ['id' => 'cms_tree_id']);
    }

}