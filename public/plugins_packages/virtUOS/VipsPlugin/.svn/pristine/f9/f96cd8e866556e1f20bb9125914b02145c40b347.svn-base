<?php
/*
 * vips_solutions.inc.php - Vips plugin for Stud.IP
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
 * Handles student solutions and corrections
 *
 * @version 2009-06-29
 */

class SolutionsController extends StudipController
{
    /**
     * Return the default action and arguments
     *
     * @return an array containing the action, an array of args and the format
     */
    function default_action_and_args()
    {
        return ['assignments', [], NULL];
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

        vips_require_status('autor');
        Navigation::activateItem('/course/vips/solutions');
        PageLayout::setHelpKeyword(vips_text_encode('Vips.Lösung'));
        PageLayout::setTitle(PageLayout::getTitle() . ' - ' . _vips('Ergebnisse'));
    }

    /**
     * Displays all exercise sheets.
     * Lecturer can select what sheet to correct.
     */
    function assignments_action()
    {
        global $vipsPlugin;

        $sort = Request::option('sort', 'start');
        $desc = Request::int('desc');

        $this->sort      = $sort;
        $this->desc      = $desc;
        $this->test_data = get_assignments_data($vipsPlugin->courseID, $vipsPlugin->userID, $sort, $desc);
        $this->blocks    = VipsBlock::findBySQL('course_id = ? ORDER BY name', [$vipsPlugin->courseID]);
        $this->blocks[]  = VipsBlock::build(['name' => _vips('Aufgabenblätter ohne Blockzuordnung')]);

        $settings = VipsSettings::find($vipsPlugin->courseID);
        $this->has_grades = isset($settings->grades);

        // display course results if grades are defined for this course
        if (!vips_has_status('tutor') && $this->has_grades) {
            $assignments = VipsAssignment::findBySQL('course_id = ?', [$vipsPlugin->courseID]);
            $show_overview = true;

            // find unreleased or unfinished assignments
            foreach ($assignments as $assignment) {
                $end     = strtotime($assignment->end);  // Unix timestamp
                $options = $assignment->options;

                if ($end > time() || $options['released'] == 0) {
                    $show_overview = false;
                }
            }

            // if all assignments are finished and released
            if ($show_overview) {
                $this->overview_data = participants_overview_data($vipsPlugin->courseID, $vipsPlugin->userID);
            }
        }

        if (vips_has_status('tutor')) {
            Helpbar::get()->addPlainText('',
                _vips('Hier finden Sie eine Übersicht über den Korrekturstatus Ihrer Aufgabenblätter und können Aufgaben korrigieren. ' .
                      'Außerdem können Sie hier die Einstellungen für die Notenberechnung in Ihrem Kurs vornehmen.'));

            $widget = new ActionsWidget();
            $widget->addLink(_vips('Notenverteilung festlegen'), vips_url('admin/edit_grades'), Icon::create('graph'), ['data-dialog' => 'size=auto']);
            Sidebar::get()->addWidget($widget);

            $widget = new ViewsWidget();
            $widget->addLink(_vips('Ergebnisse'), vips_link('solutions'))->setActive(true);
            $widget->addLink(_vips('Punkteübersicht'), vips_link('solutions/participants_overview', ['display' => 'points']));
            $widget->addLink(_vips('Notenübersicht'), vips_link('solutions/participants_overview', ['display' => 'weighting']));
            $widget->addLink(_vips('Statistik'), vips_link('solutions/statistics'));
            Sidebar::get()->addWidget($widget);
        }
    }

    /**
     * Changes which correction information is released to the student (either
     * nothing or only the points or points and correction).
     */
    function change_released_action()
    {
        vips_require_status('tutor');

        $assignment_id = Request::int('assignment_id');
        $assignment = VipsAssignment::find($assignment_id);

        check_assignment_access($assignment);

        $assignment->options['released'] = Request::int('released');  // 0, 1 or 2
        $assignment->store();

        $this->redirect(vips_url('solutions/assignment_solutions', ['assignment_id' => $assignment_id]));
    }

