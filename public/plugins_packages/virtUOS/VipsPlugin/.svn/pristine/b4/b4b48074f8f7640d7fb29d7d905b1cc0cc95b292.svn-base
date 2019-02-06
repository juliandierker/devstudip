<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>

<imsx_POXEnvelopeResponse xmlns="http://www.imsglobal.org/lis/oms1p0/pox">
    <imsx_POXHeader>
        <imsx_POXResponseHeaderInfo>
            <imsx_version>V1.0</imsx_version>
            <imsx_messageIdentifier><?= vips_xml_encode($message_id) ?></imsx_messageIdentifier>
            <imsx_statusInfo>
                <imsx_codeMajor><?= vips_xml_encode($status_code) ?></imsx_codeMajor>
                <imsx_severity><?= vips_xml_encode($status_severity) ?></imsx_severity>
                <imsx_description><?= vips_xml_encode($description) ?></imsx_description>
                <imsx_messageRefIdentifier><?= vips_xml_encode($message_ref) ?></imsx_messageRefIdentifier>
            </imsx_statusInfo>
        </imsx_POXResponseHeaderInfo>
    </imsx_POXHeader>
    <imsx_POXBody>
        <? if ($operation == 'readResultRequest'): ?>
            <readResultResponse>
                <result>
                    <resultScore>
                        <language>en</language>
                        <textString><?= vips_xml_encode($score) ?></textString>
                    </resultScore>
                </result>
            </readResultResponse>
        <? elseif ($operation == 'replaceResultRequest'): ?>
            <replaceResultResponse/>
        <? elseif ($operation == 'deleteResultRequest'): ?>
            <deleteResultResponse/>
        <? endif ?>
    </imsx_POXBody>
</imsx_POXEnvelopeResponse>
