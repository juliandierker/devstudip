<?php
/*
 * pl_exercise.php - Vips plugin for Stud.IP
 * Copyright (c) 2003-2005  Erik Schmitt, Philipp Hügelmeyer
 * Copyright (c) 2005-2006  Christa Deiwiks
 * Copyright (c) 2006-2009  Elmar Ludwig, Martin Schröder
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

Exercise::addExerciseType(_vips('Prolog'), 'pl_exercise', 'program-prolog');

/**
 * XXX detailed description
 *
 * @author    Philipp Hügelmeyer
 * @copyright XXX
 * @abstract
 */
class pl_exercise extends Exercise
{
    /**
     * Initialize this instance from the current request environment.
     */
    public function initFromRequest($request)
    {
        parent::initFromRequest($request);

        $this->parsePrologText(trim($request['answer_0']));
        $this->task['template'] = trim($request['answer_default']);
        $this->task['input']    = trim($request['query_default']);
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
        $output = $this->evaluateQuery($solution);

        list($score1,$rest) = explode('<Score>', $output);
        list($score, $rest) = explode('</Score>', $rest);

        list($validity1,$rest) = explode('<Validity>', $output);
        list($validity, $rest) = explode('</Validity>', $rest);

        list($output1,$rest) = explode('<Output>', $output);
        list($output, $rest) = explode('</Output>', $rest);

        $result[] = [
            'points'   => $score,
            'safe'     => $validity == 1,
            'validity' => $validity,
            'output'   => trim($output)
        ];

        return $result;
    }

    /**
     * Exercise handler to be called when a solution is submitted.
     */
    function submitSolutionAction($controller, $solution)
    {
        $flash = Trails_Flash::instance();

        if (Request::submitted('pl_exercise_mode_query') || Request::submitted('pl_exercise_mode_querynext')) {
            $flash['pl_answer'] = $solution->response[0];
            $flash['pl_query'] = trim(Request::get('pl_query'));
            $flash['pl_count'] = Request::submitted('pl_exercise_mode_querynext') ? Request::int('pl_count') + 1 : 1;
            $flash['pl_out'] = $this->evaluateQuery($solution, $flash['pl_query'], $flash['pl_count']);
        } else if (Request::submitted('pl_exercise_mode_upload')) {
            // if there is an uploaded solution, show that one
            $flash['pl_answer'] = $this->uploadSolution($_FILES);
            $flash['pl_query'] = trim(Request::get('pl_query'));
        } else if (Request::submitted('pl_exercise_mode_download')) {
            $this->downloadSolution($controller, $solution);
        }
    }

    /**
     * Exercise handler to be called when a solution is corrected.
     */
    function correctSolutionAction($controller, $solution)
    {
        $flash = Trails_Flash::instance();
        $commented_solution = trim(Request::get('commented_solution'));

        if ($commented_solution !== $solution->response[0]) {
            $solution->commented_solution = $commented_solution;
        }

        if (Request::submitted('delete_commented_solution')) {
            $solution->commented_solution = NULL;
            $solution->store();

            PageLayout::postSuccess(_vips('Die kommentierte Lösung wurde gelöscht.'));
        }

        if (Request::submitted('pl_exercise_mode_query') || Request::submitted('pl_exercise_mode_querynext')) {
            $solution->response = [$commented_solution];
            $flash['pl_answer'] = $solution->response[0];
            $flash['pl_query'] = trim(Request::get('pl_query'));
            $flash['pl_count'] = Request::submitted('pl_exercise_mode_querynext') ? Request::int('pl_count') + 1 : 1;
            $flash['pl_out'] = $this->evaluateQuery($solution, $flash['pl_query'], $flash['pl_count']);
        } else if (Request::submitted('pl_exercise_mode_eval')) {
            $solution->response = [$commented_solution];
            $evaluation = $this->evaluateItems($solution);
            $flash['pl_answer'] = $solution->response[0];
            $flash['pl_query'] = trim(Request::get('pl_query'));
            $flash['pl_out'] = $evaluation[0]['output'];
        } else if (Request::submitted('pl_exercise_mode_download')) {
            $this->downloadSolution($controller, $solution);
        }
    }



