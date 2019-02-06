<?php
/*
 * vips_common.inc.php - Vips plugin for Stud.IP
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
 * This file contains classes and functions needed everywhere in the vips code.
 */

define('VIPS_DATE_INFINITY', '2038-01-01 00:00:00'); // used as end date for unlimited tests

/**
 * Vips translation function, alias for dgettext('vips', $message).
 */
function _vips($message)
{
    return vips_text_encode(dgettext('vips', $message));
}

/**
 * Vips translation function, alias for dngettext('vips', $msgid1, $msgid2, $n).
 */
function n_vips($msgid1, $msgid2, $n)
{
    return vips_text_encode(dngettext('vips', $msgid1, $msgid2, $n));
}

/**
 * Encode a string from latin1 to Stud.IP encoding (WINDOWS-1252 with numeric HTML-ENTITIES).
 */
function vips_text_encode($string)
{
    if (version_compare($GLOBALS['SOFTWARE_VERSION'], '4', '>=')) {
        return utf8_encode($string);
    }

    return $string;
}

/**
 * Encode a string from UTF-8 to Stud.IP encoding (WINDOWS-1252 with numeric HTML-ENTITIES).
 */
function vips_text_encode_1252($string)
{
    if (function_exists('legacy_studip_utf8decode')) {
        return legacy_studip_utf8decode($string);
    }

    return $string;
}

/**
 * Encode a string from Stud.IP encoding (WINDOWS-1252 with numeric HTML-ENTITIES) to UTF-8.
 */
function vips_text_decode_1252($string)
{
    if (function_exists('legacy_studip_utf8decode')) {
        return mb_decode_numericentity(mb_convert_encoding($string, 'UTF-8', 'cp1252'),
                                       [0x100, 0xffff, 0, 0xffff], 'UTF-8');
    }

    return $string;
}

/**
 * Variant of htmlReady for CSV strings in UTF-8.
 */
function vips_csv_encode($string)
{
    return str_replace('"', '""', studip_utf8encode($string));
}

/**
 * Variant of htmlReady for XML strings in UTF-8.
 */
function vips_xml_encode($string)
{
    $string = vips_filter_xml_string($string);

    return htmlspecialchars(studip_utf8encode($string), ENT_COMPAT, 'UTF-8');
}

/**
 * Delete all characters outside the valid character range for XML
 * documents (#x9 | #xA | #xD | [#x20-#xD7FF] | [#xE000-#xFFFD]).
 */
function vips_filter_xml_string($xml)
{
    return preg_replace("/[^\t\n\r -\xFF]/", '', $xml);
}

/**
 * Encode an HTTP header parameter (e.g. filename for 'Content-Disposition').
 *
 * @param string $name  parameter name
 * @param string $value parameter value
 *
 * @return string encoded header text (using RFC 2616 or 5987 encoding)
 */
function vips_encode_header_parameter($name, $value)
{
    if (preg_match('/[\200-\377]/', $value)) {
        // use RFC 5987 encoding (ext-parameter)
        return $name . "*=UTF-8''" . rawurlencode(studip_utf8encode($value));
    } else {
        // use RFC 2616 encoding (quoted-string)
        return $name . '="' . addslashes($value) . '"';
    }
}

/**
* Render a modal dialog to confirm a user interaction.
*
* @param   string $message       question of the modal dialog
* @param   string $accept_url    link to be used on accept
* @param   string $cancel_url    link to be used on cancel
*/
function vips_confirm_dialog($message, $accept_url, $cancel_url)
{
    if (class_exists('QuestionBox')) {
        PageLayout::postQuestion(formatReady($message), $accept_url, $cancel_url);
        return;
    }

    $template = $GLOBALS['template_factory']->open('shared/question');
    $template->set_attribute('question', $message);
    $template->set_attribute('approvalLink', $accept_url);
    $template->set_attribute('disapprovalLink', $cancel_url);

    $_SESSION['messages'][] = $template->render();
}

/**
 * Return HTML encoded URL of Vips dispatcher script.
 * $params can contain optional additional parameters
 */
