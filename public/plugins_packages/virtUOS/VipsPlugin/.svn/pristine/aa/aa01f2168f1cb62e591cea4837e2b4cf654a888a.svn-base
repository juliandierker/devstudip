<?php
/*
 * VipsPlugin.php - Vips plugin class for Stud.IP
 * Copyright (c) 2007-2009  Elmar Ludwig
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

require_once 'lib/vips_common.inc.php';
require_once 'lib/VipsAssignment.php';
require_once 'lib/VipsAssignmentAttempt.php';
require_once 'lib/VipsBlock.php';
require_once 'lib/VipsExerciseRef.php';
require_once 'lib/VipsFile.php';
require_once 'lib/VipsFileRef.php';
require_once 'lib/VipsGroup.php';
require_once 'lib/VipsGroupMember.php';
require_once 'lib/VipsLTILink.php';
require_once 'lib/VipsSettings.php';
require_once 'lib/VipsSolution.php';
require_once 'lib/VipsSolutionArchive.php';
require_once 'lib/VipsTest.php';
require_once 'lib/CharacterPicker.php';

require_once 'exercises/Exercise.php';
require_once 'exercises/sc_exercise.php';
require_once 'exercises/sco_exercise.php';
require_once 'exercises/mc_exercise.php';
require_once 'exercises/mco_exercise.php';
require_once 'exercises/yn_exercise.php';
require_once 'exercises/lt_exercise.php';
require_once 'exercises/cloze_exercise.php';
require_once 'exercises/tb_exercise.php';
require_once 'exercises/rh_exercise.php';
require_once 'exercises/me_exercise.php';
require_once 'exercises/lti_exercise.php';

// only available if server is configured
if (get_config('VIPS_VEA_SERVER_URL')) {
    require_once 'exercises/pl_exercise.php';
}

// this is included for Stud.IP >= 4.2
if (!interface_exists('PrivacyPlugin')) {
    require_once 'lib/PrivacyPlugin.php';
}

/**
 * Vips plugin class for Stud.IP
 */
class VipsPlugin extends StudIPPlugin implements StandardPlugin, SystemPlugin, PrivacyPlugin
{
    public $userID;
    public $courseID;
    public static $exam_mode;

    public function __construct()
    {
        global $auth;

        parent::__construct();

        $GLOBALS['vipsPlugin'] = $this;
        $GLOBALS['vipsTemplateFactory'] = new Flexi_TemplateFactory(dirname(__FILE__).'/views');

        $this->userID = $auth->auth['uid'];
        $this->courseID = class_exists('Context') ? Context::getId() : $_SESSION['SessionSeminar'];

        NotificationCenter::addObserver($this, 'userDidDelete', 'UserDidDelete');
        NotificationCenter::addObserver($this, 'courseDidDelete', 'CourseDidDelete');

        // set up translation domain
        bindtextdomain('vips', dirname(__FILE__) . '/locale');

        if (Navigation::hasItem('/browse') && vips_count_assignments($this->userID)) {
            $nav_item = new Navigation(_vips('Meine Aufgaben'));
            Navigation::addItem('/browse/vips', $nav_item);

            $sub_item = new Navigation(_vips('Aufgaben'), vips_url('pool'));
            $nav_item->addSubNavigation('exercises', $sub_item);

            $sub_item = new Navigation(_vips('Aufgabenblätter'), vips_url('pool/tests'));
            $nav_item->addSubNavigation('tests', $sub_item);
        }

        // check for running exams
        if (get_config('VIPS_EXAM_RESTRICTIONS') && !isset(self::$exam_mode)) {
            $courses = get_courses_with_running_exams($this->userID);
            self::$exam_mode = count($courses) > 0;

            if (self::$exam_mode) {
                $page = basename($_SERVER['PHP_SELF']);
                $path_info = $_SERVER['PATH_INFO'];

                // redirect page calls if necessary
                if ($page !== 'plugins.php' || strpos($path_info, '/vipsplugin/exam_mode') !== 0) {
                    // course with running exam is selected
                    if (isset($this->courseID) && in_array($this->courseID, array_keys($courses))) {
                        // allow all exam actions
                        if ($page !== 'plugins.php' || strpos($path_info, '/vipsplugin/sheets') !== 0) {
                            header('Location: ' . vips_url('sheets'));
                            die();
                        }
                    } else {
                        // forward to overview of all running courses with exams
                        header('Location: ' . vips_url('exam_mode'));
                        die();
                    }
                }
            }
        }
    }

