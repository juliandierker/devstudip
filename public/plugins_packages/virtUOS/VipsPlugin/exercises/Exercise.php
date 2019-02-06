<?php
/*
 * exercise.php - base class for all exercise types
 * Copyright (c) 2003-2005  Erik Schmitt, Philipp Hügelmeyer
 * Copyright (c) 2005-2006  Christa Deiwiks
 * Copyright (c) 2006-2009  Elmar Ludwig, Martin Schröder
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

/**
 * This class provides methods accessible by exercise-classes implementing a
 * unique type of exercise. The methods to be used by inheriting exercise
 * classes invoke internal methods to parse XML and return values encapsulated
 * by XML.
 */

abstract class Exercise extends SimpleORMap
{
    public $task = [];

    private static $exercise_types = [];

    /**
     * Configure the database mapping.
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'vips_exercise';

        $config['serialized_fields']['options'] = 'JSONArrayObject';

        $config['has_and_belongs_to_many']['tests'] = [
            'class_name'        => 'VipsTest',
            'thru_table'        => 'vips_exercise_ref',
            'thru_key'          => 'exercise_id',
            'thru_assoc_key'    => 'test_id'
        ];

        $config['has_many']['exercise_refs'] = [
            'class_name'        => 'VipsExerciseRef',
            'assoc_foreign_key' => 'exercise_id',
            'on_delete'         => 'delete'
        ];
        $config['has_many']['solutions'] = [
            'class_name'        => 'VipsSolution',
            'assoc_foreign_key' => 'exercise_id',
            'on_delete'         => 'delete'
        ];
        $config['has_many']['old_solutions'] = [
            'class_name'        => 'VipsSolutionArchive',
            'assoc_foreign_key' => 'exercise_id',
            'on_delete'         => 'delete'
        ];

        $config['belongs_to']['user'] = [
            'class_name'  => 'User',
            'foreign_key' => 'user_id'
        ];

        parent::configure($config);
    }

    /**
     * Initialize a new instance of this class.
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        if (!isset($id)) {
            $this->type = get_class($this);
            $this->task = ['answers' => []];
        }

        if (is_null($this->options)) {
            $this->options = [];
        }
    }

    /**
     * Initialize this instance from the current request environment.
     */
    public function initFromRequest($request)
    {
        $this->title               = trim($request['exercise_name']);
        $this->description         = trim($request['exercise_question']);
        $this->description         = Studip\Markup::purifyHtml($this->description);
        $this->options             = [];
        $this->options['hint']     = trim($request['exercise_hint']);
        $this->options['comment']  = (int) $request['exercise_comment'];
        $this->options['feedback'] = trim($request['mistake_comment']);
        $this->task                = ['answers' => []];
    }

    /**
     * Load a specific exercise from the database.
     */
    public static function find($id)
    {
        $db = DBManager::get();

        $stmt = $db->prepare('SELECT * FROM vips_exercise WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            return self::buildExisting($data);
        }

        return NULL;
    }

    /**
     * Load an array of exercises filtered by given sql from the database.
     *
     * @param string sql clause to use on the right side of WHERE
     * @param array parameters for query
     */
    public static function findBySQL($sql, $params = [])
    {
        $db = DBManager::get();

        $has_join = stripos($sql, 'JOIN ');
        if ($has_join === false || $has_join > 10) {
            $sql = 'WHERE ' . $sql;
        }
        $stmt = $db->prepare('SELECT * FROM vips_exercise ' . $sql);
        $stmt->execute($params);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = [];

        while ($data = $stmt->fetch()) {
            $result[] = self::buildExisting($data);
        }

        return $result;
    }

    /**
     * Find related records for an n:m relation (has_and_belongs_to_many)
     * using a combination table holding the keys.
     *
     * @param string value of foreign key to find related records
     * @param array relation options from other side of relation
     */
    public static function findThru($foreign_key_value, $options)
    {
        $thru_table = $options['thru_table'];
        $thru_key = $options['thru_key'];
        $thru_assoc_key = $options['thru_assoc_key'];

        $sql = "JOIN `$thru_table` ON `$thru_table`.`$thru_assoc_key` = vips_exercise.id
                WHERE `$thru_table`.`$thru_key` = ? " . $options['order_by'];

        return self::findBySQL($sql, [$foreign_key_value]);
    }

    /**
     * Create a new exercise object from a data array.
     */
    public static function create($data)
    {
        if (static::class === 'Exercise') {
            return $data['type']::create($data);
        } else {
            return parent::create($data);
        }
    }

    /**
     * Build an exercise object from a data array.
     */
    public static function buildExisting($data)
    {
        return $data['type']::build($data, false);
    }

