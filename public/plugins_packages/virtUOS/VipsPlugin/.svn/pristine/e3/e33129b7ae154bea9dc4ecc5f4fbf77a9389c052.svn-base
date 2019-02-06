<?php

class XmlToJsonFormat extends Migration
{
    function description()
    {
        return 'convert internal vips data from XML to JSON format';
    }

    function up()
    {
        $db = DBManager::get();

        // cope with large number of solutions
        ini_set('memory_limit', -1);

        // set primary key for vips_gewichtung
        $sql = 'ALTER TABLE vips_gewichtung DROP KEY Item_id, ADD PRIMARY KEY (Item_id, Item_type)';
        $db->exec($sql);

        // set primary key for vips_inBlock
        $sql = 'ALTER TABLE vips_inBlock DROP KEY block_id, ADD PRIMARY KEY (block_id, test_id)';
        $db->exec($sql);

        // set primary key for vips_inGruppe
        $sql = "ALTER TABLE vips_inGruppe
                CHANGE user_id user_id VARCHAR(32) NOT NULL,
                CHANGE start start TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
                CHANGE end end TIMESTAMP NULL DEFAULT NULL,
                DROP KEY group_id, ADD PRIMARY KEY (group_id, user_id, start)";
        $db->exec($sql);

        // convert solution in vips_solution to JSON format
        $result = $db->query('SELECT id, solution FROM vips_solution');
        $sql = 'UPDATE vips_solution SET solution = ? WHERE id = ?';
        $this->solution_to_json($result, $db->prepare($sql));

        // convert solution in vips_solution_archive to JSON format
        $result = $db->query('SELECT id, solution FROM vips_solution_archive');
        $sql = 'UPDATE vips_solution_archive SET solution = ? WHERE id = ?';
        $this->solution_to_json($result, $db->prepare($sql));

        // rename solution column in vips_solution to response
        $db->exec('ALTER TABLE vips_solution CHANGE solution response TEXT NOT NULL');
        $db->exec('ALTER TABLE vips_solution_archive CHANGE solution response TEXT NOT NULL');

        // update schema for vips_aufgaben_zeit table
        $sql = "ALTER TABLE vips_aufgaben_zeit
                RENAME TO vips_test_attempt,
                CHANGE ID id INT(11) NOT NULL AUTO_INCREMENT,
                CHANGE Kurs course_id VARCHAR(32) NOT NULL AFTER test_id,
                CHANGE Beginn start TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
                ADD end TIMESTAMP NULL DEFAULT NULL AFTER start,
                CHANGE ip ip_address VARCHAR(39) NOT NULL,
                ADD options TEXT NULL,
                DROP vips_aufgabe,
                DROP KEY Kurs, DROP KEY user_id, DROP KEY test_id,
                ADD KEY test_id (test_id, user_id)";
        $db->exec($sql);

        // convert exercise to new table schema (JSON format)
        $sql = 'ALTER TABLE vips_aufgabe
                RENAME TO vips_exercise,
                CHANGE ID id INT(11) NOT NULL AUTO_INCREMENT,
                CHANGE URI type VARCHAR(64) NOT NULL AFTER id,
                CHANGE Name title VARCHAR(255) NOT NULL,
                ADD description TEXT NOT NULL AFTER title,
                CHANGE Aufgabe task_json TEXT NOT NULL,
                CHANGE Userid user_id VARCHAR(32) NOT NULL,
                ADD options TEXT NULL';
        $db->exec($sql);

        $result = $db->query('SELECT * FROM vips_exercise');
        $sql = 'UPDATE vips_exercise SET description = ?, task_json = ?, options = ? WHERE id = ?';
        $this->exercise_to_json($result, $db->prepare($sql));

        SimpleORMap::expireTableScheme();
    }

