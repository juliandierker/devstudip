<? if (count($test_data['assignments'])): ?>
    <? if (vips_has_status('tutor')): ?>
        <?= $this->render_partial('solutions/assignments_list', $test_data) ?>
    <? else: ?>
        <?= $this->render_partial('solutions/assignments_list_student', $test_data) ?>
        <? if (isset($overview_data)): ?>
            <?= $this->render_partial('solutions/student_grade', $overview_data) ?>
        <? endif ?>
    <? endif ?>
<? else: ?>
    <?= MessageBox::info(_vips('Es ist kein beendetes Aufgabenblatt vorhanden.')) ?>
<? endif ?>
