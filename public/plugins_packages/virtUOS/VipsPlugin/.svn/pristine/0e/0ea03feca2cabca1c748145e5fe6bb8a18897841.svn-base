<?php

class SplitVipsTest extends Migration
{
    function description()
    {
        return 'split table vips_test in vips_test and vips_assignment';
    }

    function up()
    {
        $db = DBManager::get();

        // create new table for vips_assignment
        $sql = "CREATE TABLE vips_assignment (
                id INT(11) NOT NULL AUTO_INCREMENT,
                test_id INT(11) NOT NULL,
                course_id VARCHAR(32) NOT NULL,
                type ENUM('exam', 'practice', 'selftest') NOT NULL,
                start TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
                end TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
                active BOOL NOT NULL DEFAULT 1,
                options TEXT NULL,
                PRIMARY KEY (id),
                KEY test_id (test_id),
                KEY course_id (course_id))";
        $db->exec($sql);

        // copy the necessary columns into the new table vips_assignment
        $sql = 'INSERT INTO vips_assignment (id, test_id, course_id, type, start, end, active, options)
                SELECT id, id, course_id, type, start, end, NOT halted, options FROM vips_test';
        $db->exec($sql);

        // drop the old columns
        $sql = 'ALTER TABLE vips_test
                DROP type,
                DROP course_id,
                DROP position,
                DROP start,
                DROP end,
                DROP halted,
                CHANGE options options TEXT NULL';
        $db->exec($sql);
        $db->exec('UPDATE vips_test SET options = NULL');

        // rename test_id to assignment_id where appropriate
        $db->exec('ALTER TABLE vips_inBlock CHANGE test_id assignment_id INT(11) NOT NULL');
        $db->exec('ALTER TABLE vips_solution CHANGE test_id assignment_id INT(11) NOT NULL');
        $db->exec('ALTER TABLE vips_solution_archive CHANGE test_id assignment_id INT(11) NOT NULL');
        $db->exec('ALTER TABLE vips_test_attempt CHANGE test_id assignment_id INT(11) NOT NULL');
        $db->exec('ALTER TABLE vips_test_attempt RENAME TO vips_assignment_attempt');

        SimpleORMap::expireTableScheme();
    }

    function down()
    {
        $db = DBManager::get();

        // rename assignment_id to test_id where appropriate
        $db->exec('ALTER TABLE vips_inBlock CHANGE assignment_id test_id INT(11) NOT NULL');
        $db->exec('ALTER TABLE vips_solution CHANGE assignment_id test_id INT(11) NOT NULL');
        $db->exec('ALTER TABLE vips_solution_archive CHANGE assignment_id test_id INT(11) NOT NULL');
        $db->exec('ALTER TABLE vips_assignment_attempt RENAME TO vips_test_attempt');
        $db->exec('ALTER TABLE vips_test_attempt CHANGE assignment_id test_id INT(11) NOT NULL');

        // add the necessary columns
        $sql = "ALTER TABLE vips_test
                ADD type ENUM('exam', 'practice', 'selftest') NOT NULL AFTER id,
                ADD course_id VARCHAR(32) NULL AFTER type,
                ADD position INT(11) NOT NULL AFTER course_id,
                ADD start TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER created,
                ADD end TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER start,
                ADD halted BOOL NOT NULL AFTER end,
                CHANGE options options TEXT NOT NULL,
                ADD KEY type (type),
                ADD KEY course_id (course_id)";
        $db->exec($sql);

        // copy the data back into the vips_test table
        $sql = 'UPDATE vips_test, vips_assignment
                SET vips_test.type      = vips_assignment.type,
                    vips_test.course_id = vips_assignment.course_id,
                    vips_test.position  = 1,
                    vips_test.start     = vips_assignment.start,
                    vips_test.end       = vips_assignment.end,
                    vips_test.halted    = NOT vips_assignment.active,
                    vips_test.options   = vips_assignment.options
                WHERE vips_test.id = vips_assignment.test_id';
        $db->exec($sql);

        // drop the superfluous table
        $db->exec('DROP TABLE vips_assignment');

        SimpleORMap::expireTableScheme();
    }
}