function vips_link($path, $params = [])
{
    return htmlReady(vips_url($path, $params));
}

/**
 * Return unencoded URL of Vips dispatcher script.
 * $params can contain optional additional parameters
 */
function vips_url($path, $params = [])
{
    global $vipsPlugin;

    return PluginEngine::getURL($vipsPlugin, $params, $path);
}

/**
 * Return unencoded URL of Vips image.
 */
function vips_image_url($path)
{
    global $vipsPlugin;

    return $vipsPlugin->getPluginURL() . '/images/' . $path;
}

/**
 * Return HTML button element with given label and parameters.
 * This is a temporary replacement for the old makeButton() API.
 */
function vips_button($label, $name, $attributes = [])
{
    return Studip\Button::create($label, $name, $attributes);
}

/**
 * Return HTML button element with given label and parameters.
 * This is a temporary replacement for the old makeButton() API.
 */
function vips_accept_button($label, $name, $attributes = [])
{
    return Studip\Button::createAccept($label, $name, $attributes);
}

/**
 * Return HTML button element with given label and parameters.
 * This is a temporary replacement for the old makeButton() API.
 */
function vips_cancel_button($label, $name, $attributes = [])
{
    return Studip\Button::createCancel($label, $name, $attributes);
}

/**
 * Return HTML link button element with given label and parameters.
 * This is a temporary replacement for the old makeButton() API.
 */
function vips_link_button($label, $url, $attributes = [])
{
    return Studip\LinkButton::create($label, $url, $attributes);
}

/**
 * Cuts a double after specified kommastellen, e.g.:
 * roundLikeFloor(2.6318,3) => 2.631
 * roundLikeFloor(2.6315,0) => 2
 *
 * @param: double $double Double to round
 * @param: int $kommastelle Number of kommastellen to be retained
 * @return: double $double The cut-off double
 * @author: cdeiwiks
 */
function roundLikeFloor($double, $kommastelle)
{
    $factor = pow(10, $kommastelle);
    $double = $double * $factor;
    $double = floor($double);
    $double = $double / $factor;
    return $double;
}

/**
 * Returns a normalized version of a string (see comments within function)
 *
 * @param: String $string String to be normalized
 * @param: boolean $lowercase make string lower case
 * @return: The normalized string
 * @author: cdeiwiks
 */
function normalizeText($string, $lowercase = true)
{
    // remove leading/trailing spaces
    $string = trim($string);

    // compress white space
    $string = preg_replace('/[\s ]+/', ' ', $string);

    // delete blanks before and after [](){}:,;.!?"=*/+-
    $string = preg_replace('/ *([][(){}:,;.!?"=*\\/+-]) */', '$1', $string);

    // convert to lower case if requested
    return $lowercase ? strtolower($string) : $string;
}

/**
 * Returns the full name of a user (without title) or group.
 *
 * @param  string $solver_id user or group id of solver
 * @param  boolean $single_solver true if solver is a single user
 * @return string full name of user or name of group (or NULL)
 */
function get_solver_fullname($solver_id, $single_solver)
{
    // solver_id may be a single user
    if ($single_solver) {
        return get_fullname($solver_id, 'no_title');
    }

    // solver_id may be a group number
    $group = VipsGroup::find($solver_id);

    return $group->name;
}

/**
 * Returns the name of a user (login name) or group.
 *
 * @param  string $solver_id user or group id of solver
 * @return string login name of user or name of group (or NULL)
 */
function get_solver_name($solver_id)
{
    if (strlen($solver_id) == 32) {
        return get_username($solver_id);
    }

    // solver_id may be a group number
    $group = VipsGroup::find($solver_id);

    return $group->name;
}

/**
 * Returns the student id for the given user id.
 */
function get_student_id($user_id)
{
    $stud_id = Config::get()->getValue('VIPS_STUDENT_ID_DATAFIELD');
    $fields = DataFieldEntry::getDataFieldEntries($user_id);
    $result = isset($fields[$stud_id]) ? $fields[$stud_id]->getValue() : NULL;
    return $result;
}

