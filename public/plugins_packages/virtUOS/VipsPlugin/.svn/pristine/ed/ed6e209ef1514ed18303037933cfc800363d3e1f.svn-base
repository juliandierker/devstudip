<?php
/*
 * me_exercise.php - Vips plugin for Stud.IP
 * Copyright (c) 2003-2005  Erik Schmitt, Philipp Hügelmeyer
 * Copyright (c) 2005-2006  Christa Deiwiks
 * Copyright (c) 2006-2009  Elmar Ludwig, Martin Schröder
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

require_once 'algebraq/question.php';

Exercise::addExerciseType(_vips('Mathematischer Ausdruck'), 'me_exercise', 'math-expression');

/**
 * Verifies wether two expressions are mathematical equivalent
 *
 * @author    XXX
 * @copyright XXX
 * @abstract
 */
class me_exercise extends Exercise
{
    /**
     * Initialize a new instance of this class.
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        if (!isset($id)) {
            $this->task['variables'] = [];
        }
    }

    /**
     * Initialize this instance from the current request environment.
     */
    public function initFromRequest($request)
    {
        parent::initFromRequest($request);

        $this->task['answers'][0] = [
            'text'  => trim($request['answer_0']),
            'score' => 1
        ];

        $this->task['variables'] = [];

        foreach ($request['var'] as $i => $var) {
            if (trim($var) != '') {
                $this->task['variables'][] = [
                    'name' => trim($var),
                    'min'  => (float) $request['min'][$i],
                    'max'  => (float) $request['max'][$i]
                ];
            }
        }
    }

    /**
     * Evaluates a student's solution
     *
     * @param solution The solution XML string as returned by responseFromRequest().
     */
    function evaluateItems($solution)
    {
        $result = [];
        $response = $solution->response;

        // default, if no variable is declared by user
        $variables = $this->task['variables'] ?: [['name' => 'default', 'min' => -100, 'max' => 100]];

        // moodle-stuff
        $eval = new qtype_algebra_question();
        $eval->compareby = 'eval';  // Auswertungsmethode
        $eval->nchecks = 10;        // Anzahl der zufälligen Checks
        $eval->tolerance = 0.00001; // Toleranz für Wertevergleich

        foreach ($variables as $var) {
            $variable = new stdClass();
            $variable->name = $var['name'];
            $variable->min  = $var['min'];
            $variable->max  = $var['max'];
            $eval->variables[] = $variable;
        }

        $equiv = $eval->is_same_response(['answer' => $this->task['answers'][0]['text']],
                                         ['answer' => $response[0]]);
        $result[] = ['points' => $equiv ? 1 : 0, 'safe' => true];

        return $result;
    }

    /**
     * Return the list of keywords used for text export. The first keyword
     * in the list must be the keyword for the exercise type.
     */
    public static function getTextKeywords()
    {
        return ['Mathematischer Ausdruck', 'Musterloesung', 'Variablen'];
    }

    /**
     * Initialize this instance from the given text data array.
     */
    public function initText($exercise)
    {
        parent::initText($exercise);

        foreach ($exercise as $tag) {
            if (key($tag) === 'Musterloesung') {
                $this->task['answers'][0] = [
                    'text'  => current($tag),
                    'score' => 1
                ];
            }

            if (key($tag) === 'Variablen') {
                foreach (explode("\n", current($tag)) as $line) {
                    list($name, $min, $max) = explode(',', $line);

                    $this->task['variables'][] = [
                        'name' => trim($name),
                        'min'  => (float) $min,
                        'max'  => (float) $max
                    ];
                }
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
            $this->task['answers'][] = [
                'text'  => studip_utf8decode(trim($answer)),
                'score' => (int) $answer['score']
            ];
        }

        // extract variables and limits
        foreach ($exercise->items->item->{'evaluation-hints'}->{'input-data'} as $data) {
            $values = explode(':', trim($data));

            $this->task['variables'][] = [
                'name' => studip_utf8decode(trim($data['name'])),
                'min'  => (float) $values[0],
                'max'  => (float) $values[1]
            ];
        }
    }

    /**
     * Export this exercise to plain text format.
     */
    function exportText($exercise_tag = NULL)
    {
        $result = parent::exportText($exercise_tag ?: 'Mathematischer Ausdruck');

        $musterloesung = $this->task['answers'][0]['text'];

        if ($musterloesung != '') {
            $result .= "Musterloesung:\n";
            $result .= $musterloesung."\n";
            $result .= "\\Musterloesung\n";
        }

        if (count($this->task['variables'])) {
            $result .= "Variablen:\n";
            foreach ($this->task['variables'] as $variable) {
                $result .= $variable['name'].','.$variable['min'].','.$variable['max']."\n";
            }
            $result .= "\\Variablen\n";
        }

        return $result;
    }

    /**
     * Creates a template for editing a me_exercise.
     *
     * @return The template for editing this exercise
     */
    function getEditTemplate($assignment)
    {
        // validate answer
        if ($this->task['answers'][0]['text'] != '') {
            $question = new qtype_algebra_question();
            $test_answer = $question->parse_expression($this->task['answers'][0]['text'])->str();

            if ($test_answer == '') {
                PageLayout::postError(_vips('Die Musterlösung ist kein gültiger mathematischer Ausdruck.'));
            }
        }

        return parent::getEditTemplate($assignment);
    }

    /**
     * Create a template for viewing an exercise.
     *
     * @return The template
     */
    public function getViewTemplate($view, $solution, $assignment, $user_id)
    {
        $template = parent::getViewTemplate($view, $solution, $assignment, $user_id);

        if ($solution) {
            $template->results = $this->evaluateItems($solution);
        }

        return $template;
    }
}
