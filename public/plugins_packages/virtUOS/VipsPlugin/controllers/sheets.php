<?php
/*
 * vips_assignments.inc.php - Vips plugin for Stud.IP
 * Copyright (c) 2003-2005  Erik Schmitt, Philipp Hügelmeyer
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
 * Core file of Vips (I guess...). Handles sheets and exams,
 * everything from creating exercises to assigning assignments to blocks
 *
 * @author: Christa Deiwiks <cdeiwiks@uos.de>
 */
class SheetsController extends StudipController
{
    /**
     * Return the default action and arguments
     *
     * @return an array containing the action, an array of args and the format
     */
    function default_action_and_args()
    {
        $action = vips_has_status('tutor') ? 'list_assignments' : 'list_assignments_stud';
        return [$action, [], NULL];
    }

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

        if ($action !== 'relay') {
            vips_require_status('autor');
            Navigation::activateItem('/course/vips/sheets');
            PageLayout::setHelpKeyword(vips_text_encode('Vips.Übungklausur'));
        }
    }

    #####################################
    #                                   #
    #          Student Methods          #
    #                                   #
    #####################################

    /**
     *
     * EXAM
     * Only possible if exam is selftest: Deletes all the solutions of a student or
     * the student's group to enable him/her to redo it.
     */
    function delete_solutions_action()
    {
        global $vipsPlugin;

        $assignment_id = Request::int('assignment_id');
        $assignment = VipsAssignment::find($assignment_id);

        check_assignment_access($assignment);

        if ($assignment->type === 'selftest') {
            if (Request::submitted('delete_solutions_confirmed')) {
                $assignment->deleteSolutions($vipsPlugin->userID);
                PageLayout::postSuccess(_vips('Die Lösungen wurden gelöscht.'));
            } else {
                vips_confirm_dialog(sprintf(_vips('Wollen Sie die Lösungen des Übungsblatts "[nop]%s[/nop]" wirklich löschen?'), $assignment->test->title),
                                    vips_url('sheets/delete_solutions', ['assignment_id' => $assignment_id, 'delete_solutions_confirmed' => 1]),
                                    vips_url('sheets/list_assignments_stud', ['assignment_id' => $assignment_id]));
            }
        }

        $this->redirect(vips_url('sheets/list_assignments_stud', compact('assignment_id')));
    }

    /**
     * SHEETS/EXAMS
     *
     * Is called when the submit button at the bottom of an exercise is called.
     * If there is already a solution of this exercise by the same user or same group,
     * a dialog pops up to confirm the submission. On database-level: EVERY solution is stored
     * (even the unconfirmed ones), with the last solution being marked as last.
     */
    function submit_exercise_action()
    {
        global $vipsPlugin;

        $exercise_id = Request::int('exercise_id');
        $assignment_id = Request::int('assignment_id');
        $assignment = VipsAssignment::find($assignment_id);

        check_exercise_assignment($exercise_id, $assignment);
        check_assignment_access($assignment);

        ##################################################################
        # in case student solution is submitted by tutor or lecturer     #
        # (can happen if the student submits his/her solution by email)  #
        ##################################################################

        $solver_id = Request::option('solver_id');

        if ($solver_id == '' || !vips_has_status('tutor')) {
            $solver_id = $vipsPlugin->userID;
        }

        // from here on we only use $solver_id and not $vipsPlugin->userID

        ############################
        # Checks before submission #
        ############################

        $start = $assignment->start;
        $ende = $assignment->end;

        if (!vips_has_status('tutor')) {
            $now = date('Y-m-d H:i:s');

            // not yet started
            if ($start > $now) {
                PageLayout::postError(_vips('Das Aufgabenblatt wurde noch nicht gestartet.'));
                $this->redirect(vips_url('sheets/list_assignments_stud', compact('assignment_id')));
                return;
            }

            // already ended
            if (time() - strtotime($ende) > 600) {
                PageLayout::postError(_vips('Das Aufgabenblatt wurde bereits beendet.'));
                $this->redirect(vips_url('sheets/list_assignments_stud', compact('assignment_id')));
                return;
            }

            // time has just elapsed
            if ($assignment->type == 'exam' && getRemainingMinutes($solver_id, $assignment) <= -2 || $now > $ende) {
                PageLayout::postError(_vips('Ihre Zeit ist leider abgelaufen! Ihre Lösung wurde gespeichert und mit dem Abgabezeitpunkt versehen.'));
            }
        }

        // let exercise class handle special controls added to the form
        $request  = Request::getInstance();
        $exercise = Exercise::find($exercise_id);
        $solution = $exercise->getSolutionFromRequest($request, $_FILES);
        $solution->user_id = $solver_id;
        $exercise->submitSolutionAction($this, $solution);

        /* if an exercise has been submitted */
        if (Request::submitted('submit_exercise') || Request::int('forced')) {
            $assignment->storeSolution($solution);

            PageLayout::postSuccess(sprintf(_vips('Die Aufgabe &bdquo;%s&ldquo; wurde abgegeben.'), htmlReady($exercise->title)));
            $dialog = 'submitted';
        }

        $this->redirect(vips_url('sheets/show_exercise', compact('assignment_id', 'exercise_id', 'dialog', 'solver_id')));
    }

    /**
     * SHEETS/EXAMS
     *
     * Displays an exercise (from student perspective)
     *
     * This function is also invoked by a function in vips_solutions.inc.php to show the
     * solution of a student to a lecturer.
     */
    function show_exercise_action()
    {
        global $vipsPlugin;

        $exercise_id   = Request::int('exercise_id');
        $assignment_id = Request::int('assignment_id');
        $assignment    = VipsAssignment::find($assignment_id);
        $solver_id     = Request::option('solver_id');  // solver is handed over via address line, ie. user is either a lecturer or a student who is looking at corrections
        $single_solver = Request::int('single_solver'); // user is not participant in any group

        check_exercise_assignment($exercise_id, $assignment);
        check_assignment_access($assignment);

        if ($solver_id == '' || !vips_has_status('tutor')) {
            $solver_id = $vipsPlugin->userID;
        }

        ##############################################################
        #    check for ip_address, remaining time and interrupted    #
        ##############################################################

        // restrict access for students!
        if (!vips_has_status('tutor')) {
            $now = date('Y-m-d H:i:s');

            // the assignment is not accessible any more after it has run out
            if ($assignment->start > $now || $now > $assignment->end) {
                PageLayout::postError(_vips('Das Aufgabenblatt kann zur Zeit nicht bearbeitet werden.'));
                $this->redirect(vips_url('sheets/list_assignments_stud', compact('assignment_id')));
                return;
            }

            if (!$assignment->active) {
                PageLayout::postError(_vips('Das Aufgabenblatt wurde vom Dozenten unterbrochen!'));
                $this->redirect(vips_url('sheets/list_assignments_stud', compact('assignment_id')));
                return;
            }

            if ($assignment->type == 'exam') {
                if ($assignment->checkIPAccess($_SERVER['REMOTE_ADDR']) == false) {
                    PageLayout::postError(_vips('Kein Zugriff möglich!'));
                    $this->redirect(vips_url('sheets/list_assignments_stud', compact('assignment_id')));
                    return;
                }

                if (getRemainingMinutes($solver_id, $assignment) <= 0) {
                    PageLayout::postError(_vips('Die Zeit ist leider abgelaufen!'));
                    $this->redirect(vips_url('sheets/list_assignments_stud', compact('assignment_id')));
                    return;
                }
            }

            // enter user start time the moment he/she first clicks on any exercise
            $assignment->recordAssignmentAttempt($solver_id);
        }

        // fetch exercise info, type, points
        $exercise_ref = VipsExerciseRef::find([$exercise_id, $assignment->test_id]);
        $exercise     = $exercise_ref->exercise;

        // fetch previous and next exercises
        $prev_exercise_id = NULL;
        $next_exercise_id = NULL;
        $before_current   = true;  // flag

        foreach ($assignment->test->exercise_refs as $ref) {
            if ($ref->exercise_id != $exercise_id) {
                if ($before_current) {
                    $prev_exercise_id = $ref->exercise_id;
                } else {
                    $next_exercise_id = $ref->exercise_id;
                    break;  // break while loop
                }
            } else {
                $before_current = false;
            }
        }

        ###################################
        # get user solution if applicable #
        ###################################

        $solution = $assignment->getSolution($solver_id, $exercise_id);

        $max_points = $exercise_ref->points;
        $reached_points = $solution->points;

        // if a solution has been submitted during a selftest
        $dialog = Request::option('dialog');  //internal communication :-) not from forms
        $show_solution = $dialog == 'submitted' && $assignment->type === 'selftest';

        if ($show_solution) {  // correction mode
            $exercise_template = $exercise->getCorrectionTemplate($solution);
        } else {  // solve exercise
            $exercise_template = $exercise->getSolveTemplate($solution, $assignment, $solver_id);
        }

        ##############################
        #   set template variables   #
        ##############################

        $this->assignment            = $assignment;
        $this->assignment_id         = $assignment_id;
        $this->exercise              = $exercise;
        $this->exercise_id           = $exercise_id;
        $this->prev_exercise_id      = $prev_exercise_id;
        $this->next_exercise_id      = $next_exercise_id;
        $this->exercise_position     = $exercise_ref->position;
        $this->exercise_template     = $exercise_template;

        $this->solver_id             = $solver_id;
        $this->solution              = $solution;  // can be empty
        $this->max_points            = $max_points;
        $this->reached_points        = $reached_points;
        $this->show_solution         = $show_solution;
        $this->studentAbgabe         = getAbgabezeitpunkt($solver_id, $assignment);

        if (vips_has_status('tutor')) {
            Helpbar::get()->addPlainText('',
                _vips('Dies ist die Studentenansicht (Vorschau) der Aufgabe. Sie können hier auch Lösungen von Studenten ansehen oder für sie abgeben.'));

            $widget = new ActionsWidget();
            $widget->addLink(_vips('Diese Aufgabe bearbeiten'),
                vips_link('sheets/edit_exercise', ['assignment_id' => $assignment_id, 'exercise_id' => $exercise_id]),
                Icon::create('edit'));
            $widget->addLink(_vips('Zeichenwähler öffnen'), '#',
                Icon::create('comment'),
                ['class' => 'open_character_picker', 'data-language' => $exercise->options['lang']]);
            Sidebar::get()->addWidget($widget);

            $widget = new SelectWidget(_vips('Anzeigen für'), vips_link('sheets/show_exercise', compact('assignment_id', 'exercise_id')), 'solver_id');
            $element = new SelectElement($vipsPlugin->userID, '', $vipsPlugin->userID == $solver_id);
            $widget->addElement($element);

            foreach ($assignment->course->members->findBy('status', 'autor')->orderBy('nachname, vorname') as $member) {
                $element = new SelectElement($member->user_id, $member->nachname . ', ' . $member->vorname, $member->user_id == $solver_id);
                $widget->addElement($element);
            }
            Sidebar::get()->addWidget($widget);
        } else {
            Helpbar::get()->addPlainText('',
                _vips('Bitte denken Sie daran, vor dem Verlassen der Seite Ihre Lösung zu speichern. Für die Eingabe von Sonderzeichen steht der Zeichenwähler zur Verfügung.'));

            $widget = new ActionsWidget();
            $widget->addLink(_vips('Zeichenwähler öffnen'), '#',
                Icon::create('comment'),
                ['class' => 'open_character_picker', 'data-language' => $exercise->options['lang']]);
            Sidebar::get()->addWidget($widget);
        }

        $widget = new ViewsWidget();
        $widget->setTitle(_vips('Aufgabenblatt'));

        foreach ($assignment->test->exercise_refs as $item) {
            $this->item = $item;
            $this->solution = $assignment->getSolution($solver_id, $item->exercise_id);
            $element = new WidgetElement($this->render_template_as_string('exercises/show_exercise_link'));
            $element->active = $item->exercise_id === $exercise_id;
            $widget->addElement($element, 'exercise-' . $item->exercise_id);
        }

        Sidebar::get()->addWidget($widget);

        $this->render_template('exercises/show_exercise', $this->layout);
    }

    /**
     * Displays all running assignments "work-on ready" for students (view of
     * students when clicking on tab Uebungsblatt), respectively student view
     * for lecturers and tutors.
     */
    function list_assignments_stud_action()
    {
        global $vipsPlugin;

        $this->assignments = [];
        $this->ip_address  = $_SERVER['REMOTE_ADDR'];

        if (!vips_has_status('tutor')) {
            $assignments = VipsAssignment::findBySQL('course_id = ? ORDER BY start', [$vipsPlugin->courseID]);

            foreach ($assignments as $assignment) {
                if ($assignment->isRunning() && $assignment->isVisible()) {
                    $this->assignments[] = $assignment;
                }
            }
            $this->solver_id = $vipsPlugin->userID;
        } else {
            // for lecturers/tutors who want to see student view, just get one test
            $assignment_id = Request::int('assignment_id');
            $assignment = VipsAssignment::find($assignment_id);
            $solver_id = Request::option('solver_id', $vipsPlugin->userID);

            check_assignment_access($assignment);

            $this->assignments[] = $assignment;
            $this->solver_id = $solver_id;

            Helpbar::get()->addPlainText('',
                _vips('Dies ist die Studentenansicht (Vorschau) des Aufgabenblatts.'));

            $widget = new ActionsWidget();
            $widget->addLink(_vips('Aufgabenblatt bearbeiten'),
                vips_link('sheets/edit_assignment', ['assignment_id' => $assignment_id]),
                Icon::create('edit'));
            $widget->addLink(_vips('Aufgabenblatt drucken'),
                vips_link('export/print_student_overview', ['assignment_id' => $assignment_id]),
                Icon::create('print'));
            Sidebar::get()->addWidget($widget);

            $widget = new SelectWidget(_vips('Anzeigen für'), vips_link('sheets/list_assignments_stud', compact('assignment_id')), 'solver_id');
            $element = new SelectElement($vipsPlugin->userID, '', $vipsPlugin->userID == $solver_id);
            $widget->addElement($element);

            foreach ($assignment->course->members->findBy('status', 'autor')->orderBy('nachname, vorname') as $member) {
                $element = new SelectElement($member->user_id, $member->nachname . ', ' . $member->vorname, $member->user_id == $solver_id);
                $widget->addElement($element);
            }
            Sidebar::get()->addWidget($widget);
        }
    }

    #####################################
    #                                   #
    #         Lecturer Methods          #
    #                                   #
    #####################################


    /**
     * Toggle student access to the Vips tab in this course.
     */
    function toggle_visibility_action()
    {
        global $vipsPlugin;

        vips_require_status('tutor');

        $settings = new VipsSettings($vipsPlugin->courseID);
        $settings->visible = !$settings->visible;
        $settings->store();

        $this->redirect(vips_url('sheets'));
    }

    /**
     * EXAMS/SHEETS
     *
     * If an assignment hasn't started yet this function sets the start time to NOW
     * so that it's running
     *
     */
    function start_assignment_action()
    {
        vips_require_status('tutor');

        $assignment_id = Request::int('assignment_id');
        $assignment = VipsAssignment::find($assignment_id);

        check_assignment_access($assignment);

        // set new start time in database
        $assignment->start = date('Y-m-d H:i:s');
        $assignment->active = 1;
        $assignment->store();

        // delete start time for exam from database
        VipsAssignmentAttempt::deleteBySQL('assignment_id = ?', [$assignment_id]);

        $this->redirect(vips_url('sheets'));
    }


    /**
     * EXAMS/SHEETS
     *
     * Stops/continues an assignment (no change of start/end time but temporary closure)
     *
     */
    function stopgo_assignment_action()
    {
        vips_require_status('tutor');

        $assignment_id = Request::int('assignment_id');
        $assignment = VipsAssignment::find($assignment_id);

        check_assignment_access($assignment);

        $assignment->active = !$assignment->active;
        $assignment->store();

        $this->redirect(vips_url('sheets'));
    }


    /**
     * EXAMS/SHEETS
     *
     * Deletes an assignment from the course (and block if applicable).
     */
    function delete_assignment_action()
    {
        vips_require_status('tutor');

        $assignment_id = Request::int('assignment_id');
        $assignment = VipsAssignment::find($assignment_id);
        $test_title = $assignment->test->title;

        check_assignment_access($assignment);

        if (Request::submitted('delete_assignment_confirmed')) {
            $assignment->delete();

            PageLayout::postSuccess(sprintf(_vips('Das Aufgabenblatt &bdquo;%s&ldquo; wurde gelöscht.'), htmlReady($test_title)));
        } else {
            vips_confirm_dialog(sprintf(_vips('Wollen Sie wirklich das Aufgabenblatt "[nop]%s[/nop]" löschen?'), $test_title),
                                vips_url('sheets/delete_assignment', ['assignment_id' => $assignment_id, 'delete_assignment_confirmed' => 1]),
                                vips_url('sheets'));
        }

        $this->redirect(vips_url('sheets'));
    }

    /**
     * Delete a list of assignments from the course (and block if applicable).
     */
    function delete_assignments_action()
    {
        vips_require_status('tutor');

        $assignment_ids = Request::intArray('assignment_ids');

        foreach ($assignment_ids as $assignment_id) {
            $assignment = VipsAssignment::find($assignment_id);
            check_assignment_access($assignment);

            $assignment->delete();
        }

        PageLayout::postSuccess(sprintf(_vips('Es wurden %s Aufgabenblätter gelöscht.'), count($assignment_ids)));

        $this->redirect(vips_url('sheets'));
    }

    /**
     * Dialog for selecting a block for a list of assignments.
     */
    function assign_block_dialog_action()
    {
        global $vipsPlugin;

        vips_require_status('tutor');

        $this->assignment_ids = Request::intArray('assignment_ids');
        $this->blocks = VipsBlock::findBySQL('course_id = ? ORDER BY name', [$vipsPlugin->courseID]);
    }

    /**
     * Assign a list of assignments to the specified block.
     */
    function assign_block_action()
    {
        vips_require_status('tutor');

        $assignment_ids = Request::intArray('assignment_ids');
        $block_id = Request::int('block_id');

        if ($block_id) {
            $block = VipsBlock::find($block_id);
            check_block_access($block);
        }

        foreach ($assignment_ids as $assignment_id) {
            $assignment = VipsAssignment::find($assignment_id);
            check_assignment_access($assignment);

            $assignment->block_id = $block_id ?: NULL;
            $assignment->store();
        }

        PageLayout::postSuccess(_vips('Die Blockzuordnung wurde gespeichert.'));

        $this->redirect(vips_url('sheets'));
    }

    /**
     * Dialog for moving a list of assignments to another course.
     */
    function move_assignments_dialog_action()
    {
        global $vipsPlugin;

        vips_require_status('tutor');

        $this->assignment_ids = Request::intArray('assignment_ids');
        $plugin_manager = PluginManager::getInstance();

        $sql = "JOIN seminar_user USING(Seminar_id)
                WHERE user_id = ? AND seminar_user.status IN ('dozent', 'tutor')
                ORDER BY start_time DESC, Name";
        $this->courses = Course::findBySQL($sql, [$vipsPlugin->userID]);

        // remove courses where Vips is not active
        foreach ($this->courses as $key => $course) {
            if (!$plugin_manager->isPluginActivated($vipsPlugin->getPluginId(), $course->id)) {
                unset($this->courses[$key]);
            }
        }
    }

    /**
     * Move a list of assignments to the specified course.
     */
    function move_assignments_action()
    {
        vips_require_status('tutor');

        $assignment_ids = Request::intArray('assignment_ids');
        $course_id = Request::option('course_id');

        vips_require_status('tutor', $course_id);

        foreach ($assignment_ids as $assignment_id) {
            $assignment = VipsAssignment::find($assignment_id);
            check_assignment_access($assignment);

            $assignment->course_id = $course_id;
            $assignment->block_id = NULL;
            $assignment->store();
        }

        PageLayout::postSuccess(_vips('Die Aufgabenblätter wurden verschoben.'));

        $this->redirect(vips_url('sheets'));
    }



    /**
     * SHEETS/EXAMS
     *
     * Takes an exercise off an assignment and deletes it.
     */
    function delete_exercise_action()
    {
        vips_require_status('tutor');

        $exercise_id = Request::int('exercise_id');
        $assignment_id = Request::int('assignment_id');
        $assignment = VipsAssignment::find($assignment_id);
        $exercise = Exercise::find($exercise_id);

        check_exercise_assignment($exercise_id, $assignment);
        check_assignment_access($assignment);

        if (Request::submitted('delete_exercise_confirmed')) {
            $assignment->test->removeExercise($exercise_id, true);

            PageLayout::postSuccess(sprintf(_vips('Die Aufgabe &bdquo;%s&ldquo; wurde gelöscht.'), htmlReady($exercise->title)));
        } else {
            vips_confirm_dialog(sprintf(_vips('Wollen Sie wirklich die Aufgabe "[nop]%s[/nop]" löschen?'), $exercise->title),
                                vips_url('sheets/delete_exercise', compact('assignment_id', 'exercise_id') + ['delete_exercise_confirmed' => 1]),
                                vips_url('sheets/edit_assignment', compact('assignment_id')));
        }

        $this->redirect(vips_url('sheets/edit_assignment', compact('assignment_id')));
    }


    /**
     * List all exercises in the given assignment (AJAX).
     */
    function list_exercises_ajax_action()
    {
        vips_require_status('tutor');

        $assignment_id = Request::int('assignment_id');
        $assignment = VipsAssignment::find($assignment_id);

        check_assignment_access($assignment);

        $this->assignment_id          = $assignment_id;
        $this->test                   = $assignment->test;
        $this->exercises              = $assignment->test->exercises;

        $this->render_template('sheets/list_exercises');
    }


    /**
     * Takes an exercise off an assignment and deletes it.
     */
    function delete_exercise_ajax_action()
    {
        vips_require_status('tutor');

        $exercise_id = Request::int('exercise_id');
        $assignment_id = Request::int('assignment_id');
        $assignment = VipsAssignment::find($assignment_id);

        check_exercise_assignment($exercise_id, $assignment);
        check_assignment_access($assignment);

        $assignment->test->removeExercise($exercise_id, true);

        $this->redirect(vips_url('sheets/list_exercises_ajax', compact('assignment_id')));
    }

    /**
     * SHEETS/EXAMS
     *
     * Changes position of an exercise within an assignment
     *
     */
    function move_exercise_action()
    {
        vips_require_status('tutor');

        $exercise_id   = Request::int('exercise_id');
        $assignment_id = Request::int('assignment_id');
        $assignment    = VipsAssignment::find($assignment_id);
        $test_id       = $assignment->test_id;

        check_exercise_assignment($exercise_id, $assignment);
        check_assignment_access($assignment);

        $direction = Request::option('direction');
        $position  = Request::int('exercise_position');

        if ($direction === 'down') {
            $position_target = $position - 1;
        } else {
            $position_target = $position + 1;
        }

        // move selected
        $exercise_ref1 = VipsExerciseRef::findOneBySQL('test_id = ? AND position = ?', [$test_id, $position]);
        $exercise_ref2 = VipsExerciseRef::findOneBySQL('test_id = ? AND position = ?', [$test_id, $position_target]);

        if ($exercise_ref1 && $exercise_ref2) {
            $exercise_ref1->position = $position_target;
            $exercise_ref1->store();

            $exercise_ref2->position = $position;
            $exercise_ref2->store();
        }

        $this->redirect(vips_url('sheets/edit_assignment', compact('assignment_id')));
    }

    /**
     * Reorder exercise positions within an assignment (AJAX).
     */
    function move_exercise_ajax_action()
    {
        vips_require_status('tutor');

        $assignment_id = Request::int('assignment_id');
        $assignment = VipsAssignment::find($assignment_id);

        check_assignment_access($assignment);

        $list = Request::intArray('list');

        /* renumber all exercises in current assignment */
        foreach ($list as $i => $exercise_id) {
            $exercise_ref = VipsExerciseRef::find([$exercise_id, $assignment->test_id]);

            if ($exercise_ref) {
                $exercise_ref->position = $i + 1;
                $exercise_ref->store();
            }
        }

        $this->render_nothing();
    }

    /**
     * Pick a semester for copying an exercise (AJAX).
     */
    function pick_semester_ajax_action()
    {
        global $vipsPlugin;

        vips_require_status('tutor');

        $assignment_id = Request::int('assignment_id');
        $assignment = VipsAssignment::find($assignment_id);

        check_assignment_access($assignment);

        $start_time = Request::int('start_time');
        $close      = Request::int('close');

        $this->assignment_id = $assignment_id;

        // get semester, course, assignment and exercise data for copying.
        // available are all assignments that all lecturers in this course
        // have access to (including other courses)

        $this->semester = Semester::findByTimestamp($start_time);

        if (empty($close)) {
            $this->start_time = $start_time;
            $this->courses = get_accessible_courses($vipsPlugin->userID, $start_time);
        }

        $this->render_template('sheets/pick_semester');
    }

    /**
     * Pick a test for copying an exercise (AJAX).
     */
    function pick_test_ajax_action()
    {
        vips_require_status('tutor');

        $assignment_id = Request::int('assignment_id');
        $start_time    = Request::int('start_time');
        $selected_id   = Request::int('selected_id');
        $close         = Request::int('close');

        $this->assignment_id = $assignment_id;
        $this->start_time    = $start_time;

        // get semester, course, assignment and exercise data for copying.
        // available are all assignments that all lecturers in this course
        // have access to (including other courses)

        $this->semester = Semester::findByTimestamp($start_time);
        $this->assignment = VipsAssignment::find($selected_id);

        check_copy_assignment_access($this->assignment);

        if (empty($close)) {
            $this->selected_id = $selected_id;
            $this->all_exercises = $this->assignment->test->exercises;
        }

        $this->render_template('sheets/pick_test');
    }

    /**
     * Get search results for copying an exercise (AJAX).
     */
    function search_exercise_ajax_action()
    {
        global $vipsPlugin;

        vips_require_status('tutor');

        $search = studip_utf8decode(Request::get('search'));
        $this->search_result = get_matching_exercises($vipsPlugin->userID, $search);

        $this->render_template('sheets/pick_exercise');
    }

    /**
     * SHEETS/EXAMS
     *
     * Displays the form for editing an exercise.
     *
     * Is called when editing an existing exercise or creating a new exercise.
     *
     *
     */
    function edit_exercise_action()
    {
        vips_require_status('tutor');

        $exercise_id = Request::int('exercise_id');  // is not set when creating new exercise
        $assignment_id = Request::int('assignment_id');
        $assignment = VipsAssignment::find($assignment_id);

        check_assignment_access($assignment);

        if ($exercise_id) {
            check_exercise_assignment($exercise_id, $assignment);
        }

        if ($exercise_id) {
            // edit already existing exercise
            $exercise = Exercise::find($exercise_id);

            // fetch previous and next exercises
            $prev_exercise_id = NULL;
            $next_exercise_id = NULL;
            $before_current   = true;

            foreach ($assignment->test->exercise_refs as $item) {
                $id = (int) $item->exercise_id;
                if ($id != $exercise_id) {
                    if ($before_current) {
                        $prev_exercise_id = $id;
                    } else {
                        $next_exercise_id = $id;
                        break;  // break while loop
                    }
                } else {
                    $before_current = false;
                }
            }
        } else {
            // create new exercise
            $exercise_type = Request::option('new_exercise_type');
            $exercise = new $exercise_type();
        }

        $this->assignment            = $assignment;
        $this->assignment_id         = $assignment_id;
        $this->exercise              = $exercise;
        $this->prev_exercise_id      = $prev_exercise_id;
        $this->next_exercise_id      = $next_exercise_id;

        Helpbar::get()->addPlainText('',
            _vips('Sie können hier den Aufgabentext und die Antwortoptionen dieser Aufgabe bearbeiten.'));

        $widget = new ActionsWidget();
        $widget->addLink(_vips('Neue Aufgabe erstellen'),
            vips_url('sheets/add_exercise_dialog', ['assignment_id' => $assignment_id, 'exercise_type' => $exercise->type]),
            Icon::create('add'), ['data-dialog' => 'size=auto']);
        $widget->addLink(_vips('Zeichenwähler öffnen'), '#',
            Icon::create('comment'), ['class' => 'open_character_picker']);
        Sidebar::get()->addWidget($widget);

        if ($exercise->id) {
            $widget = new LinksWidget();
            $widget->setTitle(_vips('Ansicht'));
            $widget->addLink(_vips('Aufgabe aus Studentensicht anzeigen'),
                vips_link('sheets/show_exercise', ['exercise_id' => $exercise->id, 'assignment_id' => $assignment_id]),
                Icon::create('community'));
            Sidebar::get()->addWidget($widget);

            if ($exercise->getQTITemplate($assignment)) {
                $widget = new ExportWidget();
                $widget->addLink(_vips('Aufgabe exportieren (IMS-QTI)'),
                    vips_link('sheets/export_qti', ['assignment_id' => $assignment_id, 'exercise_id' => $exercise->id]),
                    Icon::create('download'));
                Sidebar::get()->addWidget($widget);
            }
        }

        $this->render_template('exercises/edit_exercise', $this->layout);
    }


    /**
     * SHEETS/EXAMS
     *
     * Inserts/Updates an exercise into the database
     */
    function store_exercise_action()
    {
        global $vipsPlugin;

        vips_require_status('tutor');

        $exercise_id = Request::int('exercise_id');  // not set when storing new exercise
        $exercise_type = Request::option('exercise_type');
        $assignment_id = Request::int('assignment_id');
        $assignment = VipsAssignment::find($assignment_id);
        $test_id = $assignment->test_id;
        $request = Request::getInstance();

        check_assignment_access($assignment);

        if ($exercise_id) {
            check_exercise_assignment($exercise_id, $assignment);

            // update existing exercise.
            $exercise = Exercise::find($exercise_id);
            $item_count = $exercise->itemCount();
            $exercise->initFromRequest($request);
            $exercise->store();

            // update maximum points
            if ($exercise->itemCount() != $item_count) {
                $exercise_ref = VipsExerciseRef::find([$exercise_id, $test_id]);
                $exercise_ref->points = $exercise->itemCount();
                $exercise_ref->store();
            }
        } else {
            // store exercise in database.
            $exercise = new $exercise_type();
            $exercise->initFromRequest($request);
            $exercise->user_id = $vipsPlugin->userID;
            $exercise->created = date('Y-m-d H:i:s');
            $exercise->store();

            // link new exercise to the assignment.
            $assignment->test->addExercise($exercise);
            $exercise_id = $exercise->id;
        }

        PageLayout::postSuccess(_vips('Die Aufgabe wurde eingetragen.'));

        $this->redirect(vips_url('sheets/edit_exercise', compact('assignment_id', 'exercise_id')));
    }

    /**
     * Copy the selected exercises into this assignment.
     */
    function copy_exercise_action()
    {
        vips_require_status('tutor');

        $assignment_id = Request::int('assignment_id');
        $assignment = VipsAssignment::find($assignment_id);
        $exercise_id = Request::int('exercise_id');
        $exercise_ids = $exercise_id ? [$exercise_id => $assignment_id] : Request::intArray('exercise_ids');

        check_assignment_access($assignment);

        if ($exercise_ids) {
            copy_exercise_list($assignment, $exercise_ids);
            PageLayout::postSuccess(n_vips('Die Aufgabe wurde kopiert.', 'Die Aufgaben wurden kopiert.', count($exercise_ids)));
        }

        $this->redirect(vips_url('sheets/edit_assignment', compact('assignment_id')));
    }

    /**
     * Copy the selected exercise into this assignment.
     */
    function copy_exercise_ajax_action()
    {
        vips_require_status('tutor');

        $assignment_id = Request::int('assignment_id');
        $assignment = VipsAssignment::find($assignment_id);
        $exercise_id = Request::int('exercise_id');

        check_assignment_access($assignment);

        copy_exercise_list($assignment, [$exercise_id => $assignment_id]);

        $this->redirect(vips_url('sheets/list_exercises_ajax', compact('assignment_id')));
    }

    /**
     * Exports all exercises in this assignment in a plain text format.
     */
    function export_assignment_action()
    {
        vips_require_status('tutor');

        $assignment_id = Request::int('assignment_id');
        $assignment = VipsAssignment::find($assignment_id);

        check_assignment_access($assignment);

        $this->set_content_type('text/plain; charset=windows-1252');
        header('Content-Disposition: attachment; ' . vips_encode_header_parameter('filename', $assignment->test->title.'.txt'));

        $this->header_line = class_exists('Context') ? Context::getHeaderLine() : $_SESSION['SessSemName']['header_line'];
        $this->assignment = $assignment;

        $this->render_template('sheets/export_assignment_text');
    }

    /**
     * SHEETS/EXAMS
     *
     * Stores the specification (Grunddaten) of an assignment
     * OR add new exercise, edit points/Bewertung (basically everything that can be done on
     * page edit_exercise_action())
     */
    function store_assignment_action()
    {
        global $vipsPlugin;

        vips_require_status('tutor');

        $assignment_id = Request::int('assignment_id');

        if ($assignment_id) {
            $assignment = VipsAssignment::find($assignment_id);
            check_assignment_access($assignment);
        } else {
            $assignment = new VipsAssignment();
            $assignment->course_id = $vipsPlugin->courseID;
            $assignment->options = ['released' => 0];
        }

        $assignment_name        = trim(Request::get('assignment_name'));
        $assignment_description = trim(Request::get('assignment_description'));
        $assignment_description = Studip\Markup::purifyHtml($assignment_description);
        $assignment_notes       = trim(Request::get('assignment_notes'));
        $assignment_type        = Request::option('assignment_type', 'practice');
        $assignment_block       = Request::int('assignment_block', 0);
        $assignment_block_name  = trim(Request::get('assignment_block_name'));
        $start_date             = trim(Request::get('start_date'));
        $start_time             = trim(Request::get('start_time'));
        $end_date               = trim(Request::get('end_date'));
        $end_time               = trim(Request::get('end_time'));

        $exam_length            = Request::int('exam_length');
        $ip_range               = trim(Request::get('ip_range'));
        $evaluation_mode        = Request::int('evaluation_mode');
        $exercise_points        = Request::floatArray('exercise_points');

        $start_datetime = DateTime::createFromFormat('d.m.Y H:i', $start_date.' '.$start_time);
        $end_datetime   = DateTime::createFromFormat('d.m.Y H:i', $end_date.' '.$end_time);

        if ($start_datetime) {
            $start = $start_datetime->format('Y-m-d H:i:s');
        } else {
            $start = date('Y-m-d H:00:00');
            PageLayout::postInfo(_vips('Ungültiger Startzeitpunkt, der Wert wurde nicht übernommen.'));
        }

        // unlimited selftest
        if ($assignment_type == 'selftest' && $end_date == '' && $end_time == '') {
            $end = VIPS_DATE_INFINITY;
        } else if ($end_datetime) {
            $end = $end_datetime->format('Y-m-d H:i:s');
        } else {
            $end = date('Y-m-d H:00:00');
            PageLayout::postInfo(_vips('Ungültiger Endzeitpunkt, der Wert wurde nicht übernommen.'));
        }

        if ($end <= $start) {  // start is *later* than end!
            $end = $start;
            PageLayout::postInfo(_vips('Bitte überprüfen Sie den Start- und den Endzeitpunkt!'));
        }

        /*** store basic data (Grunddaten) of assignment */
        if ($assignment_id) {
            // check whether the exam's start time has been moved and delete
            // start times for users which already have begun to solve the exam
            if ($assignment->start != $start && time() <= strtotime($start)) {
                $assignment->active = 1;
                VipsAssignmentAttempt::deleteBySQL('assignment_id = ?', [$assignment_id]);
            }

            $assignment->test->setData([
                'title'       => $assignment_name,
                'description' => $assignment_description
            ]);
            $assignment->test->store();
        } else {
            $assignment->test = VipsTest::create([
                'title'       => $assignment_name,
                'description' => $assignment_description,
                'user_id'     => $vipsPlugin->userID,
                'created'     => date('Y-m-d H:i:s')
            ]);
        }

        if ($assignment_block_name != '') {
            $block = VipsBlock::create(['name' => $assignment_block_name, 'course_id' => $vipsPlugin->courseID]);
        } else if ($assignment_block) {
            $block = VipsBlock::find($assignment_block);
            check_block_access($block);
        } else {
            $block = NULL;
        }

        $assignment->setData([
            'type'      => $assignment_type,
            'start'     => $start,
            'end'       => $end,
            'block_id'  => $block->id
        ]);

        // update options array
        $assignment->options['evaluation_mode'] = $evaluation_mode;
        $assignment->options['notes']           = $assignment_notes;

        if ($assignment_type == 'exam') {
            $assignment->options['duration'] = $exam_length;
            $assignment->options['ip_range'] = $ip_range;
        }

        if ($ip_range == '') {
            unset($assignment->options['ip_range']);
        }

        $assignment->store();
        $assignment_id = $assignment->id;

        foreach ($exercise_points as $exercise_id => $points) {
            $exercise_ref = VipsExerciseRef::find([$exercise_id, $test_id]);

            if ($exercise_ref) {
                $exercise_ref->points = round_to_half_point($points);
                $exercise_ref->store();
            }
        }

        PageLayout::postSuccess(_vips('Das Aufgabenblatt wurde gespeichert.'));
        $this->redirect(vips_url('sheets/edit_assignment', compact('assignment_id')));
    }

    /**
     * Returns the dialog content to create a new exercise.
     */
    function add_exercise_dialog_action()
    {
        vips_require_status('tutor');

        $assignment_id = Request::int('assignment_id');
        $exercise_type = Request::option('exercise_type');

        $this->assignment_id  = $assignment_id;
        $this->exercise_type  = $exercise_type;
        $this->exercise_types = Exercise::getExerciseTypes();
    }

    /**
     * Returns the dialog content to copy an existing exercise.
     */
    function copy_exercise_dialog_action()
    {
        global $vipsPlugin;

        vips_require_status('tutor');

        $assignment_id = Request::int('assignment_id');

        // get semester data for copying. available are all assignments that
        // the user in this course has access to (including other courses).

        $this->assignment_id = $assignment_id;
        $this->semesters     = get_accessible_semesters($vipsPlugin->userID);
    }


    /**
     * SHEETS/EXAMS
     *
     * Displays form to edit an existing assignment
     *
     */
    function edit_assignment_action()
    {
        global $vipsPlugin;

        vips_require_status('tutor');

        $assignment_id = Request::int('assignment_id');
        $assignment = VipsAssignment::find($assignment_id);

        check_assignment_access($assignment);

        $blocks = VipsBlock::findBySQL('course_id = ? ORDER BY name', [$assignment->course_id]);

        $this->assignment             = $assignment;
        $this->assignment_id          = $assignment_id;
        $this->test                   = $assignment->test;
        $this->blocks                 = $blocks;
        $this->exercises              = $assignment->test->exercises;
        $this->assignment_types       = VipsAssignment::getAssignmentTypes();

        Helpbar::get()->addPlainText('',
            _vips('Sie können hier die Grunddaten des Aufgabenblatts verwalten und Aufgaben hinzufügen, bearbeiten oder löschen.') . ' ' .
            _vips('Alle Daten können später geändert oder ergänzt werden.'));

        if ($assignment_id) {
            $widget = new ActionsWidget();
            $widget->addLink(_vips('Neue Aufgabe erstellen'),
                vips_url('sheets/add_exercise_dialog', compact('assignment_id')),
                Icon::create('add'), ['data-dialog' => 'size=auto']);
            $widget->addLink(_vips('Vorhandene Aufgabe kopieren'),
                vips_url('sheets/copy_exercise_dialog', compact('assignment_id')),
                Icon::create('assessment+add'), ['data-dialog' => '']);
            $widget->addLink(_vips('Zeichenwähler öffnen'), '#',
                Icon::create('comment'), ['class' => 'open_character_picker']);
            $widget->addLink(_vips('Aufgabenblatt korrigieren'),
                vips_link('solutions/assignment_solutions', ['assignment_id' => $assignment_id]),
                Icon::create('accept'));
            $widget->addLink(_vips('Aufgabenblatt drucken'),
                vips_link('export/print_student_overview', ['assignment_id' => $assignment_id]),
                Icon::create('print'));
            Sidebar::get()->addWidget($widget);

            $widget = new LinksWidget();
            $widget->setTitle(_vips('Ansicht'));
            $widget->addLink(_vips('Aufgabenblatt aus Studentensicht anzeigen'),
                vips_link('sheets/list_assignments_stud', ['assignment_id' => $assignment_id]),
                Icon::create('community'));
            Sidebar::get()->addWidget($widget);

            $widget = new ExportWidget();
            $widget->addLink(_vips('Aufgabenblatt exportieren (XML)'),
                vips_link('sheets/export_xml', ['assignment_id' => $assignment_id]),
                Icon::create('download'));
            $widget->addLink(_vips('Aufgabenblatt exportieren (Text)'),
                vips_link('sheets/export_assignment', ['assignment_id' => $assignment_id]),
                Icon::create('download'));
            Sidebar::get()->addWidget($widget);
        }
    }

    /**
     * Show preview of an existing exercise (using print view for now).
     */
    function preview_exercise_action()
    {
        vips_require_status('tutor');

        $exercise_id   = Request::int('exercise_id');
        $assignment_id = Request::int('assignment_id');
        $assignment    = VipsAssignment::find($assignment_id);

        check_exercise_assignment($exercise_id, $assignment);
        check_copy_assignment_access($assignment);

        // fetch exercise info
        $exercise_ref = VipsExerciseRef::find([$exercise_id, $assignment->test_id]);
        $exercise     = $exercise_ref->exercise;

        $this->exercise          = $exercise;
        $this->exercise_position = $exercise_ref->position;
        $this->max_points        = $exercise_ref->points;
        $this->exercise_template = $exercise->getPrintTemplate(null, $assignment, null);

        $this->render_template('exercises/print_exercise');
    }

    /**
     * SHEETS/EXAMS
     *
     * Copies assignment completely, i.e. inserts new assignment plus copied exercises from old
     * assignment
     *
     */
    function copy_assignment_action()
    {
        global $vipsPlugin;

        vips_require_status('tutor');

        $assignment_id = Request::int('assignment_id');
        $assignment = VipsAssignment::find($assignment_id);

        if (!$assignment_id) {
            PageLayout::postError(_vips('Sie müssen ein Aufgabenblatt zum Kopieren auswählen.'));
            $this->redirect(vips_url('sheets'));
            return;
        }

        check_copy_assignment_access($assignment);

        #################################
        # copy and paste new assignment #
        #################################

        // determine title of new assignment
        if ($assignment->course_id === $vipsPlugin->courseID) {
            $new_assignment_title = sprintf(_vips('Kopie von %s'), $assignment->test->title);
        } else {
            $new_assignment_title = $assignment->test->title;
        }

        $new_test = VipsTest::create([
            'title'       => $new_assignment_title,
            'description' => $assignment->test->description,
            'user_id'     => $vipsPlugin->userID,
            'created'     => date('Y-m-d H:i:s')
        ]);

        $new_assignment = VipsAssignment::create([
            'test_id'   => $new_test->id,
            'course_id' => $vipsPlugin->courseID,
            'type'      => $assignment->type,
            'start'     => $assignment->start,
            'end'       => $assignment->end,
            'options'   => $assignment->options
        ]);

        ####################################################################
        # copy and paste all exercises associated with original assignment #
        # and copy and paste info from inAssignment table                  #
        ####################################################################

        foreach ($assignment->test->exercise_refs as $exercise_ref) {
            $exercise = $exercise_ref->exercise;

            $new_exercise = Exercise::create([
                'type'        => $exercise->type,
                'title'       => $exercise->title,
                'description' => $exercise->description,
                'task_json'   => $exercise->task_json,
                'options'     => $exercise->options,
                'user_id'     => $vipsPlugin->userID,
                'created'     => date('Y-m-d H:i:s')
            ]);

            VipsExerciseRef::create([
                'exercise_id' => $new_exercise->id,
                'test_id'     => $new_test->id,
                'points'      => $exercise_ref->points,
                'position'    => $exercise_ref->position
            ]);
        }

        PageLayout::postSuccess(_vips('Das Aufgabenblatt wurde kopiert.'));
        $this->redirect(vips_url('sheets'));
    }



    /**
     * Imports a test from a text file.
     */
    function import_test_action()
    {
        global $vipsPlugin;

        vips_require_status('tutor');

        if (!is_uploaded_file($_FILES['import_file']['tmp_name'])) {
            if ($_FILES['userfile']['name'] == '') {
                PageLayout::postError(_vips('Sie müssen eine Datei zum Importieren auswählen.'));
            } else {
                PageLayout::postError(_vips('Es trat ein Fehler beim Hochladen der Datei auf.'));
            }
            $this->redirect(vips_url('sheets'));
            return;
        }

        $text = file_get_contents($_FILES['import_file']['tmp_name']);

        if (strpos($text, '<?xml') !== false) {
            $assignment = VipsAssignment::importXML($text, $vipsPlugin->userID, $vipsPlugin->courseID);
        } else {
            // convert from windows-1252 if legacy text format
            $text = vips_text_decode_1252($text);
            $test_title = basename($_FILES['import_file']['name'], '.txt');
            $assignment = VipsAssignment::importText($test_title, $text, $vipsPlugin->userID, $vipsPlugin->courseID);
        }

        $num_exercises = count($assignment->test->exercise_refs);

        $message = sprintf(n_vips('Das Aufgabenblatt &bdquo;%s&ldquo; mit %d Aufgabe wurde hinzugefügt.',
                                  'Das Aufgabenblatt &bdquo;%s&ldquo; mit %d Aufgaben wurde hinzugefügt.', $num_exercises),
                           htmlReady($assignment->test->title), $num_exercises);
        PageLayout::postSuccess($message);
        $this->redirect(vips_url('sheets'));
    }



    /**
     * SHEETS/EXAMS
     *
     * Displays form to create a new assignment
     * Uses the same template as edit_assignment
     *
     */
    function new_assignment_action()
    {
        global $vipsPlugin;

        vips_require_status('tutor');

        $blocks = VipsBlock::findBySQL('course_id = ? ORDER BY name', [$vipsPlugin->courseID]);

        // default values
        $test = new VipsTest();
        $test->title = _vips('Aufgabenblatt');

        $assignment = new VipsAssignment();
        $assignment->type = 'practice';
        $assignment->start = date('Y-m-d H:00:00');
        $assignment->end = date('Y-m-d H:00:00');

        $this->assignment             = $assignment;
        $this->test                   = $test;
        $this->blocks                 = $blocks;
        $this->assignment_types       = VipsAssignment::getAssignmentTypes();

        Helpbar::get()->addPlainText('',
            _vips('Sie können hier die Grunddaten des Aufgabenblatts verwalten und Aufgaben hinzufügen, bearbeiten oder löschen.') . ' ' .
            _vips('Alle Daten können später geändert oder ergänzt werden.'));

        $this->render_action('edit_assignment');
    }

    /**
     * SHEETS/EXAMS
     *
     * Main page of sheets/exams.
     * Lists all the assignments (sheets or exams) in the course, grouped by "not yet started",
     * "running" and "finished".
     */
    function list_assignments_action()
    {
        global $vipsPlugin;

        vips_require_status('tutor');

        $sort = Request::option('sort', 'start');
        $desc = Request::int('desc');
        $group = Request::int('group', $_SESSION['group_assignments']);

        $_SESSION['group_assignments'] = $group;
        $settings = new VipsSettings($vipsPlugin->courseID);

        ######################################
        #   get assignments in this course   #
        ######################################

        $sql = "JOIN vips_test ON vips_test.id = vips_assignment.test_id
                WHERE course_id = ? ORDER BY $sort " . ($desc ? 'DESC' : 'ASC');
        $assignments = VipsAssignment::findBySQL($sql, [$vipsPlugin->courseID]);

        if ($group) {
            $blocks   = VipsBlock::findBySQL('course_id = ? ORDER BY name', [$vipsPlugin->courseID]);
            $blocks[] = VipsBlock::build(['name' => _vips('Aufgabenblätter ohne Blockzuordnung')]);

            foreach ($blocks as $block) {
                $assignment_data[$block->id] = [
                    'assignments' => [],
                    'title'       => $block->name,
                    'block'       => $block
                ];
            }

            foreach ($assignments as $assignment) {
                $assignment_data[$assignment->block_id]['assignments'][] = $assignment;
            }
        } else {
            $assignment_data = [
                [
                    'assignments' => [],
                    'title'       => _vips('Noch nicht gestartete Aufgabenblätter')
                ], [
                    'assignments' => [],
                    'title'       => _vips('Laufende Aufgabenblätter')
                ], [
                    'assignments' => [],
                    'title'       => _vips('Beendete Aufgabenblätter')
                ]
            ];

            foreach ($assignments as $assignment) {
                if ($assignment->isFinished()) {
                    $assignment_data[2]['assignments'][] = $assignment;
                } else if ($assignment->isRunning()) {
                    $assignment_data[1]['assignments'][] = $assignment;
                } else {
                    $assignment_data[0]['assignments'][] = $assignment;
                }
            }
        }

        $this->assignment_data = $assignment_data;
        $this->sort = $sort;
        $this->desc = $desc;

        Helpbar::get()->addPlainText('',
            _vips('In Vips können Übungen, Tests und Klausuren online vorbereitet und durchgeführt werden. Sie erhalten ' .
                  'dabei auch eine Übersicht über die Lösungen bzw. Antworten der Studierenden.') . "\n\n" .
            _vips('Auf dieser Seite können Sie Aufgabenblätter in Ihrem Kurs anlegen und verwalten.'));

        $widget = new ActionsWidget();
        $widget->addLink(_vips('Aufgabenblatt erstellen'),
            vips_link('sheets/new_assignment'),
            Icon::create('add'));
        $widget->addLink(_vips('Aufgabenblatt kopieren'),
            vips_url('sheets/copy_assignment_dialog'),
            Icon::create('file+add'), ['data-dialog' => 'size=auto']);
        $widget->addLink(_vips('Aufgabenblatt importieren'),
            vips_url('sheets/import_assignment_dialog'),
            Icon::create('upload'), ['data-dialog' => 'size=auto']);
        $widget->addLink(_vips('Neuen Block erstellen'),
            vips_url('admin/edit_block'),
            Icon::create('folder-empty+add'), ['data-dialog' => 'size=auto']);
        Sidebar::get()->addWidget($widget);

        $widget = new ViewsWidget();
        $widget->addLink(_vips('Gruppiert nach Status'), vips_link('sheets', ['group' => 0]))->setActive(!$group);
        $widget->addLink(_vips('Gruppiert nach Blöcken'), vips_link('sheets', ['group' => 1]))->setActive($group);
        Sidebar::get()->addWidget($widget);

        $widget = new OptionsWidget();
        $widget->addCheckbox(_vips('Vips für Studierende sichtbar'), $settings->visible, vips_link('sheets/toggle_visibility'));
        Sidebar::get()->addWidget($widget);

        if (vips_count_assignments($vipsPlugin->userID)) {
            $widget = new LinksWidget();
            $widget->setTitle(_vips('Links'));
            $widget->addLink(_vips('Meine Aufgaben'), vips_url('pool'), Icon::create('link-intern'));
            Sidebar::get()->addWidget($widget);
        }
    }



    /**
     * Exports the exercise in QTI XML format.
     */
    function export_qti_action()
    {
        vips_require_status('tutor');

        $exercise_id = Request::int('exercise_id');
        $assignment_id = Request::int('assignment_id');
        $assignment = VipsAssignment::find($assignment_id);

        check_exercise_assignment($exercise_id, $assignment);
        check_assignment_access($assignment);

        // construct exercise object
        $exercise = Exercise::find($exercise_id);

        $this->set_content_type('text/xml; charset=UTF-8');
        header('Content-Disposition: attachment; ' . vips_encode_header_parameter('filename', $exercise->title.'.xml'));

        $this->render_template($exercise->getQTITemplate($assignment));
    }

    /**
     * Returns the dialog content to import an assignment from text file.
     */
    function import_assignment_dialog_action()
    {
        vips_require_status('tutor');
    }

    /**
     * Returns the dialog content to copy available assignments.
     */
    function copy_assignment_dialog_action()
    {
        global $vipsPlugin;

        vips_require_status('tutor');

        $this->assignments = get_accessible_tests($vipsPlugin->userID);
    }

    /**
     * Exports all exercises in this assignment in Vips XML format.
     */
    function export_xml_action()
    {
        vips_require_status('tutor');

        $assignment_id = Request::int('assignment_id');
        $assignment = VipsAssignment::find($assignment_id);

        check_assignment_access($assignment);

        $this->set_content_type('text/xml; charset=UTF-8');
        header('Content-Disposition: attachment; ' . vips_encode_header_parameter('filename', $assignment->test->title.'.xml'));

        $this->render_text($assignment->exportXML());
    }

    function get_character_picker_ajax_action()
    {
        $active_character_set = Request::get('active_character_set');
        $character_picker = new CharacterPicker($active_character_set);

        // Expires header uses RFC1123 date format
        $expires = time() + 7 * 60 * 60 * 24;
        header('Expires: '.gmdate('D, d M Y H:i:s O', $expires));
        header('Cache-Control: public');

        $this->render_text($character_picker->render());
    }

    function relay_action($action)
    {
        $params = func_get_args();
        $params[0] = $this;
        $exercise_id = Request::int('exercise_id');
        $exercise = Exercise::find($exercise_id);
        $action = $action . '_action';

        if (method_exists($exercise, $action)) {
            call_user_func_array([$exercise, $action], $params);
        } else {
            throw new InvalidArgumentException(get_class($exercise) . '::' . $action);
        }
    }
}


