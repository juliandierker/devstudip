<!-- start correct_yn_exercise -->

<div class="label_text">
    <?= _vips('Antwort:') ?>
</div>

<div class="mc_list">
    <? foreach ($exercise->task[0]['answers'] as $key => $answer): ?>
        <div class="<?= $answer['score'] == 1 ? 'correct_item' : 'mc_item' ?>">
            <? if ($response[0] === "$key"): ?>
                <?= Assets::img(vips_image_url('choice_checked.svg')) ?>
            <? else: ?>
                <?= Assets::img(vips_image_url('choice_unchecked.svg')) ?>
            <? endif ?>

            <?= htmlReady($answer['text']) ?>

            <? if ($response[0] === "$key"): ?>
                <? if ($answer['score'] == 1): ?>
                    <?= Icon::create('accept', 'status-green', ['class' => 'correction_marker', 'title' => _vips('richtig')]) ?>
                <? else: ?>
                    <?= Icon::create('decline', 'status-red', ['class' => 'correction_marker', 'title' => _vips('falsch')]) ?>
                <? endif ?>
            <? endif ?>
        </div>
    <? endforeach ?>
</div>

<?= $this->render_partial('exercises/evaluation_mode_info', ['correct' => true, 'evaluation_mode' => false]) ?>

<!-- end correct_yn_exercise -->
