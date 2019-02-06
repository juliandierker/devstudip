<?php
/*
 * yn_exercise.php - Vips plugin for Stud.IP
 * Copyright (c) 2003-2005  Erik Schmitt, Philipp Hügelmeyer
 * Copyright (c) 2005-2006  Christa Deiwiks
 * Copyright (c) 2006-2009  Elmar Ludwig, Martin Schröder
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

Exercise::addExerciseType(_vips('Ja/Nein-Frage'), 'yn_exercise');

/**
 * XXX detailed description
 *
 * @author    XXX
 * @copyright XXX
 * @abstract
 */
class yn_exercise extends sc_exercise
{
    /**
     * Initialize this instance from the current request environment.
     */
    public function initFromRequest($request)
    {
        parent::initFromRequest($request);

        $correct = $request['correct'];
        $answers = $request['answer'];
        $this->task = [];

        foreach ($answers[0] as $i => $answer) {
            // don't skip empty answers for yn_exercise
            $this->task[0]['answers'][] = [
                'text'  => trim($answer),
                'score' => $correct[0] == $i ? 1 : 0
            ];
        }
    }

    /**
     * Return the list of keywords used for text export. The first keyword
     * in the list must be the keyword for the exercise type.
     */
    public static function getTextKeywords()
    {
        return ['JN-Frage', '[+~]?Antwort'];
    }

    /**
     * Export this exercise to plain text format.
     */
    function exportText($exercise_tag = NULL)
    {
        return parent::exportText($exercise_tag ?: 'JN-Frage');
    }

    /**
     * Creates a template for editing a yn_exercise.
     *
     * @return The template for editing this exercise
     */
    function getEditTemplate($assignment)
    {
        if (!$this->task) {
            $this->task[0]['answers'] = [
                ['text' => _vips('Ja'),   'score' => 1],
                ['text' => _vips('Nein'), 'score' => 0]
            ];
        }

        return parent::getEditTemplate($assignment);
    }
}
?>