    function down()
    {
        $db = DBManager::get();

        // cope with large number of solutions
        ini_set('memory_limit', -1);

        // delete primary keys
        $sql = 'ALTER TABLE vips_gewichtung DROP PRIMARY KEY, ADD UNIQUE KEY Item_id (Item_id, Item_type)';
        $db->exec($sql);

        $sql = 'ALTER TABLE vips_inBlock DROP PRIMARY KEY, ADD KEY block_id (block_id)';
        $db->exec($sql);

        $sql = 'ALTER TABLE vips_inGruppe DROP PRIMARY KEY, ADD KEY group_id (group_id)';
        $db->exec($sql);

        // rename response column in vips_solution to solution
        $db->exec('ALTER TABLE vips_solution CHANGE response solution TEXT NOT NULL');
        $db->exec('ALTER TABLE vips_solution_archive CHANGE response solution TEXT NOT NULL');

        // convert solution in vips_solution to XML format
        $result = $db->query('SELECT id, exercise_id, solution FROM vips_solution');
        $sql = 'UPDATE vips_solution SET solution = ? WHERE id = ?';
        $this->solution_to_xml($result, $db->prepare($sql));

        // convert solution in vips_solution_archive to XML format
        $result = $db->query('SELECT id, exercise_id, solution FROM vips_solution_archive');
        $sql = 'UPDATE vips_solution_archive SET solution = ? WHERE id = ?';
        $this->solution_to_xml($result, $db->prepare($sql));

        // update schema for vips_test_attempt table
        $sql = "ALTER TABLE vips_test_attempt
                RENAME TO vips_aufgaben_zeit,
                CHANGE id ID INT(11) NOT NULL AUTO_INCREMENT,
                CHANGE course_id Kurs VARCHAR(32) NOT NULL AFTER ID,
                CHANGE start Beginn TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
                DROP end,
                CHANGE ip_address ip VARCHAR(39) NOT NULL,
                DROP options,
                ADD vips_aufgabe INT(11) NOT NULL DEFAULT 0,
                DROP KEY test_id, ADD KEY (Kurs), ADD KEY (user_id), ADD KEY (test_id)";
        $db->exec($sql);

        // convert exercise to old table schema (XML format)
        $result = $db->query('SELECT * FROM vips_exercise');
        $sql = 'UPDATE vips_exercise SET task_json = ? WHERE id = ?';
        $this->exercise_to_xml($result, $db->prepare($sql));

        $sql = 'ALTER TABLE vips_exercise
                RENAME TO vips_aufgabe,
                CHANGE id ID INT(11) NOT NULL AUTO_INCREMENT,
                CHANGE title Name VARCHAR(255) NOT NULL,
                DROP description,
                CHANGE task_json Aufgabe TEXT NOT NULL,
                CHANGE type URI VARCHAR(64) NOT NULL AFTER Aufgabe,
                CHANGE user_id Userid VARCHAR(32) NOT NULL,
                DROP options';
        $db->exec($sql);

        SimpleORMap::expireTableScheme();
    }

    function solution_to_json($result, $stmt)
    {
        foreach ($result as $row) {
            $answers = [];
            $dom_xml = DOMDocument::loadXML(studip_utf8encode($row['solution']));

            if (is_object($dom_xml)) {
                foreach ($dom_xml->getElementsByTagName('answer') as $node) {
                    $answers[] = $node->textContent;
                }

                $stmt->execute([json_encode($answers), $row['id']]);
            }
        }

    }

    function solution_to_xml($result, $stmt)
    {
        foreach ($result as $row) {
            $json = json_decode($row['solution'], true);

            if (is_array($json)) {
                $xml = '<solution id="' . $row['exercise_id'] . '">';

                foreach ($json as $index => $item) {
                    $xml .= '<answer id="' . $index . '">';
                    $xml .= htmlspecialchars($item, ENT_COMPAT, 'UTF-8');
                    $xml .= '</answer>';
                }

                $xml .= '</solution>';
                $stmt->execute([studip_utf8decode($xml), $row['id']]);
            }
        }
    }

    function exercise_to_json($result, $stmt)
    {
        foreach ($result as $row) {
            $type = $row['type'];
            $task = $row['task_json'];
            $func = $type . '_to_json';
            $dom_xml = DOMDocument::loadXML(studip_utf8encode($task));

            if ($dom_xml && function_exists($func)) {
                $data = $func($type, $dom_xml);
                $task = json_encode(studip_utf8encode($data['task']));
                $options = json_encode(studip_utf8encode($data['options']));
                $stmt->execute([$data['description'], $task, $options, $row['id']]);
            }
        }
    }

    function exercise_to_xml($result, $stmt)
    {
        foreach ($result as $row) {
            $type = $row['type'];
            $func = $type . '_to_xml';

            if (function_exists($func)) {
                $row['task'] = studip_utf8decode(json_decode($row['task_json'], true));
                $row['options'] = studip_utf8decode(json_decode($row['options'], true));
                $stmt->execute([$func($row), $row['id']]);
            }
        }
    }
}

