<?php

class CleanUpTables extends Migration
{
    function description()
    {
        return 'clean up obsolete vips data and tables';
    }

    function up()
    {
        $db = DBManager::get();

        // delete all data for deleted courses
        $db->exec('DELETE vips_block FROM vips_block
                   LEFT JOIN seminare ON seminare.Seminar_id = vips_block.Kurs
                   WHERE seminare.Seminar_id IS NULL');

        $db->exec('DELETE vips_gruppe FROM vips_gruppe
                   LEFT JOIN seminare ON seminare.Seminar_id = vips_gruppe.Kursid
                   WHERE seminare.Seminar_id IS NULL');

        $db->exec('DELETE vips_inGruppe FROM vips_inGruppe
                   LEFT JOIN vips_gruppe ON vips_gruppe.Gruppenid = vips_inGruppe.group_id
                   WHERE vips_gruppe.Gruppenid IS NULL');

        $db->exec('DELETE vips_noten FROM vips_noten
                   LEFT JOIN seminare ON seminare.Seminar_id = vips_noten.Kurs
                   WHERE seminare.Seminar_id IS NULL');

        $db->exec('DELETE vips_optionen FROM vips_optionen
                   LEFT JOIN seminare ON seminare.Seminar_id = vips_optionen.Kursid
                   WHERE seminare.Seminar_id IS NULL');

        $db->exec('DELETE vips_assignment FROM vips_assignment
                   LEFT JOIN seminare ON seminare.Seminar_id = vips_assignment.course_id
                   WHERE seminare.Seminar_id IS NULL');

        $db->exec('DELETE vips_test FROM vips_test
                   LEFT JOIN vips_assignment ON vips_assignment.test_id = vips_test.id
                   WHERE vips_assignment.test_id IS NULL');

        $db->exec('DELETE vips_exercise_ref FROM vips_exercise_ref
                   LEFT JOIN vips_test ON vips_test.id = vips_exercise_ref.test_id
                   WHERE vips_test.id IS NULL');

        $db->exec('DELETE vips_exercise FROM vips_exercise
                   LEFT JOIN vips_exercise_ref ON vips_exercise_ref.exercise_id = vips_exercise.id
                   WHERE vips_exercise_ref.exercise_id IS NULL');

        $db->exec('DELETE vips_solution FROM vips_solution
                   LEFT JOIN vips_exercise ON vips_exercise.id = vips_solution.exercise_id
                   WHERE vips_exercise.id IS NULL');

        $db->exec('DELETE vips_solution_archive FROM vips_solution_archive
                   LEFT JOIN vips_exercise ON vips_exercise.id = vips_solution_archive.exercise_id
                   WHERE vips_exercise.id IS NULL');

        $db->exec('DELETE vips_assignment_attempt FROM vips_assignment_attempt
                   LEFT JOIN vips_assignment ON vips_assignment.id = vips_assignment_attempt.assignment_id
                   WHERE vips_assignment.id IS NULL');

        // delete unused column
        $db->exec('ALTER TABLE vips_optionen DROP Tutor, ADD grades TEXT NULL');

        $grades = [];
        $result = $db->query('SELECT * FROM vips_noten WHERE Prozent < 101 ORDER BY Kurs, Note');
        $stmt = $db->prepare('INSERT INTO vips_optionen (Kursid, grades) VALUES(?, ?)
                              ON DUPLICATE KEY UPDATE grades = VALUES(grades)');

        foreach ($result as $row) {
            $grades[$row['Kurs']][] = [
                'grade'   => $row['Note'],
                'percent' => $row['Prozent'],
                'comment' => $row['comment']
            ];
        }

        foreach ($grades as $course_id => $grade) {
            $stmt->execute([$course_id, json_encode(studip_utf8encode($grade))]);
        }

        // add weight to vips_assignment, vips_block
        $db->exec('ALTER TABLE vips_assignment ADD weight FLOAT NOT NULL DEFAULT 0 AFTER active');
        $db->exec('ALTER TABLE vips_block ADD weight FLOAT NOT NULL DEFAULT 0');

        $db->exec("UPDATE vips_assignment, vips_gewichtung
                     SET vips_assignment.weight = vips_gewichtung.Gewichtung
                   WHERE vips_assignment.id = vips_gewichtung.Item_id
                     AND vips_gewichtung.Item_type IN('sheets', 'exams')");

        $db->exec("UPDATE vips_block, vips_gewichtung
                     SET vips_block.weight = vips_gewichtung.Gewichtung
                   WHERE vips_block.id = vips_gewichtung.Item_id
                     AND vips_gewichtung.Item_type = 'blocks'");

        // drop obsolete tables
        $db->exec('DROP TABLE vips_noten, vips_gewichtung, vips_klausur_gruppierung');

        SimpleORMap::expireTableScheme();
    }

    function down()
    {
        $db = DBManager::get();

        // restore tables and their contents
        $sql = "CREATE TABLE vips_klausur_gruppierung (
                id INT(11) NOT NULL AUTO_INCREMENT,
                k1 INT(11) NOT NULL DEFAULT 0,
                k2 INT(11) NOT NULL DEFAULT 0,
                fail_k1 ENUM('bestehen_k2','prozent_k2') COLLATE latin1_bin NOT NULL DEFAULT 'bestehen_k2',
                pass_k1 ENUM('max_k1_k2','prozent_k2') COLLATE latin1_bin NOT NULL DEFAULT 'max_k1_k2',
                kurs VARCHAR(32) COLLATE latin1_bin NOT NULL,
                PRIMARY KEY (id))";
        $db->exec($sql);

        $sql = "CREATE TABLE vips_gewichtung (
                Item_id INT(11) NOT NULL DEFAULT 0,
                Gewichtung FLOAT NOT NULL DEFAULT 0,
                Item_type ENUM('sheets','exams','blocks','groups') COLLATE latin1_bin NOT NULL DEFAULT 'sheets',
                PRIMARY KEY (Item_id, Item_type))";
        $db->exec($sql);

        $sql = "CREATE TABLE vips_noten (
                Note CHAR(3) COLLATE latin1_bin NOT NULL DEFAULT '0',
                Prozent SMALLINT(6) unsigned NOT NULL default '0',
                Kurs VARCHAR(32) COLLATE latin1_bin NOT NULL,
                comment VARCHAR(64) NOT NULL default '',
                PRIMARY KEY (Note, Kurs),
                KEY Kurs (Kurs))";
        $db->exec($sql);

        $db->exec("INSERT INTO vips_gewichtung SELECT id, weight, IF(type = 'exam', 'exams', 'sheets')
                   FROM vips_assignment WHERE weight > 0");

        $db->exec("INSERT INTO vips_gewichtung SELECT id, weight, 'blocks'
                   FROM vips_block WHERE weight > 0");

        $grades = ['0,7', '1,0', '1,3', '1,7', '2,0', '2,3', '2,7', '3,0', '3,3', '3,7', '4,0'];
        $result = $db->query('SELECT * FROM vips_optionen WHERE grades IS NOT NULL');
        $stmt = $db->prepare('REPLACE INTO vips_noten (Note, Prozent, Kurs, comment) VALUES(?, ?, ?, ?)');

        foreach ($result as $row) {
            $grade_settings = studip_utf8decode(json_decode($row['grades'], true));

            foreach ($grades as $grade) {
                $stmt->execute([$grade, 101, $row['Kursid'], '']);
            }

            foreach ($grade_settings as $setting) {
                $stmt->execute([$setting['grade'], $setting['percent'], $row['Kursid'], $setting['comment']]);
            }
        }

        // drop weight from vips_assignment, vips_block
        $db->exec('ALTER TABLE vips_assignment DROP weight');
        $db->exec('ALTER TABLE vips_block DROP weight');

        // restore unused column
        $db->exec('ALTER TABLE vips_optionen DROP grades, ADD Tutor TINYINT(1) NOT NULL DEFAULT 0');

        SimpleORMap::expireTableScheme();
    }
}
