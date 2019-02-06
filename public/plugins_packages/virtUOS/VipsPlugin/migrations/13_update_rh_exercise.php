<?php

class UpdateRhExercise extends Migration
{
    function description()
    {
        return 'assign unique ids for answers in assign exercises';
    }

    function up()
    {
        $db = DBManager::get();

        $select_exercises = $db->query("SELECT id, task_json FROM vips_exercise WHERE type = 'rh_exercise'");
        $exercises = $select_exercises->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_GROUP | PDO::FETCH_UNIQUE);

        $update_exercise = $db->prepare('UPDATE vips_exercise SET task_json = ? WHERE id = ?');
        $select_solution = $db->prepare('SELECT id, response FROM vips_solution WHERE exercise_id = ?');
        $update_solution = $db->prepare('UPDATE vips_solution SET response = ? WHERE id = ?');
        $select_solution_archive = $db->prepare('SELECT id, response FROM vips_solution_archive WHERE exercise_id = ?');
        $update_solution_archive = $db->prepare('UPDATE vips_solution_archive SET response = ? WHERE id = ?');

        foreach ($exercises as $exercise_id => $task_json) {
            $task = json_decode($task_json, true);
            $ids = [0 => 0];

            foreach ($task['answers'] as $i => &$answer) {
                do {
                    $answer['id'] = rand();
                } while (isset($ids[$answer['id']]));

                $ids[$answer['id']] = $i;
            }

            $ids = array_flip($ids);
            $update_exercise->execute([json_encode($task), $exercise_id]);
            $this->upgrade_solutions($exercise_id, $ids, $select_solution, $update_solution);
            $this->upgrade_solutions($exercise_id, $ids, $select_solution_archive, $update_solution_archive);
        }
    }

    function down()
    {
        $db = DBManager::get();

        $select_exercises = $db->query("SELECT id, task_json FROM vips_exercise WHERE type = 'rh_exercise'");
        $exercises = $select_exercises->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_GROUP | PDO::FETCH_UNIQUE);

        $update_exercise = $db->prepare('UPDATE vips_exercise SET task_json = ? WHERE id = ?');
        $select_solution = $db->prepare('SELECT id, response FROM vips_solution WHERE exercise_id = ?');
        $update_solution = $db->prepare('UPDATE vips_solution SET response = ? WHERE id = ?');
        $select_solution_archive = $db->prepare('SELECT id, response FROM vips_solution_archive WHERE exercise_id = ?');
        $update_solution_archive = $db->prepare('UPDATE vips_solution_archive SET response = ? WHERE id = ?');

        foreach ($exercises as $exercise_id => $task_json) {
            $task = json_decode($task_json, true);

            foreach ($task['answers'] as $i => &$answer) {
                unset($answer['id']);
            }

            $update_exercise->execute([json_encode($task), $exercise_id]);
            $this->downgrade_solutions($exercise_id, $select_solution, $update_solution);
            $this->downgrade_solutions($exercise_id, $select_solution_archive, $update_solution_archive);
        }
    }

    function upgrade_solutions($exercise_id, $ids, $select_stmt, $update_stmt)
    {
        $select_stmt->execute([$exercise_id]);

        foreach ($select_stmt as $solution) {
            $response = json_decode($solution['response'], true);
            $new_response = [];

            foreach ($response as $i => $value) {
                $new_response[$ids[$i]] = $value;
            }

            $update_stmt->execute([json_encode($new_response), $solution['id']]);
        }
    }

    function downgrade_solutions($exercise_id, $select_stmt, $update_stmt)
    {
        $select_stmt->execute([$exercise_id]);

        foreach ($select_stmt as $solution) {
            $response = json_decode($solution['response'], true);
            $new_response = array_values($response);

            $update_stmt->execute([json_encode($new_response), $solution['id']]);
        }
    }
}
