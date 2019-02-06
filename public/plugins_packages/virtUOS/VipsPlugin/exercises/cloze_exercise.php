<?php
/*
 * cloze_exercise.php - Vips plugin for Stud.IP
 * Copyright (c) 2003-2005  Erik Schmitt, Philipp Hügelmeyer
 * Copyright (c) 2005-2006  Christa Deiwiks
 * Copyright (c) 2006-2009  Elmar Ludwig, Martin Schröder
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

Exercise::addExerciseType(_vips('Lückentext'), 'cloze_exercise', ['cloze-input', 'cloze-select']);

class cloze_exercise extends Exercise
{
    /**
     * Initialize this instance from the current request environment.
     */
    public function initFromRequest($request)
    {
        parent::initFromRequest($request);

        $this->parseClozeText(trim($request['cloze_text']));
        $this->options['lang'] = $request['character_picker'];
        $this->task['compare'] = $request['answer_distance'];
        $this->task['select']  = (int) $request['choose_item'];
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
     * Return the list of keywords used for text export. The first keyword
     * in the list must be the keyword for the exercise type.
     */
    public static function getTextKeywords()
    {
        return ["L'text", 'Eingabehilfe', 'Abgleich'];
    }

    /**
     * Initialize this instance from the given text data array.
     */
    public function initText($exercise)
    {
        parent::initText($exercise);

        $this->parseClozeText($this->description);
        $this->description = '';

        foreach ($exercise as $tag) {
            if (key($tag) === 'Eingabehilfe') {
                $this->options['lang'] = current($tag);
            }

            if (key($tag) === 'Abgleich') {
                if (current($tag) === 'Kleinbuchstaben') {
                    $this->task['compare'] = 'ignorecase';
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

        foreach ($exercise->items->item->description->children() as $name => $elem) {
            if ($name == 'text') {
                $this->task['text'] .= studip_utf8decode((string) $elem);
            } else if ($name == 'answers') {
                $answers = [];

                foreach ($elem->answer as $answer) {
                    $answers[] = [
                        'text'  => studip_utf8decode((string) $answer),
                        'score' => (string) $answer['score']
                    ];
                }

                $this->task['answers'][] = $answers;
                $this->task['text'] .= '[[]]';
            }
        }

        $this->options['hint'] = studip_utf8decode(trim($exercise->items->item->description->hint));
        $this->options['lang'] = vips_character_picker_language((string) $exercise['lang']);
        $this->task['select']  = $exercise->items->item['type'] == 'cloze-select';

        switch ($exercise->items->item->{'evaluation-hints'}->similarity) {
            case 'ignorecase':
                $this->task['compare'] = 'ignorecase';
        }
    }



    /**
     * Returns the exercise in im-/export format (plain text).
     */
    function exportText($exercise_tag = NULL)
    {
        // $this->description = $this->getClozeText();
        // $result = parent::exportText($exercise_tag ?: "L'text");

        $result = 'Name: '.$this->title."\n";
        $result .= "L'text: ".$this->getClozeText()."\n";
        $result .= "\\L'text\n";

        if ($this->options['hint'] != '') {
            $result .= "Tipp:\n";
            $result .= $this->options['hint']."\n";
            $result .= "\\Tipp\n";
        }

        if ($this->options['lang']) {
            $result .= 'Eingabehilfe: '.$this->options['lang']."\n";
        }

        if ($this->task['compare'] == 'ignorecase') {
            $result .= "Abgleich: Kleinbuchstaben\n";
        }

        return $result;
    }


    /**
     * Creates a template for editing a cloze exercise. NOTE: As a cloze
     * exercise has no special fields (it consists only of the question),
     * normally, an empty template will be returned. The only elements it can
     * contain are message boxes alerting that for the same cloze an answer
     * alternative has been set repeatedly.
     *
     * @return The template for editing this exercise
     */
    function getEditTemplate($assignment)
    {
        $duplicate_alternatives = $this->findDuplicateAlternatives();

        foreach ($duplicate_alternatives as $alternative) {
            $message = sprintf(_vips('Achtung: Sie haben bei der %d. Lücke die Antwort &bdquo;%s&ldquo; mehrfach eingetragen.'),
                               $alternative['index'] + 1, htmlReady($alternative['text']));
            PageLayout::postInfo($message);
        }

        $tooltip = sprintf('<p>%s:<br>[[ ... ]]</p><p>%s:<br>[[ ... | ... | ... ]]</p><p>%s:<br>[[ ... | ... | *... ]]</p>',
            _vips('Lücke hinzufügen'), _vips('Mehrere Lösungen mit | trennen'), _vips('Falsche Antworten mit * markieren'));

        $template = parent::getEditTemplate($assignment);
        $template->tooltip = $tooltip;

        return $template;
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
     * Returns all the correct answers for an item in an array.
     */
    function correctAnswers($item)
    {
        $answers = [];

        foreach ($this->task['answers'][$item] as $answer) {
            if ($answer['score'] == 1) {
                $answers[] = $answer['text'];
            }
        }

        return $answers;
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
        $ignorecase = $this->task['compare'] == 'ignorecase';

        foreach ($this->task['answers'] as $blank => $answer) {
            $student_answer = normalizeText($response[$blank], $ignorecase);
            $options = ['' => 0];

            foreach ($answer as $option) {  // different answer options
                $content = normalizeText($option['text'], $ignorecase);
                $options[$content] = $option['score'];
            }

            if (isset($options[$student_answer])) {
                $points = $options[$student_answer];
                $safe = true;
            } else {
                $points = 0;
                $safe = false;
            }

            $result[] = ['points' => $points, 'safe' => $safe];
        }

        return $result;
    }



    #######################################
    #                                     #
    #   h e l p e r   f u n c t i o n s   #
    #                                     #
    #######################################



    /**
     * Returns the exercise for the lecturer. Clozes are represented by square brackets. Special characters are escaped.
     */
    function getClozeText()
    {
        $result = '';

        foreach (explode('[[]]', $this->task['text']) as $blank => $text) {
            $result .= $text;

            if (isset($this->task['answers'][$blank])) {  // blank
                $answers = [];

                foreach ($this->task['answers'][$blank] as $answer) {
                    if ($answer['score'] == 0) {
                        $answers[] = '*' . $answer['text'];
                    } else if ($answer['score'] == 0.5) {
                        $answers[] = '~' . $answer['text'];
                    } else {
                        $answers[] = $answer['text'];
                    }
                }

                $result .= '[[' . implode('|', $answers) . ']]';
            }
        }

        return $result;
    }



    /**
     * Converts plain text ("foo bar [blank] text...") to array.
     */
    function parseClozeText($question)
    {
        $question = Studip\Markup::purifyHtml($question);

        // $question_array contains text elements and blanks (surrounded by [[ and ]]).
        $parts = preg_split('/(\[\[.*?\]\])/s', $question, -1, PREG_SPLIT_DELIM_CAPTURE);

        foreach ($parts as $part) {
            if (preg_match('/^\[\[(.*)\]\]$/s', $part, $matches)) {
                // remove outer square brackets and rare stuff (\n, \r) from the blank
                $part = str_replace(["\n", "\r"], '', $matches[1]);
                $answers = [];

                foreach (explode('|', $part) as $answer) {
                    $points = 1;

                    if ($answer != '') {
                        if ($answer[0] == '*') {
                            $points = 0;
                            $answer = substr($answer, 1);
                        } else if ($answer[0] == '~') {
                            $points = 0.5;
                            $answer = substr($answer, 1);
                        }
                    }

                    $answers[] = ['text' => $answer, 'score' => $points];
                }

                $this->task['answers'][] = $answers;
                $this->task['text'] .= '[[]]';
            } else {
                $this->task['text'] .= $part;
            }
        }
    }

    /**
     * Searches in each cloze if an answer alternative is given repatedly.
     *
     * @return Either an empty array or an array of arrays, each containing the
     *          elements 'index' (index of the cloze where the duplicate
     *          entry was found) and 'text' (text of the duplicate entry).
     */
    private function findDuplicateAlternatives()
    {
        $duplicate_alternatives = [];

        foreach ($this->task['answers'] as $index => $answers) {
            $alternatives = [];

            foreach ($answers as $answer) {
                if (in_array($answer['text'], $alternatives, true)) {
                    $duplicate_alternatives[] = [
                        'index' => $index,
                        'text'  => $answer['text']
                    ];
                }

                $alternatives[] = $answer['text'];
            }
        }

        return $duplicate_alternatives;
    }
}
?>
