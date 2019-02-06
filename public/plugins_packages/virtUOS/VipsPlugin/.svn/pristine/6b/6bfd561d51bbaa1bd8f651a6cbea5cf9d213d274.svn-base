<? if ($assignment->id == $selected_id): ?>
    <div class="picker">
        <a href="#" onclick="jQuery('#test_<?= $assignment->id ?>').load(
                '<?= vips_link('sheets/pick_test_ajax', [
                                    'assignment_id' => $assignment_id,
                                    'start_time' => $semester->beginn,
                                    'selected_id' => $assignment->id,
                                    'close' => '1']) ?>'); return false;"
           class="tree">
            <?= Icon::create('arr_1down') ?>
            <?= htmlReady($assignment->test->title) ?>
        </a>
    </div>
    <div style="padding-left: 20px;">
        <table>
            <? foreach ($all_exercises as $exercise): ?>
                <tr>
                    <td>
                        <label class="undecorated">
                            <input type="checkbox" name="exercise_ids[<?= $exercise->id ?>]" value="<?= $assignment->id ?>">
                            <a href="<?= vips_link('sheets/preview_exercise', [
                                                        'assignment_id' => $assignment->id,
                                                        'exercise_id' => $exercise->id]) ?>"
                               data-dialog="id=vips_preview;size=600x400" target="_blank">
                                <?= Icon::create('question-circle', 'clickable', ['title' => _vips('Vorschau anzeigen')]) ?>
                            </a>
                            <?= htmlReady($exercise->title) ?>
                        </label>
                    </td>
                </tr>
            <? endforeach ?>
        </table>
    </div>
<? else: ?>
    <div class="picker">
        <a href="#" onclick="jQuery('#test_<?= $assignment->id ?>').load(
                '<?= vips_link('sheets/pick_test_ajax', [
                                    'assignment_id' => $assignment_id,
                                    'start_time' => $semester->beginn,
                                    'selected_id' => $assignment->id]) ?>'); return false;"
           class="tree">
            <?= Icon::create('arr_1right') ?>
            <?= htmlReady($assignment->test->title) ?>
        </a>
    </div>
<? endif ?>
