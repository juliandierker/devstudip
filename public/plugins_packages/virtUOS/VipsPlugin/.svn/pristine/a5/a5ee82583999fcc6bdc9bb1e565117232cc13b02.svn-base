<?php

class Subexercises extends Migration
{
    function description()
    {
        return 'add table for storing subexercises of random exercises';
    }

    function up()
    {
        $db = DBManager::get();

        // create table vips_subexercise
        $sql = 'CREATE TABLE vips_subexercise (
            exercise_id INT(11) NOT NULL,
            subexercise_id INT(11) NOT NULL,
            position INT(11) NOT NULL,
            KEY exercise_id (exercise_id),
            KEY subexercise_id (subexercise_id))';
        $db->exec($sql);

        // get all random exercises
        $sql = "SELECT ID, Aufgabe
            FROM vips_aufgabe
            WHERE URI = 'rnd_exercise'";
        $result = $db->query($sql);

        foreach ($result as $exercise) {
            $exercise_id  = $exercise['ID'];
            $exercise_xml = $exercise['Aufgabe'];

            $xml = new SimpleXMLElement(studip_utf8encode($exercise_xml));

            $position = 0;
            foreach ($xml->SubExercise as $subexercise) {
                if (!empty($subexercise['ID'])) {  // neither null nor 0 nor ''
                    $sql = 'INSERT INTO vips_subexercise (
                            exercise_id,
                            subexercise_id,
                            position)
                        VALUES (
                            '.$exercise_id.',
                            '.$subexercise['ID'].',
                            '.++$position.')';  // starting with position 1
                    $db->exec($sql);
                }
            }

            // delete all <SubExercise> tags
            unset($xml->SubExercise);

            $sql = "UPDATE vips_aufgabe
                SET Aufgabe = ".$db->quote(studip_utf8decode($xml->asXML()))."
                WHERE ID = ".$exercise_id;
            $db->exec($sql);
        }  // end for each exercise
    }

    function down()
    {
        $db = DBManager::get();

        // get all random exercises
        $sql = "SELECT * FROM vips_aufgabe WHERE URI = 'rnd_exercise'";
        $result = $db->query($sql);

        foreach ($result as $exercise) {
            $exercise_id  = $exercise['ID'];
            $exercise_xml = $exercise['Aufgabe'];

            $xml = new SimpleXMLElement(studip_utf8encode($exercise_xml));

            // get all subexercises from database
            $sql = "SELECT subexercise_id
                FROM vips_subexercise
                WHERE exercise_id = ".$exercise_id."
                ORDER BY position";
            $subexercise_ids = $db->query($sql);

            // add subexercise ids to xml
            foreach ($subexercise_ids as $subexercise_id) {
                $child = $xml->addChild('SubExercise');
                $child->addAttribute('ID', $subexercise_id['subexercise_id']);
            }

            // update exercise xml
            $sql = "UPDATE vips_aufgabe
                SET Aufgabe = ".$db->quote(studip_utf8decode($xml->asXML()))."
                WHERE ID = ".$exercise_id;
            $db->exec($sql);
        }  // end for each exercise

        // delete table vips_subexercise
        $sql = 'DROP TABLE vips_subexercise';
        $db->exec($sql);
    }
}
