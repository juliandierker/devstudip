<!-- start solve_sco_exercise -->

<div class="label_text">
    <?= _vips('Antworten:') ?>
</div>

<? foreach ($exercise->task as $group => $task): ?>
    <div <?= $group ? 'class="group_separator"' : '' ?>>
        <? foreach ($task['answers'] + [-1 => ['text' => _vips('keine Antwort')]] as $key => $entry): ?>
            <label>
                <input type="radio" name="answer[<?= $group ?>]" value="<?= $key ?>"
                    <? if (!isset($response[$group]) && $key == -1 || $response[$group] === "$key"): ?>checked<? endif ?>>
                <?= formatReady($entry['text']) ?>
            </label>
        <? endforeach ?>
    </div>
<? endforeach ?>

<?= $this->render_partial('exercises/evaluation_mode_info') ?>

<!-- end solve_sco_exercise -->