    /**
     * Shows solution points for each student/group with a link to view solution and correct it.
     */
    function assignment_solutions_action()
    {
        vips_require_status('tutor');

        $assignment_id = Request::int('assignment_id');
        $assignment    = VipsAssignment::find($assignment_id);
        $format        = Request::option('format');

        check_assignment_access($assignment);

        $view      = Request::option('view');
        $expand    = Request::get('expand');

        // fetch info about assignment
        $test_type  = $assignment->type;
        $test_title = $assignment->test->title;
        $end        = strtotime($assignment->end);
        $options    = $assignment->options;
        $duration   = $options['duration'];
        $released   = $options['released'];



        // fetch solvers, exercises and solutions //

        $arrays    = get_solutions($assignment_id, $view);
        $solvers   = is_array($arrays['solvers']) ? $arrays['solvers'] : [];
        $exercises = is_array($arrays['exercises']) ? $arrays['exercises'] : [];
        $solutions = is_array($arrays['solutions']) ? $arrays['solutions'] : [];



        // remove users which are not shown //

        if ($test_type === 'exam') {
            $hidden_solvers = $solvers;
            $solvers        = [];
            $started        = [];

            // find all user ids which have an entry in vips_assignment_attempt
            foreach ($assignment->assignment_attempts as $attempt) {
                $start = strtotime($attempt->start);
                $user_end = min($end, $start + $duration * 60);
                $remaining_time = ceil($user_end - time() / 60);

                $started[$attempt->user_id] = [
                    'start'     => $start,
                    'remaining' => $remaining_time,
                    'ip'        => $attempt->ip_address
                ];
            }

            foreach ($hidden_solvers as $solver) {
                $user_id = $solver['id'];

                if (isset($started[$user_id])) {
                    $remaining = $started[$user_id]['remaining'];

                    if ($view === 'working' && $remaining > 0 || $view == '' && $remaining <= 0) {
                        // working or finished
                        $solvers[$user_id] = $hidden_solvers[$user_id];
                        $solvers[$user_id]['running_info'] = $started[$user_id];
                        unset($hidden_solvers[$user_id]);
                    }
                } else if ($view === 'pending') {  // not yet started
                    // add all solvers which DON'T have started to solvers array
                    $solvers[$user_id] = $hidden_solvers[$user_id];
                    unset($hidden_solvers[$user_id]);
                }
            }
        }



        // overall max points //

        $overall_max_points = 0;
        if (is_array($exercises)) {
            foreach ($exercises as $exercise) {
                $overall_max_points += $exercise['points'];
            }
        }



        // overall uncorrected solutions and first uncorrected solution //

        $overall_uncorrected_solutions = 0;
        $first_uncorrected_solution    = null;

        // each solver (tutors and lecturers won't appear) and his solutions
        foreach (array_keys($solvers) as $solver_id) {
            if (is_array($solutions[$solver_id])) {
                foreach ($solutions[$solver_id] as $solution) {
                    if (!$solution['corrected']) {
                        $overall_uncorrected_solutions += 1;

                        if (!isset($first_uncorrected_solution)) {
                            $solver_type       = $solvers[$solver_id]['type'];
                            $exercise_id       = $solution['exercise_id'];
                            $exercise_position = $exercises[$exercise_id]['position'];

                            $first_uncorrected_solution = [
                                'solver_id'         => $solver_id,
                                'solver_type'       => $solver_type,
                                'exercise_id'       => $exercise_id,
                                'exercise_position' => $exercise_position
                            ];
                        }
                    }
                }
            }
        }

        $this->test_type                     = $test_type;
        $this->assignment_id                 = $assignment_id;
        $this->test_title                    = $test_title;
        $this->view                          = $view;
        $this->expand                        = $expand;
        $this->released                      = $released;
        $this->solutions                     = $solutions;
        $this->solvers                       = $solvers;
        $this->exercises                     = $exercises;
        $this->overall_max_points            = $overall_max_points;
        $this->overall_uncorrected_solutions = $overall_uncorrected_solutions;
        $this->first_uncorrected_solution    = $first_uncorrected_solution;

        if ($format == 'csv') {
            $this->set_content_type('text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; ' . vips_encode_header_parameter('filename', $test_title.'.csv'));

            $this->render_template('solutions/assignment_solutions_csv');
        } else {
            Helpbar::get()->addPlainText('',
                _vips('In dieser Übersicht können Sie sich anzeigen lassen, welche Teilnehmer Lösungen abgegeben haben, und diese Lösungen korrigieren und freigeben.'));

            $widget = new ActionsWidget();
            $widget->addLink(_vips('Aufgabenblatt bearbeiten'),
                vips_link('sheets/edit_assignment', ['assignment_id' => $assignment_id]),
                Icon::create('edit'));
            $widget->addLink(_vips('Aufgabenblatt drucken'),
                vips_link('export/print_student_overview', ['assignment_id' => $assignment_id]),
                Icon::create('print'));
            $widget->addLink(_vips('Autokorrektur starten'),
                vips_link('solutions/autocorrect_solutions', compact('assignment_id', 'expand', 'view')),
                Icon::create('accept'));
            $widget->addLink(_vips('Ergebnisse exportieren'),
                vips_link('solutions/assignment_solutions', ['assignment_id' => $assignment_id, 'format' => 'csv']),
                Icon::create('download'));
            Sidebar::get()->addWidget($widget);

            $widget = new OptionsWidget();
            $widget->setTitle(_vips('Freigabe für Studenten'));
            $widget->addRadioButton(_vips('nichts'),
                vips_url('solutions/change_released', ['assignment_id' => $assignment_id, 'released' => 0]),
                $released == 0);
            $widget->addRadioButton(_vips('Punkte und Kommentare'),
                vips_url('solutions/change_released', ['assignment_id' => $assignment_id, 'released' => 1]),
                $released == 1);
            $widget->addRadioButton(_vips('Punkte, Lösung und Korrekturen'),
                vips_url('solutions/change_released', ['assignment_id' => $assignment_id, 'released' => 2]),
                $released == 2);
            Sidebar::get()->addWidget($widget);

            $widget = new SidebarWidget();
            $widget->setTitle(_vips('Legende'));
            $widget->addElement(new WidgetElement(
                sprintf(_vips('%sBlau dargestellte Aufgaben%s wurden automatisch und sicher korrigiert.'), '<span class="solution-autocorrected">', '</span>') . '<br>'));
            $widget->addElement(new WidgetElement(
                sprintf(_vips('%sGrün dargestellte Aufgaben%s wurden von Hand korrigiert.'), '<span class="solution-corrected">', '</span>') . '<br>'));
            $widget->addElement(new WidgetElement(
                sprintf(_vips('%sRot dargestellte Aufgaben%s wurden noch nicht fertig korrigiert.'), '<span class="solution-uncorrected">', '</span>') . '<br>'));
            $widget->addElement(new WidgetElement(
                sprintf(_vips('%sAusgegraute Aufgaben%s wurden vom Studenten nicht bearbeitet.'), '<span class="solution-none">', '</span>') . '<br>'));
            Sidebar::get()->addWidget($widget);
        }
    }



    /******************************************************************************/
    /*
    /* A U T O K O R R E K T U R
    /*
    /******************************************************************************/



    /**
     * Deletes all solution-points, where the solutions are automatically corrected
     */
    function autocorrect_solutions_action()
    {
        vips_require_status('tutor');

        $assignment_id = Request::int('assignment_id');
        $assignment    = VipsAssignment::find($assignment_id);
        $view          = Request::option('view');
        $expand        = Request::get('expand');

        check_assignment_access($assignment);

        $corrected_solutions = 0;

        // select all solutions not manually corrected
        $solutions = $assignment->solutions->findBy('corrector_id', NULL);

        foreach ($solutions as $solution) {
            $assignment->correctSolution($solution);
            $solution->store();

            if ($solution->corrected) {
                ++$corrected_solutions;
            }
        }

        $message = sprintf(n_vips('Es wurde %d Lösung korrigiert.', 'Es wurden %d Lösungen korrigiert.', $corrected_solutions), $corrected_solutions);
        PageLayout::postSuccess($message);
        $this->redirect(vips_url('solutions/assignment_solutions', ['assignment_id' => $assignment_id, 'view' => $view, 'expand' => $expand]));
    }



