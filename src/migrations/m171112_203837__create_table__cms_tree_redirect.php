<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 28.08.2015
 */
use yii\db\Schema;
use yii\db\Migration;

class m171112_203837__create_table__cms_tree_redirect extends Migration
{
    public function safeUp()
    {
        $tableExist = $this->db->getTableSchema("{{%cms_tree_redirect}}", true);
        if ($tableExist) {
            return true;
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable("{{%cms_tree_redirect}}", [
            'id'                        => $this->primaryKey(),

            'created_by'                => $this->integer(),
            'updated_by'                => $this->integer(),

            'created_at'                => $this->integer(),
            'updated_at'                => $this->integer(),

            'cms_tree_id'               => $this->integer()->notNull(),
            'slug'                      => $this->string(255)->notNull()->unique(),

        ], $tableOptions);

        $this->createIndex('updated_by', '{{%cms_tree_redirect}}', 'updated_by');
        $this->createIndex('created_by', '{{%cms_tree_redirect}}', 'created_by');
        $this->createIndex('created_at', '{{%cms_tree_redirect}}', 'created_at');
        $this->createIndex('updated_at', '{{%cms_tree_redirect}}', 'updated_at');
        $this->createIndex('cms_tree_id', '{{%cms_tree_redirect}}', 'cms_tree_id');


        $this->addForeignKey(
            'cms_tree_redirect__created_by', "{{%cms_tree_redirect}}",
            'created_by', '{{%cms_user}}', 'id', 'SET NULL', 'SET NULL'
        );

        $this->addForeignKey(
            'cms_tree_redirect__updated_by', "{{%cms_tree_redirect}}",
            'updated_by', '{{%cms_user}}', 'id', 'SET NULL', 'SET NULL'
        );

        $this->addForeignKey(
            'cms_tree_redirect__cms_tree_id', "{{%cms_tree_redirect}}",
            'cms_tree_id', '{{%cms_tree}}', 'id', 'CASCADE', 'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey("cms_tree_redirect__created_by", "{{%cms_tree_redirect}}");
        $this->dropForeignKey("cms_tree_redirect__updated_by", "{{%cms_tree_redirect}}");
        $this->dropForeignKey("cms_tree_redirect__cms_tree_id", "{{%cms_tree_redirect}}");

        $this->dropTable("{{%cms_tree_redirect}}");
    }
}