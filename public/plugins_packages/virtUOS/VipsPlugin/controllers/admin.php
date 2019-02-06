<?php
/*
 * vips_admin.inc.php - Vips plugin for Stud.IP
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
 * Created on 08.03.2006 by Christa Deiwiks <cdeiwiks@uos.de>
 *
 * Manages Vips-related info concerning administration:
 *
 * - sheet weightings
 * - grade distribution
 *
 * @author: Christa Deiwiks <cdeiwiks@uos.de>
 */
class AdminController extends StudipController
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

        vips_require_status('tutor');
        Navigation::activateItem('/course/vips/solutions');
        PageLayout::setHelpKeyword(vips_text_encode('Vips.Lösung'));
    }

    /**
     * Edit or create a block in the course.
     */
    function edit_block_action()
    {
        $block_id = Request::int('block_id');

        if ($block_id) {
            $this->block = VipsBlock::find($block_id);
            check_block_access($this->block);
        }
    }

    /**
     * Store changes to a block.
     */
    function store_block_action()
    {
        global $vipsPlugin;

        $block_id = Request::int('block_id');

        if ($block_id) {
            $block = VipsBlock::find($block_id);
            check_block_access($block);
        } else {
            $block = new VipsBlock();
            $block->course_id = $vipsPlugin->courseID;
        }

        $block->name = Request::get('block_name');
        $block->visible = Request::int('block_visible', 0);
        $block->store();

        PageLayout::postSuccess(sprintf(_vips('Der Block "%s" wurde gespeichert.'), htmlReady($block->name)));

        $this->redirect(vips_url('sheets'));
    }

    /**
     * Delete a block from the course.
     */
    function delete_block_action()
    {
        $block_id = Request::int('block_id');
        $block = VipsBlock::find($block_id);
        $block_name = $block->name;

        check_block_access($block);

        if ($block->delete()) {
            PageLayout::postSuccess(sprintf(_vips('Der Block "%s" wurde gelöscht.'), htmlReady($block_name)));
        }

        $this->redirect(vips_url('sheets'));
    }

    /**
     * Stores the weights of blocks, sheets and exams
     */
    function store_weight_action()
    {
        $assignment_weight = Request::floatArray('assignment_weight');
        $block_weight      = Request::floatArray('block_weight');

        $prozente = array_sum($assignment_weight) + array_sum($block_weight);

        // NOTE floating point additions may be inaccurate, so round before comparison
        if (round($prozente, 1) == 100 || $prozente == 0) {
            foreach ($assignment_weight as $assignment_id => $weight) {
                $assignment = VipsAssignment::find($assignment_id);
                check_assignment_access($assignment);

                $assignment->weight = $weight;
                $assignment->store();
            }

            foreach ($block_weight as $block_id => $weight) {
                $block = VipsBlock::find($block_id);
                check_block_access($block);

                $block->weight = $weight;
                $block->store();
            }
        } else {
            PageLayout::postError(_vips('Die Gewichtungen müssen in der Summe 100 oder 0 (keine Gewichtung) ergeben. Die Daten wurden nicht übernommen!'));
        }

        $this->redirect(vips_url('solutions'));
    }

    /**
     * Edit the grade distribution settings.
     */
    function edit_grades_action()
    {
        global $vipsPlugin;

        $grades = ['0,7', '1,0', '1,3', '1,7', '2,0', '2,3', '2,7', '3,0', '3,3', '3,7', '4,0'];
        $percentages = array_fill(0, count($grades), '');
        $comments = array_fill(0, count($grades), '');
        $settings = VipsSettings::find($vipsPlugin->courseID);

        if ($settings->grades) {
            foreach ($settings->grades as $value) {
                $index = array_search($value['grade'], $grades);

                if ($index !== false) {
                    $percentages[$index] = $value['percent'];
                    $comments[$index]    = $value['comment'];
                }
            }
        }

        $this->grades            = $grades;
        $this->grade_settings    = $settings->grades;
        $this->percentages       = $percentages;
        $this->comments          = $comments;
    }

    /**
     * Stores the distribution of grades
     */
    function store_grades_action()
    {
        global $vipsPlugin;

        $grades = ['0,7', '1,0', '1,3', '1,7', '2,0', '2,3', '2,7', '3,0', '3,3', '3,7', '4,0'];
        $percent_last = 101;
        $error = false;

        for ($i = 0; $i < count($grades); $i++)  {
            $percent = Request::int('percentage_' . $i);
            $comment = trim(Request::get('comment_' . $i));

            if ($percent) {
                $grade_settings[] = [
                    'grade'   => $grades[$i],
                    'percent' => $percent,
                    'comment' => $comment
                ];

                if ($percent < 0 || $percent > 100) {
                    PageLayout::postError(_vips('Die Notenwerte müssen zwischen 0 und 100 liegen!'));
                    $error = true;
                } else if ($percent_last <= $percent) {
                    PageLayout::postError(sprintf(_vips('Die Notenwerte müssen monoton absteigen (%s > %s)!'), $percent_last, $percent));
                    $error = true;
                }

                $percent_last = $percent;
            }
        }

        if (!$error) {
            $settings = new VipsSettings($vipsPlugin->courseID);
            $settings->grades = $grade_settings;
            $settings->store();

            PageLayout::postSuccess(_vips('Die Notenwerte wurden eingetragen.'));
        }

        $this->redirect(vips_url('solutions'));
    }
}
