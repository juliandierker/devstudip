<?php
/*
 * VipsAssignment.php - Vips test class for Stud.IP
 * Copyright (c) 2014  Elmar Ludwig
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

class VipsAssignment extends SimpleORMap
{
    /**
     * Configure the database mapping.
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'vips_assignment';

        $config['serialized_fields']['options'] = 'JSONArrayObject';

        $config['has_many']['assignment_attempts'] = [
            'class_name'        => 'VipsAssignmentAttempt',
            'assoc_foreign_key' => 'assignment_id'
        ];
        $config['has_many']['solutions'] = [
            'class_name'        => 'VipsSolution',
            'assoc_foreign_key' => 'assignment_id'
        ];

        $config['belongs_to']['course'] = [
            'class_name'  => 'Course',
            'foreign_key' => 'course_id'
        ];
        $config['belongs_to']['block'] = [
            'class_name'  => 'VipsBlock',
            'foreign_key' => 'block_id'
        ];
        $config['belongs_to']['test'] = [
            'class_name'  => 'VipsTest',
            'foreign_key' => 'test_id'
        ];

        parent::configure($config);
    }

    /**
     * Delete entry from the database.
     */
    public function delete()
    {
        VipsAssignmentAttempt::deleteBySQL('assignment_id = ?', [$this->id]);

        $this->test->delete();

        return parent::delete();
    }

    public static function importText($title, $string, $user_id, $course_id)
    {
        $duration = 7 * 24 * 60 * 60;  // one week

        $data_test = [
            'title'       => $title,
            'description' => '',
            'user_id'     => $user_id,
            'created'     => date('Y-m-d H:i:s'),
        ];
        $data = [
            'type'        => 'practice',
            'course_id'   => $course_id,
            'start'       => date('Y-m-d H:00:00'),
            'end'         => date('Y-m-d H:00:00', time() + $duration)
        ];

        // remove comments
        $string = preg_replace('/^#.*/m', '', $string);

        // split into exercises
        $segments = preg_split('/^Name:/m', $string);
        array_shift($segments);

        $test_obj = VipsTest::create($data_test);

        $result = new VipsAssignment();
        $result->setData($data);
        $result->test = $test_obj;
        $result->store();

        foreach ($segments as $segment) {
            try {
                $new_exercise = Exercise::importText($segment);
                $new_exercise->user_id = $user_id;
                $new_exercise->created = date('Y-m-d H:i:s');
                $new_exercise->store();
                $test_obj->addExercise($new_exercise);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (isset($errors)) {
            PageLayout::postError(_vips('Während des Imports sind folgende Fehler aufgetreten:'), $errors);
        }

        return $result;
    }

    public static function importXML($string, $user_id, $course_id)
    {
        // default options
        $options = [
            'evaluation_mode' => 0,
            'released'        => 0,
            'duration'        => 60
        ];

        $duration = 7 * 24 * 60 * 60;  // one week

        $data_test = [
            'title'       => _vips('Aufgabenblatt'),
            'description' => '',
            'user_id'     => $user_id,
            'created'     => date('Y-m-d H:i:s'),
        ];
        $data = [
            'type'        => 'practice',
            'course_id'   => $course_id,
            'start'       => date('Y-m-d H:00:00'),
            'end'         => date('Y-m-d H:00:00', time() + $duration),
            'options'     => $options
        ];

        $test = new SimpleXMLElement($string, LIBXML_COMPACT | LIBXML_NOCDATA);
        $data['type'] = $test['type'];
        $data_test['title'] = studip_utf8decode(trim($test->title));

        if ($test->description) {
            $data_test['description'] = studip_utf8decode(trim($test->description));
        }
        if ($test->notes) {
            $data['options']['notes'] = studip_utf8decode(trim($test->notes));
        }
        if ($test['start']) {
            $data['start'] = date('Y-m-d H:i:s', strtotime($test['start']));
        }
        if ($test['end']) {
            $data['end'] = date('Y-m-d H:i:s', strtotime($test['end']));
        }

        $test_obj = VipsTest::create($data_test);

        $result = new VipsAssignment();
        $result->setData($data);
        $result->test = $test_obj;
        $result->store();

        foreach ($test->exercises->exercise as $exercise) {
            try {
                $new_exercise = Exercise::importXML($exercise);
                $new_exercise->user_id = $user_id;
                $new_exercise->created = date('Y-m-d H:i:s');
                $new_exercise->store();
                $test_obj->addExercise($new_exercise);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (isset($errors)) {
            PageLayout::postError(_vips('Während des Imports sind folgende Fehler aufgetreten:'), $errors);
        }

        return $result;
    }

    /**
     * Get the name of this assignment type.
     */
    public function getTypeName()
    {
        $assignment_types = self::getAssignmentTypes();

        return $assignment_types[$this->type]['name'];
    }

    /**
     * Get the icon of this assignment type.
     */
    public function getTypeIcon($role = Icon::DEFAULT_ROLE)
    {
        $assignment_types = self::getAssignmentTypes();

        return Icon::create($assignment_types[$this->type]['icon'], $role,
                            ['title' => $assignment_types[$this->type]['name']]);
    }

    /**
     * Get the list of supported assignment types.
     */
    public static function getAssignmentTypes()
    {
        return [
            'practice' => ['name' => _vips('Übung'),      'icon' => 'file'],
            'selftest' => ['name' => _vips('Selbsttest'), 'icon' => 'check-circle'],
            'exam'     => ['name' => _vips('Klausur'),    'icon' => 'doctoral_cap']
        ];
    }

    /**
     * Check if this assignment is visible to students.
     */
    public function isVisible()
    {
        return $this->block_id ? $this->block->visible : 1;
    }

    /**
     * Check if this assignment is currently running.
     */
    public function isRunning()
    {
        $now = date('Y-m-d H:i:s');

        return $now >= $this->start && $now <= $this->end;
    }

    /**
     * Check if this assignment is already finished.
     */
    public function isFinished()
    {
        $now = date('Y-m-d H:i:s');

        return $now > $this->end;
    }

    /**
     * Check if this assignment has no end date.
     */
    public function isUnlimited()
    {
        return $this->type === 'selftest' && $this->end === VIPS_DATE_INFINITY;
    }

    /**
     * Check whether the given exercise is part of this assignment.
     *
     * @param int $exercise_id  exercise id
     */
    public function hasExercise($exercise_id)
    {
        return VipsExerciseRef::exists([$exercise_id, $this->test_id]);
    }

    /**
     * Export this assignment to XML format. Returns the XML string.
     */
    public function exportXML()
    {
        global $vipsTemplateFactory;

        $template = $vipsTemplateFactory->open('sheets/export_assignment');
        $template->assignment = $this;

        return $template->render();
    }

    /**
     * Check whether the given IP address listed among the IP addresses given
     * by the lecturer for this exam (if applicable).
     *
     * @param string $ip_addr   IPv4 address (IPv6 is not yet supported)
     */
    public function checkIPAccess($ip_addr)
    {
        // Explode space separated list into an array and check the resulting single IPs
        $ip_ranges = preg_split('/[ ,]+/', $this->options['ip_range'], -1, PREG_SPLIT_NO_EMPTY);

        // No IP given: user has access.
        if (count($ip_ranges) == 0) {
            return true;
        }

        // One or more IPs are given and user IP matches at least one: user has access.
        foreach ($ip_ranges as $ip_range) {
            if (strpos($ip_range, '-') === false) {
                $ip_start = $ip_end = $ip_range;
            } else {
                list($ip_start, $ip_end) = explode('-', $ip_range);
            }

            $ip_addr_part = explode('.', $ip_addr);
            $ip_start_part = explode('.', $ip_start) + array_fill(0, 4, 0);
            $ip_end_part = explode('.', $ip_end) + array_fill(0, 4, 255);

            if ($ip_start_part[0] <= $ip_addr_part[0] && $ip_addr_part[0] <= $ip_end_part[0] &&
                $ip_start_part[1] <= $ip_addr_part[1] && $ip_addr_part[1] <= $ip_end_part[1] &&
                $ip_start_part[2] <= $ip_addr_part[2] && $ip_addr_part[2] <= $ip_end_part[2] &&
                $ip_start_part[3] <= $ip_addr_part[3] && $ip_addr_part[3] <= $ip_end_part[3]) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the assignment attempt of the given user for this assignment.
     * Returns NULL if there is no assignment attempt for this user.
     *
     * @param string $user_id   user id
     */
    public function getAssignmentAttempt($user_id)
    {
        return VipsAssignmentAttempt::findOneBySQL('assignment_id = ? AND user_id = ?', [$this->id, $user_id]);
    }

    /**
     * Record an assignment attempt for the given user for this assignment.
     *
     * @param string $user_id   user id
     */
    public function recordAssignmentAttempt($user_id)
    {
        if (!$this->getAssignmentAttempt($user_id)) {
            VipsAssignmentAttempt::create([
                'assignment_id' => $this->id,
                'user_id'       => $user_id,
                'start'         => date('Y-m-d H:i:s'),
                'ip_address'    => $_SERVER['REMOTE_ADDR']
            ]);
        }
    }

    /**
     * Get all members that were assigned to a particular group for
     * this assignment.
     *
     * @param VipsGroup $group   The group object
     */
    public function getGroupMembers($group)
    {
        return VipsGroupMember::findBySQL(
            'group_id = ? AND start <= ? AND (end > ? OR end IS NULL)',
            [$group->id, $this->end, $this->end]
        );
    }

    /**
     * Get the group the user was assigned to for this assignment.
     * Returns NULL if there is no group assignment for this user.
     *
     * @param string $user_id   user id
     */
    public function getUserGroup($user_id)
    {
        if ($this->type !== 'practice') {
            return NULL;
        }

        return VipsGroup::findOneBySQL(
            'JOIN vips_group_member ON group_id = id
             WHERE course_id = ?
               AND user_id   = ?
               AND start    <= ?
               AND (end      > ? OR end IS NULL)',
            [$this->course_id, $user_id, $this->end, $this->end]
        );
    }

    /**
     * Store a solution related to this assignment into the database.
     *
     * @param VipsSolution $solution The solution object
     */
    public function storeSolution($solution)
    {
        $db = DBManager::get();

        $exercise = $solution->exercise;
        $user_id  = $solution->user_id;

        $solution->assignment = $this;
        $solution->time = date('Y-m-d H:i:s');

        // move old solutions into vips_solution_archive
        $sql = 'INSERT INTO vips_solution_archive
                SELECT * FROM vips_solution
                WHERE exercise_id = ? AND assignment_id = ? AND user_id = ?';
        $stmt = $db->prepare($sql);
        $stmt->execute([$exercise->id, $this->id, $user_id]);

        $sql = 'DELETE FROM vips_solution
                WHERE exercise_id = ? AND assignment_id = ? AND user_id = ?';
        $stmt = $db->prepare($sql);
        $stmt->execute([$exercise->id, $this->id, $user_id]);

        // in selftests, autocorrect solution
        if ($this->type === 'selftest') {
            $this->correctSolution($solution);
        }

        // insert new solution into vips_solution
        return $solution->store();
    }

    /**
     * Correct a solution and store the points for the solution in the object.
     *
     * @param VipsSolution $solution The solution object
     */
    public function correctSolution($solution)
    {
        $exercise = $solution->exercise;
        $exercise_ref = $this->test->getExerciseRef($exercise->id);
        $max_points = (float) $exercise_ref->points;

        // always set corrected to true for selftest exercises
        $selftest        = $this->type === 'selftest';
        $evaluation      = $exercise->evaluate($solution);
        $mistake_comment = $exercise->options['feedback'];

        $reached_points = round_to_half_point($evaluation['percent'] * $max_points);
        $corrected      = (int) ($selftest || $evaluation['safe']);  // 0 or 1

        // insert solution points
        $solution->corrected = $corrected;
        $solution->points = $reached_points;
        $solution->correction_time = date('Y-m-d H:i:s');

        if ($evaluation['percent'] != 1 && $mistake_comment != '') {
            $solution->corrector_comment = $mistake_comment;
        }
    }

    /**
     * Fetch a solution related to this assignment from the database.
     * Returns NULL if there is no solution for this exercise yet.
     *
     * @param string $group_id  group id
     * @param int $exercise_id  exercise id
     */
    public function getGroupSolution($group_id, $exercise_id)
    {
        return VipsSolution::findOneBySQL(
            'JOIN vips_group_member USING(user_id)
             WHERE exercise_id   = ?
               AND assignment_id = ?
               AND group_id      = ?
               AND start        <= ?
               AND (end          > ? OR end IS NULL)
             ORDER BY time DESC',
            [$exercise_id, $this->id, $group_id, $this->end, $this->end]
        );
    }

    /**
     * Fetch a solution related to this assignment from the database.
     * NOTE: This method will NOT check the group solution, if applicable.
     * Returns NULL if there is no solution for this exercise yet.
     *
     * @param string $user_id   user id
     * @param int $exercise_id  exercise id
     */
    public function getUserSolution($user_id, $exercise_id)
    {
        return VipsSolution::findOneBySQL(
            'exercise_id = ? AND assignment_id = ? AND user_id = ?',
            [$exercise_id, $this->id, $user_id]
        );
    }

    /**
     * Fetch a solution related to this assignment from the database.
     * Returns NULL if there is no solution for this exercise yet.
     *
     * @param string $user_id   user id
     * @param int $exercise_id  exercise id
     */
    public function getSolution($user_id, $exercise_id)
    {
        $group = $this->getUserGroup($user_id);

        if ($group) {
            return $this->getGroupSolution($group->id, $exercise_id);
        }

        return $this->getUserSolution($user_id, $exercise_id);
    }

    /**
     * Delete all solutions of the given user for a single exercise of
     * this test from the DB.
     *
     * @param string $user_id   user id
     * @param int $exercise_id  exercise id
     */
    public function deleteSolution($user_id, $exercise_id)
    {
        $sql = 'exercise_id = ? AND assignment_id = ? AND user_id = ?';

        // delete in vips_solution and vips_solution_archive
        VipsSolution::deleteBySQL($sql, [$exercise_id, $this->id, $user_id]);
        VipsSolutionArchive::deleteBySQL($sql, [$exercise_id, $this->id, $user_id]);
    }

    /**
     * Delete all solutions of the given user for this test from the DB.
     *
     * @param string $user_id   user id
     */
    public function deleteSolutions($user_id)
    {
        $sql = 'assignment_id = ? AND user_id = ?';

        // delete in vips_solution and vips_solution_archive
        VipsSolution::deleteBySQL($sql, [$this->id, $user_id]);
        VipsSolutionArchive::deleteBySQL($sql, [$this->id, $user_id]);

        // delete start times
        VipsAssignmentAttempt::deleteBySQL($sql, [$this->id, $user_id]);
    }
}
