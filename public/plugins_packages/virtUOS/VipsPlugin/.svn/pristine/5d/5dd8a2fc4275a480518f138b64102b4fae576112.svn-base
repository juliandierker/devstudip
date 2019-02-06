<? if (count($assignment_data) == 0): ?>
    <?= MessageBox::info(_vips('Es wurden noch keine Aufgabenblätter eingerichtet.')) ?>
<? endif ?>

<? foreach ($assignment_data as $i => $assignment_list): ?>
    <? if (count($assignment_list['assignments']) || $assignment_list['block_id']): ?>
        <?= $this->render_partial('sheets/list_assignments_list', ['i' => $i] + $assignment_list) ?>
    <? endif ?>
<? endforeach ?>
