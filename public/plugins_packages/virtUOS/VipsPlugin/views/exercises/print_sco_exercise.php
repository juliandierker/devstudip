<!-- start print_sco_exercise -->

<div class="mc_list">
    <? foreach ($exercise->task as $group => $task): ?>
        <div <?= $group ? 'class="group_separator"' : '' ?>>
            <? foreach ($task['answers'] + [-1 => ['text' => _vips('keine Antwort')]] as $key => $entry): ?>
                <div class="<?= $show_solution && $entry['score'] == 1 ? 'correct_item' : 'mc_item' ?>">
                    <? if ($response[$group] === "$key"): ?>
                        <?= Assets::img(vips_image_url('choice_checked.svg')) ?>
                    <? else: ?>
                        <?= Assets::img(vips_image_url('choice_unchecked.svg')) ?>
                    <? endif ?>

                    <?= formatReady($entry['text']) ?>

                    <? if ($print_correction): ?>
                        <? if ($response[$group] === "$key"): ?>
                            <? if ($entry['score'] == 1): ?>
                                <?= Icon::create('accept', 'status-green', ['class' => 'correction_marker', 'title' => _vips('richtig')]) ?>
                            <? elseif ($key != -1): ?>
                                <?= Icon::create('decline', 'status-red', ['class' => 'correction_marker', 'title' => _vips('falsch')]) ?>
                            <? endif ?>
                        <? endif ?>
                    <? endif ?>
                </div>
            <? endforeach ?>
        </div>
    <? endforeach ?>
</div>

<?= $this->render_partial('exercises/evaluation_mode_info', ['correct' => $show_solution]) ?>

<!-- end print_sco_exercise -->
