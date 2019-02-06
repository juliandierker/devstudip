<?php
/*
 * mc_exercise.php - Vips plugin for Stud.IP
 * Copyright (c) 2003-2005  Erik Schmitt, Philipp Hügelmeyer
 * Copyright (c) 2005-2006  Christa Deiwiks
 * Copyright (c) 2006-2009  Elmar Ludwig, Martin Schröder
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

Exercise::addExerciseType(_vips('Multiple Choice'), 'mc_exercise', 'choice-multiple');

/**
 * XXX detailed description
 *
 * @author    XXX
 * @copyright XXX
 * @abstract
 */
class mc_exercise extends Exercise
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
                    'score' => (int) $request['correct'][$i]
                ];
            }
        }
    }

    /**
     * Compute the default maximum points which can be reached in this
     * exercise, dependent on the number of answers.
     */
    public function itemCount()
    {
        return count($this->task['answers']);
    }

    /**
     * Shuffles the answer alternatives.
     *
     * @param $user_id is used for initialising the randomiser.
     */
    function shuffleAnswers($user_id)
    {
        $random_order = range(0, $this->itemCount() - 1);
        srand(crc32($user_id));
        shuffle($random_order);

        $answer_temp = [];
        foreach ($random_order as $index) {
            $answer_temp[$index] = $this->task['answers'][$index];
        }
        $this->task['answers'] = $answer_temp;
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

        foreach ($this->task['answers'] as $i => $answer) {
            if ($response[$i] == '-1') {
                $points = NULL;
            } else {
                $points = $response[$i] == $answer['score'] ? 1 : 0;
            }

            $result[] = ['points' => $points, 'safe' => true];
        }

        return $result;
    }

    /**
     *    Evaluates a solution.
     *
     *    @abstract
     *    @access public
     *    @return float (from 0 to 1) as an % value
     */
    function evaluate($solution) {
        $results = $this->evaluateItems($solution);
        $mc_auswertung = $solution->assignment->options['evaluation_mode'];
        $wrong_answers = 0;
        $points = 0;
        $safe   = true;

        foreach ($results as $item) {
            if ($item['points'] === 0) {
                ++$wrong_answers;
            }

            $points += $item['points'];
            $safe &= $item['safe'];     // only true if all items are marked as 'safe'
        }

        if ($mc_auswertung == 1 || $mc_auswertung == 2) {
            // abzug bei falscher antwort
            $points -= $wrong_answers;
        } else if ($mc_auswertung == 3 && $wrong_answers > 0) {
            $points = 0;
        }

        if ($points < 0 && $mc_auswertung != 2) {
            $points = 0;
        }

        $percent = $points / max(count($results), 1);

        return ['percent' => $percent, 'safe' => $safe];
    }

    /**
     * Return the list of keywords used for text export. The first keyword
     * in the list must be the keyword for the exercise type.
     */
    public static function getTextKeywords()
    {
        return ['MC-Frage', '[+~]?Antwort'];
    }

    /**
     * Initialize this instance from the given text data array.
     */
    public function initText($exercise)
    {
        parent::initText($exercise);

        foreach ($exercise as $tag) {
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
                'score' => (int) $answer['score']
            ];
        }
    }

    /**
     * Export this exercise to plain text format.
     */
    function exportText($exercise_tag = NULL)
    {
        $result = parent::exportText($exercise_tag ?: 'MC-Frage');

        if ($this instanceof mco_exercise) {
            $result .= 'Auswahl: '.$this->task['choices'][1].' / '.$this->task['choices'][0]."\n";
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
     * Creates a template for editing a mc_exercise.
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
}
?>
