<?php

class VipsExamMode extends Migration
{
    function description()
    {
        return 'add setting for vips exam mode';
    }

    function up()
    {
        $db = DBManager::get();

        // fix missing values in old Courseware import
        $db->exec("UPDATE vips_test
                    JOIN vips_assignment ON vips_test.id = vips_assignment.test_id
                     SET vips_test.created = vips_assignment.start
                   WHERE vips_test.created = '0000-00-00 00:00:00'");

        $db->exec("UPDATE vips_exercise
                    JOIN vips_exercise_ref ON vips_exercise.id = vips_exercise_ref.exercise_id
                    JOIN vips_test ON vips_exercise_ref.test_id = vips_test.id
                     SET vips_exercise.created = vips_test.created
                   WHERE vips_exercise.created = '0000-00-00 00:00:00'");

        // update vips plugin type
        $db->exec("UPDATE plugins SET plugintype = 'StandardPlugin,StudipModule,SystemPlugin' WHERE pluginname = 'Vips'");

        $description = 'Sperrt während einer Klausur andere Bereiche von Stud.IP für die Teilnehmer';

        if (version_compare($GLOBALS['SOFTWARE_VERSION'], '4', '>=')) {
            $description = utf8_encode($description);
        }

        Config::get()->create('VIPS_EXAM_RESTRICTIONS', [
            'type' => 'boolean', 'value' => 0, 'description' => $description
        ]);
    }

    function down()
    {
        $db = DBManager::get();

        // update vips plugin type
        $db->exec("UPDATE plugins SET plugintype = 'StandardPlugin,StudipModule' WHERE pluginname = 'Vips'");

        Config::get()->delete('VIPS_EXAM_RESTRICTIONS');
    }
}
