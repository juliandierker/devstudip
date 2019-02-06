<?php
/*
 * vips_export.inc.php - Vips plugin for Stud.IP
 * Copyright (c) 2005-2006  Christa Deiwiks
 * Copyright (c) 2006-2009  Elmar Ludwig, Martin Schröder
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

/**
 *
 * Handles export of assignments, print preview etc (so far)
 *
 * @author: Christa Deiwiks <cdeiwiks@uos.de>
 */
class ExportController extends StudipController
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
    function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        vips_require_status('autor');
        Navigation::activateItem('/course/vips/sheets');
        PageLayout::setHelpKeyword(vips_text_encode('Vips.Drucken'));
    }

    /**
     * Lists all students of the course
     * => print solution of one/all student(s) for a given assignment
     *
     * get participants of course
     * list students with checkboxes to select one,two,all students
     * button:print assignment for selected students
     * new page for every student... pdf?
     */
    function print_student_overview_action()
    {
        global $vipsPlugin;

        vips_require_status('tutor');

        $assignment_id = Request::int('assignment_id');
        $assignment = VipsAssignment::find($assignment_id);

        check_assignment_access($assignment);

        // fetch users which shall be shown //

        if (!vips_has_status('tutor')) {
            // if a student is viewing, show no one but himself
            $user_ids[] = $vipsPlugin->userID;
        } else {
            $user_ids = $assignment->course->members->findBy('status', 'autor')->pluck('user_id');
        }

        // fetch user / group info //

        $students    = [];
        $group_array = [];

        foreach ($user_ids as $user_id) {
            $user_info = [
                'id'       => $user_id,
                'name'     => get_fullname($user_id, 'no_title_rev'),
                'username' => get_username($user_id)];

            $group = null;

            if ($assignment->type == 'practice') {
                // find out which group the user was in at the time the sheet ran out
                $group = get_user_group($user_id, $assignment);
            }

            if (isset($group)) {  // user is group member
                if (!isset($group_array[$group['name']])) {  // group info
                    $group_array[$group['name']] = [  // use unique name as key
                        // use id of first member as id of the group
                        'id'            => $user_id,
                        'name'          => $group['name'],
                        'single_solver' => false,
                        'users'         => []];  // contains group members
                }

                foreach ($group['members'] as $member) {
                    $group_array[$group['name']]['users'][$member] = [
                        'id'       => $member,
                        'name'     => get_fullname($member, 'no_title_rev'),
                        'username' => get_username($member)];
                }
            } else {  // user is single solver
                $user_info['single_solver'] = true;
                $students[] = $user_info;
            }
        }

        // sort groups by name (= key) and append them to the end of $students
        ksort($group_array);
        $students = array_merge($students, $group_array);

        $this->assignment = $assignment;
        $this->students = $students;
    }

    /**
     * Creates html print view of a test (new window) specified by id
     */
    function print_assignment_action()
    {
        global $vipsPlugin, $vipsTemplateFactory;

        $assignment_id = Request::int('assignment_id');
        $assignment = VipsAssignment::find($assignment_id);

        check_assignment_access($assignment);

        $test_start       = strtotime($assignment->start);
        $test_end         = strtotime($assignment->end);
        $released         = $assignment->options['released'];

        if (vips_has_status('tutor')) {
            $user_id               = Request::option('user_id');
            $print_correction      = Request::int('print_correction');
            $print_sample_solution = Request::int('print_sample_solution');
        } else {
            $user_id               = $vipsPlugin->userID;
            $print_correction      = $test_end < time() && $released == 2;
            $print_sample_solution = $print_correction;

            if (time() < $test_start || $assignment->type == 'exam' && $test_end < time() && $released != 2) {
                PageLayout::postError(_vips('Zugriff nur für Dozenten erlaubt.'));
                $this->redirect(vips_url('solutions'));
                return;
            }

            if (time() <= $test_end) {
                $assignment->recordAssignmentAttempt($user_id);
            }
        }

        PageLayout::addStylesheet($vipsPlugin->getPluginURL() . '/css/vips_print.css');
        $template = get_print_assignment_template($assignment_id, $user_id, $print_correction, $print_sample_solution);
        $layout = $vipsTemplateFactory->open('export/print_layout');
        $template->set_layout($layout);

        $this->render_template($template);
    }

    /**
     * Creates html print view of a sheet/exam (new window) specified by id
     * This function just calls get_print_assignment_template() for each user or group in turn.
     */
    function print_all_assignments_action()
    {
        global $vipsPlugin, $vipsTemplateFactory;

        vips_require_status('tutor');

        $assignment_id = Request::int('assignment_id');
        $assignment = VipsAssignment::find($assignment_id);

        check_assignment_access($assignment);

        $user_ids              = Request::optionArray('user_ids');
        $print_correction      = Request::int('print_correction');
        $print_sample_solution = Request::int('print_sample_solution');
        $assignment_templates  = [];

        // when printing for all students, $user_ids is an array
        for ($i = 0; $i < count($user_ids); ++$i) {
            $assignment_templates[] = get_print_assignment_template($assignment_id, $user_ids[$i], $print_correction, $print_sample_solution);
        }

        PageLayout::addStylesheet($vipsPlugin->getPluginURL() . '/css/vips_print.css');
        $layout = $vipsTemplateFactory->open('export/print_layout');
        $this->assignment_templates = $assignment_templates;
        $this->set_layout($layout);
    }
}

