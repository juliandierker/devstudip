<?php
/*
 * lt_exercise.php - Vips plugin for Stud.IP
 * Copyright (c) 2003-2005  Erik Schmitt, Philipp Hügelmeyer
 * Copyright (c) 2005-2006  Christa Deiwiks
 * Copyright (c) 2006-2011  Elmar Ludwig, Martin Schröder
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

Exercise::addExerciseType(_vips('Freie Antwort'), 'lt_exercise', 'text-line');

/**
 * XXX detailed description
 *
 * @author    XXX
 * @copyright XXX
 * @abstract
 */
class lt_exercise extends Exercise
{
    /**
     * Initialize this instance from the current request environment.
     */
    public function initFromRequest($request)
    {
        parent::initFromRequest($request);

        foreach ($request['answer'] as $i => $answer) {
            if (trim($answer) != '') {
                $this->task['answers'][] = [
                    'text'  => trim($answer),
                    'score' => (float) $request['correct'][$i]
                ];
            }
        }

        $this->options['lang'] = $request['character_picker'];
        $this->task['compare'] = $request['answer_distance'];
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

        $response = $solution->response;
        $studentSolution = $response[0];

        $similarity = 0;
        $safe = false;
        $studentSolution = normalizeText($studentSolution, true);

        if ($studentSolution == '') {
            $result[] = ['points' => 0, 'safe' => true];
            return $result;
        }

        foreach ($this->task['answers'] as $answer) {
            $musterLoesung = normalizeText($answer['text'], true);
            $divisor = max(strlen($musterLoesung), strlen($studentSolution));
            $similarity_temp = 0;

            if ($musterLoesung == $studentSolution) {
                $similarity_temp = 1;
            } else if ($this->task['compare'] == 'levenshtein') {  // Levenshtein-Zeichen
                $levenshtein = levenshtein(substr($studentSolution, 0, 255), substr($musterLoesung, 0, 255)) / $divisor;
                $similarity_temp = 1 - $levenshtein;
            } else if ($this->task['compare'] == 'soundex') {  // Soundex-Aussprache
                $levenshtein = levenshtein(soundex($musterLoesung), soundex($studentSolution));

                if (soundex($musterLoesung) == soundex($studentSolution)) {
                    $similarity_temp = 0.8;
                } else if ($levenshtein == 1) {
                    $similarity_temp = 0.6;
                } else if ($levenshtein == 2) {
                    $similarity_temp = 0.4;
                } else if ($levenshtein == 3) {
                    $similarity_temp = 0.2;
                } else {// $levenshtein == 4
                    $similarity_temp = 0;
                }
            }

            if ($answer['score'] == 1) {  // correct
                if ($similarity_temp > $similarity) {
                    $similarity = $similarity_temp;
                    $safe = $similarity_temp == 1;
                }
            } else if ($answer['score'] == 0.5) {  // half correct
                if ($similarity_temp > $similarity) {
                    $similarity = $similarity_temp * 0.5;
                    $safe = $similarity_temp == 1;
                }
            } else if ($similarity_temp == 1) {  // false
                $similarity = 0;
                $safe = true;
                break;
            }
        }

        $result[] = ['points' => $similarity, 'safe' => $safe];

        return $result;
    }

    /**
     * Return the list of keywords used for text export. The first keyword
     * in the list must be the keyword for the exercise type.
     */
    public static function getTextKeywords()
    {
        return ['Frage', 'Eingabehilfe', 'Abgleich', '[+~]?Antwort'];
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
                } else if (current($tag) === 'Soundex') {
                    $this->task['compare'] = 'soundex';
                }
            }

            if (key($tag) === '+Antwort') {
                $this->task['answers'][] = [
                    'text'  => current($tag),
                    'score' => 1
                ];
            } else if (key($tag) === '~Antwort') {
                $this->task['answers'][] = [
                    'text'  => current($tag),
                    'score' => 0.5
                ];
            } else if (key($tag) === 'Antwort') {
                $this->task['answers'][] = [
                    'text'  => current($tag),
                    'score' => 0
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
            $this->task['answers'][] = [
                'text'  => studip_utf8decode(trim($answer)),
                'score' => (float) $answer['score']
            ];
        }

        $this->options['lang'] = vips_character_picker_language((string) $exercise['lang']);

        switch ($exercise->items->item->{'evaluation-hints'}->similarity) {
            case 'levenshtein':
            case 'soundex':
                $this->task['compare'] = (string) $exercise->items->item->{'evaluation-hints'}->similarity;
        }
    }

    /**
     * Export this exercise to plain text format.
     */
    function exportText($exercise_tag = NULL)
    {
        $result = parent::exportText($exercise_tag ?: 'Frage');

        if ($this->options['lang']) {
            $result .= 'Eingabehilfe: '.$this->options['lang']."\n";
        }

        if ($this->task['compare'] == 'levenshtein') {
            $result .= "Abgleich: Levenshtein\n";
        } else if ($this->task['compare'] == 'soundex') {
            $result .= "Abgleich: Soundex\n";
        }

        foreach ($this->task['answers'] as $answer) {
            if ($answer['score'] == 1) {
                $result .= '+';
            } else if ($answer['score'] == 0.5) {
                $result .= '~';
            }

            $result .= 'Antwort: '.$answer['text']."\n";
        }

        return $result;
    }

    /**
     * Creates a template for editing a lt_exercise.
     *
     * @return The template for editing this exercise
     */
    function getEditTemplate($assignment)
    {
        if (!$this->task['answers']) {
            $this->task['answers'] = array_fill(0, 5, ['text' => '', 'score' => 0]);
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

    /**
     * Returns all the correct answers in an array.
     */
    function correctAnswers()
    {
        $answers = [];

        foreach ($this->task['answers'] as $answer) {
            if ($answer['score'] == 1) {
                $answers[] = $answer['text'];
            }
        }

        return $answers;
    }
}
?>