    /**
     * Invokes [xx]_exercise.php and gets formular field that allows lecturer
     * to correct the student's solution.
     */
    function edit_solution_action()
    {
        global $vipsPlugin;

        $exercise_id   = Request::int('exercise_id');
        $assignment_id = Request::int('assignment_id');
        $assignment    = VipsAssignment::find($assignment_id);

        check_exercise_assignment($exercise_id, $assignment);
        check_assignment_access($assignment);

        $solver_id     = Request::option('solver_id');
        $single_solver = Request::int('single_solver');
        $view          = Request::option('view');

        // restrict access for students and prevent url hacking
        if (!vips_has_status('tutor')) {
            if (strtotime($assignment->end) > time() || $assignment->options['released'] != 2) {
                // the assignment is not finished or not yet released
                PageLayout::postError(_vips('Die Korrekturen dürfen erst nach Ablaufen des Aufgabenblatts betrachtet werden'));
                $this->redirect(vips_url('solutions/student_assignment_solutions', ['assignment_id' => $assignment_id]));
                return;
            }

            $single_solver = 1;                    // reset single_solver
            $solver_id     = $vipsPlugin->userID;  // don't rely on passed solver_id

            // was user in a group when the assignment ended?
            $group = $assignment->getUserGroup($solver_id);
            if ($group) {
                $single_solver = 0;
                $solver_id     = $group->id;
            }
        }

        $solver_name = get_solver_fullname($solver_id, $single_solver);

        if (!isset($solver_name)) {
            $solver_name = $solver_id;
        }

        // fetch solvers, exercises and solutions //

        // $solvers
        // $exercises
        // $solutions
        $arrays = get_solutions($assignment_id, $view);

        if (is_array($arrays)) {
            extract($arrays);
        }

        $exercise = $exercises[$exercise_id];              // this exercise
        $solution = $solutions[$solver_id][$exercise_id];  // this solution

        if (!isset($solution)) {
            throw new InvalidArgumentException(sprintf(_vips('Keine abgegebene Lösung für Aufgabe %d gefunden.'), $exercise_id));
        } else if (!vips_has_status('tutor') && !$solution['corrected']) {
            throw new InvalidArgumentException(sprintf(_vips('Für Aufgabe %d liegt noch keine Korrektur vor.'), $exercise_id));
        }

        $exercise_name       = $exercise['title'];
        $exercise_type       = $exercise['type'];
        $number_of_exercises = count($exercises);
        $solution_id         = $solution['id'];
        $reached_points      = $solution['points'];
        $max_points          = $exercise['points'];
        $corrector_comment   = $solution['corrector_comment'];
        $exercise_position   = $exercise['position'];

        if (isset($solution['corrector_id'])) {
            $corrector_user_name = get_username($solution['corrector_id']);
            $corrector_full_name = get_fullname($solution['corrector_id']);
        }

        // previous and next solver, exercise and uncorrected exercise //

        $next_solver               = null;
        $next_exercise             = null;
        $next_uncorrected_exercise = null;
        $before_current            = true;  // before current solver + current exercise

        foreach ($solvers as $solver) {  // each solver
            foreach ($exercises as $exercise) {  // each exercise
                $solution = $solutions[$solver['id']][$exercise['id']];  // may be null

                // current solver and current exercise
                if ($solver['id'] == $solver_id && $exercise['id'] == $exercise_id) {
                    $before_current = false;
                    continue;
                }

                // previous solver (same exercise)
                if ($solver['id'] != $solver_id && $exercise['id'] == $exercise_id && $before_current) {
                    if (isset($solution)) {
                        $prev_solver = $solver;
                    } elseif (!isset($prev_solver)) {
                        $prev_solver = [];  // meaning: prev. solver exists, but w/o solution
                    }
                }

                // next solver (same exercise)
                if ($solver['id'] != $solver_id && $exercise['id'] == $exercise_id && !$before_current && empty($next_solver)) {
                    if (isset($solution)) {
                        $next_solver = $solver;
                    } elseif (!isset($next_solver)) {
                        $next_solver = [];  // next solver exists, but w/o solution
                    }
                }

                // previous exercise (same solver)
                if ($solver['id'] == $solver_id && $exercise['id'] != $exercise_id && $before_current && isset($solution)) {
                    if ($solution['corrected'] || vips_has_status('tutor')) {
                        $prev_exercise = $exercise;
                    }
                }

                // next exercise (same solver)
                if ($solver['id'] == $solver_id && $exercise['id'] != $exercise_id && !$before_current && !isset($next_exercise) && isset($solution)) {
                    if ($solution['corrected'] || vips_has_status('tutor')) {
                        $next_exercise = $exercise;
                    }
                }

                // previous uncorrected exercise
                if (isset($solution) && !$solution['corrected'] && $before_current) {
                    $prev_uncorrected_exercise = [
                        'id'          => $exercise['id'],
                        'position'    => $exercise['position'],
                        'solver_id'   => $solver['id'],
                        'solver_type' => $solver['type']
                    ];
                }

                // next uncorrected exercise
                if (isset($solution) && !$solution['corrected'] && !$before_current && !isset($next_uncorrected_exercise)) {
                    $next_uncorrected_exercise = [
                        'id'          => $exercise['id'],
                        'position'    => $exercise['position'],
                        'solver_id'   => $solver['id'],
                        'solver_type' => $solver['type']
                    ];
                }

                // break condition
                if (isset($next_uncorrected_exercise) && !empty($next_solver)) {
                    break 2;
                }
            }  // end: each exercise
        }  // end: each solver



        ###################################
        # get user solution if applicable #
        ###################################

        if ($single_solver) {
            $solution_obj = $assignment->getUserSolution($solver_id, $exercise_id);
        } else {
            $solution_obj = $assignment->getGroupSolution($solver_id, $exercise_id);
        }

        $exercise_obj = Exercise::find($exercise_id);
        $exercise_template = $exercise_obj->getCorrectionTemplate($solution_obj);
        $exercise_template->korrektur = true;

        ##############################
        #   set template variables   #
        ##############################

        $this->exercise                  = $exercise_obj;
        $this->exercise_position         = $exercise_position;
        $this->exercise_id               = $exercise_id;
        $this->exercise_name             = $exercise_name;
        $this->exercise_template         = $exercise_template;
        $this->assignment                = $assignment;
        $this->assignment_type           = $assignment->type;
        $this->assignment_id             = $assignment_id;
        $this->assignment_title          = $assignment->test->title;
        $this->number_of_exercises       = $number_of_exercises;
        $this->solver_id                 = $solver_id;
        $this->single_solver             = $single_solver;
        $this->solver_name               = $solver_name;
        $this->solution                  = $solution_obj;
        $this->solution_id               = $solution_id;
        $this->reached_points            = $reached_points;
        $this->max_points                = $max_points;
        $this->corrector_user_name       = $corrector_user_name;
        $this->corrector_full_name       = $corrector_full_name;
        $this->corrector_comment         = $corrector_comment;
        $this->prev_solver               = $prev_solver;
        $this->prev_exercise             = $prev_exercise;
        $this->next_solver               = $next_solver;
        $this->next_exercise             = $next_exercise;
        $this->view                      = $view;

        if (vips_has_status('tutor')) {
            Helpbar::get()->addPlainText('',
                _vips('Sie können hier die Ergebnisse der Autokorrektur ansehen und Aufgaben manuell korrigieren.'));

            $widget = new ActionsWidget();
            $widget->addLink(_vips('Diese Aufgabe bearbeiten'),
                vips_link('sheets/edit_exercise', compact('assignment_id', 'exercise_id')),
                Icon::create('edit'));
            $widget->addLink(_vips('Zeichenwähler öffnen'), '#',
                Icon::create('comment'), ['class' => 'open_character_picker']);
            Sidebar::get()->addWidget($widget);

            $widget = new LinksWidget();
            $widget->setTitle(_vips('Links'));
            if (isset($prev_uncorrected_exercise)) {
                $widget->addLink(_vips('vorige unkorrigierte Aufgabe'),
                    vips_link('solutions/edit_solution', ['assignment_id' => $assignment_id, 'exercise_id' => $prev_uncorrected_exercise['id'],
                              'solver_id' => $prev_uncorrected_exercise['solver_id'],
                              'single_solver' => $prev_uncorrected_exercise['solver_type'] == 'group' ? 0 : 1, 'view' => $view]),
                    Icon::create('arr_1left'));
            }
            if (isset($next_uncorrected_exercise)) {
                $widget->addLink(_vips('nächste unkorrigierte Aufgabe'),
                    vips_link('solutions/edit_solution', ['assignment_id' => $assignment_id, 'exercise_id' => $next_uncorrected_exercise['id'],
                              'solver_id' => $next_uncorrected_exercise['solver_id'],
                              'single_solver' => $next_uncorrected_exercise['solver_type'] == 'group' ? 0 : 1, 'view' => $view]),
                    Icon::create('arr_1right'));
            }
            Sidebar::get()->addWidget($widget);
        }
    }

