<?php
/*
 * tb_exercise.php - Vips plugin for Stud.IP
 * Copyright (c) 2003-2005  Erik Schmitt, Philipp Hügelmeyer
 * Copyright (c) 2005-2006  Christa Deiwiks
 * Copyright (c) 2006-2009  Elmar Ludwig, Martin Schröder
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

Exercise::addExerciseType(_vips('Text Box'), 'tb_exercise', 'text-area');

/**
 * XXX detailed description
 *
 * @author    XXX
 * @copyright XXX
 * @abstract
 */
class tb_exercise extends Exercise
{
    /**
     * Initialize this instance from the current request environment.
     */
    public function initFromRequest($request)
    {
        parent::initFromRequest($request);

        $this->task['answers'][0] = [
            'text'  => Studip\Markup::purifyHtml(trim($request['answer_0'])),
            'score' => 1
        ];

        $this->task['template'] = trim($request['answer_default']);
        $this->options['lang']  = $request['character_picker'];
        $this->task['compare']  = $request['answer_distance'];

        if ($request['file_upload']) {
            $this->options['file_upload'] = 1;
        }
    }

    /**
     * Exercise handler to be called when a solution is corrected.
     */
    function correctSolutionAction($controller, $solution)
    {
        $commented_solution = trim(Request::get('commented_solution'));
        $commented_solution = Studip\Markup::purifyHtml($commented_solution);

        if ($commented_solution !== $solution->response[0]) {
            $solution->commented_solution = $commented_solution;
        }

        if (Request::submitted('delete_commented_solution')) {
            $solution->commented_solution = NULL;
            $solution->store();

            PageLayout::postSuccess(_vips('Die kommentierte Lösung wurde gelöscht.'));
        }
    }

    /**
     * Evaluates a student's solution for the individual items in this
     * exercise. Returns an array of ('points' => float, 'safe' => boolean).
     *
     * @param solution The solution XML string as returned by responseFromRequest().
     */
    function evaluateItems($solution)
    {
        $result = [];

        $musterLoesung  = $this->task['answers'][0]['text'];
        $answerDefault  = $this->task['template'];

        $studentSolution = $solution->response;
        $studentSolution = $studentSolution[0];

        $similarity = 0; // between 0 and 1

        $answerDefault   = normalizeText($answerDefault, true);
        $studentSolution = normalizeText($studentSolution, true);
        $musterLoesung   = normalizeText($musterLoesung, true);

        if ($studentSolution == '' || $studentSolution == $answerDefault) {
            $result[] = ['points' => 0, 'safe' => empty($solution->files)];
            return $result;
        }

        if ($musterLoesung == $studentSolution) {
            $similarity = 1;
        } else if ($this->task['compare'] == 'levenshtein') {
            // similar_text, returns the number of matching characters
            // and not levenshtein, returns the number of insert, replace
            // and delete operations needed to transform str1 into str2.

            $divisor_default_student = max(strlen($answerDefault), strlen($studentSolution));
            $divisor_default_sample  = max(strlen($answerDefault), strlen($musterLoesung));
            $divisor_student_sample  = max(strlen($studentSolution), strlen($musterLoesung));

            $distance_student_sample = similar_text($studentSolution, $musterLoesung) / $divisor_student_sample;

            if ($answerDefault == $musterLoesung) {
                $similarity = $distance_student_sample;
            } else {
                $distance_default_student = similar_text($answerDefault, $studentSolution) / $divisor_default_student;
                $distance_default_sample  = similar_text($answerDefault, $musterLoesung) / $divisor_default_sample;
                $similarity               = min((1 - $distance_default_student) / (1 - $distance_default_sample) * $distance_student_sample, 0.9);
            }
        } else if ($this->task['compare'] == 'soundex') {
            // Soundex-values: four character string
            // [0] first letter of encoded expression
            // [1]-[3] digits, somehow representing the sound value

            $levenshtein = levenshtein(soundex($musterLoesung), soundex($studentSolution));

            if (soundex($musterLoesung) == soundex($studentSolution)) {
                $similarity = 0.8;
            } else if ($levenshtein == 1) {
                $similarity = 0.6;
            } else if ($levenshtein == 2) {
                $similarity = 0.4;
            } else if ($levenshtein == 3) {
                $similarity = 0.2;
            } else {// $levenshtein == 4
                $similarity = 0;
            }
        }

        $result[] = ['points' => $similarity, 'safe' => $similarity == 1];

        return $result;
    }

    /**
     * Construct a new solution object from the request post data.
     */
    public function getSolutionFromRequest($request, $files = NULL)
    {
        global $vipsPlugin;

        $solution = parent::getSolutionFromRequest($request, $files);
        $upload = $files['upload'];

        if ($this->options['file_upload']) {
            if ($upload['error'] == UPLOAD_ERR_NO_FILE) {
                // do nothing
            } else if ($upload['error']) {
                PageLayout::postError(_vips('Fehler beim Hochladen der Datei.'));
            } else if ($upload['size'] == 0) {
                PageLayout::postError(_vips('Die hochgeladene Datei ist leer.'));
            } else if ($upload['size'] > vips_file_upload_limit()) {
                PageLayout::postError(_vips('Die hochgeladene Datei ist zu groß.'));
            } else {
                $file = VipsFile::createWithFile($upload['name'], $upload['tmp_name'], $vipsPlugin->userID);
            }

            if (is_array($request['file_ids'])) {
                foreach ($request['file_ids'] as $file_id) {
                    $old_file = VipsFile::find($file_id);

                    if (!$file || $old_file->name !== $file->name) {
                        $solution->addFile($old_file);
                    }
                }
            }

            if ($file) {
                $solution->addFile($file);
            }
        }

        return $solution;
    }

