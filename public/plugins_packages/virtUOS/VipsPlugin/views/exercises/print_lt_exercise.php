<!-- start print_lt_exercise -->

<? if (isset($response)) : ?>
    <?= htmlReady($response[0]) ?>

    <? if ($print_correction): ?>
        <? if ($results[0]['points'] == 1): ?>
            <?= Icon::create('accept', 'status-green', ['title' => _vips('richtig')]) ?>
        <? else: ?>
            <? if ($results[0]['points'] == 0.5): ?>
                <?= Icon::create('decline', 'status-yellow', ['title' => _vips('fast richtig')]) ?>
            <? else: ?>
                <?= Icon::create('decline', 'status-red', ['title' => _vips('falsch')]) ?>
            <? endif ?>
        <? endif ?>
    <? endif ?>
<? else : ?>
    <div style="height: 6em;"></div>
<? endif ?>

<? if ($show_solution && $exercise->correctAnswers()) : ?>
    <div>
        <?= _vips('Richtige Antworten:') ?>

        <span class="correct_item">
            <?= htmlReady(implode(' | ', $exercise->correctAnswers())) ?>
        </span>
    </div>
<? endif ?>

<!-- end print_lt_exercise -->
