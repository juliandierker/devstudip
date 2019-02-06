<!-- start solve_mc_exercise -->

<div class="label_text">
    <?= _vips('Antworten:') ?>
</div>

<? foreach ($exercise->task['answers'] as $key => $entry): ?>
    <label>
        <input type="checkbox" name="answer[<?= $key ?>]" value="1"<? if ($response[$key]): ?> checked<? endif ?>>
        <?= formatReady($entry['text']) ?>
    </label>
<? endforeach ?>

<?= $this->render_partial('exercises/evaluation_mode_info') ?>

<!-- end solve_mc_exercise -->