    /**
     * Prepares the currently displayed solution for downloading.
     *
     * @return Nothing. The code can directly be downloaded or viewed.
     */
    function downloadSolution($controller, $solution)
    {
        $solution_text = $solution->commented_solution ?: $solution->response[0];
        $solution_text = studip_utf8encode($solution_text);
        $filename = preg_replace('/\W/', '_', $this->title);

        header('Content-Type: text/plain; charset=UTF-8');
        header('Content-Disposition: attachment; ' . vips_encode_header_parameter('filename', $filename.'.txt'));
        header('Content-Length: '.strlen($solution_text));

        echo $solution_text;
        die();
    }



    /**
     * Uploads a file as solution.
     *
     * @return The file content
     */
    function uploadSolution($files)
    {
        if ($files['pl_userfile'] && is_uploaded_file($files['pl_userfile']['tmp_name'])) {
            $solution_text = file_get_contents($files['pl_userfile']['tmp_name']);
        } else {
            $solution_text = NULL;
            PageLayout::postSuccess(_vips('Es wurde keine Datei zum Hochladen ausgewählt.'));
        }

        return studip_utf8decode($solution_text);
    }



    /****************************************************************************************/
    /* Queries the query inserted by the student with the corresponding code of the exercise.
    /****************************************************************************************/