    /**
     * Stores the lecturer comment and the corrected points for a solution.
     */
    function store_correction_action()
    {
        global $vipsPlugin;

        vips_require_status('tutor');

        $solution_id       = Request::int('solution_id');
        $single_solver     = Request::int('single_solver');
        $solver_id         = Request::option('solver_id');
        $view              = Request::option('view');

        $corrector_comment = trim(Request::get('corrector_comment'));
        $corrector_comment = Studip\Markup::purifyHtml($corrector_comment);
        $reached_points    = Request::float('reached_points');
        $max_points        = Request::float('max_points');

        $solution = VipsSolution::find($solution_id);
        $exercise_id = $solution->exercise_id;
        $assignment_id = $solution->assignment_id;

        check_assignment_access($solution->assignment);

        // let exercise class handle special controls added to the form
        $exercise = Exercise::find($exercise_id);
        $exercise->correctSolutionAction($this, $solution);

        if (Request::submitted('store_solution')) {
            // process lecturer's input
            $reached_points = round_to_half_point($reached_points);

            if ($reached_points > $max_points) {
                PageLayout::postInfo(sprintf(_vips('Sie haben Bonuspunkte vergeben (%s von %s).'), $reached_points, $max_points));
            } else if ($reached_points < 0) {
                PageLayout::postInfo(sprintf(_vips('Sie haben eine negative Punktzahl eingegeben (%s von %s).'), $reached_points, $max_points));
            }

            $solution->corrected = 1;
            $solution->points = $reached_points;
            $solution->corrector_id = $vipsPlugin->userID;
            $solution->corrector_comment = $corrector_comment;
            $solution->correction_time = date('Y-m-d H:i:s');
            $solution->store();

            PageLayout::postSuccess(_vips('Ihre Korrektur wurde gespeichert.'));
        }

        // show exercise and correction form again
        $this->redirect(vips_url('solutions/edit_solution', compact('exercise_id', 'assignment_id', 'solver_id', 'single_solver', 'view')));
    }



    /**
     * Shows all corrected exercises of an exercise sheet that are set to
     * options['released'] == 2 (means: show points and solution).
     */
    function student_assignment_solutions_action()
    {
        global $vipsPlugin;

        $assignment_id = Request::int('assignment_id');
        $assignment = VipsAssignment::find($assignment_id);

        check_assignment_access($assignment);

        $released = $assignment->options['released'];

        // Security check -- is assignment really accessible for students?
        if ($released == 0) {
            PageLayout::postError(_vips('Die Korrekturen wurden noch nicht freigegeben.'));
            $this->redirect(vips_url('solutions'));
            return;
        }

        $sum_reached_points = 0;
        $sum_max_points     = 0;

        $exercise_array = [];

        // for each exercise
        foreach ($assignment->test->exercise_refs as $exercise_ref) {
            $exercise          = $exercise_ref->exercise;
            $max_points        = (float) $exercise_ref->points;
            $sum_max_points   += $max_points;

            $solution = $assignment->getSolution($vipsPlugin->userID, $exercise->id);

            if ($solution) {
                $reached_points      = (float) $solution->points;
                $sum_reached_points += $reached_points;
            }

            $exercise_array[] = [
                'exercise_ref'        => $exercise_ref,
                'exercise'            => $exercise,
                'solution'            => $solution
            ];
        }

        if ($sum_reached_points < 0) {
            $sum_reached_points = 0;
        }

        $this->assignment         = $assignment;
        $this->exercise_array     = $exercise_array;
        $this->sum_reached_points = $sum_reached_points;
        $this->sum_max_points     = $sum_max_points;
        $this->released           = $released;

        Helpbar::get()->addPlainText('',
            _vips('Sie können hier die Ergebnisse bzw. die Korrekturen ihrer Aufgaben ansehen.'));

        $widget = new ActionsWidget();
        $widget->addLink(_vips('Aufgabenblatt drucken'),
            vips_link('export/print_assignment', ['assignment_id' => $assignment_id]),
            Icon::create('print'));
        Sidebar::get()->addWidget($widget);
    }



    /**
     * Displays all course participants and all their results (reached points,
     * percent, weighted percent) for all practices, blocks and exams plus
     * their final grade.
     */
    function participants_overview_action()
    {
        global $vipsPlugin;

        vips_require_status('tutor');

        $display = Request::option('display', 'points');
        $sort    = Request::option('sort', 'name');
        $desc    = Request::int('desc');
        $format  = Request::option('format');

        $attributes = participants_overview_data($vipsPlugin->courseID, NULL, $display, $sort, $desc);

        foreach ($attributes as $key => $value) {
            $this->$key = $value;
        }

        if ($format == 'csv') {
            $this->set_content_type('text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; ' . vips_encode_header_parameter('filename', 'Notenliste.csv'));

            $this->render_template('solutions/participants_overview_csv');
        } else {
            Helpbar::get()->addPlainText('',
                _vips('Diese Seite gibt einen Überblick über die von allen Teilnehmern erreichten Punkte und ggf. Noten.'));

            $widget = new ViewsWidget();
            $widget->addLink(_vips('Ergebnisse'), vips_link('solutions'));
            $widget->addLink(_vips('Punkteübersicht'), vips_link('solutions/participants_overview', ['display' => 'points']))->setActive($display == 'points');
            $widget->addLink(_vips('Notenübersicht'), vips_link('solutions/participants_overview', ['display' => 'weighting']))->setActive($display == 'weighting');
            $widget->addLink(_vips('Statistik'), vips_link('solutions/statistics'));
            Sidebar::get()->addWidget($widget);

            $widget = new ExportWidget();
            $widget->addLink(_vips('Liste im CSV-Format exportieren'),
                vips_link('solutions/participants_overview', ['display' => $display, 'sort' => $sort, 'format' => 'csv']),
                Icon::create('download'));
            Sidebar::get()->addWidget($widget);
        }
    }

    function statistics_action()
    {
        global $vipsPlugin;

        vips_require_status('tutor');

        $db = DBManager::get();

        $format = Request::option('format');
        $assignments = [];

        $query = "SELECT vips_assignment.*, vips_test.title FROM vips_assignment, vips_test
                  WHERE vips_assignment.course_id = '$vipsPlugin->courseID' AND vips_assignment.type != 'selftest'
                    AND vips_assignment.test_id = vips_test.id
                  ORDER BY vips_assignment.start";
        $result = $db->query($query);

        foreach ($result as $row) {
            $assignment_id   = (int) $row['id'];
            $course_id       = $row['course_id'];
            $test_id         = $row['test_id'];
            $test_title      = $row['title'];
            $test_type       = $row['type'];
            $test_points     = 0;
            $test_average    = 0;
            $exercises       = [];

            $query = "SELECT * FROM vips_exercise
                      JOIN vips_exercise_ref ON vips_exercise.id = vips_exercise_ref.exercise_id
                      WHERE test_id = $test_id ORDER BY position";

            $exercise_result = $db->query($query);
            $num_exercises = $exercise_result->rowCount();

            foreach ($exercise_result as $exercise_row) {
                $exercise_id      = (int) $exercise_row['id'];
                $exercise_name    = $exercise_row['title'];
                $exercise_pos     = (int) $exercise_row['position'];
                $exercise_points  = (float) $exercise_row['points'];
                $exercise_average = 0;
                $exercise_correct = 0;
                $exercise_items   = [];
                $exercise_items_c = [];

                $exercise = Exercise::find($exercise_id);

                $query = "SELECT vips_solution.* FROM vips_solution
                          LEFT JOIN seminar_user USING(user_id)
                          WHERE vips_solution.assignment_id = $assignment_id
                            AND vips_solution.exercise_id = $exercise_id
                            AND seminar_user.Seminar_id = '$course_id'
                            AND seminar_user.status = 'autor'";

                $solution_result = $db->query($query);
                $num_solutions = $solution_result->rowCount();
                $exercise_items = [];

                foreach ($solution_result as $solution_row) {
                    $solution        = VipsSolution::buildExisting($solution_row);
                    $solution_points = (float) $solution->points;

                    // array of ('points' => float, 'safe' => boolean).
                    $items = $exercise->evaluateItems($solution);

                    foreach ($items as $index => $item) {
                        $exercise_items[$index] += $item['points'] / $num_solutions;

                        if ($item['points'] == 1) {
                            $exercise_items_c[$index] += 1 / $num_solutions;
                        }
                    }

                    if ($solution_points >= $exercise_points) {
                        ++$exercise_correct;
                    }

                    $exercise_average += $solution_points / $num_solutions;
                }

                $exercises[] = [
                    'id'       => $exercise_id,
                    'name'     => $exercise_name,
                    'position' => $exercise_pos,
                    'points'   => $exercise_points,
                    'average'  => $exercise_average,
                    'correct'  => $exercise_correct / max($num_solutions, 1),
                    'items'    => $exercise_items,
                    'items_c'  => $exercise_items_c
                ];

                $test_points += $exercise_points;
                $test_average += $exercise_average;
            }

            $assignments[] = [
                'id'        => $assignment_id,
                'title'     => $test_title,
                'type'      => $test_type,
                'points'    => $test_points,
                'average'   => $test_average,
                'exercises' => $exercises
            ];
        }

        $this->assignments = $assignments;

        if ($format == 'csv') {
            $this->set_content_type('text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; ' . vips_encode_header_parameter('filename', _vips('Statistik.csv')));

            $this->render_template('solutions/statistics_csv');
        } else {
            Helpbar::get()->addPlainText('',
                _vips('Diese Seite gibt einen Überblick über die im Durchschnitt von allen Teilnehmern erreichten Punkte ' .
                      'sowie den Prozentsatz der vollständig korrekten Lösungen.'));

            $widget = new ViewsWidget();
            $widget->addLink(_vips('Ergebnisse'), vips_link('solutions'));
            $widget->addLink(_vips('Punkteübersicht'), vips_link('solutions/participants_overview', ['display' => 'points']));
            $widget->addLink(_vips('Notenübersicht'), vips_link('solutions/participants_overview', ['display' => 'weighting']));
            $widget->addLink(_vips('Statistik'), vips_link('solutions/statistics'))->setActive(true);
            Sidebar::get()->addWidget($widget);

            $widget = new ExportWidget();
            $widget->addLink(_vips('Liste im CSV-Format exportieren'),
                vips_link('solutions/statistics', ['format' => 'csv']),
                Icon::create('download'));
            Sidebar::get()->addWidget($widget);
        }
    }
}