/**
 * Convert vips compare mode id to mode string.
 */
function vips_compare_mode($id)
{
    static $compare_modes = [
        1 => 'levenshtein',
        2 => 'soundex',
        3 => 'ignorecase'
    ];

    return $compare_modes[$id];
}

/**
 * Convert vips compare mode string to mode id.
 */
function vips_compare_mode_id($mode)
{
    static $compare_ids = [
        'levenshtein' => 1,
        'soundex'     => 2,
        'ignorecase'  => 3
    ];

    return $compare_ids[$mode] ?: 0;
}

/**
 * Initialize this instance from the database representation.
 */
function base_exercise_to_json($type, $dom_xml)
{
    $question_node = $dom_xml->getElementsByTagName('Question')->item(0);
    $hint_node     = $dom_xml->getElementsByTagName('Hint')->item(0);
    $comment_node  = $dom_xml->getElementsByTagName('CommentField')->item(0);
    $feedback_node = $dom_xml->getElementsByTagName('MistakeComment')->item(0);

    $data['type'] = $type;
    $data['task'] = ['answers' => []];

    if (is_object($question_node)) {
        $data['description'] = studip_utf8decode($question_node->textContent);
    }

    $data['options']['hint']     = studip_utf8decode($hint_node->textContent);
    $data['options']['feedback'] = studip_utf8decode($feedback_node->textContent);

    if (is_object($comment_node)) {
        $data['options']['comment'] = (int) $comment_node->getAttribute('Visible');
    }

    return $data;
}

/**
 * Initialize this instance from the database representation.
 */
function cloze_exercise_to_json($type, $dom_xml)
{
    $data = base_exercise_to_json($type, $dom_xml);

    $cp_node = $dom_xml->getElementsByTagName('CharacterPicker')->item(0);
    $ad_node = $dom_xml->getElementsByTagName('AnswerDistance')->item(0);
    $ci_node = $dom_xml->getElementsByTagName('ChooseItem')->item(0);

    $data['description'] = '';
    $question_node = $dom_xml->getElementsByTagName('Question')->item(0);

    foreach ($question_node->childNodes as $node) {
        if ($node->nodeType === XML_TEXT_NODE) {
            $data['task']['text'] .= studip_utf8decode($node->textContent);
        } else {
            $answers = [];

            foreach ($node->childNodes as $answer_node) {
                $answers[] = [
                    'text'  => studip_utf8decode($answer_node->textContent),
                    'score' => $answer_node->getAttribute('Points')
                ];
            }

            $data['task']['answers'][] = $answers;
            $data['task']['text'] .= '[[]]';
        }
    }

    $data['options']['lang'] = studip_utf8decode($cp_node->textContent);
    $data['task']['compare'] = vips_compare_mode($ad_node->textContent);
    $data['task']['select']  = (int) $ci_node->textContent;

    return $data;
}

/**
 * Generate the XML database representation for this exercise.
 */
function cloze_exercise_to_xml($data)
{
    $xml  = '<TestItem ID="'.$data['type'].'" Type="Cloze">';
    $xml .= '<Name>'.htmlReady($data['title'], false).'</Name>';
    $xml .= '<Hint>'.htmlReady($data['options']['hint'], false).'</Hint>';
    $xml .= '<CommentField Visible="'.$data['options']['comment'].'"/>';
    $xml .= '<AnswerDistance>'.vips_compare_mode_id($data['task']['compare']).'</AnswerDistance>';
    $xml .= '<Question>';

    foreach (explode('[[]]', $data['task']['text']) as $blank => $text) {
        $xml .= htmlReady($text, false);
        if (isset($data['task']['answers'][$blank])) {
            $xml .= '<Cloze>';
            foreach ($data['task']['answers'][$blank] as $answer) {
                $xml .= '<Answer Points="'.$answer['score'].'">'.htmlReady($answer['text'], false).'</Answer>';
            }
            $xml .= '</Cloze>';
        }
    }

    $xml .= '</Question>';

    if ($data['options']['lang']) {
        $xml .= '<CharacterPicker>' . $data['options']['lang'] . '</CharacterPicker>';
    }

    if ($data['task']['select']) {
        $xml .= '<ChooseItem>' . $data['task']['select'] . '</ChooseItem>';
    }

    if ($data['options']['feedback'] != '') {
        $xml .= '<MistakeComment>'.htmlReady($data['options']['feedback'], false).'</MistakeComment>';
    }

    $xml .= '</TestItem>';

    return preg_replace("/[^\t\n\r -\xFF]/", '', $xml);
}

