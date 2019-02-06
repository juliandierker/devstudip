<!-- start solve_sc_exercise -->

<div class="label_text">
    <?= _vips('Antworten:') ?>
</div>

<? foreach ($exercise->task as $group => $task): ?>
    <div <?= $group ? 'class="group_separator"' : '' ?>>
        <? foreach ($task['answers'] as $key => $entry): ?>
            <label>
                <input type="radio" name="answer[<?= $group ?>]" value="<?= $key ?>"<? if ($response[$group] === "$key"): ?> checked<? endif ?>>
                <?= formatReady($entry['text']) ?>
            </label>
        <? endforeach ?>
    </div>
<? endforeach ?>

<?= $this->render_partial('exercises/evaluation_mode_info') ?>

<!-- end solve_sc_exercise -->