/**
 * Shows all exercise sheets belonging to course.
 */
function get_assignments_data($course_id, $user_id, $sort, $desc)
{
    $m_sum_max_points   = 0; // holds the maximum points of all exercise sheets
    $sum_reached_points = 0; // holds the reached points of all assignments

    // find all assignments
    if (vips_has_status('tutor', $course_id)) {
        $assignments = VipsAssignment::findBySQL('course_id = ?', [$course_id]);
    } else {
        $assignments = VipsAssignment::findBySQL('course_id = ? AND end < NOW()', [$course_id]);
    }

    usort($assignments, function($a, $b) use ($sort) {
        if ($sort === 'title') {
            return strcoll($a->test->title, $b->test->title);
        } else if ($sort === 'start') {
            return strcmp($a->start, $b->start);
        } else {
            return strcmp($a->end, $b->end);
        }
    });

    if ($desc) {
        $assignments = array_reverse($assignments);
    }

    foreach ($assignments as $assignment) {
        $max_points = $assignment->test->getTotalPoints();
        $m_sum_max_points += $max_points;

        // for students, get reached points
        if (!vips_has_status('tutor', $course_id)) {
            if ($assignment->block_id && !$assignment->block->visible) {
                continue;
            } else if ($assignment->options['released'] > 0) {
                $reached_points = array_sum(vips_get_reached_points($user_id, $assignment->id));
                $sum_reached_points += $reached_points;
            } else {
                $reached_points = 0;
            }
        }

        // count uncorrected solutions
        $uncorrected_solutions = vips_count_uncorrected_solutions($assignment->id);

        $assignments_array[] = [
            'assignment'            => $assignment,
            'type'                  => $assignment->type,
            'title'                 => $assignment->test->title,
            'start'                 => $assignment->start,
            'end'                   => $assignment->end,
            'id'                    => $assignment->id,
            'released'              => $assignment->options['released'],
            'reached_points'        => $reached_points,
            'max_points'            => $max_points,
            'uncorrected_solutions' => $uncorrected_solutions
        ];
    }

    return [
        'assignments'        => $assignments_array,
        'sum_reached_points' => $sum_reached_points,
        'sum_max_points'     => $m_sum_max_points
    ];
}