/**
 * Initialize this instance from the database representation.
 */
function lt_exercise_to_json($type, $dom_xml)
{
    $data = base_exercise_to_json($type, $dom_xml);

    $answers = $dom_xml->getElementsByTagName('Answer');
    $cp_node = $dom_xml->getElementsByTagName('CharacterPicker')->item(0);
    $ad_node = $dom_xml->getElementsByTagName('AnswerDistance')->item(0);

    foreach ($answers as $node) {
        $data['task']['answers'][] = [
            'text'  => studip_utf8decode($node->textContent),
            'score' => (float) $node->getAttribute('Correct')
        ];
    }

    $data['options']['lang'] = studip_utf8decode($cp_node->textContent);
    $data['task']['compare'] = vips_compare_mode($ad_node->textContent);

    return $data;
}

/**
 * Generate the XML database representation for this exercise.
 */
function lt_exercise_to_xml($data)
{
    $xml  = "<TestItem ID=\"" . $data['type'] . "\" Type=\"Freie Antwort\">";
    $xml .= "<Name>".htmlReady($data['title'], false)."</Name>";
    $xml .= "<Hint>".htmlReady($data['options']['hint'], false)."</Hint>";
    $xml .= "<CommentField Visible=\"".$data['options']['comment']."\"></CommentField>";
    $xml .= "<AnswerDistance>".vips_compare_mode_id($data['task']['compare'])."</AnswerDistance>";
    $xml .= "<Question>";
    $xml .= "<Paragraph>".htmlReady($data['description'], false)."</Paragraph>";
    $xml .= "</Question>";

    foreach ($data['task']['answers'] as $answer) {
        $xml .= '<Answer Correct="'.$answer['score'].'">'.htmlReady($answer['text'], false).'</Answer>';
    }

    if ($data['options']['lang']) {
        $xml .= '<CharacterPicker>' . $data['options']['lang'] . '</CharacterPicker>';
    }

    if ($data['options']['feedback'] != '') {
        $xml .= '<MistakeComment>'.htmlReady($data['options']['feedback'], false).'</MistakeComment>';
    }

    $xml .= "</TestItem>";

    return preg_replace("/[^\t\n\r -\xFF]/", '', $xml);
}

/**
 * Initialize this instance from the database representation.
 */
function mc_exercise_to_json($type, $dom_xml)
{
    $data = base_exercise_to_json($type, $dom_xml);

    $answers = $dom_xml->getElementsByTagName('Answer');

    foreach ($answers as $node) {
        $data['task']['answers'][] = [
            'text'  => studip_utf8decode($node->textContent),
            'score' => (int) $node->getAttribute('Correct')
        ];
    }

    return $data;
}

/**
 * Generate the XML database representation for this exercise.
 */
function mc_exercise_to_xml($data)
{
    $xml  = "<TestItem ID=\"" . $data['type'] . "\" Type=\"MultipleChoice\">";
    $xml .= "<Name>".htmlReady($data['title'], false)."</Name>";
    $xml .= "<Hint>".htmlReady($data['options']['hint'], false)."</Hint>";
    $xml .= "<CommentField Visible=\"".$data['options']['comment']."\"></CommentField>";
    $xml .= "<Question>";
    $xml .= "<Paragraph>".htmlReady($data['description'], false)."</Paragraph>";
    $xml .= "</Question>";

    if ($data['options']['feedback'] != '') {
        $xml .= '<MistakeComment>'.htmlReady($data['options']['feedback'], false).'</MistakeComment>';
    }

    foreach ($data['task']['answers'] as $answer) {
        $xml .= '<Answer Correct="'.$answer['score'].'">'.htmlReady($answer['text'], false).'</Answer>';
    }

    $xml .= "</TestItem>";

    return preg_replace("/[^\t\n\r -\xFF]/", '', $xml);
}

/**
 * Initialize this instance from the database representation.
 */
