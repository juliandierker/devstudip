<?php

class UpdateDbCollation extends Migration
{
    function description()
    {
        return 'update database collation for Stud.IP 4.0';
    }

    function up()
    {
        $db = DBManager::get();

        $db->exec("ALTER TABLE vips_assignment
                   CHANGE course_id course_id VARCHAR(32) COLLATE latin1_bin NOT NULL,
                   CHANGE type type ENUM('exam','practice','selftest') COLLATE latin1_bin NOT NULL");
        $db->exec('ALTER TABLE vips_assignment_attempt
                   CHANGE user_id user_id VARCHAR(32) COLLATE latin1_bin NOT NULL');
        $db->exec('ALTER TABLE vips_block
                   CHANGE Kurs Kurs VARCHAR(32) COLLATE latin1_bin NOT NULL');
        $db->exec('ALTER TABLE vips_exercise
                   CHANGE user_id user_id VARCHAR(32) COLLATE latin1_bin NOT NULL');
        $db->exec("ALTER TABLE vips_gewichtung
                   CHANGE Item_type Item_type ENUM('sheets','exams','blocks','groups') COLLATE latin1_bin NOT NULL DEFAULT 'sheets'");
        $db->exec('ALTER TABLE vips_gruppe
                   CHANGE Kursid Kursid VARCHAR(32) COLLATE latin1_bin NOT NULL');
        $db->exec('ALTER TABLE vips_inGruppe
                   CHANGE user_id user_id VARCHAR(32) COLLATE latin1_bin NOT NULL');
        $db->exec("ALTER TABLE vips_klausur_gruppierung
                   CHANGE fail_k1 fail_k1 ENUM('bestehen_k2','prozent_k2') COLLATE latin1_bin NOT NULL DEFAULT 'bestehen_k2',
                   CHANGE pass_k1 pass_k1 ENUM('max_k1_k2','prozent_k2') COLLATE latin1_bin NOT NULL DEFAULT 'max_k1_k2',
                   CHANGE kurs kurs VARCHAR(32) COLLATE latin1_bin NOT NULL");
        $db->exec("ALTER TABLE vips_noten
                   CHANGE Note Note CHAR(3) COLLATE latin1_bin NOT NULL DEFAULT '0',
                   CHANGE Kurs Kurs VARCHAR(32) COLLATE latin1_bin NOT NULL");
        $db->exec('ALTER TABLE vips_optionen
                   CHANGE Kursid Kursid VARCHAR(32) COLLATE latin1_bin NOT NULL,
                   CHANGE Selbstzuweisung Selbstzuweisung TINYINT(1) NOT NULL DEFAULT 0,
                   CHANGE Tutor Tutor TINYINT(1) NOT NULL DEFAULT 0');
        $db->exec('ALTER TABLE vips_solution
                   CHANGE user_id user_id VARCHAR(32) COLLATE latin1_bin NOT NULL,
                   CHANGE corrector_id corrector_id VARCHAR(32) COLLATE latin1_bin DEFAULT NULL,
                   ADD options TEXT NOT NULL');
        $db->exec('ALTER TABLE vips_solution_archive
                   CHANGE user_id user_id VARCHAR(32) COLLATE latin1_bin NOT NULL,
                   CHANGE corrector_id corrector_id VARCHAR(32) COLLATE latin1_bin DEFAULT NULL,
                   ADD options TEXT NOT NULL');
        $db->exec('ALTER TABLE vips_test
                   CHANGE user_id user_id VARCHAR(32) COLLATE latin1_bin NOT NULL');

        // add block_id to vips_assignment
        $db->exec('ALTER TABLE vips_assignment ADD block_id INT(11) NULL AFTER active');

        $db->exec('UPDATE vips_assignment, vips_inBlock, vips_block
                     SET vips_assignment.block_id = vips_inBlock.block_id
                   WHERE vips_assignment.id = vips_inBlock.assignment_id
                     AND vips_inBlock.block_id = vips_block.id');

        // drop obsolete tables
        $db->exec('DROP TABLE vips_inBlock, vips_subexercise');

        SimpleORMap::expireTableScheme();
    }

    function down()
    {
        $db = DBManager::get();

        // restore tables and their contents
        $sql = 'CREATE TABLE vips_inBlock (
                block_id INT(11) NOT NULL DEFAULT 0,
                assignment_id INT(11) NOT NULL,
                PRIMARY KEY (block_id, assignment_id))';
        $db->exec($sql);

        $sql = 'CREATE TABLE vips_subexercise (
                exercise_id INT(11) NOT NULL,
                subexercise_id INT(11) NOT NULL,
                position INT(11) NOT NULL,
                KEY exercise_id (exercise_id),
                KEY subexercise_id (subexercise_id))';
        $db->exec($sql);

        $db->exec('INSERT INTO vips_inBlock SELECT block_id, id
                   FROM vips_assignment WHERE block_id IS NOT NULL');

        $db->exec('ALTER TABLE vips_solution DROP options');
        $db->exec('ALTER TABLE vips_solution_archive DROP options');

        // drop block_id from vips_assignment
        $db->exec('ALTER TABLE vips_assignment DROP block_id');

        SimpleORMap::expireTableScheme();
    }
}