function participants_overview_data($course_id, $param_user_id, $display = NULL, $sort = NULL, $desc = NULL)
{
    $db = DBManager::get();

    // fetch all course participants //

    $participants = [];

    $sql = "SELECT user_id
        FROM seminar_user
        WHERE Seminar_id = '$course_id'
          AND status NOT IN ('dozent', 'tutor')";
    $result = $db->query($sql);

    foreach ($result as $row) {
        $participants[$row['user_id']] = [];
    }



    // fetch all assignments with maximum points, assigned to blocks //
    // (if appropriate), and with weighting (if appropriate)         //

    $sql = "SELECT vips_assignment.id,
            vips_assignment.type,
            vips_test.title,
            vips_assignment.end,
            vips_assignment.weight,
            vips_assignment.options,
            vips_assignment.block_id,
            SUM(vips_exercise_ref.points) AS points,
            vips_block.name AS block_name,
            vips_block.weight AS block_weight
        FROM vips_assignment
             JOIN vips_test ON vips_test.id = vips_assignment.test_id
        LEFT JOIN vips_exercise_ref
               ON vips_exercise_ref.test_id = vips_test.id
        LEFT JOIN vips_block
               ON vips_block.id = vips_assignment.block_id
        WHERE vips_assignment.course_id = '$course_id'
          AND vips_assignment.type != 'selftest'
        GROUP BY vips_assignment.id
        ORDER BY vips_assignment.type DESC,
            vips_block.name,
            vips_assignment.start";
    $result = $db->query($sql);

    // the result is ordered by
    //  * practices
    //  * practice blocks
    //  * exams
    // with ascending start points in each category

    $assignments    = [];
    $items          = [
        'practices' => [],
        'blocks'    => [],
        'exams'     => []
    ];
    $compute_grade  = true;  // needed in solutions_student_grade template

    // each assignment
    foreach ($result as $row) {
        $assignment_id = (int) $row['id'];
        $test_type     = $row['type'];
        $test_title    = $row['title'];
        $points        = (float) $row['points'];   // 0 if NULL
        $block_id      = $row['block_id'];         // may be NULL
        $block_name    = $row['block_name'];
        $weighting     = (float) $row['weight'];

        if (isset($block_id)) {
            $category = 'blocks';

            // store assignment
            $assignments[$assignment_id] = [
                'category' => $category,
                'item_id'  => $block_id
            ];

            // store item
            if (!isset($items[$category][$block_id])) {
                $weighting = (float) $row['block_weight'];

                // initialise block
                $items[$category][$block_id] = [
                    'id'        => $block_id,
                    'name'      => $block_name,
                    'tooltip'   => $block_name.': '.$test_title,
                    'points'    => 0,
                    'weighting' => $weighting
                ];

                // increase overall weighting (just once for each block!)
                $overall_weighting += $weighting;
            } else {
                // extend tooltip for existing block
                $items[$category][$block_id]['tooltip'] .= ', '.$test_title;
            }

            // increase block's points (for each assignment)
            $items[$category][$block_id]['points'] += $points;

            // increase overall points (for each assignment)
            $overall_points += $points;
        } else {
            $category = $test_type == 'practice' ? 'practices' : 'exams';

            // store assignment
            $assignments[$assignment_id] = [
                'category' => $category,
                'item_id'  => $assignment_id
            ];

            // store item
            $items[$category][$assignment_id] = [
                'id'        => $assignment_id,
                'name'      => $test_title,
                'tooltip'   => $test_title,
                'points'    => $points,
                'weighting' => $weighting
            ];

            // increase overall points and weighting
            $overall_points    += $points;
            $overall_weighting += $weighting;
        }
    }

    // overall sum column
    $overall = [
        'points'    => $overall_points,
        'weighting' => $overall_weighting
    ];

    if ($overall['weighting'] == 0 && count($assignments) > 0) {
        // if weighting is not used, all items weigh equally
        $equal_weight = 100 / (count($items['practices']) + count($items['blocks']) + count($items['exams']));

        foreach ($items as $category => &$list) {
            foreach ($list as &$item) {
                $item['weighting']     = $equal_weight;
                $overall['weighting'] += $equal_weight;
            }
        }
    }

    if (count($assignments) > 0) {

        // fetch all assignments, grouped and summed up by user       //
        // (assignments that are not solved by any user won't appear) //

        $sql = "SELECT vips_solution.assignment_id,
                vips_solution.user_id
            FROM vips_solution
            LEFT JOIN seminar_user
                   ON seminar_user.user_id = vips_solution.user_id
                  AND seminar_user.Seminar_id = '$course_id'
            WHERE vips_solution.assignment_id IN (".implode(',', array_keys($assignments)).")
              AND (seminar_user.status IS NULL OR
                   seminar_user.status NOT IN ('dozent', 'tutor'))
            GROUP BY vips_solution.assignment_id,
                vips_solution.user_id";
        $result = $db->query($sql);

        // each assignment
        foreach ($result as $row) {
            $assignment_id  = (int) $row['assignment_id'];
            $assignment     = VipsAssignment::find($assignment_id);
            $user_id        = $row['user_id'];
            $reached_points = array_sum(vips_get_reached_points($user_id, $assignment_id)); // points in the assignment

            $category = $assignments[$assignment_id]['category'];
            $item_id  = $assignments[$assignment_id]['item_id'];

            $item_points = $participants[$user_id]['items'][$category][$item_id]['points'] + $reached_points;  // points in the item (which can contain more than one assignment)
            $max_points  = $items[$category][$item_id]['points'];  // max points for the item
            $weighting   = $items[$category][$item_id]['weighting'];  // item weighting

            // compute percent and weighted percent
            if ($max_points != 0) {  // avoid division by zero warning
                $percent          = roundLikeFloor(100 * $item_points / $max_points, 1);
                $weighted_percent = round($weighting * $item_points / $max_points, 1);
            } else {
                $percent          = 0;
                $weighted_percent = 0;
            }



            // practices //

            if ($category == 'practices') {
                $group = get_user_group($user_id, $assignment);

                if (!isset($group)) {  // no group member
                    $group['members'][] = $user_id;  // create pseudo-group with user as only member
                }

                foreach ($group['members'] as $member_id) {
                    if (!isset($participants[$member_id]['items']['practices'][$item_id])) {
                        $weighting_until_now = $participants[$member_id]['items']['blocks'][$item_id]['weighting'];  // may be null

                        // store reached points, percent and weighted percent for this
                        // item, added up for each group member
                        $participants[$member_id]['items']['practices'][$item_id]['points']    += $reached_points;
                        $participants[$member_id]['items']['practices'][$item_id]['percent']    = $percent;
                        $participants[$member_id]['items']['practices'][$item_id]['weighting'] += $weighted_percent - $weighting_until_now;

                        // sum up overall points and weighted percent
                        $participants[$member_id]['overall']['points']              += $reached_points;
                        $participants[$member_id]['overall']['points_practices']    += $reached_points;
                        $participants[$member_id]['overall']['weighting']           += $weighted_percent;
                        $participants[$member_id]['overall']['weighting_practices'] += $weighted_percent;
                    }
                }
            }



            // blocks //

            if ($category == 'blocks') {
                $group = get_user_group($user_id, $assignment);

                if (!isset($group)) {  // no group member
                    $group['members'][] = $user_id;  // create pseudo-group with user as only member
                }

                foreach ($group['members'] as $member_id) {
                    if (!isset($participants[$member_id]['items']['tests_seen'][$assignment_id])) {
                        $participants[$member_id]['items']['tests_seen'][$assignment_id] = true;
                        $weighting_until_now = $participants[$member_id]['items']['blocks'][$item_id]['weighting'];  // may be null

                        // store reached points, percent and weighted percent for each
                        // practice in this block
                        $participants[$member_id]['items']['blocks'][$item_id]['points']    += $reached_points;
                        $participants[$member_id]['items']['blocks'][$item_id]['percent']    = $percent;
                        $participants[$member_id]['items']['blocks'][$item_id]['weighting'] += $weighted_percent - $weighting_until_now;

                        // sum up overall points and weighted percent (less the
                        // percentage that has already been added)
                        $participants[$member_id]['overall']['points']           += $reached_points;
                        $participants[$member_id]['overall']['points_blocks']    += $reached_points;
                        $participants[$member_id]['overall']['weighting']        += $weighted_percent - $weighting_until_now;
                        $participants[$member_id]['overall']['weighting_blocks'] += $weighted_percent - $weighting_until_now;
                    }
                }
            }



            // exams //

            if ($category == 'exams') {
                // store reached points, percent and weighted percent for this item
                $participants[$user_id]['items'][$category][$item_id] = [
                    'points'    => $reached_points,
                    'percent'   => $percent,
                    'weighting' => $weighted_percent
                ];

                // sum up overall points and weighted percent
                $participants[$user_id]['overall']['points']          += $reached_points;
                $participants[$user_id]['overall']['points_exams']    += $reached_points;
                $participants[$user_id]['overall']['weighting']       += $weighted_percent;
                $participants[$user_id]['overall']['weighting_exams'] += $weighted_percent;
            }
        }  // end: each assignment
    }

    // if user_id parameter has been passed, delete all participants but the
    // requested user (this must take place AFTER all that has been done before
    // for to catch all group solutions)
    if (isset($param_user_id)) {
        $participants = [$param_user_id => $participants[$param_user_id]];
    }

    // get information for each participant
    foreach ($participants as $user_id => $rest) {
        $user = User::find($user_id);
        $forename = $user->vorname;
        $surname  = $user->nachname;
        $stud_id  = get_student_id($user_id);

        $participants[$user_id]['forename'] = $forename;
        $participants[$user_id]['surname']  = $surname;
        $participants[$user_id]['name']     = $surname.', '.$forename;
        $participants[$user_id]['stud_id']  = $stud_id;
    }


    // sort participant array //

    function sort_by_name($a, $b) {  // sort by name
        return strcoll($a['surname'].' '.$a['forename'], $b['surname'].' '.$b['forename']);
    }

    function sort_by_points($a, $b) {  // sort by points (or name, if points are equal)
        if ($a['overall']['points'] == $b['overall']['points']) {
            return sort_by_name($a, $b);
        } else {
            return $a['overall']['points'] < $b['overall']['points'] ? -1 : 1;
        }
    }

    function sort_by_grade($a, $b) {  // sort by grade (or name, if grade is equal)
        if ($a['overall']['weighting'] == $b['overall']['weighting']) {
            return sort_by_name($a, $b);
        } else {
            return $a['overall']['weighting'] < $b['overall']['weighting'] ? -1 : 1;
        }
    }

    switch ($sort) {
        case 'sum':  // sort by sum row
            if ($display == 'points') {
                uasort($participants, 'sort_by_points');
            } else {
                uasort($participants, 'sort_by_grade');
            }
            break;

        case 'grade':  // sort by grade (or name, if grade is equal)
            uasort($participants, 'sort_by_grade');
            break;

        case 'name':  // sort by name
        default:
            uasort($participants, 'sort_by_name');
    }

    if ($desc) {
        $participants = array_reverse($participants, true);
    }

    // fetch grades from database
    $settings = VipsSettings::find($course_id);
    $grades = $settings->grades;

    // grading is used
    if (isset($grades)) {
        foreach ($participants as $user_id => $participant) {
            $participants[$user_id]['grade'] = '5,0';
            $participants[$user_id]['ects']  = 'F';

            foreach ($grades as $g) {
                $grade     = $g['grade'];
                $percent   = $g['percent'];
                $comment   = $g['comment'];

                if ($participant['overall']['weighting'] >= $percent) {
                    // TODO this mapping needs to be configurable
                    if ($grade <= '1,5') {
                        $ects = 'A';
                    } else if ($grade <= '2,0') {
                        $ects = 'B';
                    } else if ($grade <= '3,0') {
                        $ects = 'C';
                    } else if ($grade <= '3,5') {
                        $ects = 'D';
                    } else if ($grade <= '4,0') {
                        $ects = 'E';
                    } else {
                        $ects = 'F';
                    }

                    $participants[$user_id]['grade']         = $grade;
                    $participants[$user_id]['ects']          = $ects;
                    $participants[$user_id]['grade_comment'] = $comment;
                    break;
                }
            }
        }
    }

    return [
        'display'        => $display,
        'sort'           => $sort,
        'desc'           => $desc,
        'compute_grade'  => $compute_grade,
        'items'          => $items,
        'overall'        => $overall,
        'participants'   => $participants
    ];
}