function mco_exercise_to_json($type, $dom_xml)
{
    $data = mc_exercise_to_json($type, $dom_xml);

    $labels = $dom_xml->getElementsByTagName('AnswerLabel');

    $data['task']['choices'][1] = 'Ja';
    $data['task']['choices'][0] = 'Nein';

    foreach ($labels as $node) {
        $key = (int) $node->getAttribute('Correct');
        $data['task']['choices'][$key] = studip_utf8decode($node->textContent);
    }

    return $data;
}

/**
 * Generate the XML database representation for this exercise.
 */
function mco_exercise_to_xml($data)
{
    $xml  = "<TestItem ID=\"" . $data['type'] . "\" Type=\"MultipleChoice\">";
    $xml .= "<Name>".htmlReady($data['title'], false)."</Name>";
    $xml .= "<Hint>".htmlReady($data['options']['hint'], false)."</Hint>";
    $xml .= "<CommentField Visible=\"".$data['options']['comment']."\"></CommentField>";
    $xml .= "<Question>";
    $xml .= "<Paragraph>".htmlReady($data['description'], false)."</Paragraph>";
    $xml .= "</Question>";

    if ($data['options']['feedback'] != '') {
        $xml .= '<MistakeComment>'.htmlReady($data['options']['feedback'], false).'</MistakeComment>';
    }

    foreach ($data['task']['choices'] as $key => $label) {
        $xml .= '<AnswerLabel Correct="'.$key.'">'.htmlReady($label, false).'</AnswerLabel>';
    }

    foreach ($data['task']['answers'] as $answer) {
        $xml .= '<Answer Correct="'.$answer['score'].'">'.htmlReady($answer['text'], false).'</Answer>';
    }

    $xml .= "</TestItem>";

    return preg_replace("/[^\t\n\r -\xFF]/", '', $xml);
}

/**
 * Initialize this instance from the database representation.
 */
function me_exercise_to_json($type, $dom_xml)
{
    $data = base_exercise_to_json($type, $dom_xml);

    // getting variables from xml
    $vars = $dom_xml->getElementsByTagName('Variable');
    $node = $dom_xml->getElementsByTagName('Answer')->item(0);

    $data['task']['answers'][0] = [
        'text'  => studip_utf8decode($node->textContent),
        'score' => 1
    ];

    $data['task']['variables'] = [];

    foreach ($vars as $node) {
        $data['task']['variables'][] = [
            'name' => studip_utf8decode($node->getAttribute('varname')),
            'min'  => (float) $node->getAttribute('min'),
            'max'  => (float) $node->getAttribute('max')
        ];
    }

    return $data;
}

/**
 * Generate the XML database representation for this exercise.
 */
function me_exercise_to_xml($data)
{
    $xml  = "<TestItem ID=\"" . $data['type'] . "\" Type=\"Algebra\">";
    $xml .= "<Name>".htmlReady($data['title'], false)."</Name>";
    $xml .= "<Hint>".htmlReady($data['options']['hint'], false)."</Hint>";
    $xml .= "<CommentField Visible=\"".$data['options']['comment']."\"></CommentField>";
    $xml .= "<Question>";
    $xml .= "<Paragraph>".htmlReady($data['description'], false)."</Paragraph>";
    $xml .= "</Question>";
    $xml .= "<Answer>".htmlReady($data['task']['answers'][0]['text'], false)."</Answer>";

    foreach ($data['task']['variables'] as $variable) {
        $xml .= '<Variable varname="'.htmlReady($variable['name'], false).'" min="'.$variable['min'].'" max="'.$variable['max'].'"/>';
    }

    if ($data['options']['feedback'] != '') {
        $xml .= '<MistakeComment>'.htmlReady($data['options']['feedback'], false).'</MistakeComment>';
    }

    $xml .= "</TestItem>";

    return preg_replace("/[^\t\n\r -\xFF]/", '', $xml);
}

/**
 * Initialize this instance from the database representation.
 */
function pl_exercise_to_json($type, $dom_xml)
{
    $data = base_exercise_to_json($type, $dom_xml);

    $answer  = $dom_xml->getElementsByTagName('Answer')->item(0);
    $ad_node = $dom_xml->getElementsByTagName('AnswerDefault')->item(0);
    $qd_node = $dom_xml->getElementsByTagName('QueryDefault')->item(0);

    list($solutions, $test) = explode('%T', studip_utf8decode($answer->textContent));
    $solutions = explode('%or', $solutions);

    foreach ($solutions as $solution) {
        if (preg_match('/^%s(.*)/m', $solution, $matches)) {
            $score = (float) $matches[1];
        } else {
            $score = 1;
        }

        $data['task']['answers'][] = [
            'text'  => $solution,
            'score' => $score
        ];
    }

    $data['task']['test'] = $test;
    $data['task']['template'] = studip_utf8decode($ad_node->textContent);
    $data['task']['input']    = studip_utf8decode($qd_node->textContent);

    return $data;
}