    /**
     * Trigger download of uploaded file for a selected solution.
     */
    public function download_action($controller)
    {
        global $vipsPlugin;

        vips_require_status('autor');

        $assignment_id = Request::int('assignment_id');
        $assignment    = VipsAssignment::find($assignment_id);
        $solver_id     = Request::option('solver_id');
        $file_id       = Request::option('file_id');

        check_exercise_assignment($this->id, $assignment);
        check_assignment_access($assignment);

        if (!vips_has_status('tutor')) {
            $solver_id = $vipsPlugin->userID;
        }

        $solution = $assignment->getSolution($solver_id, $this->id);
        $file_ref = VipsFileRef::find([$file_id, $solution->id]);
        $file = $file_ref->file;

        header('Content-Type: ' . $file->mime_type);
        header('Content-Disposition: attachment; ' . vips_encode_header_parameter('filename', $file->name));
        header('Content-Length: ' . $file->size);

        readfile($file->getFilePath());
        die();
    }

    /**
     * Trigger download of all uploaded files for a selected solution.
     */
    public function download_zip_action($controller)
    {
        global $vipsPlugin;

        vips_require_status('autor');

        $assignment_id = Request::int('assignment_id');
        $assignment    = VipsAssignment::find($assignment_id);
        $solver_id     = Request::option('solver_id');

        check_exercise_assignment($this->id, $assignment);
        check_assignment_access($assignment);

        if (!vips_has_status('tutor')) {
            $solver_id = $vipsPlugin->userID;
        }

        $solution = $assignment->getSolution($solver_id, $this->id);
        $filename = 'solution_' . $solution->id . '.zip';
        $zipfile = $GLOBALS['TMP_PATH'] . '/' . $filename;
        $zip = new ZipArchive();

        if (!$zip->open($zipfile, ZipArchive::CREATE)) {
            throw new Exception(_vips('ZIP-Archiv konnte nicht erzeugt werden.'));
        }

        foreach ($solution->files as $file) {
            $zip->addFile($file->getFilePath(), studip_utf8encode($file->name));
        }

        $zip->close();

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; ' . vips_encode_header_parameter('filename', $filename));
        header('Content-Length: ' . filesize($zipfile));

        readfile($zipfile);
        die();
    }

    /**
     * Return the list of keywords used for text export. The first keyword
     * in the list must be the keyword for the exercise type.
     */
    public static function getTextKeywords()
    {
        return ['Offene Frage', 'Eingabehilfe', 'Abgleich', 'Vorgabe', 'Antwort'];
    }

    /**
     * Initialize this instance from the given text data array.
     */
    public function initText($exercise)
    {
        parent::initText($exercise);

        foreach ($exercise as $tag) {
            if (key($tag) === 'Eingabehilfe') {
                $this->options['lang'] = current($tag);
            }

            if (key($tag) === 'Abgleich') {
                if (current($tag) === 'Levenshtein') {
                    $this->task['compare'] = 'levenshtein';
                }
            }

            if (key($tag) === 'Vorgabe') {
                $this->task['template'] = current($tag);
            }

            if (key($tag) === 'Antwort') {
                $this->task['answers'][0] = [
                    'text'  => current($tag),
                    'score' => 1
                ];
            }
        }
    }

    /**
     * Initialize this instance from the given SimpleXMLElement object.
     */
    public function initXML($exercise)
    {
        parent::initXML($exercise);

        foreach ($exercise->items->item->answers->answer as $answer) {
            if ($answer['score'] == '1') {
                $this->task['answers'][0] = [
                    'text'  => studip_utf8decode(trim($answer)),
                    'score' => 1
                ];
            } else if ($answer['default']) {
                $this->task['template'] = studip_utf8decode(trim($answer));
            }
        }

        $this->options['lang'] = vips_character_picker_language((string) $exercise['lang']);

        switch ($exercise->items->item->{'evaluation-hints'}->similarity) {
            case 'levenshtein':
                $this->task['compare'] = 'levenshtein';
        }
    }



    /**
     * Export this exercise to plain text format.
     */
    function exportText($exercise_tag = NULL)
    {
        $result = parent::exportText($exercise_tag ?: 'Offene Frage');

        $answer_text = $this->task['answers'][0]['text'];

        if ($this->options['lang']) {
            $result .= 'Eingabehilfe: '.$this->options['lang']."\n";
        }

        if ($this->task['compare'] == 'levenshtein') {
            $result .= "Abgleich: Levenshtein\n";
        }

        if ($this->task['template'] != '') {
            $result .= "Vorgabe:\n";
            $result .= $this->task['template']."\n";
            $result .= "\\Vorgabe\n";
        }

        if ($answer_text != '') {
            $result .= "Antwort:\n";
            $result .= $answer_text."\n";
            $result .= "\\Antwort\n";
        }

        return $result;
    }
}
?>
