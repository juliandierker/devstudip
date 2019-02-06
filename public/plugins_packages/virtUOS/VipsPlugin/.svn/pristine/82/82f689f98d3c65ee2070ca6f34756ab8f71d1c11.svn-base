<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>

<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1.xsd" identifier="MultipleChoiceAbstention" title="<?= vips_xml_encode($exercise->title) ?>" adaptive="false" timeDependent="false">

    <responseDeclaration identifier="RESPONSE" cardinality="multiple" baseType="directedPair">
        <correctResponse>
            <? foreach ($exercise->task['answers'] as $index => $answer) : ?>
                <value>answer<?= $answer['score'] ?> choice<?= $index ?></value>
            <? endforeach ?>
        </correctResponse>
        <mapping<? if ($evaluation_mode != 2) :  /* no overall negative points allowed */ ?> lowerBound="0"<? endif ?> defaultValue="<?= $evaluation_mode == 0 ? 0 : -1 ?>">
            <? foreach ($exercise->task['answers'] as $index => $answer) : ?>
                <mapEntry mapKey="answer<?= $answer['score'] ?> choice<?= $index ?>" mappedValue="1"/>
                <mapEntry mapKey="answerNone choice<?= $index ?>" mappedValue="0"/>
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

        <matchInteraction responseIdentifier="RESPONSE" shuffle="true" maxAssociations="<?= count($exercise->task['answers']) ?>" minAssociations="<?= count($exercise->task['answers']) ?>"><? /* shuffle="false" doesn't work... */ ?>
            <prompt /><? /* work around a bug in R2Q2 parser */ ?>
            <simpleMatchSet>
                <simpleAssociableChoice identifier="answer1" matchMax="<?= count($exercise->task['answers']) ?>" fixed="true">
                    <?= vips_qti_format($exercise->task['choices'][1]) ?>
                </simpleAssociableChoice>
                <simpleAssociableChoice identifier="answer0" matchMax="<?= count($exercise->task['answers']) ?>" fixed="true">
                    <?= vips_qti_format($exercise->task['choices'][0]) ?>
                </simpleAssociableChoice>
                <simpleAssociableChoice identifier="answerNone" matchMax="<?= count($exercise->task['answers']) ?>" fixed="true">
                    <?= _vips('keine Antwort') ?>
                </simpleAssociableChoice>
            </simpleMatchSet>
            <simpleMatchSet>
                <? foreach ($exercise->task['answers'] as $index => $answer) : ?>
                    <simpleAssociableChoice identifier="choice<?= $index ?>" matchMax="1" matchMin="1" fixed="true">
                        <?= vips_qti_format($answer['text']) ?>
                    </simpleAssociableChoice>
                <? endforeach ?>
            </simpleMatchSet>
        </matchInteraction>
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
