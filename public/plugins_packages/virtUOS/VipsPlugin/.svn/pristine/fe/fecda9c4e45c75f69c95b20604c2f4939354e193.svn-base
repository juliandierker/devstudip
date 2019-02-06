<!-- start correct_sc_exercise -->

<div class="label_text">
    <?= _vips('Antworten:') ?>
</div>

<div class="mc_list">
    <? foreach ($exercise->task as $group => $task): ?>
        <div <?= $group ? 'class="group_separator"' : '' ?>>
            <? foreach ($task['answers'] as $key => $entry): ?>
                <div class="<?= $entry['score'] == 1 ? 'correct_item' : 'mc_item' ?>">
                    <? if ($response[$group] === "$key"): ?>
                        <?= Assets::img(vips_image_url('choice_checked.svg')) ?>
                    <? else: ?>
                        <?= Assets::img(vips_image_url('choice_unchecked.svg')) ?>
                    <? endif ?>

                    <?= formatReady($entry['text']) ?>

                    <? if ($response[$group] === "$key"): ?>
                        <? if ($entry['score'] == 1): ?>
                            <?= Icon::create('accept', 'status-green', ['class' => 'correction_marker', 'title' => _vips('richtig')]) ?>
                        <? else: ?>
                            <?= Icon::create('decline', 'status-red', ['class' => 'correction_marker', 'title' => _vips('falsch')]) ?>
                        <? endif ?>
                    <? endif ?>
                </div>
            <? endforeach ?>
        </div>
    <? endforeach ?>
</div>

<?= $this->render_partial('exercises/evaluation_mode_info', ['correct' => true]) ?>

<!-- end correct_sc_exercise -->
