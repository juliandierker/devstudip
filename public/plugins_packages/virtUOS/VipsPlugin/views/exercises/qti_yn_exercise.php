<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>

<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1.xsd" identifier="YesNo" title="<?= vips_xml_encode($exercise->title) ?>" adaptive="false" timeDependent="false">

    <responseDeclaration identifier="RESPONSE" cardinality="single" baseType="identifier">
        <correctResponse>
            <value>choice<?= $exercise->task[0]['answers'][1]['score'] ?></value>
        </correctResponse>
    </responseDeclaration>

    <outcomeDeclaration identifier="SCORE" cardinality="single" baseType="integer" normalMinimum="0">
        <defaultValue>
            <value>0</value>
        </defaultValue>
    </outcomeDeclaration>

    <itemBody>
        <p><?= vips_qti_format($exercise->description) ?></p>

        <? if (!empty($exercise->options['hint'])) : ?>
            <p><?= _vips('Hinweise zur Aufgabe:') ?> <?= vips_qti_format($exercise->options['hint']) ?></p>
        <? endif ?>

        <choiceInteraction responseIdentifier="RESPONSE" shuffle="false" maxChoices="1">
            <simpleChoice identifier="choice0">
                <?= vips_qti_format($exercise->task[0]['answers'][0]['text']) ?>
            </simpleChoice>
            <simpleChoice identifier="choice1">
                <?= vips_qti_format($exercise->task[0]['answers'][1]['text']) ?>
            </simpleChoice>
        </choiceInteraction>
    </itemBody>

    <responseProcessing>
        <responseCondition>
            <responseIf>
                <match>
                    <variable identifier="RESPONSE"/>
                    <correct identifier="RESPONSE"/>
                </match>
                <setOutcomeValue identifier="SCORE">
                    <baseValue baseType="integer">1</baseValue>
                </setOutcomeValue>
            </responseIf>
        </responseCondition>
    </responseProcessing>

</assessmentItem>