/**
 * Generate the XML database representation for this exercise.
 */
function pl_exercise_to_xml($data)
{
    $program = '';

    foreach ($data['task']['answers'] as $answer) {
        if ($program != '') {
            $program .= '%or';
        }

        $program .= $answer['text'];
    }

    if ($data['task']['test']) {
        $program .= '%T' . $data['task']['test'];
    }

    $xml  = "<TestItem ID=\"" . $data['type'] . "\" Type=\"Prolog\">";
    $xml .= "<Name>".htmlReady($data['title'], false)."</Name>";
    $xml .= "<Hint>".htmlReady($data['options']['hint'], false)."</Hint>";
    $xml .= "<CommentField Visible=\"".$data['options']['comment']."\"></CommentField>";
    $xml .= "<Question>";
    $xml .= "<Paragraph>".htmlReady($data['description'], false)."</Paragraph>";
    $xml .= "</Question>";
    $xml .= "<Answer>".htmlReady($program, false)."</Answer>";
    $xml .= "<AnswerDefault>".htmlReady($data['task']['template'], false)."</AnswerDefault>";
    $xml .= "<QueryDefault>".htmlReady($data['task']['input'], false)."</QueryDefault>";

    if ($data['options']['feedback'] != '') {
        $xml .= '<MistakeComment>'.htmlReady($data['options']['feedback'], false).'</MistakeComment>';
    }

    $xml .= "</TestItem>";

    return preg_replace("/[^\t\n\r -\xFF]/", '', $xml);
}

/**
 * Initialize this instance from the database representation.
 */
function rh_exercise_to_json($type, $dom_xml)
{
    $data = base_exercise_to_json($type, $dom_xml);

    $answers = $dom_xml->getElementsByTagName('Answer');

    foreach ($answers as $node) {
        $data['task']['answers'][] = [
            'text'  => studip_utf8decode($node->textContent),
            'group' => (int) $node->getAttribute('Default')
        ];
    }

    $groups = $dom_xml->getElementsByTagName('Default');

    $data['task']['groups'] = [];

    foreach ($groups as $group) {
        $data['task']['groups'][] = studip_utf8decode($group->textContent);
    }

    return $data;
}

/**
 * Generate the XML database representation for this exercise.
 */
function rh_exercise_to_xml($data)
{
    $xml = '<TestItem ID="'.$data['type'].'" Type="Zuordnung">';
    $xml .= '<Name>'.htmlReady($data['title'], false).'</Name>';
    $xml .= '<Hint>'.htmlReady($data['options']['hint'], false).'</Hint>';
    $xml .= '<CommentField Visible="'.$data['options']['comment'].'"></CommentField>';
    $xml .= '<Question>';
    $xml .= '<Paragraph>'.htmlReady($data['description'], false).'</Paragraph>';
    $xml .= '</Question>';
    foreach ($data['task']['groups'] as $i => $group) {
        $xml .= '<Default ID="'.$i.'">'.htmlReady($group, false).'</Default>';
    }
    foreach ($data['task']['answers'] as $i => $answer) {
        $xml .= '<Answer ID="'.$i.'" Default="'.$answer['group'].'">'.htmlReady($answer['text'], false).'</Answer>';
    }

    if ($data['options']['feedback'] != '') {
        $xml .= '<MistakeComment>'.htmlReady($data['options']['feedback'], false).'</MistakeComment>';
    }

    $xml .= '</TestItem>';

    return preg_replace("/[^\t\n\r -\xFF]/", '', $xml);
}

/**
 * Initialize this instance from the database representation.
 */
function sc_exercise_to_json($type, $dom_xml)
{
    $data = base_exercise_to_json($type, $dom_xml);

    $answers = $dom_xml->getElementsByTagName('Answer');

    $data['task'] = [];

    foreach ($answers as $node) {
        $group = (int) $node->getAttribute('group');
        $data['task'][$group]['answers'][] = [
            'text'  => studip_utf8decode($node->textContent),
            'score' => (int) $node->getAttribute('Correct')
        ];
    }

    return $data;
}

