<?php
/*
 * sco_exercise.php - Vips plugin for Stud.IP
 * Copyright (c) 2003-2005  Erik Schmitt, Philipp Hügelmeyer
 * Copyright (c) 2005-2006  Christa Deiwiks
 * Copyright (c) 2006-2009  Elmar Ludwig, Martin Schröder
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

Exercise::addExerciseType(_vips('Single Choice mit Enthaltung'), 'sco_exercise');

/**
 * XXX detailed description
 *
 * @author    XXX
 * @copyright XXX
 * @abstract
 */
class sco_exercise extends sc_exercise
{
    /**
     * Return the list of keywords used for text export. The first keyword
     * in the list must be the keyword for the exercise type.
     */
    public static function getTextKeywords()
    {
        return ['SCO-Frage', '[+~]?Antwort'];
    }

    /**
     * Export this exercise to plain text format.
     */
    function exportText($exercise_tag = NULL)
    {
        return parent::exportText($exercise_tag ?: 'SCO-Frage');
    }
}
?>
