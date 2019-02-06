<?php

class MergeTests extends Migration
{
    function description()
    {
        return 'merge tables vips_klausur and vips_uebungsblatt to vips_test';
    }



    function up()
    {
        $db = DBManager::get();

        // create table vips_test
        // (old vips_uebungsblatt and vips_klausur)
        $sql = "CREATE TABLE vips_test (
            id INT(11) NOT NULL AUTO_INCREMENT,
            type ENUM('exam', 'practice', 'selftest') NOT NULL,
            course_id VARCHAR(32) NOT NULL,
            position INT(11) NOT NULL,
            title VARCHAR(64) NOT NULL,
            description TEXT NOT NULL,
            start TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
            end TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
            halted BOOL NOT NULL,
            options TEXT NOT NULL,
            PRIMARY KEY (id),
            KEY type (type),
            KEY course_id (course_id))";
        $db->exec($sql);

        // create table vips_solution
        // (old vips_T_Loesung, vips_T_Loesung_Punkte, vips_K_Loesung and
        // vips_K_Loesung_Punkte)
        $sql = "CREATE TABLE vips_solution (
            id INT(11) NOT NULL AUTO_INCREMENT,
            exercise_id INT(11) NOT NULL,
            test_id INT(11) NOT NULL,
            user_id VARCHAR(32) NOT NULL,
            solution TEXT NOT NULL,
            student_comment TEXT NULL,
            time TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
            ip_address VARCHAR(39) NOT NULL,
            corrected BOOL NOT NULL,
            points FLOAT NULL,
            corrector_id VARCHAR(32) NULL,
            corrector_comment TEXT NULL,
            commented_solution TEXT NULL,
            correction_time TIMESTAMP NULL DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY exercise_id (exercise_id, test_id, user_id))";
        $db->exec($sql);

        // create table vips_solution_archive
        // (for outdated solutions (isLastSolution == "f"))
        $sql = "CREATE TABLE vips_solution_archive (
            id INT(11) NOT NULL,
            exercise_id INT(11) NOT NULL,
            test_id INT(11) NOT NULL,
            user_id VARCHAR(32) NOT NULL,
            solution TEXT NOT NULL,
            student_comment TEXT NULL,
            time TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
            ip_address VARCHAR(39) NOT NULL,
            corrected BOOL NOT NULL,
            points FLOAT NULL,
            corrector_id VARCHAR(32) NULL,
            corrector_comment TEXT NULL,
            commented_solution TEXT NULL,
            correction_time TIMESTAMP NULL DEFAULT NULL,
            PRIMARY KEY (id),
            KEY exercise_id (exercise_id, test_id, user_id))";
        $db->exec($sql);

        // create table vips_exercise_ref
        // (old vips_inUebungsblatt and vips_inKlausur)
        $sql = "CREATE TABLE vips_exercise_ref (
            exercise_id INT(11) NOT NULL,
            test_id INT(11) NOT NULL,
            position INT(11) NOT NULL,
            points FLOAT NOT NULL DEFAULT 0,
            disregard BOOL NOT NULL DEFAULT 0,
            KEY exercise_id (exercise_id),
            KEY test_id (test_id))";
        $db->exec($sql);

        // alter table vips_aufgaben_zeit
        $sql = "ALTER TABLE vips_aufgaben_zeit
            ADD test_id INT(11) NOT NULL
            AFTER vips_uebungsblatt,
            ADD KEY test_id (test_id)";
        $db->exec($sql);

        // alter table vips_inBlock
        $sql = "ALTER TABLE vips_inBlock
            ADD test_id INT(11) NOT NULL,
            ADD KEY block_id (block_id)";
        $db->exec($sql);



        ///////////////////////////////////////////////
        // process each exercise sheet and each exam //
        ///////////////////////////////////////////////

        $tables = [
            ['type'     => 'practice',  // first exercise sheets...
                  'sheet'    => 'vips_uebungsblatt',
                  'solution' => 'vips_T_Loesung',
                  'points'   => 'vips_T_Loesung_Punkte',
                  'in_sheet' => 'vips_inUebungsblatt'],
            ['type'     => 'exam',  // ... then exams
                  'sheet'    => 'vips_klausur',
                  'solution' => 'vips_K_Loesung',
                  'points'   => 'vips_K_Loesung_Punkte',
                  'in_sheet' => 'vips_inKlausur']];
        foreach ($tables as $table) {  // first every exercise sheet, then every exam
            $type     = $table['type'];
            $sheet    = $table['sheet'];
            $solution = $table['solution'];
            $points   = $table['points'];
            $in_sheet = $table['in_sheet'];

            $test_id_mapping = [];  // stores mappings from old test ids to new ones



            /////////////////////
            // each assignment //
            /////////////////////

            $sql = "SELECT * FROM $sheet";
            $assignments = $db->query($sql);

            foreach ($assignments as $ass) {  // every exercise sheet / exam
                $old_test_id = $ass['ID'];

                // assemble options
                $options_array = [
                    'evaluation_mode' => (int) $ass['mc_auswertung'],
                    'shuffle_answers' => $type == 'exam',
                    'printable'       => (boolean) $ass['Sichtbarkeit'],
                    'released'        => $ass['korrekturenSichtbar'] == 't' ? 2 : 0];
                if ($ass['Dauer'] != 0 && $type == 'exam') {
                    $options_array['duration'] = (int) $ass['Dauer'];
                }
                if ($ass['IPBereich'] != '' && $type == 'exam') {
                    $options_array['ip_range'] = $ass['IPBereich'];
                }
                if ($ass['Bestehen'] != 0 && $type == 'exam') {
                    $options_array['pass_threshold'] = (int) $ass['Bestehen'];
                }
                $options = json_encode($options_array);

                // selftest exams will become selftests
                $test_type = $ass['Selbsttest'] == 1
                           ? 'selftest'
                           : $type;

                // insert new data set
                $sql = "INSERT INTO vips_test (
                        type,
                        course_id,
                        position,
                        title,
                        description,
                        start,
                        end,
                        halted,
                        options)
                    VALUES (
                        '$test_type',
                        '{$ass['Kurs']}',
                        {$ass['Position']},
                        ".$db->quote($ass['Name']).",
                        ".$db->quote($ass['Thema']).",
                        '{$ass['Beginn']}',
                        '{$ass['Ende']}',
                        ".($ass['istGesperrt'] == 'y' ? 1 : 0).",
                        ".$db->quote($options).")";
                $db->exec($sql);

                $new_test_id = $db->lastInsertId();

                $test_id_mapping[$old_test_id] = $new_test_id;
            }  // end for each assignment



            //////////////////////////////////////////////
            // delete duplicate entries in points table //
            //////////////////////////////////////////////

            $sql = "SELECT * FROM $points GROUP BY idLoesung HAVING COUNT(idLoesung) > 1";
            $solutions = $db->query($sql);

            foreach ($solutions as $sol) {
                $sql = "SELECT * FROM $points WHERE idLoesung = ".$sol['idLoesung'];
                $result = $db->query($sql);
                unset($punkte);

                foreach ($result as $row) {
                    if (!isset($punkte) ||
                        $row['Punkte'] > $punkte ||
                        $row['Punkte'] == $punkte && $row['Safe'] == 't') {
                        $user_id = $row['idUser'];
                        $punkte = $row['Punkte'];
                        $safe = $row['Safe'];
                    }
                }

                $db->exec("DELETE FROM $points WHERE idLoesung = ".$sol['idLoesung']);
                $db->exec("INSERT INTO $points VALUES ('$user_id', ".$sol['idLoesung'].", $punkte, '$safe')");
            }



            ////////////////////////////////////////
            // delete outdated selftest solutions //
            ////////////////////////////////////////

            $sql = "DELETE $solution
                FROM $solution, $sheet
                WHERE $solution.Assignmentid = $sheet.ID
                  AND $solution.isLastSolution = 'f'
                  AND $sheet.Selbsttest = 1";
            $db->exec($sql);



            //////////////////////////////
            // each solution and points //
            //////////////////////////////

            $sql = "SELECT *
                FROM $solution
                LEFT JOIN $points
                       ON idLoesung = Loesungsid
                ORDER BY isLastSolution, Zeitpunkt";
            $solutions = $db->query($sql);

            foreach ($solutions as $sol) {
                // set values for corrected, punkte, corrector_comment
                $punkte = $sol['Punkte'];
                $corrector_comment = 'NULL';
                if (!isset($sol['Punkte'])) {  // not corrected at all
                    $corrected = 0;
                    $punkte = 'NULL';
                }
                else if ($sol['isAlreadyCorrected'] == '1') {  // corrected manually
                    $corrected = 1;
                    $corrector_comment = $db->quote($sol['Korrektur']);
                }
                else if ($sol['Safe'] == 't') {  // corrected automatically and safe
                    $corrected = 1;
                }
                else {  // corrected automatically and unsafe
                    $corrected = 0;
                }

                if (isset($test_id_mapping[$sol['Assignmentid']])) {  // if there is a new test id to this old one
                    // insert new data set
                    $sql = "REPLACE INTO vips_solution (
                            exercise_id,
                            test_id,
                            user_id,
                            solution,
                            student_comment,
                            time,
                            ip_address,
                            corrected,
                            points,
                            corrector_comment,
                            commented_solution)
                        VALUES (
                            {$sol['Aufgabenid']},
                            {$test_id_mapping[$sol['Assignmentid']]},
                            '{$sol['Userid']}',
                            ".$db->quote($sol['Loesung']).",
                            ".($sol['Anmerkung'] != '' ? $db->quote($sol['Anmerkung']) : 'NULL').",
                            '{$sol['Zeitpunkt']}',
                            '".$sol['IPAdresse']."',
                            $corrected,
                            $punkte,
                            $corrector_comment,
                            ".($sol['bearbeiteteLoesung'] != '' ? $db->quote($sol['bearbeiteteLoesung']) : 'NULL').")";
                    $db->exec($sql);

                    $solution_id = $db->lastInsertId();

                    // if solution is not current, move it into solution archive
                    // (AFTER having it inserted into vips_solution to keep ids
                    // autoincremented)
                    if ($sol['isLastSolution'] != 't') {
                        // copy
                        $sql = 'INSERT INTO vips_solution_archive SELECT * FROM vips_solution WHERE id = '.$solution_id;
                        $db->exec($sql);

                        // delete
                        $sql = 'DELETE FROM vips_solution WHERE id = '.$solution_id;
                        $db->exec($sql);
                    }
                }
                else {
                    // do not insert data set into new table, thus it will be deleted
                }
            }  // end for each solution



            /////////////////////////////
            // each exercise reference //
            /////////////////////////////

            // TODO why can vips_aufgabe be NULL here?
            $sql = "SELECT * FROM $in_sheet WHERE vips_aufgabe != 0";
            $reference = $db->query($sql);

            foreach ($reference as $ref) {
                if (isset($test_id_mapping[$ref[$sheet]])) {  // if there is a new test id to this old one
                    // insert new data set
                    $sql = "INSERT INTO vips_exercise_ref (
                            exercise_id,
                            test_id,
                            position,
                            points,
                            disregard)
                        VALUES (
                            {$ref['vips_aufgabe']},
                            {$test_id_mapping[$ref[$sheet]]},
                            {$ref['Position']},
                            {$ref['Punkte']},
                            ".($ref['Bewertet'] == 'f' ? 1 : 0).")";
                    $db->exec($sql);
                }
                else {
                    // do not insert data set into new table, thus it will be deleted
                }
            }  // end for each reference


            /////////////////////////////////////////////////////
            // update other tables with references to test ids //
            // (vips_aufgaben_zeit, vips_inBlock and           //
            // vips_klausur_gruppierung)                       //
            /////////////////////////////////////////////////////

            // delete wrong entries
            $sql = 'DELETE FROM vips_aufgaben_zeit WHERE vips_klausur = 0 AND vips_uebungsblatt = 0';
            $db->exec($sql);

            // vips_aufgaben_zeit //
            $sql = "SELECT DISTINCT $sheet
                FROM vips_aufgaben_zeit
                WHERE $sheet != 0";
            $result = $db->query($sql);
            foreach ($result as $res) {  // process each assignment id
                $old_test_id = $res[$sheet];
                $new_test_id = $test_id_mapping[$res[$sheet]];  // may be null

                if (isset($new_test_id)) {
                    $sql = "UPDATE vips_aufgaben_zeit
                        SET test_id = $new_test_id
                        WHERE $sheet = $old_test_id";
                    $db->exec($sql);
                }
                else {
                    $sql = "DELETE
                        FROM vips_aufgaben_zeit
                        WHERE $sheet = $old_test_id";
                    $db->exec($sql);
                }
            }

            // vips_inBlock //
            if ($type == 'practice') {  // only for practices
                $sql = "SELECT *
                    FROM vips_inBlock
                    GROUP BY assignment_id";
                $result = $db->query($sql);
                foreach ($result as $res) {  // process each assignment id
                    $old_test_id = $res['assignment_id'];
                    $new_test_id = $test_id_mapping[$res['assignment_id']];  // may be null

                    if (isset($new_test_id)) {
                        $sql = "UPDATE vips_inBlock
                            SET test_id = $new_test_id
                            WHERE assignment_id = $old_test_id";
                        $db->exec($sql);
                    }
                    else {
                        $sql = "DELETE
                            FROM vips_inBlock
                            WHERE assignment_id = $old_test_id";
                        $db->exec($sql);
                    }
                }
            }

            // vips_klausur_gruppierung //
            if ($type == 'exam') {  // only for exams
                $sql = "SELECT * FROM vips_klausur_gruppierung";
                $result = $db->query($sql);
                foreach ($result as $res) {
                    $old_k1 = $res['k1'];
                    $old_k2 = $res['k2'];
                    $new_k1 = $test_id_mapping[$res['k1']];  // may be null
                    $new_k2 = $test_id_mapping[$res['k2']];  // may be null

                    if (isset($new_k1) && isset($new_k2)) {
                        $sql = "UPDATE vips_klausur_gruppierung
                            SET k1 = $new_k1,
                                k2 = $new_k2
                            WHERE id = {$res['id']}";
                        $db->exec($sql);
                    }
                    else {
                        $sql = "DELETE
                            FROM vips_klausur_gruppierung
                            WHERE id = {$res['id']}";
                        $db->exec($sql);
                    }
                }
            }
        }  // end for each table (ie. each practice and each exam)



        // delete obsolete tables
        $sql = "DROP TABLE
            vips_uebungsblatt,
            vips_klausur,
            vips_T_Loesung,
            vips_T_Loesung_Punkte,
            vips_K_Loesung,
            vips_K_Loesung_Punkte,
            vips_inUebungsblatt,
            vips_inKlausur";
        $db->exec($sql);

        // alter table vips_aufgaben_zeit
        $sql = "ALTER TABLE vips_aufgaben_zeit
            DROP vips_uebungsblatt,
            DROP vips_klausur";
        $db->exec($sql);

        // alter table vips_inBlock
        $sql = "ALTER TABLE vips_inBlock
            DROP assignment_id";
        $db->exec($sql);
    }



    function down()
    {
        $db = DBManager::get();

        // create table vips_uebungsblatt
        $sql = "CREATE TABLE vips_uebungsblatt (
            ID int(11) unsigned NOT NULL auto_increment,
            Name varchar(64) NOT NULL default '',
            Beginn datetime NOT NULL default '0000-00-00 00:00:00',
            Ende datetime NOT NULL default '0000-00-00 00:00:00',
            Kurs varchar(32) NOT NULL default '',
            Dauer int(11) unsigned NOT NULL default '0',
            Selbsttest tinyint(4) default '0',
            Sichtbarkeit tinyint(4) unsigned NOT NULL default '0',
            IPBereich varchar(255) NOT NULL default '',
            Position mediumint(9) NOT NULL default '0',
            Thema text NOT NULL,
            istGesperrt char(1) NOT NULL default 'n',
            korrekturenSichtbar char(1) NOT NULL default 'f',
            mc_auswertung int(2) NOT NULL default '0',
            PRIMARY KEY (ID),
            KEY Kurs (Kurs))";
        $db->exec($sql);

        // create table vips_klausur
        $sql = "CREATE TABLE vips_klausur (
            ID int(11) unsigned NOT NULL auto_increment,
            Name varchar(64) NOT NULL default '',
            Beginn datetime NOT NULL default '0000-00-00 00:00:00',
            Ende datetime NOT NULL default '0000-00-00 00:00:00',
            Kurs varchar(32) NOT NULL default '',
            Dauer int(11) unsigned NOT NULL default '0',
            Selbsttest tinyint(4) default '0',
            Sichtbarkeit tinyint(4) unsigned NOT NULL default '0',
            IPBereich varchar(255) NOT NULL default '',
            Position mediumint(9) unsigned NOT NULL default '0',
            Thema text NOT NULL,
            istGesperrt char(1) NOT NULL default 'n',
            korrekturenSichtbar char(1) NOT NULL default 'f',
            Bestehen int(3) NOT NULL default '0',
            mc_auswertung int(2) NOT NULL default '0',
            PRIMARY KEY (ID),
            KEY Kurs (Kurs))";
        $db->exec($sql);

        // create table vips_T_Loesung
        $sql = "CREATE TABLE vips_T_Loesung (
            Loesungsid int(11) unsigned NOT NULL auto_increment,
            Loesung text NOT NULL,
            Assignmentid int(11) unsigned NOT NULL default '0',
            Gruppenid int(11) unsigned NOT NULL default '0',
            Userid varchar(32) NOT NULL default '',
            Aufgabenid int(11) unsigned NOT NULL default '0',
            Korrektur text NOT NULL,
            bearbeiteteLoesung text NOT NULL,
            Anmerkung text NOT NULL,
            Zeitpunkt datetime default NULL,
            isLastSolution char(1) NOT NULL default '0',
            isAlreadyCorrected char(1) NOT NULL default '0',
            visibleForStudent char(1) NOT NULL default 'f',
            PRIMARY KEY (Loesungsid),
            KEY Userid (Userid),
            KEY Aufgabenid (Aufgabenid),
            KEY Gruppenid (Gruppenid),
            KEY Uebungsblattid (Assignmentid))";
        $db->exec($sql);

        // create table vips_T_Loesung_Punkte
        $sql = "CREATE TABLE vips_T_Loesung_Punkte (
            idUser varchar(32) NOT NULL default '',
            idLoesung int(11) unsigned NOT NULL default '0',
            Punkte float NOT NULL default '0',
            Safe char(1) NOT NULL default '0',
            KEY idLoesung (idLoesung),
            KEY idUser (idUser))";
        $db->exec($sql);

        // create table vips_K_Loesung
        $sql = "CREATE TABLE vips_K_Loesung (
            Loesungsid int(11) unsigned NOT NULL auto_increment,
            Loesung text NOT NULL,
            Assignmentid int(11) unsigned NOT NULL default '0',
            Gruppenid varchar(32) NOT NULL default '',
            Userid varchar(32) NOT NULL default '',
            Aufgabenid int(11) unsigned NOT NULL default '0',
            Korrektur text NOT NULL,
            bearbeiteteLoesung text NOT NULL,
            Anmerkung text NOT NULL,
            Zeitpunkt datetime default NULL,
            isLastSolution char(1) NOT NULL default '0',
            isAlreadyCorrected char(1) NOT NULL default '0',
            visibleForStudent char(1) NOT NULL default 'f',
            IPAdresse varchar(20) NOT NULL default '',
            PRIMARY KEY  (Loesungsid),
            KEY Userid (Userid),
            KEY Aufgabenid (Aufgabenid),
            KEY Gruppenid (Gruppenid),
            KEY Klausurid (Assignmentid))";
        $db->exec($sql);

        // create table vips_K_Loesung_Punkte
        $sql = "CREATE TABLE vips_K_Loesung_Punkte (
            idUser varchar(32) NOT NULL default '',
            idLoesung int(11) unsigned NOT NULL default '0',
            Punkte float NOT NULL default '0',
            Safe char(1) NOT NULL default '0',
            KEY idLoesung (idLoesung),
            KEY idUser (idUser))";
        $db->exec($sql);

        // create table vips_inUebungsblatt
        $sql = "CREATE TABLE vips_inUebungsblatt (
            vips_aufgabe int(11) unsigned NOT NULL default '0',
            vips_uebungsblatt int(11) NOT NULL default '0',
            Punkte float NOT NULL default '0',
            Bewertet char(1) NOT NULL default 'f',
            Position tinyint(4) unsigned NOT NULL default '0',
            KEY Aufgabe (vips_aufgabe),
            KEY Uebungsblatt (vips_uebungsblatt))";
        $db->exec($sql);

        // create table vips_inKlausur
        $sql = "CREATE TABLE vips_inKlausur (
            vips_aufgabe int(11) unsigned NOT NULL default '0',
            vips_klausur int(11) unsigned NOT NULL default '0',
            Punkte float NOT NULL default '0',
            Bewertet char(1) NOT NULL default 'f',
            Position tinyint(4) unsigned NOT NULL default '0',
            KEY Aufgabe (vips_aufgabe),
            KEY Klausur (vips_klausur))";
        $db->exec($sql);

        // alter table vips_aufgaben_zeit
        $sql = "ALTER TABLE vips_aufgaben_zeit
            ADD vips_klausur int(11) unsigned NOT NULL default '0' AFTER Kurs,
            ADD vips_uebungsblatt int(11) unsigned NOT NULL default '0' AFTER vips_klausur,
            ADD KEY Klausur (vips_klausur),
            ADD KEY Uebungsblatt (vips_uebungsblatt)";
        $db->exec($sql);

        // alter table vips_inBlock (just rename column)
        $sql = "ALTER TABLE vips_inBlock
            CHANGE test_id assignment_id INT(11) NOT NULL DEFAULT '0',
            DROP KEY block_id";
        $db->exec($sql);



        ///////////////
        // each test //
        ///////////////

        $klausur_ids = [];

        $tests = $db->query('SELECT * FROM vips_test');
        foreach ($tests as $test) {
            $selftest       = $test['type'] == 'selftest' ? 1 : 0;
            $options        = json_decode($test['options'], true);
            $duration       = isset($options['duration']) ? $options['duration'] : 0;
            $released       = $options['released'] ? 't' : 'f';
            $pass_threshold = isset($options['pass_threshold']) ? $options['pass_threshold'] : 0;

            if ($test['type'] == 'exam') {  // exam
                $klausur_ids[$test['id']] = true;

                $sql = "INSERT INTO vips_klausur (
                        ID,
                        Name,
                        Beginn,
                        Ende,
                        Kurs,
                        Dauer,
                        Sichtbarkeit,
                        IPBereich,
                        Position,
                        Thema,
                        istGesperrt,
                        korrekturenSichtbar,
                        Bestehen,
                        mc_auswertung)
                    VALUES (
                        ".$test['id'].",
                        ".$db->quote($test['title']).",
                        '".$test['start']."',
                        '".$test['end']."',
                        '".$test['course_id']."',
                        ".$duration.",
                        ".(int) $options['printable'].",
                        ".$db->quote($options['ip_range']).",
                        ".$test['position'].",
                        ".$db->quote($test['description']).",
                        ".$test['halted'].",
                        '".$released."',
                        ".$pass_threshold.",
                        ".$options['evaluation_mode'].")";
            }
            else {  // practice / selftest
                $sql = "INSERT INTO vips_uebungsblatt (
                        ID,
                        Name,
                        Beginn,
                        Ende,
                        Kurs,
                        Selbsttest,
                        Sichtbarkeit,
                        Position,
                        Thema,
                        istGesperrt,
                        korrekturenSichtbar,
                        mc_auswertung)
                    VALUES (
                        ".$test['id'].",
                        ".$db->quote($test['title']).",
                        '".$test['start']."',
                        '".$test['end']."',
                        '".$test['course_id']."',
                        ".$selftest.",
                        ".(int) $options['printable'].",
                        ".$test['position'].",
                        ".$db->quote($test['description']).",
                        ".$test['halted'].",
                        '".$released."',
                        ".$options['evaluation_mode'].")";
            }
            $db->exec($sql);
            // note that selftests will be stored in vips_uebungsblatt!

            // IMPORTANT INFO: All ids remain the same!

        }  // end for each test



        ///////////////
        // solutions //
        ///////////////

        // copy all exam solutions from vips_solution
        $sql = "INSERT INTO vips_K_Loesung
            SELECT vips_solution.id AS Loesungsid,
                vips_solution.solution AS Loesung,
                vips_solution.test_id AS Assignmentid,
                0 AS Gruppenid,
                vips_solution.user_id AS Userid,
                vips_solution.exercise_id AS Aufgabenid,
                IFNULL(vips_solution.corrector_comment, '') AS Korrektur,
                IFNULL(vips_solution.commented_solution, '') AS bearbeiteteLoesung,
                IFNULL(vips_solution.student_comment, '') AS Anmerkung,
                vips_solution.time AS Zeitpunkt,
                't' AS isLastSolution,
                IF(vips_solution.corrector_comment IS NULL, 0, 1) AS isAlreadyCorrected,
                'f' AS visibleForStudent,
                vips_solution.ip_address AS IPAdresse
            FROM vips_solution
            JOIN vips_test
              ON vips_test.id = vips_solution.test_id
            WHERE vips_test.type = 'exam'";
        $db->exec($sql);

        $sql = "INSERT INTO vips_K_Loesung_Punkte
            SELECT vips_solution.user_id AS idUser,
                vips_solution.id AS idLoesung,
                vips_solution.points AS Punkte,
                IF(vips_solution.corrected = 1, 't', 'f') AS Safe
            FROM vips_solution
            JOIN vips_test
              ON vips_test.id = vips_solution.test_id
            WHERE vips_test.type = 'exam'
              AND vips_solution.points IS NOT NULL";
        $db->exec($sql);

        // copy all exam solutions from vips_solution_archive
        $sql = "INSERT INTO vips_K_Loesung
            SELECT vips_solution_archive.id AS Loesungsid,
                vips_solution_archive.solution AS Loesung,
                vips_solution_archive.test_id AS Assignmentid,
                0 AS Gruppenid,
                vips_solution_archive.user_id AS Userid,
                vips_solution_archive.exercise_id AS Aufgabenid,
                IFNULL(vips_solution_archive.corrector_comment, '') AS Korrektur,
                IFNULL(vips_solution_archive.commented_solution, '') AS bearbeiteteLoesung,
                IFNULL(vips_solution_archive.student_comment, '') AS Anmerkung,
                vips_solution_archive.time AS Zeitpunkt,
                'f' AS isLastSolution,
                IF(vips_solution_archive.corrector_comment IS NULL, 0, 1) AS isAlreadyCorrected,
                'f' AS visibleForStudent,
                vips_solution_archive.ip_address AS IPAdresse
            FROM vips_solution_archive
            JOIN vips_test
              ON vips_test.id = vips_solution_archive.test_id
            WHERE vips_test.type = 'exam'";
        $db->exec($sql);

        $sql = "INSERT INTO vips_K_Loesung_Punkte
            SELECT vips_solution_archive.user_id AS idUser,
                vips_solution_archive.id AS idLoesung,
                vips_solution_archive.points AS Punkte,
                IF(vips_solution_archive.corrected = 1, 't', 'f') AS Safe
            FROM vips_solution_archive
            JOIN vips_test
              ON vips_test.id = vips_solution_archive.test_id
            WHERE vips_test.type = 'exam'
              AND vips_solution_archive.points IS NOT NULL";
        $db->exec($sql);

        // fetch each practice and selftest solution
        $sql = "SELECT vips_solution.*,
                1 AS current
            FROM vips_solution
            JOIN vips_test
              ON vips_test.id = vips_solution.test_id
            WHERE vips_test.type != 'exam'

            UNION

            SELECT vips_solution_archive.*,
                0 AS current
            FROM vips_solution_archive
            JOIN vips_test
              ON vips_test.id = vips_solution_archive.test_id
            WHERE vips_test.type != 'exam'";
        $solutions = $db->query($sql);
        foreach ($solutions as $sol) {  // only practices and selftests!
            // find out user's group id
            $sql = "SELECT group_id
                FROM vips_inGruppe
                JOIN vips_gruppe
                  ON vips_gruppe.Gruppenid = vips_inGruppe.group_id
                JOIN vips_test
                  ON vips_test.course_id = vips_gruppe.Kursid
                WHERE vips_inGruppe.user_id = '{$sol['user_id']}'
                  AND vips_inGruppe.start <= '{$sol['time']}'
                  AND (vips_inGruppe.end > '{$sol['time']}' OR vips_inGruppe.end IS NULL)
                  AND vips_test.id = {$sol['test_id']}";
            $result = $db->query($sql);

            $group_id = $result->fetchColumn();
            if ($group_id === false)
                $group_id = 0;

            $is_last_solution     = $sol['current'] ? 't' : 'f';
            $is_already_corrected = isset($sol['corrector_comment']) ? 1 : 0;

            $sql = "INSERT INTO vips_T_Loesung (
                    Loesungsid,
                    Loesung,
                    Assignmentid,
                    Gruppenid,
                    Userid,
                    Aufgabenid,
                    Korrektur,
                    bearbeiteteLoesung,
                    Anmerkung,
                    Zeitpunkt,
                    isLastSolution,
                    isAlreadyCorrected)
                VALUES (
                    ".$sol['id'].",
                    ".$db->quote($sol['solution']).",
                    ".$sol['test_id'].",
                    ".$group_id.",
                    '".$sol['user_id']."',
                    ".$sol['exercise_id'].",
                    ".$db->quote($sol['corrector_comment']).",
                    ".$db->quote($sol['commented_solution']).",
                    ".$db->quote($sol['student_comment']).",
                    '".$sol['time']."',
                    '".$is_last_solution."',
                    '".$is_already_corrected."')";
            $db->exec($sql);

            // if there is a corrected solution
            if (isset($sol['points'])) {
                // set $safe
                if (isset($sol['corrector_comment'])) {  // manually corrected
                    $safe = 't';
                }
                else {  // automatically corrected
                    if ($sol['corrected'] == 1) {  // safe
                        $safe = 't';
                    }
                    else {  // unsafe
                        $safe = 'f';
                    }
                }

                $sql = "INSERT INTO vips_T_Loesung_Punkte (
                        idUser,
                        idLoesung,
                        Punkte,
                        Safe)
                    VALUES (
                        '".$sol['user_id']."',
                        ".$sol['id'].",
                        '".$sol['points']."',
                        '".$safe."')";
                $db->exec($sql);
            }
            // note that selftest solutions will be stored in vips_T_Loesung[_Punkte]
        }  // end for each practice solution



        ////////////////
        // references //
        ////////////////

        // exams
        $sql = "INSERT INTO vips_inKlausur
            SELECT vips_exercise_ref.exercise_id AS vips_aufgabe,
                    vips_exercise_ref.test_id AS vips_klausur,
                    vips_exercise_ref.points AS Punkte,
                    IF(vips_exercise_ref.disregard = 1, 'f', 't') AS Bewertet,
                    vips_exercise_ref.position AS Position
                FROM vips_exercise_ref
                JOIN vips_test
                  ON vips_test.id = vips_exercise_ref.test_id
                WHERE vips_test.type = 'exam'";
        $db->exec($sql);

        // practices, selftests
        $sql = "INSERT INTO vips_inUebungsblatt
            SELECT vips_exercise_ref.exercise_id AS vips_aufgabe,
                    vips_exercise_ref.test_id AS vips_uebungsblatt,
                    vips_exercise_ref.points AS Punkte,
                    IF(vips_exercise_ref.disregard = 1, 'f', 't') AS Bewertet,
                    vips_exercise_ref.position AS Position
                FROM vips_exercise_ref
                JOIN vips_test
                  ON vips_test.id = vips_exercise_ref.test_id
                WHERE vips_test.type != 'exam'";
        $db->exec($sql);



        /////////////////////////////////////////////////////
        // update other tables with references to test ids //
        // (vips_aufgaben_zeit, vips_inBlock and           //
        // vips_klausur_gruppierung)                       //
        /////////////////////////////////////////////////////

        // vips_aufgaben_zeit //
        $sql = 'SELECT DISTINCT test_id FROM vips_aufgaben_zeit';
        foreach ($db->query($sql) as $elem) {
            if ($klausur_ids[$elem['test_id']]) {
                $sql = 'UPDATE vips_aufgaben_zeit
                    SET vips_klausur = '.$elem['test_id'].'
                    WHERE test_id = '.$elem['test_id'];
            }
            else {
                $sql = 'UPDATE vips_aufgaben_zeit
                    SET vips_uebungsblatt = '.$elem['test_id'].'
                    WHERE test_id = '.$elem['test_id'];
            }
            $db->exec($sql);
        }

        // vips_inBlock //
        // nothing to be done

        // vips_klausur_gruppierung //
        // nothing to be done



        // delete obsolete tables
        $sql = 'DROP TABLE
            vips_test,
            vips_solution,
            vips_solution_archive,
            vips_exercise_ref';
        $db->exec($sql);

        // alter table vips_aufgaben_zeit
        $sql = 'ALTER TABLE vips_aufgaben_zeit
            DROP test_id';
        $db->exec($sql);

    }  // end function down()
}

?>
