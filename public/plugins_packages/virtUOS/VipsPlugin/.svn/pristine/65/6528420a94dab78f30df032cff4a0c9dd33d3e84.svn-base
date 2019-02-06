<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>

<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1.xsd" identifier="SingleChoice" title="<?= vips_xml_encode($exercise->title) ?>" adaptive="false" timeDependent="false">

    <? foreach ($exercise->task as $block => $task) :  /* each block has its own RESPONSE variable */ ?>
        <responseDeclaration identifier="RESPONSE<?= $block ?>" cardinality="single" baseType="identifier">
            <correctResponse>
                <? foreach ($task['answers'] as $i => $answer) : ?>
                    <? if ($answer['score'] == 1) : ?>
                        <value>choice<?= $i ?></value>
                    <? endif ?>
                <? endforeach ?>
            </correctResponse>
        </responseDeclaration>
    <? endforeach ?>

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

        <? foreach ($exercise->task as $block => $task) :  /* each block */ ?>
            <choiceInteraction responseIdentifier="RESPONSE<?= $block ?>" shuffle="false" maxChoices="1">
                <? foreach ($task['answers'] as $index => $answer) :  /* each answer in the block */ ?>
                    <simpleChoice identifier="choice<?= $index ?>">
                        <?= vips_qti_format($answer['text']) ?>
                    </simpleChoice>
                <? endforeach ?>
            </choiceInteraction>
        <? endforeach ?>
    </itemBody>

    <responseProcessing>
        <? for ($i = 0; $i < count($exercise->task); $i++) : ?>
            <responseCondition>
                <responseIf>
                    <match>
                        <variable identifier="RESPONSE<?= $i ?>"/>
                        <correct identifier="RESPONSE<?= $i ?>"/>
                    </match>
                    <setOutcomeValue identifier="SCORE">
                        <sum>
                            <variable identifier="SCORE"/>
                            <baseValue baseType="integer">1</baseValue>
                        </sum>
                    </setOutcomeValue>
                </responseIf>
                <responseElse>
                    <setOutcomeValue identifier="SCORE">
                        <sum>
                            <variable identifier="SCORE"/>
                            <baseValue baseType="integer"><?= $evaluation_mode == 0 ? 0 : -1 ?></baseValue>
                        </sum>
                    </setOutcomeValue>
                </responseElse>
            </responseCondition>
        <? endfor ?>
    </responseProcessing>

</assessmentItem>
