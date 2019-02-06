<?php

class FloatWeight extends Migration
{
    function description()
    {
        return 'database changes for floating point weights';
    }

    function up()
    {
        $db = DBManager::get();
        $db->exec('ALTER TABLE vips_gewichtung CHANGE Gewichtung Gewichtung FLOAT NOT NULL');
    }

    function down()
    {
        $db = DBManager::get();
        $db->exec('ALTER TABLE vips_gewichtung CHANGE Gewichtung Gewichtung INT(2) NOT NULL');
    }
}
