<!-- start print_tb_exercise -->

<? if (isset($response)) : ?>
    <?= htmlReady($response[0]) ?>

    <? if ($print_correction): ?>
        <? if ($results[0]['points'] == 1): ?>
            <?= Icon::create('accept', 'status-green', ['title' => _vips('richtig')]) ?>
        <? else: ?>
            <?= Icon::create('decline', 'status-red', ['title' => _vips('falsch')]) ?>
        <? endif ?>
    <? endif ?>
<? endif ?>

<? if ($show_solution && $exercise->task['answers'][0]['text'] != '') : ?>
    <div class="label_text">
        <?= _vips('Musterlösung:') ?>
    </div>

    <?= htmlReady($exercise->task['answers'][0]['text']) ?>
<? endif ?>

<!-- end print_tb_exercise -->
