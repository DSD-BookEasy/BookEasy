<?php

use yii\db\Schema;
use yii\db\Migration;

class m141220_180550_init_images extends Migration
{
    public function up()
    {
        // Taken from widget's migration m140622_111540_create_image_table in v1.0.4

        $this->createTable('image', [
            'id' => 'pk',
            'filePath' => 'VARCHAR(400) NOT NULL',
            'itemId' => 'int(20) NOT NULL',
            'isMain' => 'int(1)',
            'modelName' => 'VARCHAR(150) NOT NULL',
            'urlAlias' => 'VARCHAR(400) NOT NULL',
        ]);
    }

    public function down()
    {
        $this->dropTable('image');
    }
}