#####################################
#                                   #
#           Help Methods            #
#                                   #
#####################################

/**
 * Return list of semesters with assignments accessible to the current user.
 * This includes assignments from other courses.
 */
function get_accessible_semesters($user_id)
{
    $db = DBManager::get();

    $semesters = [];

    $sql = "SELECT DISTINCT
                semester_data.*
            FROM
                semester_data,
                vips_assignment,
                seminare,
                seminar_user
            WHERE
                vips_assignment.course_id = seminare.Seminar_id AND
                seminare.start_time = semester_data.beginn AND
                seminar_user.Seminar_id = seminare.Seminar_id AND
                seminar_user.status IN ('dozent', 'tutor') AND
                seminar_user.user_id = '$user_id'
            ORDER BY
                semester_data.beginn DESC";

    $result = $db->query($sql);

    foreach ($result as $row) {
        $semesters[] = Semester::buildExisting($row);
    }

    return $semesters;
}

/**
 * Return list of courses with assignments accessible to the current user.
 * This includes assignments from other courses.
 */
function get_accessible_courses($user_id, $start_time)
{
    $db = DBManager::get();

    $courses = [];

    $sql = "SELECT
                vips_assignment.*
            FROM
                vips_assignment,
                seminare,
                seminar_user
            WHERE
                vips_assignment.course_id = seminare.Seminar_id AND
                seminare.start_time = '$start_time' AND
                seminar_user.Seminar_id = seminare.Seminar_id AND
                seminar_user.status IN ('dozent', 'tutor') AND
                seminar_user.user_id = '$user_id'
            ORDER BY
                seminare.Name, vips_assignment.start";

    $result = $db->query($sql);

    foreach ($result as $row) {
        $assignment = VipsAssignment::buildExisting($row);
        $course = $assignment->course;
        $courses[$course->id]['name'] = $course->getFullName();
        $courses[$course->id]['assignments'][] = $assignment;
    }

    return $courses;
}

