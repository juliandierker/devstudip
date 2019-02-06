<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>

<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1.xsd" identifier="TextBox" title="<?= vips_xml_encode($exercise->title) ?>" adaptive="false" timeDependent="false">
    <responseDeclaration identifier="RESPONSE" cardinality="single" baseType="string">
        <correctResponse>
            <? /* can only test on equality... */ ?>
            <value><?= vips_qti_format($exercise->task['answers'][0]['text']) ?></value>
        </correctResponse>
    </responseDeclaration>

    <outcomeDeclaration identifier="SCORE" cardinality="single" baseType="float" />

    <itemBody>
        <p><?= vips_qti_format($exercise->description) ?></p>

        <? if (!empty($exercise->options['hint'])) : ?>
            <p><?= _vips('Hinweise zur Aufgabe:') ?> <?= vips_qti_format($exercise->options['hint']) ?></p>
        <? endif ?>

        <extendedTextInteraction responseIdentifier="RESPONSE"/>
    </itemBody>
</assessmentItem>
