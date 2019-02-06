<!-- start print_mc_exercise -->

<div class="mc_list">
    <? foreach ($exercise->task['answers'] as $key => $entry): ?>
        <div class="<?= $show_solution && $entry['score'] ? 'correct_item' : 'mc_item' ?>">
            <? if ($response[$key]): ?>
                <?= Assets::img(vips_image_url('choice_checked.svg')) ?>
            <? else: ?>
                <?= Assets::img(vips_image_url('choice_unchecked.svg')) ?>
            <? endif ?>

            <?= formatReady($entry['text']) ?>

            <? if ($print_correction): ?>
                <? if ($response[$key] == $entry['score']): ?>
                    <?= Icon::create('accept', 'status-green', ['class' => 'correction_marker', 'title' => _vips('richtig')]) ?>
                <? else: ?>
                    <?= Icon::create('decline', 'status-red', ['class' => 'correction_marker', 'title' => _vips('falsch')]) ?>
                <? endif ?>
            <? endif ?>
        </div>
    <? endforeach ?>
</div>

<?= $this->render_partial('exercises/evaluation_mode_info', ['correct' => $show_solution]) ?>

<!-- end print_mc_exercise -->