    private function setupExamNavigation()
    {
        $navigation = new Navigation('');

        $start = Navigation::getItem('/start');
        $start->setURL(vips_url('exam_mode'));
        $navigation->addSubNavigation('start', $start);

        $course = new Navigation(_vips('Veranstaltung'));
        $navigation->addSubNavigation('course', $course);

        $vips = new Navigation($this->getPluginName());
        $vips->setImage(Icon::create(vips_image_url('vips_white.svg')));
        $vips->setActiveImage(Icon::create(vips_image_url('vips_black.svg')));
        $course->addSubNavigation('vips', $vips);

        $nav_item = new Navigation(_vips('Aufgabenblätter'), vips_url('sheets'));
        $vips->addSubNavigation('sheets', $nav_item);

        $links = new Navigation('Links');
        $links->addSubNavigation('logout', new Navigation(_vips('Logout'), 'logout.php'));
        $navigation->addSubNavigation('links', $links);

        Navigation::setRootNavigation($navigation);
    }

    public function getIconNavigation($course_id, $last_visit, $user_id)
    {
        if (vips_has_status('tutor', $course_id)) {
            // find all uncorrected exercises in finished tests in this course
            // Added JOIN with seminar_user to filter out lecturer/tutor solutions.
            $new_items = VipsSolution::countBySQL(
                "JOIN vips_assignment ON vips_solution.assignment_id = vips_assignment.id
                 JOIN seminar_user ON
                      seminar_user.Seminar_id = vips_assignment.course_id AND
                      seminar_user.user_id = vips_solution.user_id
                 WHERE vips_assignment.course_id = ? AND
                       vips_assignment.end <= NOW() AND
                       vips_solution.corrected = 0 AND
                       seminar_user.status = 'autor'",
                [$course_id]
            );

            $message = n_vips('%d unkorrigierte Lösung', '%d unkorrigierte Lösungen', $new_items);
        } else {
            // find all active tests not yet seen by the student
            $new_items = VipsAssignment::countBySQL(
                'LEFT JOIN vips_block ON vips_assignment.block_id = vips_block.id
                 LEFT JOIN vips_assignment_attempt ON
                      vips_assignment_attempt.assignment_id = vips_assignment.id AND
                      vips_assignment_attempt.user_id = ?
                 WHERE vips_assignment.course_id = ? AND
                       vips_assignment.start <= NOW() AND
                       vips_assignment.end > NOW() AND
                       vips_assignment_attempt.user_id IS NULL AND
                       (vips_block.id IS NULL OR vips_block.visible = 1)',
                [$user_id, $course_id]
            );

            $message = n_vips('%d neues Aufgabenblatt', '%d neue Aufgabenblätter', $new_items);
        }

        $overview_message = $this->getPluginName();
        $icon = vips_image_url('vips_grey.svg');

        if ($new_items > 0) {
            $overview_message = sprintf($message, $new_items);
            $icon = vips_image_url('vips_red.svg');
        }

        $icon_navigation = new Navigation($this->getPluginName(), vips_url('sheets'));
        $icon_navigation->setImage(Icon::create($icon), ['title' => $overview_message]);

        if ($this->isNavigationEnabled($course_id)) {
            return $icon_navigation;
        }
    }

    public function getNotificationObjects($course_id, $since, $user_id)
    {
        return NULL;
    }

    public function getInfoTemplate($course_id)
    {
        return NULL;
    }

    public function getTabNavigation($course_id)
    {
        $navigation = new Navigation($this->getPluginName());
        $navigation->setImage(Icon::create(vips_image_url('vips_white.svg')));
        $navigation->setActiveImage(Icon::create(vips_image_url('vips_black.svg')));

        $nav_item = new Navigation(_vips('Aufgabenblätter'), vips_url('sheets'));
        $navigation->addSubNavigation('sheets', $nav_item);

        $nav_item = new Navigation(_vips('Ergebnisse'), vips_url('solutions'));
        $navigation->addSubNavigation('solutions', $nav_item);

        $nav_item = new Navigation(_vips('Übungsgruppen'), vips_url('groups'));
        $navigation->addSubNavigation('groups', $nav_item);

        if ($this->isNavigationEnabled($course_id)) {
            return ['vips' => $navigation];
        }
    }

    public function isNavigationEnabled($course_id)
    {
        if (!vips_has_status('tutor', $course_id)) {
            $settings = VipsSettings::find($course_id);

            if ($settings && !$settings->visible) {
                return false;
            }
        }

        return $this->userID !== 'nobody';
    }

