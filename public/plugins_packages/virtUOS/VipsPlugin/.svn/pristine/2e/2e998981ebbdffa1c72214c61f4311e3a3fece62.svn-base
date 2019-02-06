<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>

<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1.xsd" identifier="ClozeExercise" title="<?= vips_xml_encode($exercise->title) ?>" adaptive="false" timeDependent="false">
    <? for ($i = 0; $i < count($exercise->task['answers']); $i++) : ?>
        <responseDeclaration identifier="RESPONSE<?= $i ?>" cardinality="single" baseType="string">
            <mapping defaultValue="0">
                <? foreach ($exercise->correctAnswers($i) as $option) : ?>
                    <mapEntry mapKey="<?= $option ?>" mappedValue="1" />
                <? endforeach ?>
            </mapping>
        </responseDeclaration>
    <? endfor ?>

    <outcomeDeclaration identifier="SCORE" cardinality="single" baseType="float">
        <defaultValue>
            <value>0</value>
        </defaultValue>
    </outcomeDeclaration>

    <itemBody>
        <? if (!empty($exercise->options['hint'])) : ?>
            <p><?= _vips('Hinweise zur Aufgabe:') ?> <?= vips_qti_format($exercise->options['hint']) ?></p>
        <? endif ?>
<p>
<? foreach (explode('[[]]', vips_qti_format($exercise->task['text'])) as $blank => $text): ?>
<?= $text ?>
<? if (isset($exercise->task['answers'][$blank])) : ?>
<textEntryInteraction responseIdentifier="RESPONSE<?= $blank ?>"/><??>
<? endif ?>
<? endforeach ?>
</p>
    </itemBody>

    <responseProcessing>
        <? for ($i = 0; $i < count($exercise->task['answers']); $i++) : ?>
            <responseCondition>
                <responseIf>
                    <not>
                        <isNull>
                            <variable identifier="RESPONSE<?= $i ?>"/>
                        </isNull>
                    </not>
                    <setOutcomeValue identifier="SCORE">
                        <sum>
                            <variable identifier="SCORE" />
                            <mapResponse identifier="RESPONSE<?= $i ?>"/>
                        </sum>
                    </setOutcomeValue>
                </responseIf>
            </responseCondition>
        <? endfor ?>
    </responseProcessing>

</assessmentItem>