///////////////////////////////////////////////
//   A U X I L I A R Y   F U N C T I O N S   //
///////////////////////////////////////////////



/**
 * Get all solutions for a assignment.
 *
 * @param int $assignment_id The assignment id
 * @param bool $view If set to the empty string, only users with solutions are
 *                    returned.  If set to string <code>all</code>, virtually
 *                    <i>all</i> course participants (including those who have
 *                    not delivered any solution) are returned.
 * @return Array An array consisting of <i>three</i> arrays, namely 'solvers'
 *               (containing all single solvers and groups), 'exercises'
 *               (containing all exercises in the assignment) and 'solutions'
 *               (containing all solvers and their solved exercises).
 */
function get_solutions($assignment_id, $view)
{
    $db = DBManager::get();

    // get info about assignment //

    $sql = "SELECT test_id, course_id, type, end FROM vips_assignment WHERE id = $assignment_id";
    $result = $db->query($sql);

    if (($row = $result->fetch())) {
        $course_id = $row['course_id'];
        $test_id   = $row['test_id'];
        $test_type = $row['type'];
        $test_end  = $row['end'];
    }



    // get exercises //

    $sql = "SELECT vips_exercise.id,
            vips_exercise.title,
            vips_exercise.type,
            vips_exercise_ref.position,
            vips_exercise_ref.points
        FROM vips_exercise
        JOIN vips_exercise_ref
          ON vips_exercise_ref.exercise_id = vips_exercise.id
        WHERE vips_exercise_ref.test_id = $test_id
        ORDER BY vips_exercise_ref.position";
    $result = $db->query($sql);

    if ($result->rowCount() == 0) {  // no exercises existent
        return null;
    }

    foreach ($result as $row) {
        $id        = (int) $row['id'];
        $title     = $row['title'];
        $type      = $row['type'];
        $position  = (int) $row['position'];
        $points    = (float) $row['points'];  // max points

        $exercises[$id] = [
            'id'        => $id,
            'title'     => $title,
            'type'      => $type,
            'position'  => $position,
            'points'    => $points
        ];
    }

    if (is_array($exercises)) {
        $exercises_list_sql = "'".implode("','", array_keys($exercises))."'";
    } else {  // no exercises existent
        return null;
    }



    // get course participants //

    $solvers = [];
    $tutors = [];

    $sql = "SELECT user_id, status FROM seminar_user WHERE Seminar_id = '$course_id'";
    $result = $db->query($sql);

    foreach ($result as $row) {
        $user_id = $row['user_id'];
        $status  = $row['status'];

        // don't include tutors and lecturers
        if ($status == 'tutor' || $status == 'dozent') {
            $tutors[$user_id] = $status;
        } else {
            $solvers[$user_id] = [
                'type'      => 'single',
                'id'        => $user_id,
                'user_name' => null,
                'name'      => $user_id,
                'forename'  => null,
                'surname'   => null
            ];
        }
    }

    /// NOTE: $solvers may be empty, but will probably be filled later on when
    /// processing solutions



    // get solutions //

    $sql = "SELECT id,
            exercise_id,
            user_id,
            time,
            corrected,
            points,
            corrector_id,
            corrector_comment
        FROM vips_solution
        WHERE exercise_id IN ($exercises_list_sql)
          AND assignment_id = $assignment_id
          AND user_id != 'nobody'";
    $result = $db->query($sql);

    foreach ($result as $row) {
        $id                = (int) $row['id'];
        $exercise_id       = (int) $row['exercise_id'];
        $user_id           = $row['user_id'];
        $time              = $row['time'];
        $corrected         = (boolean) $row['corrected'];
        $points            = (float) $row['points'];
        $corrector_id      = $row['corrector_id'];
        $corrector_comment = $row['corrector_comment'];

        $solutions[$user_id][$exercise_id] = [
            'id'                => $id,
            'exercise_id'       => $exercise_id,
            'user_id'           => $user_id,
            'time'              => $time,
            'corrected'         => $corrected,
            'points'            => $points,
            'corrector_id'      => $corrector_id,
            'corrector_comment' => $corrector_comment
        ];

        // solver may be a non-participant (and must not be a tutor)
        if (!isset($solvers[$user_id]) && !isset($tutors[$user_id])) {
            $solvers[$user_id] = [
                'type'      => 'single',
                'id'        => $user_id,
                'user_name' => null,
                'name'      => $user_id,
                'forename'  => null,
                'surname'   => null
            ];
        }
    }

    /// NOTE: $solvers now *additionally* contains all students which have
    /// submitted a solution


    // get groups //

    $groups = [];

    if ($test_type == 'practice') {
        // all users which are group member
        $sql = "SELECT vips_group.id,
                vips_group.name,
                vips_group_member.user_id
            FROM vips_group
            JOIN vips_group_member
              ON vips_group_member.group_id = vips_group.id
            WHERE vips_group.course_id = '$course_id'
              AND vips_group_member.start <= '$test_end'
              AND (vips_group_member.end > '$test_end'
                  OR vips_group_member.end IS NULL)
            ORDER BY vips_group.name";
        $result = $db->query($sql);

        foreach ($result as $row) {
            $group_id   = (int) $row['id'];
            $group_name = $row['name'];
            $user_id    = $row['user_id'];

            if (!isset($solvers[$user_id])) {  // add group member to $solvers
                $solvers[$user_id] = [
                    'type'      => 'group_member',
                    'id'        => $user_id,
                    'user_name' => null,
                    'name'      => $user_id,
                    'forename'  => null,
                    'surname'   => null
                ];
            } else {  // update type for existing solvers
                $solvers[$user_id]['type'] = 'group_member';
            }

            if (!isset($groups[$group_id])) {  // add group
                $groups[$group_id] = [
                    'type'    => 'group',
                    'id'      => $group_id,
                    'name'    => $group_name,
                    'members' => []
                ];
            }

            // store which user is member of which group (user_id => group_id)
            $map_user_to_group[$user_id] = $group_id;
        }
    }

    /// NOTE: $solvers now *additionally* contains group members (if assignment is a
    /// practice)



    if (count($solvers) == 0) {  // no users existent
        return NULL;
    }

    // build list of solvers for use in sql statement
    $solver_list_sql = "'".implode("','", array_keys($solvers))."'";

    // get user names
    $sql = "SELECT user_id, username, Vorname, Nachname FROM auth_user_md5 WHERE user_id IN ($solver_list_sql)";
    $result = $db->query($sql);

    foreach ($result as $row) {
        $user_id   = $row['user_id'];
        $username  = $row['username'];
        $forename  = $row['Vorname'];
        $surname   = $row['Nachname'];

        $solvers[$user_id]['user_name'] = $username;
        $solvers[$user_id]['forename']  = $forename;
        $solvers[$user_id]['surname']   = $surname;
        $solvers[$user_id]['name']      = $surname.', '.$forename;
    }

    function sort_by_name($a, $b) {  // sort by name
        return strcoll($a['surname'].' '.$a['forename'], $b['surname'].' '.$b['forename']);
    }

    // sort solvers by surname - forename - user id
    uasort($solvers, 'sort_by_name');



    // add groups to $solvers array //

    foreach ($groups as $group_id => $group) {
        $solvers[$group_id] = $group;
    }



    // sort single solvers to groups //

    foreach ($solvers as $solver_id => $solver) {
        if ($solver['type'] == 'group_member') {
            $group_id = $map_user_to_group[$solver_id];  // get group id

            $solvers[$group_id]['members'][$solver_id] = $solver;  // store solver as group member
            unset($solvers[$solver_id]);  // delete him as single solver
        }
    }

    // change solution user ids to group ids //

    if (!empty($solutions)) {
        foreach ($solutions as $solver_id => $exercise_solutions) {
            $group_id = $map_user_to_group[$solver_id];  // may be null

            if (isset($group_id)) {
                foreach ($exercise_solutions as $exercise_id => $solution) {
                    // always store most recent solution
                    if (!isset($solutions[$group_id][$exercise_id]) || $solution['time'] > $solutions[$group_id][$exercise_id]['time']) {
                        $solutions[$group_id][$exercise_id] = $solution;  // store solution as group solution
                        unset($solutions[$solver_id][$exercise_id]);  // delete single-solver-solution
                    } else {  // remove «deprecated» solution from array
                        unset($solutions[$solver_id][$exercise_id]);
                    }
                }
            }
        }
    }

    // remove hidden solver entries //

    foreach ($solvers as $solver_id => $solver) {
        if (!isset($solutions[$solver_id])) {  // has no solutions
            if (!$view || $view == 'todo') {
                unset($solvers[$solver_id]);
            }
        } else if ($view == 'todo') {
            foreach ($solutions[$solver_id] as $solution) {
                if (!$solution['corrected']) {
                    continue 2;
                }
            }

            unset($solvers[$solver_id]);
        }
    }

    return [
        'solvers'   => $solvers,    // ordered by surname - forename - user id
        'exercises' => $exercises,  // ordered by position
        'solutions' => $solutions   // first single solvers then groups, furthermore unordered
    ];
}