/**
 * Rounds a float value to the nearest half point, ie x.0 or x.5.
 *
 * @param float $points
 * @return float The result.
 *
 * @author Martin Schröder
 * @date 2007-05-31
 */
function round_to_half_point($points)
{
    return round($points * 2) / 2;
}

/**
 * Test if this assignment is unlimited (valid for self test only).
 */
function vips_test_unlimited($assignment)
{
    return $assignment['type'] === 'selftest' && $assignment['end'] === VIPS_DATE_INFINITY;
}

/**
 * Get a (clickable) icon for a test with the given type.
 */
function vips_test_icon($type, $role = 'clickable')
{
    $assignment_types = VipsAssignment::getAssignmentTypes();

    return Icon::create($assignment_types[$type]['icon'], $role,
                        ['title' => $assignment_types[$type]['name']]);
}

/**
 * Return the language tag as used by the Vips character picker.
 */
function vips_character_picker_language($tag)
{
    static $picker_map = [
        'de-fonipa' => 'ipa',
        'de'        => 'german',
        'fr'        => 'french',
        'es'        => 'spanish',
        'pt'        => 'portuguese',
        'ro'        => 'romanian'
    ];

    return $picker_map[$tag];
}

/**
 * Return the language tag as defined in RFC 5646 for this language.
 */
function vips_rfc5646_language_tag($lang)
{
    static $rfc5646_map = [
        'ipa'        => 'de-fonipa',
        'german'     => 'de',
        'french'     => 'fr',
        'spanish'    => 'es',
        'portuguese' => 'pt',
        'romanian'   => 'ro'
    ];

    return $rfc5646_map[$lang];
}

/**
 * Formats a string XML ready: keeps Stud.IP format, replaces entities <, > and &.
 */
function vips_qti_format($string)
{
    $string = vips_filter_xml_string($string);
    $string = studip_utf8encode(Studip\Markup::apply(new StudipFormat(), $string, true));

    // close all HTML tags with EMPTY content model to create well-formed XML
    $string = preg_replace('/<(AREA|BR|HR|IMG|INPUT|PARAM)([^>]*[^/])?>/i', '<$1$2/>', $string);
    // search expression:
    // (1) <       starting angle bracket
    // (2) (...)   an HTML tag with EMPTY content model
    // (3) ([^>]*) optionally followed by any number of chars (the HTML attributes)
    // (4) >       closing angle bracket
    //
    // replace with:
    // (1) <    starting angle bracket
    // (2) $1   back reference to HTML tag from search expression
    // (3) $2   back reference to (optional) HTML attributes
    // (4) />   slash and closing angle bracket

    return $string;
}

/**
 * Returns information about the vips group a user participated or participates in.
 *
 * @param string $user_id The user id
 * @param string $assignment The assignment object
 * @return array An associative array with <code>id</code>, <code>name</code>
 *               and <code>members</code> (an array of user ids), or
 *               <code>null</code> if the user was not in a group at the given
 *               time, or <code>false</code> if a database error occured
 */
function get_user_group($user_id, $assignment)
{
    $group_obj = $assignment->getUserGroup($user_id);

    if ($group_obj) {
        $group = [
            'id'      => $group_obj->id,
            'name'    => $group_obj->name,
            'members' => []
        ];

        $members = $assignment->getGroupMembers($group_obj);

        usort($members, function($a, $b) {
            return strcoll($a->user->getFullName('no_title_rev'), $b->user->getFullName('no_title_rev'));
        });

        foreach ($members as $member) {
            $group['members'][] = $member->user_id;
        }
    }

    return $group;
}

/**
 * Get all courses with currently running exams for the given user.
 *
 * @param string $user_id The user id
 *
 * @return array    associative array of course ids and course names
 */
