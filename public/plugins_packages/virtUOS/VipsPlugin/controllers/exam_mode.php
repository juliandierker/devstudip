<?php
/*
 * exam_mode.php - Vips plugin for Stud.IP
 * Copyright (c) 2017  Elmar Ludwig
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

class ExamModeController extends StudipController
{
    /**
     * Display a list of courses with currently active tests of type 'exam'.
     * Only used when there are multiple courses with running exams.
     */
    function index_action()
    {
        global $vipsPlugin;

        PageLayout::setTitle(_vips('Klausurübersicht'));

        Helpbar::get()->addPlainText('',
            _vips('Der normale Betrieb von Stud.IP ist für Sie zur Zeit gesperrt, da Klausuren geschrieben werden.'));

        $this->vips_plugin = $vipsPlugin;
        $this->courses = get_courses_with_running_exams($vipsPlugin->userID);
    }
}
