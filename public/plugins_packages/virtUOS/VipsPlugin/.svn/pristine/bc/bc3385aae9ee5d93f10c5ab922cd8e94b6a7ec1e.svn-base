<?php

class RemoveOcRndExercise extends Migration
{
    function description()
    {
        return 'set up DB keys and remove octave and random exercise types';
    }

    function up()
    {
        $db = DBManager::get();

        // set primary key for vips_exercise_ref
        $sql = 'ALTER TABLE vips_exercise_ref DROP KEY exercise_id, ADD PRIMARY KEY (exercise_id, test_id)';
        $db->exec($sql);

        // improve performance for vips_solution access by user_id
        $sql = 'ALTER TABLE vips_solution ADD KEY user_id (user_id)';
        $db->exec($sql);

        $sql = 'ALTER TABLE vips_solution_archive ADD KEY user_id (user_id)';
        $db->exec($sql);

        // remove octave and random exercise types
        $sql = "DELETE FROM vips_aufgaben_typen WHERE URI IN ('rnd_exercise', 'oc_exercise', 'oct_exercise')";
        $db->exec($sql);

        $sql = "UPDATE vips_aufgabe
                SET URI = 'tb_exercise',
                    Aufgabe = REPLACE(Aufgabe, '<TestItem ID=\"rnd_exercise\"', '<TestItem ID=\"tb_exercise\"')
                WHERE URI = 'rnd_exercise'";
        $db->exec($sql);

        $sql = "UPDATE vips_aufgabe
                SET URI = 'tb_exercise',
                    Aufgabe = REPLACE(Aufgabe, '<TestItem ID=\"oc_exercise\"', '<TestItem ID=\"tb_exercise\"')
                WHERE URI = 'oc_exercise'";
        $db->exec($sql);

        $sql = "UPDATE vips_aufgabe
                SET URI = 'tb_exercise',
                    Aufgabe = REPLACE(Aufgabe, '<TestItem ID=\"oct_exercise\"', '<TestItem ID=\"tb_exercise\"')
                WHERE URI = 'oct_exercise'";
        $db->exec($sql);
    }

    function down()
    {
        $db = DBManager::get();

        $sql = 'ALTER TABLE vips_exercise_ref DROP PRIMARY KEY, ADD KEY exercise_id (exercise_id)';
        $db->exec($sql);

        $sql = 'ALTER TABLE vips_solution DROP KEY user_id';
        $db->exec($sql);

        $sql = 'ALTER TABLE vips_solution_archive DROP KEY user_id';
        $db->exec($sql);

        $sql = "INSERT INTO vips_aufgaben_typen VALUES ('Zufallsfrage', '', 'rnd_exercise')";
        $db->exec($sql);

        $sql = "UPDATE vips_aufgabe
                SET URI = 'rnd_exercise',
                    Aufgabe = REPLACE(Aufgabe, '<TestItem ID=\"tb_exercise\"', '<TestItem ID=\"rnd_exercise\"')
                WHERE URI = 'tb_exercise' AND Aufgabe LIKE '<TestItem ID=\"tb\\_exercise\" Type=\"RandomExercise\"%'";
        $db->exec($sql);
    }
}