    function evaluateQuery($solution, $query = null, $p_number = 1)
    {
        $username = get_solver_name($solution->user_id);

        $musterloesung = $this->getPrologText();
        $answerDefault = $this->task['template'];

        $response = $solution->response;
        $answer = $response[0];

        $post_fields = http_build_query([
            'adm' => 0,
            'srv' => 'prolog',
            'usr' => $username,
            'crs' => $solution->assignment->course_id,
            'mgc' => get_config('VIPS_VEA_SERVER_KEY'),
            'par' => $p_number,  // number
            'typ' => isset($query) ? 'query' : 'eval',
            'exc' => $answer,
            'tst' => $musterloesung,
            'ini' => $answerDefault,
            'qry' => $query
        ]);

        $ch = curl_init(get_config('VIPS_VEA_SERVER_URL'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($ch);
        $error = curl_error($ch);

        curl_close($ch);

        return trim($output);
    }

    /**
     * Return the list of keywords used for text export. The first keyword
     * in the list must be the keyword for the exercise type.
     */
    public static function getTextKeywords()
    {
        return ['PL-Frage', 'Vorgabe', 'Anfrage', 'Antwort'];
    }

    /**
     * Initialize this instance from the given text data array.
     */
    public function initText($exercise)
    {
        parent::initText($exercise);

        foreach ($exercise as $tag) {
            if (key($tag) === 'Vorgabe') {
                $this->task['template'] = current($tag);
            }

            if (key($tag) === 'Anfrage') {
                $this->task['input'] = current($tag);
            }

            if (key($tag) === 'Antwort') {
                $this->parsePrologText(current($tag));
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
                $this->parsePrologText(studip_utf8decode(trim($answer)));
            } else if ($answer['default']) {
                $this->task['template'] = studip_utf8decode(trim($answer));
            }
        }

        $input_data = $exercise->items->item->{'evaluation-hints'}->{'input-data'};
        $this->task['input'] = studip_utf8decode(trim($input_data));
    }



    /**
     * Export this exercise to plain text format.
     */
    function exportText($exercise_tag = NULL)
    {
        $result = parent::exportText($exercise_tag ?: 'PL-Frage');

        $answer_text = $this->getPrologText();

        if ($this->task['template'] != '') {
            $result .= "Vorgabe:\n";
            $result .= $this->task['template']."\n";
            $result .= "\\Vorgabe\n";
        }

        if ($this->task['input'] != '') {
            $result .= 'Anfrage: '.$this->task['input']."\n";
        }

        if ($answer_text != '') {
            $result .= "Antwort:\n";
            $result .= $answer_text."\n";
            $result .= "\\Antwort\n";
        }

        return $result;
    }

    /**
     * Exports the exercise in Proforma XML format.
     */
    function export_proforma_action($controller)
    {
        global $vipsTemplateFactory;

        $assignment_id = Request::int('assignment_id');
        $assignment = VipsAssignment::find($assignment_id);

        vips_require_status('tutor');
        check_exercise_assignment($this->id, $assignment);
        check_assignment_access($assignment);

        $template = $vipsTemplateFactory->open('exercises/proforma_pl_exercise');
        $template->exercise = $this;

        $controller->set_content_type('text/xml; charset=UTF-8');
        header('Content-Disposition: attachment; ' . vips_encode_header_parameter('filename', $this->title.'.xml'));

        $controller->render_template($template);
    }

    /**
     * Create a template for editing an exercise.
     *
     * @return The template
     */
    function getEditTemplate($assignment)
    {
        if ($this->id) {
            $widget = new ExportWidget();
            $widget->addLink(_vips('Aufgabe exportieren (ProFormA)'),
                vips_link('sheets/relay/export_proforma', ['assignment_id' => $assignment->id, 'exercise_id' => $this->id]),
                Icon::create('download'));
            Sidebar::get()->addWidget($widget);
        }

        return parent::getEditTemplate($assignment);
    }

    /**
     * Creates a template for solving a pl_exercise.
     *
     * @return The template
     */
    function getSolveTemplate($solution, $assignment, $user_id)
    {
        $flash = Trails_Flash::instance();

        if (isset($flash['pl_answer'])) {
            if (!$solution) {
                $solution = new VipsSolution();
            }

            // if there is an edited solution, show that one
            $solution->response = [$flash['pl_answer']];
            $this->task['input'] = $flash['pl_query'];
        }

        $template = parent::getSolveTemplate($solution, $assignment, $user_id);
        $template->pl_count = $flash['pl_count'];
        $template->pl_out   = $flash['pl_out'];

        return $template;
    }



    /**
     * Creates a template for correcting a pl_exercise.
     *
     * @return The template
     */
    function getCorrectionTemplate($solution)
    {
        $flash = Trails_Flash::instance();

        if (isset($flash['pl_answer'])) {
            // if there is an edited solution, show that one
            $solution->commented_solution = $flash['pl_answer'];
            $this->task['input'] = $flash['pl_query'];
        }

        $template = parent::getCorrectionTemplate($solution);
        $template->pl_count     = $flash['pl_count'];
        $template->pl_out       = $flash['pl_out'];

        return $template;
    }



    /**
     * Return prolog exercise text for editing by the lecturer.
     */
    function getPrologText()
    {
        $result = '';

        foreach ($this->task['answers'] as $answer) {
            if ($result != '') {
                $result .= '%or';
            }

            $result .= $answer['text'];

            if ($answer['score'] != 1) {
                // $result .= '%s ' . $answer['score'] . "\n";
            }
        }

        if ($this->task['test']) {
            $result .= '%T' . $this->task['test'];
        }

        return $result;
    }

    /**
     * Convert prolog exercise text to the internal representation.
     */
    function parsePrologText($program)
    {
        list($solutions, $test) = explode('%T', $program);
        $solutions = explode('%or', $solutions);

        foreach ($solutions as $solution) {
            if (preg_match('/^%s(.*)/m', $solution, $matches)) {
                $score = (float) $matches[1];
                // $solution = preg_replace('/^%s.*/m', '', $solution);
            } else {
                $score = 1;
            }

            $this->task['answers'][] = [
                'text'  => $solution,
                'score' => $score
            ];
        }

        $this->task['test'] = $test;
    }
}
?>