function get_courses_with_running_exams($user_id)
{
    $db = DBManager::get();

    $courses = [];

    $sql = "SELECT DISTINCT seminare.Seminar_id, seminare.Name, vips_assignment.id
            FROM vips_assignment
            JOIN seminar_user ON seminar_user.Seminar_id = vips_assignment.course_id
            JOIN seminare USING(Seminar_id)
            WHERE vips_assignment.type = 'exam'
              AND vips_assignment.start <= NOW()
              AND vips_assignment.end > NOW()
              AND seminar_user.user_id = '$user_id'
              AND seminar_user.status = 'autor'
            ORDER BY seminare.Name";
    $result = $db->query($sql);

    foreach ($result as $row) {
        $assignment = VipsAssignment::find($row['id']);
        $ip_range = $assignment->options['ip_range'];

        if (strlen($ip_range) > 0 && $assignment->checkIPAccess($_SERVER['REMOTE_ADDR'])) {
            $courses[$row['Seminar_id']] = $row['Name'];
        }
    }

    return $courses;
}

/**
 * Count the number of assignments that the given user can edit.
 *
 * @param string $user_id user to check
 */
function vips_count_assignments($user_id)
{
    $db = DBManager::get();

    $sql = "SELECT COUNT(*) FROM seminar_user
            JOIN vips_assignment ON Seminar_id = course_id
            WHERE user_id = ? AND status IN ('tutor', 'dozent')";
    $stmt = $db->prepare($sql);
    $stmt->execute([$user_id]);

    return $stmt->fetchColumn();
}

/**
 * Return whether or not the current user has the given status in a course.
 *
 * @param string $staus status name: 'autor', 'tutor' or 'dozent'
 * @param string $course_id course to check (defaults to current course)
 */
function vips_has_status($status, $course_id = null)
{
    global $perm, $vipsPlugin;

    return $perm->have_studip_perm($status, $course_id ?: $vipsPlugin->courseID);
}

/**
 * Check whether or not the current user has the required status in a course.
 *
 * @param string $staus required status: 'autor', 'tutor' or 'dozent'
 * @param string $course_id course to check (defaults to current course)
 * @throw AccessDeniedException if the requirement is not met, an exception is thrown
 */
function vips_require_status($status, $course_id = null)
{
    if (!vips_has_status($status, $course_id)) {
        throw new AccessDeniedException(_vips('Sie verfügen nicht über die notwendigen Rechte für diese Aktion.'));
    }
}

/**
 * Checks whether or not the current user has access to a block.
 *
 * @param VipsBlock $block The block
 * @throw AccessDeniedException If the current user doesn't have access, an exception is thrown
 */
function check_block_access($block)
{
    global $vipsPlugin;

    if ($block->course_id !== $vipsPlugin->courseID) {
        throw new AccessDeniedException(_vips('Sie haben keinen Zugriff auf diesen Block!'));
    }
}

/**
 * Checks whether or not the current user has access to a group.
 *
 * @param VipsGroup $group_id The group
 * @throw AccessDeniedException If the current user doesn't have access, an exception is thrown
 */
function check_group_access($group)
{
    global $vipsPlugin;

    if ($group->course_id !== $vipsPlugin->courseID) {
        throw new AccessDeniedException(_vips('Sie haben keinen Zugriff auf diese Gruppe!'));
    }
}

/**
 * Checks whether or not the current user has access to an assignment.
 *
 * @param VipsAssignment $assignment The assignment
 * @throw AccessDeniedException If the current user doesn't have access, an exception is thrown
 */
function check_assignment_access($assignment)
{
    global $vipsPlugin;

    if ($assignment->course_id !== $vipsPlugin->courseID) {
        throw new AccessDeniedException(_vips('Sie haben keinen Zugriff auf dieses Aufgabenblatt!'));
    }
}

/**
 * Checks whether or not the current user may copy an assignment.
 *
 * @param VipsAssignment $assignment The assignment
 * @throw AccessDeniedException If the current user doesn't have access, an exception is thrown
 */
function check_copy_assignment_access($assignment)
{
    if (!$assignment || !vips_has_status('tutor', $assignment->course_id)) {
        throw new AccessDeniedException(_vips('Sie haben keinen Zugriff auf dieses Aufgabenblatt!'));
    }
}