/**
 * Return list of all assignments accessible to the current user.
 * This includes assignments from other courses.
 */
function get_accessible_tests($user_id)
{
    $db = DBManager::get();

    $sql = "SELECT
                vips_assignment.*,
                vips_test.title,
                seminare.Name AS course_name,
                semester_data.name AS sem_name
            FROM
                seminare,
                seminar_user,
                semester_data,
                vips_assignment,
                vips_test
            WHERE
                seminare.Seminar_id = seminar_user.Seminar_id AND
                seminar_user.user_id = '$user_id' AND
                seminar_user.status IN ('dozent', 'tutor') AND
                semester_data.beginn = seminare.start_time AND
                vips_assignment.course_id = seminare.Seminar_id AND
                vips_assignment.test_id = vips_test.id
            ORDER BY
                semester_data.beginn DESC, seminare.Name, vips_test.title";

    $result = $db->query($sql);

    return $result->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Return list of exercises matching the search string from all semesters
 * with assignments accessible to teachers in the current course. This
 * includes assignments from other courses.
 */
function get_matching_exercises($user_id, $search)
{
    $db = DBManager::get();

    $sql = "SELECT
                semester_data.name AS sem_name,
                semester_data.beginn,
                seminare.Name AS course_name,
                vips_assignment.id AS assignment_id,
                vips_test.title as test_name,
                vips_exercise_ref.position,
                vips_exercise.*
            FROM
                semester_data,
                vips_assignment,
                vips_test,
                seminare,
                seminar_user,
                vips_exercise_ref,
                vips_exercise
            WHERE
                vips_assignment.test_id = vips_test.id AND
                vips_assignment.course_id = seminare.Seminar_id AND
                seminare.start_time = semester_data.beginn AND
                seminar_user.Seminar_id = seminare.Seminar_id AND
                seminar_user.status IN ('dozent', 'tutor') AND
                seminar_user.user_id = '$user_id' AND
                vips_exercise_ref.test_id = vips_test.id AND
                vips_exercise.id = vips_exercise_ref.exercise_id AND
                (vips_exercise.title LIKE ".$db->quote('%'.$search.'%')." OR
                 vips_exercise.description LIKE ".$db->quote('%'.$search.'%').")
            ORDER BY
                semester_data.beginn DESC, seminare.Name, vips_test.title, vips_exercise.title";

    $result = $db->query($sql);

    return $result->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Copy a list of exercises into the given assignment.
 */
function copy_exercise_list($assignment, $exercise_ids)
{
    global $vipsPlugin;

    $db = DBManager::get();

    // find out max position
    $result = $db->query("SELECT MAX(position) AS pos FROM vips_exercise_ref WHERE test_id = $assignment->test_id");
    $row = $result->fetch();
    $exercise_position = (int) $row['pos'];

    foreach ($exercise_ids as $exercise_id => $copy_assignment_id) {
        $copy_assignment = VipsAssignment::find($copy_assignment_id);
        check_exercise_assignment($exercise_id, $copy_assignment);
        check_copy_assignment_access($copy_assignment);

        $db->exec("INSERT INTO vips_exercise (type, title, description, task_json, user_id, created, options)
                                 SELECT type, title, description, task_json, '$vipsPlugin->userID', NOW(), options
                                 FROM vips_exercise WHERE id = $exercise_id");
        $new_exercise_id = $db->lastInsertId();

        $exercise_ref = VipsExerciseRef::find([$exercise_id, $copy_assignment->test_id]);

        VipsExerciseRef::create([
            'exercise_id' => $new_exercise_id,
            'test_id'     => $assignment->test_id,
            'position'    => ++$exercise_position,
            'points'      => $exercise_ref->points
        ]);
    }
}


/**
 * EXAM
 *
 * Calculates the time at which the student has to finish the exam (starting time of the
 * student PLUS length of the exam)
 * If the global endzeitpunkt of the assignment is closer, then this is the Abgabezeitpunkt
 * @return: Abgabezeitpunkt (timestamp)
 */
function getAbgabezeitpunkt($user_id, $assignment)
{
    $length = $assignment->options['duration'];
    $end    = strtotime($assignment->end);

    $assignment_attempt = $assignment->getAssignmentAttempt($user_id);

    if ($assignment_attempt) {
        $user_start = strtotime($assignment_attempt->start);
    } else {
        $user_start = time();
    }

    return min($end, $user_start + $length * 60);
}

/**
 * EXAM
 *
 * Calculates the remaining minutes for a student
 *
 * @return: remaining minutes of the exam
 *
 */
function getRemainingMinutes($user_id, $assignment)
{
    $user_end = getAbgabezeitpunkt($user_id, $assignment);

    return ceil($user_end - time() / 60);
}
