<!-- start print_yn_exercise -->

<div class="mc_list">
    <? foreach ($exercise->task[0]['answers'] as $key => $answer): ?>
        <div class="<?= $show_solution && $answer['score'] == 1 ? 'correct_item' : 'mc_item' ?>">
            <? if ($response[0] === "$key"): ?>
                <?= Assets::img(vips_image_url('choice_checked.svg')) ?>
            <? else: ?>
                <?= Assets::img(vips_image_url('choice_unchecked.svg')) ?>
            <? endif ?>

            <?= htmlReady($answer['text']) ?>

            <? if ($print_correction): ?>
                <? if ($response[0] === "$key"): ?>
                    <? if ($answer['score'] == 1): ?>
                        <?= Icon::create('accept', 'status-green', ['class' => 'correction_marker', 'title' => _vips('richtig')]) ?>
                    <? else: ?>
                        <?= Icon::create('decline', 'status-red', ['class' => 'correction_marker', 'title' => _vips('falsch')]) ?>
                    <? endif ?>
                <? endif ?>
            <? endif ?>
        </div>
    <? endforeach ?>
</div>

<?= $this->render_partial('exercises/evaluation_mode_info', ['correct' => $show_solution, 'evaluation_mode' => false]) ?>

<!-- end print_yn_exercise -->
