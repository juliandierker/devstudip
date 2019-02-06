<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>

<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1.xsd" identifier="Association" title="<?= vips_xml_encode($exercise->title) ?>" adaptive="false" timeDependent="false">
    <responseDeclaration identifier="RESPONSE" cardinality="multiple" baseType="directedPair">
        <correctResponse>
            <? foreach ($exercise->task['answers'] as $i => $answer) : ?>
                <value>answer<?= $i ?> default<?= $answer['group'] ?></value>
            <? endforeach ?>
        </correctResponse>
        <mapping defaultValue="0">
            <? foreach ($exercise->task['answers'] as $i => $answer) : ?>
                <mapEntry mapKey="answer<?= $i ?> default<?= $answer['group'] ?>" mappedValue="1" />
            <? endforeach ?>
        </mapping>
    </responseDeclaration>

    <outcomeDeclaration identifier="SCORE" cardinality="single" baseType="integer"/>

    <itemBody>
        <p><?= vips_qti_format($exercise->description) ?></p>

        <? if (!empty($exercise->options['hint'])) : ?>
            <p><?= _vips('Hinweise zur Aufgabe:') ?> <?= vips_qti_format($exercise->options['hint']) ?></p>
        <? endif ?>

        <matchInteraction responseIdentifier="RESPONSE" shuffle="true" maxAssociations="<?= count($exercise->task['answers']) ?>"><? /* shuffle="false" doesn't work... */ ?>
            <prompt /><? /* work around a bug in R2Q2 parser */ ?>
            <simpleMatchSet>
                <? foreach ($exercise->task['answers'] as $i => $answer) : ?>
                    <simpleAssociableChoice identifier="answer<?= $i ?>" fixed="true" matchMax="1"><?= vips_qti_format($answer['text']) ?></simpleAssociableChoice>
                <? endforeach ?>
            </simpleMatchSet>

            <simpleMatchSet>
                <? foreach ($exercise->task['groups'] as $i => $group) : ?>
                    <simpleAssociableChoice identifier="default<?= $i ?>" fixed="true" matchMax="1"><?= vips_qti_format($group) ?></simpleAssociableChoice>
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