/**
 * Computes the points a user has reached in all exercises in a given assignment.
 *
 * @param $user_id The user id
 * @param $assignment_id The assignment id
 * @return An array ([exercise_id] => reached_points, ...) containing the
 *         reached points for each exercise in this assignment
 */
function vips_get_reached_points($user_id, $assignment_id)
{
    $db = DBManager::get();

    $assignment = VipsAssignment::find($assignment_id);
    $group = get_user_group($user_id, $assignment);

    $sql_user_list = isset($group) ? implode("','", $group['members']) : $user_id;

    // for each solved exercise, take the most recent solution of this user
    // (resp. all users in the group) and sum up the reached points
    $sql = "SELECT vips_solution.exercise_id, vips_solution.points FROM vips_solution
          JOIN vips_assignment ON vips_solution.assignment_id = vips_assignment.id
          JOIN vips_exercise_ref USING(exercise_id, test_id)
        WHERE vips_solution.assignment_id = $assignment_id
          AND vips_solution.user_id IN ('$sql_user_list')
          ORDER BY vips_solution.time DESC";
    $result = $db->query($sql);

    // build array $reached_points
    $reached_points = [];
    foreach ($result as $row) {
        $exercise_id = (int) $row['exercise_id'];
        $points      = (float) $row['points'];

        // store only the points for newest solution
        if (!isset($reached_points[$exercise_id])) {
            $reached_points[$exercise_id] = $points;
        }
    }

    return $reached_points;
}



/**
 * Counts uncorrected solutions for a assignment.
 *
 * @param $assignment_id The assignment id
 * @return <code>null</code> if there does not exist any solution at all, else
 *         the number of uncorrected solutions
 */
function vips_count_uncorrected_solutions($assignment_id)
{
    $db = DBManager::get();

    $assignment = VipsAssignment::find($assignment_id);

    $course_id = $assignment->course_id;
    $test_id   = $assignment->test_id;

    // get all corrected and uncorrected solutions
    $sql = "SELECT vips_solution.exercise_id,
            vips_solution.user_id,
            vips_solution.corrected
        FROM vips_solution
        JOIN vips_exercise_ref
          ON vips_exercise_ref.exercise_id = vips_solution.exercise_id AND vips_exercise_ref.test_id = $test_id
        LEFT JOIN seminar_user
               ON seminar_user.user_id = vips_solution.user_id
              AND seminar_user.Seminar_id = '$course_id'
        WHERE vips_solution.assignment_id = $assignment_id
            AND vips_solution.user_id != 'nobody'
            AND (seminar_user.status IS NULL OR
                 seminar_user.status NOT IN ('dozent', 'tutor'))
        ORDER BY time DESC";
    $result = $db->query($sql);

    // no solutions at all
    if ($result->rowCount() == 0) {
        return null;
    }

    // count uncorrected solutions
    $uncorrected_solutions = 0;
    $solutions = [];
    $groups = [];

    foreach ($result as $row) {
        $exercise_id = (int) $row['exercise_id'];
        $user_id     = $row['user_id'];
        $corrected   = (boolean) $row['corrected'];

        if ($assignment->type === 'practice' && !array_key_exists($user_id, $groups)) {
            $groups[$user_id] = get_user_group($user_id, $assignment);
        }

        // array of user ids: either group members or just the single user
        $user_id_array = isset($groups[$user_id]) ? $groups[$user_id]['members'] : [$user_id];

        if (!array_key_exists($exercise_id . '_' . $user_id, $solutions)) {
            foreach ($user_id_array as $user_id) {
                $solutions[$exercise_id . '_' . $user_id] = true;
            }

            if (!$corrected) {
                $uncorrected_solutions++;
            }
        }
    }

    return $uncorrected_solutions;
}
