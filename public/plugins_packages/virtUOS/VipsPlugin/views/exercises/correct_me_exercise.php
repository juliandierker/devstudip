<!-- start correct_me_exercise -->

<div class="label_text">
    <?= _vips('Antwort:') ?>
</div>

<?= htmlReady($response[0]) ?>

<? if ($results[0]['points'] == 1): ?>
    <?= Icon::create('accept', 'status-green', ['title' => _vips('richtig')]) ?>
<? else: ?>
    <?= Icon::create('decline', 'status-red', ['title' => _vips('falsch')]) ?>
<? endif ?>

<? if ($exercise->task['answers'][0]['text'] != ''): ?>
    <div class="label_text">
        <?= _vips('Musterlösung:') ?>
    </div>

    <?= htmlReady($exercise->task['answers'][0]['text']) ?>
<? endif ?>

<!-- end correct_me_exercise -->