/**
 * Creates html print view of a test (new window) specified by id
 */
function get_print_assignment_template($assignment_id, $user_id, $print_correction, $print_sample_solution)
{
    global $vipsTemplateFactory;

    $assignment = VipsAssignment::find($assignment_id);

    // get lecturers
    foreach ($assignment->course->getMembersWithStatus('dozent') as $member) {
        $lecturers[] = $member->getUserFullname();
    }

    // get participants
    if ($user_id) {
        if ($assignment->type == 'practice') {
            $group = get_user_group($user_id, $assignment);
        }

        if (isset($group)) {
            foreach ($group['members'] as $user_id) {
                $students[] = get_fullname($user_id);
                $stud_ids[] = get_student_id($user_id) ?: _vips('(keine Matrikelnummer)');
            }
        } else {
            $students[] = get_fullname($user_id);
            $stud_ids[] = get_student_id($user_id) ?: _vips('(keine Matrikelnummer)');
        }
    }

    $exercises_data = [];
    $sum_reached_points = 0;
    $sum_max_points = 0;

    foreach ($assignment->test->exercise_refs as $exercise_ref) {
        $exercise = $exercise_ref->exercise;

        if ($user_id) {
            if (isset($group)) {
                $solution = $assignment->getGroupSolution($group['id'], $exercise->id);
            } else {
                $solution = $assignment->getUserSolution($user_id, $exercise->id);
            }
        }

        $max_points = $exercise_ref->points;
        $reached_points = $solution->points;

        $exercise_template = $exercise->getPrintTemplate($solution, $assignment, $user_id);
        $exercise_template->show_solution = $print_sample_solution;

        $sum_reached_points += $reached_points;
        $sum_max_points     += $max_points;

        $exercises_data[] = [
            'exercise'          => $exercise,
            'exercise_position' => $exercise_ref->position,
            'exercise_template' => $exercise_template,
            'max_points'        => $max_points,
            'solution'          => $solution,  // can be empty
            'reached_points'    => $reached_points,
        ];
    }

    // assure that no negative points are shown
    $sum_reached_points = max(0, $sum_reached_points);

    $template = $vipsTemplateFactory->open('export/print_assignment');
    $template->assignment       = $assignment;
    $template->lecturers        = $lecturers;
    $template->students         = $students;
    $template->stud_ids         = $stud_ids;
    $template->print_correction = $print_correction;
    $template->reached_points   = $sum_reached_points;
    $template->max_points       = $sum_max_points;
    $template->exercises_data   = $exercises_data;

    return $template;
}
