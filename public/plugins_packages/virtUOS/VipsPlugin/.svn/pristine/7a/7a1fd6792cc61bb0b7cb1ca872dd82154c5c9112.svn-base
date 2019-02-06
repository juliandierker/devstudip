<?php
/**
 * vips_pool.inc.php - Vips plugin for Stud.IP
 * Copyright (c) 2018  Elmar Ludwig, Dominik Feldschnieders
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

/**
 * Displays main page
 *
 * @author: Dominik Feldschnieders (dofeldsc@uos.de)
 */
class PoolController extends StudipController
{
    /**
     * Callback function being called before an action is executed. If this
     * function does not return FALSE, the action will be called, otherwise
     * an error will be generated and processing will be aborted. If this function
     * already #rendered or #redirected, further processing of the action is
     * withheld.
     *
     * @param string  Name of the action to perform.
     * @param array   An array of arguments to the action.
     *
     * @return bool
     */
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        PageLayout::setHelpKeyword(vips_text_encode('Vips.HomePage'));
    }

    /**
     * Display all exercises that are available for this user.
     * Available in this case means the exercise is in a course where the user
     * is at least tutor.
     * Lecturer/tutor can select which exercise to edit/assign/delete.
     */
    public function index_action()
    {
        Navigation::activateItem('/browse/vips/exercises');
        PageLayout::setTitle(_vips('Meine Aufgaben'));

        Helpbar::get()->addPlainText('',
            _vips('Auf dieser Seite finden Sie eine Übersicht über alle Aufgaben in Vips, die Sie bearbeiten dürfen.') . "\n" .
            _vips('Da es zur Zeit nur Aufgaben im Kontext einer Veranstaltung gibt, werden Sie bei einigen Aktionen in ' .
                  'die jeweilige Veranstaltung bzw. in das Aufgabenblatt weitergeleitet.') . "\n" .
            _vips('Im späteren Verlauf werden die Aufgaben von den Veranstaltungen getrennt und die Sammlung wird noch ' .
                  'weitere Funktionen anbieten.'));

        $sort = Request::option('sort', 'created');
        $desc = Request::int('desc', $sort === 'created');
        $page = Request::int('page', 1);
        $size = get_config('ENTRIES_PER_PAGE');

        if (Request::submitted('start_search') || Request::int('pool_search')) {
            $search_filter = [
                'search_string' => Request::get('pool_search_parameter'),
                'exercise_type' => Request::get('exercise_type')
            ];
        } else if (Request::submitted('reset_search')) {
            $search_filter = null;
        } else if (Request::getArray('search_filter')) {
            $search_filter = Request::getArray('search_filter');
        }

        // get exercises of this user and where he/she has permission
        $course_ids = $this->getActiveVipsCourseIDs();

        // set up the sql query for the quicksearch
        $sql = "SELECT vips_exercise.id, vips_exercise.title FROM vips_exercise
                JOIN vips_exercise_ref ON vips_exercise.id = vips_exercise_ref.exercise_id
                JOIN vips_assignment USING (test_id)
                WHERE vips_assignment.course_id IN ('" . implode("','", $course_ids) . "')
                  AND (vips_exercise.title LIKE :input OR vips_exercise.description LIKE :input)
                  AND IF(:exercise_type = '', 1, vips_exercise.type = :exercise_type)
                ORDER BY title";
        $search = new SQLSearch($sql, _vips('Titel der Aufgabe'));

        $result = $this->getAllExcercises($course_ids, $sort, $desc, $search_filter);

        $this->sort = $sort;
        $this->desc = $desc;
        $this->page = $page;
        $this->count = count($result);
        $this->exercises = array_slice($result, $size * ($page - 1), $size);

        $this->search = $search;
        $this->search_filter = $search_filter;
        $this->exercise_types = Exercise::getExerciseTypes();
    }

    /**
     * Display all assignments that are available for this user.
     * Available in this case means the test is in a course where the user
     * is at least tutor.
     * Lecturer/tutor can select which test to edit/delete.
     */
    public function tests_action()
    {
        Navigation::activateItem('/browse/vips/tests');
        PageLayout::setTitle(_vips('Meine Aufgabenblätter'));

        Helpbar::get()->addPlainText('',
            _vips('Auf dieser Seite finden Sie eine Übersicht über alle Aufgabenblätter in Vips, die Sie bearbeiten dürfen.') . "\n" .
            _vips('Da es zur Zeit nur Aufgabenblätter im Kontext einer Veranstaltung gibt, werden Sie bei einigen Aktionen in ' .
                  'die jeweilige Veranstaltung weitergeleitet.') . "\n" .
            _vips('Im späteren Verlauf werden die Aufgabenblätter von den Veranstaltungen getrennt und die Sammlung wird noch ' .
                  'weitere Funktionen anbieten.'));

        $sort = Request::option('sort', 'created');
        $desc = Request::int('desc', $sort === 'created');
        $page = Request::int('page', 1);
        $size = get_config('ENTRIES_PER_PAGE');

        if (Request::submitted('start_search') || Request::int('pool_search')) {
            $search_filter = [
                'search_string' => Request::get('pool_search_parameter'),
                'assignment_type' => Request::get('assignment_type')
            ];
        } else if (Request::submitted('reset_search')) {
            $search_filter = null;
        } else if (Request::getArray('search_filter')) {
            $search_filter = Request::getArray('search_filter');
        }

        // get exercises of this user and where he/she has permission
        $course_ids = $this->getActiveVipsCourseIDs();

        // set up the sql query for the quicksearch
        $sql = "SELECT vips_test.id, vips_test.title FROM vips_test
                JOIN vips_assignment ON vips_test.id = vips_assignment.test_id
                WHERE vips_assignment.course_id IN ('" . implode("','", $course_ids) . "')
                  AND (vips_test.title LIKE :input OR vips_test.description LIKE :input)
                  AND IF(:assignment_type = '', 1, vips_assignment.type = :assignment_type)
                ORDER BY title";
        $search = new SQLSearch($sql, _vips('Titel des Aufgabenblatts'));

        $result = $this->getAllAssignments($course_ids, $sort, $desc, $search_filter);

        $this->sort = $sort;
        $this->desc = $desc;
        $this->page = $page;
        $this->count = count($result);

        $this->tests = array_slice($result, $size * ($page - 1), $size);

        $this->search = $search;
        $this->search_filter = $search_filter;
        $this->assignment_types = VipsAssignment::getAssignmentTypes();
    }


    /**
     * Anfangs leitet diese Funktion nur die Anfragen an die schon vorhandenen Funktionen weiter,
     * welche aber nur innerhalb einer Veranstaltung erlaubt sind. Da später die Aufgaben von den
     * Veranstaltungen losgelöst werden sollen, ist der Umweg später nicht mehr nötig.
     */
    public function placeholder_action()
    {
        $exercise_id = Request::int('exercise_id');
        $assignment_id = Request::int('assignment_id');
        $action = Request::get('action');

        $assignment = VipsAssignment::find($assignment_id);

        if ($action == 'print_student_overview') {
            $this->redirect(vips_url('export/print_student_overview', ['cid' => $assignment->course_id, 'assignment_id' => $assignment_id]));
        } else {
            $this->redirect(vips_url('sheets/' . $action,
                ['cid' => $assignment->course_id, 'assignment_id' => $assignment_id, 'exercise_id' => $exercise_id]));
        }
    }


    /**
     * Get one page of exercises in given order, starting at $page * ENTRIES_PER_PAGE.
     * If $search_filter is not empty, search filters are applied.
     *
     * @param course_ids    list of courses to get exercises from
     * @param sort          sort exercise list by this property
     * @param desc          true if sort direction is descending
     * @param search_filter the currently active search filter
     *
     * @return array with data of all matching exercises
     */
    public function getAllExcercises($course_ids, $sort, $desc, $search_filter)
    {
        $db = DBManager::get();

        // check if some filters are active
        $search_string = $search_filter['search_string'];
        $exercise_type = $search_filter['exercise_type'];

        $sql = "SELECT vips_exercise.*, auth_user_md5.Nachname, auth_user_md5.Vorname,
                       vips_assignment.id AS assignment_id, vips_test.title AS test_title
                FROM vips_exercise LEFT JOIN auth_user_md5 USING(user_id)
                JOIN vips_exercise_ref ON vips_exercise.id = vips_exercise_ref.exercise_id
                JOIN vips_test ON vips_test.id = vips_exercise_ref.test_id
                JOIN vips_assignment USING (test_id)
                WHERE vips_assignment.course_id IN ('" . implode("','", $course_ids) . "') " .
                ($search_string ? 'AND (vips_exercise.title LIKE :input OR vips_exercise.description LIKE :input) ' : '') .
                ($exercise_type ? 'AND vips_exercise.type = :exercise_type ' : '') .
               "ORDER BY :sort :desc, title";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':input', '%' . $search_string . '%');
        $stmt->bindValue(':exercise_type', $exercise_type);
        $stmt->bindValue(':sort', $sort, StudipPDO::PARAM_COLUMN);
        $stmt->bindValue(':desc', $desc ? 'DESC' : 'ASC', StudipPDO::PARAM_COLUMN);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Get one page of assignments in given order, starting at $page * ENTRIES_PER_PAGE.
     * If $search_filter is not empty, search filters are applied.
     *
     * @param course_ids    list of courses to get assignments from
     * @param sort          sort assignment list by this property
     * @param desc          true if sort direction is descending
     * @param search_filter the currently active search filter
     *
     * @return array with data of all matching assignments
     */
    public function getAllAssignments($course_ids, $sort, $desc, $search_filter)
    {
        $db = DBManager::get();

        // check if some filters are active
        $search_string = $search_filter['search_string'];
        $assignment_type = $search_filter['assignment_type'];

        $sql = "SELECT vips_test.*, auth_user_md5.Nachname, auth_user_md5.Vorname,
                       vips_assignment.type, vips_assignment.start, vips_assignment.end,
                       vips_assignment.id AS assignment_id, vips_assignment.course_id,
                       seminare.Name, semester_data.name AS sem_name
                FROM vips_test LEFT JOIN auth_user_md5 USING(user_id)
                JOIN vips_assignment ON vips_test.id = vips_assignment.test_id
                JOIN seminare ON vips_assignment.course_id = seminare.Seminar_id
                JOIN semester_data ON seminare.start_time = semester_data.beginn
                WHERE vips_assignment.course_id IN ('" . implode("','", $course_ids) . "') " .
                ($search_string ? 'AND (vips_test.title LIKE :input OR vips_test.description LIKE :input) ' : '') .
                ($assignment_type ? 'AND vips_assignment.type = :assignment_type ' : '') .
               "ORDER BY :sort :desc, title";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':input', '%' . $search_string . '%');
        $stmt->bindValue(':assignment_type', $assignment_type);
        $stmt->bindValue(':sort', $sort, StudipPDO::PARAM_COLUMN);
        $stmt->bindValue(':desc', $desc ? 'DESC' : 'ASC', StudipPDO::PARAM_COLUMN);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Return all course-IDs where the user is at least autor and vips is activated
     *
     * @return array with all course ids, null if no courses
     */
    private function getActiveVipsCourseIDs()
    {
        global $vipsPlugin, $user;

        $plugin_manager = PluginManager::getInstance();

        $course_ids = $user->course_memberships->findBy('status', ['tutor', 'dozent'])->pluck('seminar_id');

        // remove courses where Vips is not active
        foreach ($course_ids as $key => $course_id) {
            if (!$plugin_manager->isPluginActivated($vipsPlugin->getPluginId(), $course_id)) {
                unset($course_ids[$key]);
            }
        }

        return $course_ids;
    }

    /**
     * Delete a list of assignments from the course. All exercises are retained.
     */
    public function delete_tests_action()
    {
        $assignment_ids = Request::intArray('assignment_ids');
        $sort = Request::option('sort');
        $desc = Request::int('desc');
        $page = Request::int('page');
        $search_filter = Request::getArray('search_filter');

        foreach ($assignment_ids as $assignment_id) {
            $assignment = VipsAssignment::find($assignment_id);
            check_copy_assignment_access($assignment);

            $assignment->delete();
        }

        PageLayout::postSuccess(sprintf(_vips('Es wurden %s Aufgabenblätter gelöscht.'), count($assignment_ids)));

        $this->redirect(vips_url('pool/tests', compact('sort', 'desc', 'page', 'search_filter')));
    }

    /**
     * Take a list of exercises off their respective assignments.
     */
    public function delete_exercises_action()
    {
        $exercise_ids = Request::intArray('exercise_ids');
        $sort = Request::option('sort');
        $desc = Request::int('desc');
        $page = Request::int('page');
        $search_filter = Request::getArray('search_filter');

        foreach ($exercise_ids as $exercise_id => $assignment_id) {
            $assignment = VipsAssignment::find($assignment_id);
            check_exercise_assignment($exercise_id, $assignment);
            check_copy_assignment_access($assignment);

            $assignment->test->removeExercise($exercise_id, true);
        }

        PageLayout::postSuccess(sprintf(_vips('Es wurden %s Aufgaben gelöscht.'), count($exercise_ids)));

        $this->redirect(vips_url('pool', compact('sort', 'desc', 'page', 'search_filter')));
    }
}
