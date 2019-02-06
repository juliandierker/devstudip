<?php
/*
 * mco_exercise.php - Vips plugin for Stud.IP
 * Copyright (c) 2003-2005  Erik Schmitt, Philipp Hügelmeyer
 * Copyright (c) 2005-2006  Christa Deiwiks
 * Copyright (c) 2006-2009  Elmar Ludwig, Martin Schröder
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

Exercise::addExerciseType(_vips('Multiple Choice mit Enthaltung'), 'mco_exercise');

/**
 * XXX detailed description
 *
 * @author    XXX
 * @copyright XXX
 * @abstract
 */
class mco_exercise extends mc_exercise
{
    /**
     * Initialize a new instance of this class.
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        if (!isset($id)) {
            $this->task['choices'][1] = _vips('Ja');
            $this->task['choices'][0] = _vips('Nein');
        }
    }

    /**
     * Initialize this instance from the current request environment.
     */
    public function initFromRequest($request)
    {
        parent::initFromRequest($request);

        $this->task['choices'][1] = trim($request['label_yes']);
        $this->task['choices'][0] = trim($request['label_no']);
    }

    /**
     * Return the list of keywords used for text export. The first keyword
     * in the list must be the keyword for the exercise type.
     */
    public static function getTextKeywords()
    {
        return ['MCO-Frage', 'Auswahl', '[+~]?Antwort'];
    }

    /**
     * Initialize this instance from the given text data array.
     */
    public function initText($exercise)
    {
        parent::initText($exercise);

        foreach ($exercise as $tag) {
            if (key($tag) === 'Auswahl') {
                list($label_yes, $label_no) = explode('/', current($tag));
                $this->task['choices'][1] = trim($label_yes);
                $this->task['choices'][0] = trim($label_no);
            }
        }
    }

    /**
     * Initialize this instance from the given SimpleXMLElement object.
     */
    public function initXML($exercise)
    {
        parent::initXML($exercise);

        foreach ($exercise->items->item->choices->choice as $choice) {
            if ($choice['type'] == 'yes') {
                $this->task['choices'][1] = studip_utf8decode(trim($choice));
            } else if ($choice['type'] == 'no') {
                $this->task['choices'][0] = studip_utf8decode(trim($choice));
            }
        }
    }

    /**
     * Export this exercise to plain text format.
     */
    function exportText($exercise_tag = NULL)
    {
        return parent::exportText($exercise_tag ?: 'MCO-Frage');
    }
}
?>