/**
 * Generate the XML database representation for this exercise.
 */
function sc_exercise_to_xml($data)
{
    $xml  = "<TestItem ID=\"" . $data['type'] . "\" Type=\"SingleChoice\">";
    $xml .= "<Name>".htmlReady($data['title'], false)."</Name>";
    $xml .= "<Hint>".htmlReady($data['options']['hint'], false)."</Hint>";
    $xml .= "<CommentField Visible=\"".$data['options']['comment']."\"></CommentField>";
    $xml .= "<Question>";
    $xml .= "<Paragraph>".htmlReady($data['description'], false)."</Paragraph>";
    $xml .= "</Question>";

    foreach ($data['task'] as $group => $task) {
        foreach ($task['answers'] as $answer) {
            $xml .= '<Answer group="'.$group.'" Correct="'.$answer['score'].'">'.htmlReady($answer['text'], false).'</Answer>';
        }
    }

    if ($data['options']['feedback'] != '') {
        $xml .= '<MistakeComment>'.htmlReady($data['options']['feedback'], false).'</MistakeComment>';
    }

    $xml .= "</TestItem>";

    return preg_replace("/[^\t\n\r -\xFF]/", '', $xml);
}

/**
 * Initialize this instance from the database representation.
 */
function sco_exercise_to_json($type, $dom_xml)
{
    return sc_exercise_to_json($type, $dom_xml);
}

/**
 * Generate the XML database representation for this exercise.
 */
function sco_exercise_to_xml($data)
{
    return sc_exercise_to_xml($data);
}

/**
 * Initialize this instance from the database representation.
 */
function tb_exercise_to_json($type, $dom_xml)
{
    $data = base_exercise_to_json($type, $dom_xml);

    $answer  = $dom_xml->getElementsByTagName('Answer')->item(0);
    $de_node = $dom_xml->getElementsByTagName('AnswerDefault')->item(0);
    $cp_node = $dom_xml->getElementsByTagName('CharacterPicker')->item(0);
    $ad_node = $dom_xml->getElementsByTagName('AnswerDistance')->item(0);

    $data['task']['answers'][0] = [
        'text'  => studip_utf8decode($answer->textContent),
        'score' => 1
    ];

    $data['task']['template'] = studip_utf8decode($de_node->textContent);
    $data['options']['lang']  = studip_utf8decode($cp_node->textContent);
    $data['task']['compare']  = vips_compare_mode($ad_node->textContent);

    return $data;
}

/**
 * Generate the XML database representation for this exercise.
 */
function tb_exercise_to_xml($data)
{
    $xml  = "<TestItem ID=\"" . $data['type'] . "\" Type=\"Text Box\">";
    $xml .= "<Name>".htmlReady($data['title'], false)."</Name>";
    $xml .= "<Hint>".htmlReady($data['options']['hint'], false)."</Hint>";
    $xml .= "<CommentField Visible=\"".$data['options']['comment']."\"></CommentField>";
    $xml .= "<AnswerDefault>".htmlReady($data['task']['template'], false)."</AnswerDefault>";
    $xml .= "<AnswerDistance>".vips_compare_mode_id($data['task']['compare'])."</AnswerDistance>";
    $xml .= "<Question>";
    $xml .= "<Paragraph>".htmlReady($data['description'], false)."</Paragraph>";
    $xml .= "</Question>";
    $xml .= "<Answer>".htmlReady($data['task']['answers'][0]['text'], false)."</Answer>";

    if ($data['options']['lang']) {
        $xml .= '<CharacterPicker>' . $data['options']['lang'] . '</CharacterPicker>';
    }

    if ($data['options']['feedback'] != '') {
        $xml .= '<MistakeComment>'.htmlReady($data['options']['feedback'], false).'</MistakeComment>';
    }

    $xml .= "</TestItem>";

    return preg_replace("/[^\t\n\r -\xFF]/", '', $xml);
}

/**
 * Initialize this instance from the database representation.
 */
function yn_exercise_to_json($type, $dom_xml)
{
    return sc_exercise_to_json($type, $dom_xml);
}

/**
 * Generate the XML database representation for this exercise.
 */
function yn_exercise_to_xml($data)
{
    return sc_exercise_to_xml($data);
}
