<?php
/*
 * rh_exercise.php - Vips plugin for Stud.IP
 * Copyright (c) 2006-2009  Elmar Ludwig, Martin Schröder
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

/*
 * Zuordnungsaufgabe. Eine Liste von Texten, die einander zugeordnet werden müssen.
 */

Exercise::addExerciseType(_vips('Zuordnung'), 'rh_exercise', 'matching');

class rh_exercise extends Exercise
{
    /**
     * Initialize a new instance of this class.
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        if (!isset($id)) {
            $this->task['groups'] = [];
        }
    }

    /**
     * Initialize this instance from the current request environment.
     */
    public function initFromRequest($request)
    {
        parent::initFromRequest($request);

        $id      = $request['id'];
        $_id     = $request['_id'];

        $this->task['groups'] = [];

        foreach ($request['default'] as $i => $group) {
            $answer = $request['answer'][$i];

            if (trim($group) != '' || trim($answer) != '') {
                $this->task['answers'][] = [
                    'id' => (int) $id[$i],
                    'text' => trim($answer),
                    'group' => count($this->task['groups'])
                ];
                $this->task['groups'][] = trim($group);
            }
        }

        // list of answers that must remain unassigned
        foreach ($request['_answer'] as $i => $answer) {
            if (trim($answer) != '') {
                $this->task['answers'][] = [
                    'id' => (int) $_id[$i],
                    'text' => trim($answer),
                    'group' => -1
                ];
            }
        }

        $this->createIds();
    }

    /**
     * Genereate new IDs for all answers that do not yet have one.
     */
    public function createIds()
    {
        $ids = [0 => true];

        foreach ($this->task['answers'] as $i => &$answer) {
            if ($answer['id'] == '') {
                do {
                    $answer['id'] = rand();
                } while (isset($ids[$answer['id']]));
            }

            $ids[$answer['id']] = true;
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
     * Returns the index of the correct answer for a specified group.
     */
    function correctIndex($group)
    {
        foreach ($this->task['answers'] as $i => $answer) {
            if ($answer['group'] == $group) {
                return $i;
            }
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

        $response = $solution->response;

        foreach ($this->task['answers'] as $i => $answer) {
            $points = $response[$answer['id']] == $answer['group'] ? 1 : 0;
            $result[] = ['points' => $points, 'safe' => true];
        }

        return $result;
    }

    /**
     * Return the list of keywords used for text export. The first keyword
     * in the list must be the keyword for the exercise type.
     */
    public static function getTextKeywords()
    {
        return ['ZU-Frage', 'Vorgabe', 'Antwort', 'Distraktor'];
    }

    /**
     * Initialize this instance from the given text data array.
     */
    public function initText($exercise)
    {
        parent::initText($exercise);

        foreach ($exercise as $tag) {
            if (key($tag) === 'Vorgabe') {
                $group = count($this->task['groups']);
                $this->task['groups'][] = current($tag);
            }

            if (key($tag) === 'Antwort' && isset($group)) {
                $this->task['answers'][] = [
                    'text'  => current($tag),
                    'group' => $group
                ];
                unset($group);
            }

            if (key($tag) === 'Distraktor') {
                $this->task['answers'][] = [
                    'text'  => current($tag),
                    'group' => -1
                ];
            }
        }

        $this->createIds();
    }


    /**
     * Initialize this instance from the given SimpleXMLElement object.
     */
    public function initXML($exercise)
    {
        parent::initXML($exercise);

        foreach ($exercise->items->item->choices->choice as $choice) {
            $this->task['groups'][] = studip_utf8decode(trim($choice));
        }

        foreach ($exercise->items->item->answers->answer as $answer) {
            $this->task['answers'][] = [
                'text'  => studip_utf8decode(trim($answer)),
                'group' => (int) $answer['correct']
            ];
        }

        $this->createIds();
    }



    /**
     * Export this exercise to plain text format.
     */
    function exportText($exercise_tag = NULL)
    {
        $result = parent::exportText($exercise_tag ?: 'ZU-Frage');

        foreach ($this->task['groups'] as $i => $group) {
            foreach ($this->task['answers'] as $answer) {
                if ($answer['group'] == $i) {
                    $result .= 'Vorgabe: '.$group."\n";
                    $result .= 'Antwort: '.$answer['text']."\n";
                }
            }
        }

        foreach ($this->task['answers'] as $answer) {
            if ($answer['group'] == -1) {
                $result .= 'Distraktor: '.$answer['text']."\n";
            }
        }

        return $result;
    }



    /**
     * Creates a template for editing a rh_exercise.
     *
     * @return The template for editing this exercise
     */
    function getEditTemplate($assignment)
    {
        if (!$this->task['answers']) {
            foreach (range(0, 4) as $i) {
                $this->task['answers'][] = ['id' => '', 'text' => '', 'group' => count($this->task['groups'])];
                $this->task['groups'][] = '';
            }
        }

        return parent::getEditTemplate($assignment);
    }

    /**
     * Return the solution of the student from the request POST data.
     *
     * @param array $request array containing the postdata for the solution.
     * @return array containing the solutions of the student.
     */
    public function responseFromRequest($request)
    {
        $result = [];

        foreach ($this->task['answers'] as $answer) {
            // get the group the user has added this answer to
            $result[$answer['id']] = (int) $request['answer'][$answer['id']];
        }

        return $result;
    }
}