    /**
     * Default setter used to proxy serialized fields.
     */
    protected function setSerializedValue($field, $value)
    {
        if (is_null($value)) {
            return $this->content[$field] = $value;
        } else {
            return parent::setSerializedValue($field, $value);
        }
    }

    /**
     * Initialize task structure from JSON string.
     */
    public function setTask_json($value)
    {
        $this->content['task_json'] = $value;
        // FIXME this will override defaults set in __construct()
        $this->task = studip_json_decode($value) ?: $this->task;
    }

    /**
     * Store this exercise into the database.
     */
    public function store()
    {
        $this->content['task_json'] = studip_json_encode($this->task);

        return parent::store();
    }

    /**
     * Compute the default maximum points which can be reached in this
     * exercise, dependent on the number of answers (defaults to 1).
     */
    public function itemCount()
    {
        return 1;
    }

    /**
     * Overwrite this function for each exercise type where shuffling answer
     * alternatives makes sense.
     *
     * @param $user_id  A value for initialising the randomiser.
     */
    public function shuffleAnswers($user_id)
    {
    }

    /**
     * Evaluates a student's solution for the individual items in this
     * exercise. Returns an array of ('points' => float, 'safe' => boolean).
     *
     * @param solution The solution object as returned by responseFromRequest().
     */
    public abstract function evaluateItems($solution);

    /**
     * Evaluates a student's solution.
     *
     * @param solution The solution object as returned by responseFromRequest().
     */
    public function evaluate($solution)
    {
        $results = $this->evaluateItems($solution);
        $points  = 0;
        $safe    = true;

        foreach ($results as $item) {
            $points += $item['points'];
            // only true if all items are marked as 'safe'
            $safe &= $item['safe'];
        }

        $percent = $points / max(count($results), 1);

        return ['percent' => $percent, 'safe' => $safe];
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

        for ($i = 0; $i < $this->itemCount(); ++$i) {
            $result[] = trim($request['answer'][$i]);
        }

        return $result;
    }

    /**
     * Export this exercise to plain text format.
     */
    public function exportText($exercise_tag = NULL)
    {
        if ($exercise_tag == NULL) {
            return sprintf(_vips('# Aufgaben des Typs "%s" können nicht exportiert werden.'), $this->type)."\n";
        }

        $result = 'Name: '.$this->title."\n";
        $result .= $exercise_tag.': '.$this->description."\n";

        if ($this->options['hint'] != '') {
            $result .= "Tipp:\n";
            $result .= $this->options['hint']."\n";
            $result .= "\\Tipp\n";
        }

        return $result;
    }

    /**
     * Export this exercise to Vips XML format.
     */
    public function getXMLTemplate($assignment)
    {
        return $this->getViewTemplate('xml', null, $assignment, null);
    }

    /**
     * Export this exercise in QTI XML format.
     */
    public function getQTITemplate($assignment)
    {
        try {
            return $this->getViewTemplate('qti', null, $assignment, null);
        } catch (Flexi_TemplateNotFoundException $ex) {
            return null;
        }
    }

    /**
     * Exercise handler to be called when a solution is submitted.
     */
    public function submitSolutionAction($controller, $solution)
    {
    }

    /**
     * Exercise handler to be called when a solution is corrected.
     */
    public function correctSolutionAction($controller, $solution)
    {
    }

    /**
     * Create a template for editing an exercise.
     *
     * @return The template
     */
    public function getEditTemplate($assignment)
    {
        global $vipsTemplateFactory;

        $template = $vipsTemplateFactory->open('exercises/edit_' . $this->type);
        $template->exercise = $this;
        $template->available_character_sets = CharacterPicker::availableCharacterSets();

        return $template;
    }

    /**
     * Create a template for viewing an exercise.
     *
     * @return The template
     */
    public function getViewTemplate($view, $solution, $assignment, $user_id)
    {
        global $vipsTemplateFactory;

        if ($assignment->type === 'exam' && $user_id) {
            $this->shuffleAnswers($user_id);
        }

        $template = $vipsTemplateFactory->open('exercises/' . $view . '_' . $this->type);
        $template->exercise = $this;
        $template->solution = $solution;
        $template->response = $solution->response;
        $template->evaluation_mode = $assignment->options['evaluation_mode'];

        return $template;
    }

    /**
     * Create a template for solving an exercise.
     *
     * @return The template
     */
    function getSolveTemplate($solution, $assignment, $user_id)
    {
        return $this->getViewTemplate('solve', $solution, $assignment, $user_id);
    }

    /**
     * Create a template for correcting a cloze_exercise.
     *
     * @return The template
     */
    function getCorrectionTemplate($solution)
    {
        return $this->getViewTemplate('correct', $solution, $solution->assignment, $solution->user_id);
    }