/**
 * Checks whether or not the current user has access to an assignment.
 * @deprecated - use check_assignment_access() instead.
 *
 * @param int $assignment_id The assignment id
 * @throw AccessDeniedException If the current user doesn't have access, an exception is thrown
 */
function check_test_access($assignment_id)
{
    $assignment = VipsAssignment::find($assignment_id);

    check_assignment_access($assignment);
}

/**
 * Checks whether or not the current user has access to an exercise.
 * @deprecated - use check_exercise_assignment() and check_assignment_access() instead.
 *
 * @param int $exercise_id The exercise id
 * @param int $assignment_id     The assignment id
 * @throw AccessDeniedException If the current user doesn't have access, an exception is thrown
 */
function check_exercise_access($exercise_id, $assignment_id)
{
    $assignment = VipsAssignment::find($assignment_id);

    check_exercise_assignment($exercise_id, $assignment);
    check_assignment_access($assignment);
}

/**
 * Checks whether or not the given exercise belongs to the assignment.
 *
 * @param int $exercise_id The exercise id
 * @param VipsAssignment $assignment     The assignment
 * @throw AccessDeniedException If the current user doesn't have access, an exception is thrown
 */
function check_exercise_assignment($exercise_id, $assignment)
{
    if (!$assignment->hasExercise($exercise_id)) {
        throw new AccessDeniedException(_vips('Sie haben keinen Zugriff auf diese Aufgabe!'));
    }
}

/**
 * Calculate the optimal input field size for text exercises.
 *
 * @param array $options array of (expected) answer options
 * @return int length of input field in characters
 */
function vips_input_size($options)
{
    $max = 0;

    foreach ($options as $option) {
        $length = strlen($option['text']);

        if ($length > $max) {
            $max = $length;
        }
    }

    $length = $max ? min(max($max, 5), 80) : 10;

    // possible sizes: 5, 10, 20, 40, 80
    return 5 << ceil(log($length / 5) / log(2));
}

/**
 * Calculate the optimal textarea height for text exercises.
 *
 * @param string $text contents of textarea
 * @return int height of textarea in lines
 */
function vips_textarea_size($text)
{
    return max(substr_count($text, "\n") + 3, 5);
}

/**
 * Return the appropriate encoded HTML for editing WYSIWYG text.
 */
function vips_wysiwyg_ready($text)
{
    return function_exists('wysiwygReady') ? wysiwygReady($text) : htmlReady($text);
}

/**
 * Return the appropriate CSS class for sortable column (if any).
 *
 * @param boolean $sort sort by this column
 * @param boolean $desc set sort direction
 */
function vips_sort_class($sort, $desc)
{
    return $sort ? ($desc ? 'sortdesc' : 'sortasc') : '';
}

/**
 * Return the configured upload limit for the context in bytes.
 *
 * @param string $status user status, defaults to 'autor'
 * @return int upload limit in bytes
 */
function vips_file_upload_limit($status = 'autor')
{
    $type = class_exists('Context') ? Context::getArtNum() : $_SESSION['SessSemName']['art_num'];

    if (!isset($GLOBALS['UPLOAD_TYPES'][$type])) {
        $type = 'default';
    }

    return $GLOBALS['UPLOAD_TYPES'][$type]['file_sizes'][$status];
}

/**
 * Render a generic page chooser selector. The first occurence of '%d'
 * in the URL is replaced with the selected page number.
 *
 * @param string $url   URL for one of the pages
 * @param string $count total number of entries
 * @param string $page  current page to display
 * @param string $page_size page size (defaults to system default)
 */
function vips_page_chooser($url, $count, $page, $page_size = null)
{
    $template = $GLOBALS['template_factory']->open('shared/pagechooser');
    $template->num_postings = $count;
    $template->page = $page;
    $template->perPage = $page_size ?: get_config('ENTRIES_PER_PAGE');
    $template->pagelink = str_replace('%%25d', '%d', str_replace('%', '%%', $url));

    return $template->render();
}
?>
