<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>

<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1.xsd" identifier="MultipleChoice" title="<?= vips_xml_encode($exercise->title) ?>" adaptive="false" timeDependent="false">

    <responseDeclaration identifier="RESPONSE" cardinality="multiple" baseType="identifier">
        <? foreach ($exercise->task['answers'] as $answer) : ?>
            <? $total_score += $answer['score']; ?>
        <? endforeach ?>
        <? if ($total_score > 0) :  /* one or more correct answers exist */ ?>
            <correctResponse>
                <? foreach ($exercise->task['answers'] as $index => $answer) : ?>
                    <? if ($answer['score'] == 1) : ?>
                        <value>choice<?= $index ?></value>
                    <? endif ?>
                <? endforeach ?>
            </correctResponse>
        <? endif ?>

        <mapping<? if ($evaluation_mode != 2) :  /* no overall negative points allowed */ ?> lowerBound="0"<? endif ?> defaultValue="<?= $evaluation_mode == 0 ? 0 : -1 ?>">
            <? foreach ($exercise->task['answers'] as $index => $answer) : ?>
                <? if ($answer['score'] == 1) : ?>
                    <mapEntry mapKey="choice<?= $index ?>" mappedValue="1"/>
                <? endif ?>
            <? endforeach ?>
        </mapping>
    </responseDeclaration>

    <outcomeDeclaration identifier="SCORE" cardinality="single" baseType="integer"<? if ($evaluation_mode != 2) :  /* no negative points */ ?> normalMinimum="0"<? endif ?>>
        <defaultValue>
            <value>0</value>
        </defaultValue>
    </outcomeDeclaration>

    <itemBody>
        <p><?= vips_qti_format($exercise->description) ?></p>

        <? if (!empty($exercise->options['hint'])) : ?>
            <p><?= _vips('Hinweise zur Aufgabe:') ?> <?= vips_qti_format($exercise->options['hint']) ?></p>
        <? endif ?>

        <choiceInteraction responseIdentifier="RESPONSE" shuffle="false" maxChoices="0"<? /* unlimited */ ?>>
            <? foreach ($exercise->task['answers'] as $index => $answer) : ?>
                <simpleChoice identifier="choice<?= $index ?>">
                    <?= vips_qti_format($answer['text']) ?>
                </simpleChoice>
            <? endforeach ?>
        </choiceInteraction>
    </itemBody>

    <responseProcessing>
        <responseCondition>
            <responseIf>
                <isNull>
                    <variable identifier="RESPONSE"/>
                </isNull>
                <setOutcomeValue identifier="SCORE">
                    <baseValue baseType="integer">0</baseValue>
                </setOutcomeValue>
            </responseIf>
            <responseElse>
                <setOutcomeValue identifier="SCORE">
                    <mapResponse identifier="RESPONSE"/>
                </setOutcomeValue>
            </responseElse>
        </responseCondition>
    </responseProcessing>

</assessmentItem>