    /**
     * Create a template for printing an exercise.
     *
     * @return The template
     */
    function getPrintTemplate($solution, $assignment, $user_id)
    {
        return $this->getViewTemplate('print', $solution, $assignment, $user_id);
    }

    /**
     * Get the name of this exercise type.
     */
    public function getTypeName()
    {
        return self::$exercise_types[$this->type]['name'];
    }

    /**
     * Get the list of supported exercise types.
     */
    public static function getExerciseTypes()
    {
        return self::$exercise_types;
    }

    /**
     * Register a new exercise type and class.
     */
    public static function addExerciseType($name, $class, $type = NULL)
    {
        self::$exercise_types[$class] = compact('name', 'type');
    }

    /**
     * Return the list of keywords used for text export. The first keyword
     * in the list must be the keyword for the exercise type.
     */
    public static function getTextKeywords()
    {
        return [];
    }

    /**
     * Import a new exercise from text data array.
     */
    public static function importText($segment)
    {
        $all_keywords = ['Tipp'];

        foreach (self::$exercise_types as $key => $value) {
            $keywords = $key::getTextKeywords();

            if ($keywords) {
                $all_keywords = array_merge($all_keywords, $keywords);
                $types[$key] = array_shift($keywords);
            }
        }

        $pattern = implode('|', array_unique($all_keywords));
        $parts = preg_split("/\n($pattern):/", $segment, -1, PREG_SPLIT_DELIM_CAPTURE);
        $title = array_shift($parts);

        $exercise = [['Name' => trim($title)]];

        if ($parts) {
            $type = array_shift($parts);
            $text = array_shift($parts);
            $text = preg_replace('/\\\\' . $type . '$/', '', trim($text));

            $exercise[] = ['Text' => trim($text)];
        }

        while ($parts) {
            $tag = array_shift($parts);
            $val = array_shift($parts);
            $val = preg_replace('/\\\\' . $tag . '$/', '', trim($val));

            $exercise[] = [$tag => trim($val)];
        }

        foreach ($types as $key => $value) {
            if ($type === $value) {
                $exercise_type = $key;
            }
        }

        if (!isset($exercise_type)) {
            throw new InvalidArgumentException(_vips('Unbekannter Aufgabentyp: ') . $type);
        }

        $result = new $exercise_type();
        $result->initText($exercise);
        return $result;
    }

    /**
     * Import a new exercise from Vips XML format.
     */
    public static function importXML($exercise)
    {
        $type = (string) $exercise->items->item[0]['type'];

        foreach (self::$exercise_types as $key => $value) {
            if ($type === $value['type'] || is_array($value['type']) && in_array($type, $value['type'])) {
                $exercise_type = $key;
            }
        }

        if (!isset($exercise_type)) {
            throw new InvalidArgumentException(_vips('Unbekannter Aufgabentyp: ') . $type);
        }

        if ($exercise_type == 'mc_exercise' && $exercise->items->item[0]->choices) {
            $exercise_type = 'mco_exercise';
        } else if ($exercise_type == 'sc_exercise') {
            foreach ($exercise->items->item[0]->answers->answer as $answer) {
                if ($answer['default']) {
                    $exercise_type = 'sco_exercise';
                }
            }
        }

        $result = new $exercise_type();
        $result->initXML($exercise);
        return $result;
    }

    /**
     * Initialize this instance from the given text data array.
     */
    public function initText($exercise)
    {
        foreach ($exercise as $tag) {
            if (key($tag) === 'Name') {
                $this->title = current($tag);
            }

            if (key($tag) === 'Text') {
                $this->description = current($tag);
            }

            if (key($tag) === 'Tipp') {
                $this->options['hint'] = current($tag);
            }
        }
    }

    /**
     * Initialize this instance from the given SimpleXMLElement object.
     */
    public function initXML($exercise)
    {
        $this->title = studip_utf8decode(trim($exercise->title));

        if ($exercise->description) {
            $this->description = studip_utf8decode(trim($exercise->description));
        }

        if ($exercise->hint) {
            $this->options['hint'] = studip_utf8decode(trim($exercise->hint));
        }

        $this->options['comment'] = (boolean) $exercise['feedback'];

        if ($exercise->items->item[0]->feedback) {
            $this->options['feedback'] = studip_utf8decode(trim($exercise->items->item[0]->feedback));
        }
    }

    /**
     * Construct a new solution object from the request post data.
     */
    public function getSolutionFromRequest($request, $files = NULL)
    {
        global $vipsPlugin;

        $solution = new VipsSolution();
        $solution->exercise = $this;
        $solution->user_id = $vipsPlugin->userID;
        $solution->response = $this->responseFromRequest($request);
        $solution->student_comment = trim($request['student_comment']);
        $solution->ip_address = $_SERVER['REMOTE_ADDR'];

        return $solution;
    }
}
?>
