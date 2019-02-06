<?php

class DeleteExercises extends Migration
{
    function description()
    {
        return 'remove data of deleted users from the database';
    }

    function up()
    {
        $db = DBManager::get();

        // delete all submissions of deleted users
        $db->exec('DELETE FROM vips_assignment_attempt
                   WHERE user_id NOT IN (SELECT user_id FROM auth_user_md5)');
        $db->exec('DELETE FROM vips_inGruppe
                   WHERE user_id NOT IN (SELECT user_id FROM auth_user_md5)');
        $db->exec('DELETE FROM vips_solution
                   WHERE user_id NOT IN (SELECT user_id FROM auth_user_md5)');
        $db->exec('DELETE FROM vips_solution_archive
                   WHERE user_id NOT IN (SELECT user_id FROM auth_user_md5)');

        // delete duplicate values from vips_assignment_attempt
        $db->exec('DELETE vaa1 FROM vips_assignment_attempt vaa1
                   JOIN vips_assignment_attempt vaa2 USING(assignment_id, user_id)
                   WHERE vaa1.id > vaa2.id');

        // drop course_id from vips_assignment_attempt
        $db->exec('ALTER TABLE vips_assignment_attempt
                   DROP course_id,
                   DROP KEY test_id,
                   ADD UNIQUE KEY assignment_id (assignment_id, user_id)');

        SimpleORMap::expireTableScheme();
    }

    function down()
    {
        $db = DBManager::get();

        // restore course_id in vips_assignment_attempt
        $db->exec('ALTER TABLE vips_assignment_attempt
                   ADD course_id VARCHAR(32) NOT NULL AFTER assignment_id,
                   DROP KEY assignment_id,
                   ADD KEY test_id (assignment_id, user_id)');

        $db->exec('UPDATE vips_assignment_attempt, vips_assignment
                   SET vips_assignment_attempt.course_id = vips_assignment.course_id
                   WHERE vips_assignment_attempt.assignment_id = vips_assignment.id');

        SimpleORMap::expireTableScheme();
    }
}
