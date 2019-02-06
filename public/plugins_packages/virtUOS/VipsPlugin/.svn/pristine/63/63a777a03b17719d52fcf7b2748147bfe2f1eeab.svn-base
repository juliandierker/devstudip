<!-- start correct_mc_exercise -->

<div class="label_text">
    <?= _vips('Antworten:') ?>
</div>

<div class="mc_list">
    <? foreach ($exercise->task['answers'] as $key => $entry): ?>
        <div class="<?= $entry['score'] ? 'correct_item' : 'mc_item' ?>">
            <? if ($response[$key]): ?>
                <?= Assets::img(vips_image_url('choice_checked.svg')) ?>
            <? else: ?>
                <?= Assets::img(vips_image_url('choice_unchecked.svg')) ?>
            <? endif ?>

            <?= formatReady($entry['text']) ?>

            <? if ($response[$key] == $entry['score']): ?>
                <?= Icon::create('accept', 'status-green', ['class' => 'correction_marker', 'title' => _vips('richtig')]) ?>
            <? else: ?>
                <?= Icon::create('decline', 'status-red', ['class' => 'correction_marker', 'title' => _vips('falsch')]) ?>
            <? endif ?>
        </div>
    <? endforeach ?>
</div>

<?= $this->render_partial('exercises/evaluation_mode_info', ['correct' => true]) ?>

<!-- end correct_mc_exercise -->
