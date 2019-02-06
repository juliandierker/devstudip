<?php
/*
 * vips_groups.inc.php - Vips plugin for Stud.IP
 * Copyright (c) 2003-2005  Erik Schmitt, Philipp Hügelmeyer
 * Copyright (c) 2005-2006  Christa Deiwiks
 * Copyright (c) 2006-2008  Elmar Ludwig, Martin Schröder
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

/**
 *
 * Handles Uebungsgruppen in Vips (different from groups in Stud.IP). A group is a number of
 * students who work together on an assignment. The last solution submitted by any of the members
 * counts. Membership in a group is marked by start and end date, so when a student switches
 * groups, (s)he gets the correct points in the end.
 *
 * @author: Christa Deiwiks <cdeiwiks@uos.de>
 */

/**
 * Displays main page
 * STUDENTS: see the groups and can possibly assign themselves to a group (optional by lecturer)
 * LECTURERS: can create groups and assign any student to any group
 *
 */
class GroupsController extends StudipController
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
        Navigation::activateItem('/course/vips/groups');
        PageLayout::setHelpKeyword(vips_text_encode('Vips.Übungsgruppen'));
        PageLayout::setTitle(PageLayout::getTitle() . ' - ' . _vips('Übungsgruppen'));
    }

    function index_action()
    {
        global $vipsPlugin;

        $db = DBManager::get();

        $this->settings = VipsSettings::find($vipsPlugin->courseID);
        $this->groups = VipsGroup::findBySQL('course_id = ? ORDER BY name', [$vipsPlugin->courseID]);
        $this->user_id = $vipsPlugin->userID;

        $this->selectable_users = [];
        $this->unselectable_users = [];

        #################################
        #  get all course participants  #
        #################################

        $sql = "SELECT user_id FROM seminar_user
                JOIN auth_user_md5 USING(user_id)
                WHERE seminar_user.Seminar_id = ?
                  AND seminar_user.status     = 'autor'
                ORDER BY Nachname, Vorname";
        $stmt = $db->prepare($sql);
        $stmt->execute([$vipsPlugin->courseID]);

        foreach ($stmt as $row) {
            $group = VipsGroup::getUserGroup($row['user_id'], $vipsPlugin->courseID);

            if ($group) {
                $this->unselectable_users[] = $row['user_id'];
            } else {
                $this->selectable_users[] = $row['user_id'];
            }
        }

        if (vips_has_status('tutor')) {
            Helpbar::get()->addPlainText('',
                _vips('Hier können Sie Studierende in Gruppen einteilen, die gemeinsam Übungen bearbeiten können.'));

            $widget = new ActionsWidget();
            $widget->addLink(_vips('Übungsgruppe erstellen'),
                vips_link('groups/edit_group_dialog'),
                Icon::create('add'), ['data-dialog' => 'size=auto']);
            Sidebar::get()->addWidget($widget);

            $widget = new OptionsWidget();
            $widget->addCheckbox(_vips('Studierende dürfen sich selbst in Gruppen eintragen'),
                $this->settings->selfassign,
                vips_link('groups/store_access'));
            Sidebar::get()->addWidget($widget);
        }
    }



    /**
     * Stores a group created by lecturer
     */
    function store_group_action()
    {
        global $vipsPlugin;

        vips_require_status('tutor');

        $group_id   = Request::int('group_id', 0);
        $group_name = trim(Request::get('group_name'));
        $group_size = Request::int('group_size');

        // ensure that $group_size >= 1
        $group_size = max(1, $group_size);

        if ($group_id) {
            $group = VipsGroup::find($group_id);
            check_group_access($group);
        } else {
            $group = new VipsGroup();
            $group->course_id = $vipsPlugin->courseID;

            if (VipsGroup::findBySQL('name = ? AND course_id = ?', [$group_name, $group->course_id])) {
                PageLayout::postError(_vips('Eine Gruppe dieses Namens gibt es schon. Bitte wählen Sie einen anderen Namen.'));
                $this->redirect(vips_url('groups'));
                return;
            }
        }

        $group->name = $group_name;
        $group->size = $group_size;
        $group->store();

        if ($group_id) {
            PageLayout::postSuccess(sprintf(_vips('Die Gruppe "%s" wurde gespeichert.'), htmlReady($group_name)));
        } else {
            PageLayout::postSuccess(sprintf(_vips('Die Gruppe "%s" wurde angelegt.'), htmlReady($group_name)));
        }

        $this->redirect(vips_url('groups'));
    }

    /**
     * Deletes a group
     */
    function delete_group_action()
    {
        vips_require_status('tutor');

        $group_id = Request::int('group_id');
        $group = VipsGroup::find($group_id);
        $group_name = $group->name;

        check_group_access($group);

        if ($group->delete()) {
            PageLayout::postSuccess(sprintf(_vips('Die Gruppe "%s" wurde gelöscht.'), htmlReady($group_name)));
        }

        $this->redirect(vips_url('groups'));
    }

    /**
     * Puts a student into a group
     */
    function assign_participant_action()
    {
        global $vipsPlugin;

        $group_id = Request::int('group_id');
        $group = VipsGroup::find($group_id);
        $user_id = $vipsPlugin->userID;

        check_group_access($group);

        $number_of_participants = VipsGroupMember::countBySql('group_id = ? AND end IS NULL', [$group_id]);

        // check for membership in this or other groups
        $current_group = VipsGroup::getUserGroup($user_id, $group->course_id);

        if ($current_group->id == $group_id) {
            PageLayout::postError(_vips('Sie befinden sich schon in dieser Gruppe.'));
        } else if ($current_group) {
            PageLayout::postError(_vips('Sie befinden sich schon in einer anderen Gruppe.'));
        } else if ($number_of_participants >= $group->size) {
            PageLayout::postError(_vips('Diese Gruppe ist bereits voll.'));
        } else {
            VipsGroupMember::create([
                'group_id' => $group_id,
                'user_id'  => $user_id,
                'start'    => date('Y-m-d H:i:s')
            ]);

            PageLayout::postSuccess(_vips('Sie haben sich in die Gruppe eingetragen.'));
        }

        $this->redirect(vips_url('groups'));
    }


    /**
     * Puts a list of students into a group
     */
    function assign_participants_action()
    {
        vips_require_status('tutor');

        $group_id = Request::int('group_id');
        $group = VipsGroup::find($group_id);

        $mp_search = MultiPersonSearch::load('group' . $group_id);
        $selected_participants = $mp_search->getAddedUsers();

        check_group_access($group);

        $number_of_participants = VipsGroupMember::countBySql('group_id = ? AND end IS NULL', [$group_id]);

        foreach ($selected_participants as $user_id) {
            // check for membership in this or other groups
            $current_group = VipsGroup::getUserGroup($user_id, $group->course_id);

            if ($current_group->id == $group_id) {
                PageLayout::postError(_vips('Der Teilnehmer befindet sich schon in dieser Gruppe.'));
            } else if ($current_group) {
                PageLayout::postError(_vips('Der Teilnehmer befindet sich schon in einer anderen Gruppe.'));
            } else if ($number_of_participants >= $group->size) {
                PageLayout::postError(_vips('Diese Gruppe ist bereits voll.'));
            } else {
                VipsGroupMember::create([
                    'group_id' => $group_id,
                    'user_id'  => $user_id,
                    'start'    => date('Y-m-d H:i:s')
                ]);

                PageLayout::postSuccess(_vips('Der Teilnehmer wurde in die Gruppe eingetragen.'));
            }
        }

        $this->redirect(vips_url('groups'));
    }


    /**
     * Removes a student from a group
     */
    function delete_participant_action()
    {
        global $vipsPlugin;

        $group_id = Request::int('group_id');
        $group = VipsGroup::find($group_id);
        $user_id = Request::option('user_id');

        check_group_access($group);

        if (!vips_has_status('tutor')) {
            $user_id = $vipsPlugin->userID;
        }

        $group_member = VipsGroupMember::findOneBySQL('group_id = ? AND user_id = ? AND end IS NULL', [$group_id, $user_id]);

        if ($group_member) {
            $group_member->end = date('Y-m-d H:i:s');
            $group_member->store();

            if (vips_has_status('tutor')) {
                PageLayout::postSuccess(_vips('Der Teilnehmer wurde aus der Gruppe ausgetragen.'));
            } else {
                PageLayout::postSuccess(_vips('Sie haben sich aus der Gruppe ausgetragen.'));
            }
        }

        $this->redirect(vips_url('groups'));
    }


    /**
     * Stores student and tutor access to different areas
     */
    function store_access_action()
    {
        global $vipsPlugin;

        vips_require_status('tutor');

        $settings = new VipsSettings($vipsPlugin->courseID);
        $settings->selfassign = !$settings->selfassign;
        $settings->store();

        $this->redirect(vips_url('groups'));
    }

    /**
     * Returns the dialog content to create a new group.
     */
    function edit_group_dialog_action()
    {
        vips_require_status('tutor');

        $group_id = Request::int('group_id');

        if (isset($group_id)) {
            $this->group = VipsGroup::find($group_id);
            check_group_access($this->group);
        }
    }
}
