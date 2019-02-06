<? if ($semester->beginn == $start_time): ?>
    <div class="picker">
        <a href="#" onclick="jQuery('#sem_<?= $semester->beginn ?>').load(
                '<?= vips_link('sheets/pick_semester_ajax', [
                                    'assignment_id' => $assignment_id,
                                    'start_time' => $semester->beginn,
                                    'close' => '1']) ?>'); return false;"
           class="tree">
            <?= Icon::create('arr_1down') ?>
            <?= htmlReady($semester->name) ?>
        </a>
    </div>
    <div style="padding-left: 20px;">
        <? foreach ($courses as $course): ?>
            <div style="font-weight: bold; padding: 1em 0em;">
                <?= htmlReady($course['name']) ?>
            </div>
            <? foreach ($course['assignments'] as $assignment): ?>
                <div id="test_<?= $assignment->id ?>">
                    <?= $this->render_partial('sheets/pick_test', compact('assignment')) ?>
                </div>
            <? endforeach ?>
        <? endforeach ?>
    </div>
<? else: ?>
    <div class="picker">
        <a href="#" onclick="jQuery('#sem_<?= $semester->beginn ?>').load(
                '<?= vips_link('sheets/pick_semester_ajax', [
                                    'assignment_id' => $assignment_id,
                                    'start_time' => $semester->beginn]) ?>'); return false;"
           class="tree">
            <?= Icon::create('arr_1right') ?>
            <?= htmlReady($semester->name) ?>
        </a>
    </div>
<? endif ?>