    public function getMetadata()
    {
        $metadata = parent::getMetadata();

        if (version_compare($GLOBALS['SOFTWARE_VERSION'], '4', '>=')) {
            foreach ($metadata as $key => $value) {
                if (is_string($value)) {
                    $metadata[$key] = utf8_encode($value);
                }
            }
        }

        return $metadata;
    }

    /**
     * This method dispatches all actions.
     *
     * @param string   part of the dispatch path that was not consumed
     *
     * @return void
     */
    public function perform($unconsumed_path)
    {
        global $SMILE_SHORT, $SYMBOL_SHORT;

        $SMILE_SHORT = $SYMBOL_SHORT = [];
        $header_line = class_exists('Context') ? Context::getHeaderLine() : $_SESSION['SessSemName']['header_line'];

        if (self::$exam_mode) {
            // add current user name to header line, install minimal navigation
            $header_line = '[' . $GLOBALS['user']->username . '] ' . $header_line;
            $this->setupExamNavigation();
        }

        PageLayout::addHeadElement('script', [], 'VIPS_BASE_URL = "' . dirname(vips_url('sheets')) . '";');
        PageLayout::addStylesheet($this->getPluginURL() . '/css/vips_style.css');
        PageLayout::addScript($this->getPluginURL() . '/js/vips.js');
        PageLayout::setTitle($header_line . ' - ' . $this->getPluginName());

        Sidebar::get()->setImage('sidebar/checkbox-sidebar');

        parent::perform($unconsumed_path);
    }

    public function userDidDelete($event, $user)
    {
        // delete in vips_solution and vips_solution_archive
        VipsSolution::deleteBySQL('user_id = ?', [$user->id]);
        VipsSolutionArchive::deleteBySQL('user_id = ?', [$user->id]);

        // delete start times and group memberships
        VipsAssignmentAttempt::deleteBySQL('user_id = ?', [$user->id]);
        VipsGroupMember::deleteBySQL('user_id = ?', [$user->id]);
    }

    public function courseDidDelete($event, $course)
    {
        // delete all assignments in course
        VipsAssignment::deleteBySQL('course_id = ?', [$course->id]);

        // delete other course related info
        VipsBlock::deleteBySQL('course_id = ?', [$course->id]);
        VipsGroup::deleteBySQL('course_id = ?', [$course->id]);
        VipsSettings::deleteBySQL('course_id = ?', [$course->id]);
    }

    /**
     * Export available data of a given user into a storage object
     * (an instance of the StoredUserData class) for that user.
     *
     * @param StoredUserData $store object to store data into
     */
    public function exportUserData(StoredUserData $store)
    {
        $db = DBManager::get();

        $data = $db->fetchAll('SELECT * FROM vips_exercise WHERE user_id = ?', [$store->user_id]);
        $store->addTabularData(_vips('Vips-Aufgaben'), 'vips_exercise', $data);

        $data = $db->fetchAll('SELECT * FROM vips_test WHERE user_id = ?', [$store->user_id]);
        $store->addTabularData(_vips('Vips-Aufgabenblätter'), 'vips_test', $data);

        $data = $db->fetchAll('SELECT * FROM vips_file WHERE user_id = ?', [$store->user_id]);
        $store->addTabularData(_vips('Vips-Dateien'), 'vips_file', $data);

        $data = $db->fetchAll('SELECT * FROM vips_group_member WHERE user_id = ?', [$store->user_id]);
        $store->addTabularData(_vips('Vips-Gruppenzuordnung'), 'vips_group_member', $data);

        $data = $db->fetchAll('SELECT * FROM vips_solution WHERE user_id = ?', [$store->user_id]);
        $store->addTabularData(_vips('Vips-Lösungen'), 'vips_solution', $data);

        $data = $db->fetchAll('SELECT * FROM vips_solution_archive WHERE user_id = ?', [$store->user_id]);
        $store->addTabularData(_vips('Vips-Lösungsarchiv'), 'vips_solution_archive', $data);

        $data = $db->fetchAll('SELECT * FROM vips_assignment_attempt WHERE user_id = ?', [$store->user_id]);
        $store->addTabularData(_vips('Vips-Startzeitpunkte'), 'vips_assignment_attempt', $data);

        foreach (VipsFile::findByUser_id($store->user_id) as $file) {
            $store->addFileAtPath($file->name, $file->getFilePath());
        }
    }
}
