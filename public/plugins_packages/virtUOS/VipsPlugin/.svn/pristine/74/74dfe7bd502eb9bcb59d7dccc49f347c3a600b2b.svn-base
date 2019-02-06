<?php

class AddTestMetadata extends Migration
{
    function description()
    {
        return 'add user_id and time attributes to vips_test';
    }

    function up()
    {
        $db = DBManager::get();

        $sql = "ALTER TABLE vips_aufgabe
                ADD created TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00'";
        $db->exec($sql);

        $sql = "ALTER TABLE vips_test
                ADD user_id VARCHAR(32) NOT NULL AFTER description,
                ADD created TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER user_id,
                CHANGE course_id course_id VARCHAR(32) NULL,
                ADD KEY user_id (user_id)";
        $db->exec($sql);

        // set initial default for all exercises
        $sql = 'UPDATE vips_aufgabe SET created = NOW()';
        $db->exec($sql);

        // update user_id of test with course lecturer
        $sql = "UPDATE vips_test, seminar_user
                SET vips_test.created = vips_test.start,
                    vips_test.user_id = seminar_user.user_id
                WHERE vips_test.course_id = seminar_user.Seminar_id
                  AND status = 'dozent'";
        $db->exec($sql);

        // update user_id of test with exercise author
        $sql = 'UPDATE vips_test, vips_exercise_ref, vips_aufgabe
                SET vips_aufgabe.created = vips_test.start,
                    vips_test.created = vips_test.start,
                    vips_test.user_id = vips_aufgabe.Userid
                WHERE vips_exercise_ref.exercise_id = vips_aufgabe.ID
                  AND vips_exercise_ref.test_id = vips_test.id';
        $db->exec($sql);
    }

    function down()
    {
        $db = DBManager::get();

        $sql = 'ALTER TABLE vips_aufgabe DROP created';
        $db->exec($sql);

        $sql = 'ALTER TABLE vips_test DROP KEY user_id, DROP user_id, DROP created';
        $db->exec($sql);
    }
}
?>
