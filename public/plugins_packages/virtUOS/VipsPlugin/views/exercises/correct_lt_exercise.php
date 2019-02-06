<!-- start correct_lt_exercise -->

<div class="label_text">
    <?= _vips('Antwort:') ?>
</div>

<?= htmlReady($response[0]) ?>

<? if ($results[0]['points'] == 1): ?>
    <?= Icon::create('accept', 'status-green', ['title' => _vips('richtig')]) ?>
<? else: ?>
    <? if ($results[0]['points'] == 0.5): ?>
        <?= Icon::create('decline', 'status-yellow', ['title' => _vips('fast richtig')]) ?>
    <? elseif (!vips_has_status('tutor') || !$korrektur || $results[0]['safe']): ?>
        <?= Icon::create('decline', 'status-red', ['title' => _vips('falsch')]) ?>
    <? else: ?>
        <?= Icon::create('question', 'status-red', ['title' => _vips('unbekannte Antwort')]) ?>
    <? endif ?>
<? endif ?>

<? if ($exercise->correctAnswers()): ?>
    <div class="label_text">
        <?= _vips('Richtige Antworten:') ?>

        <span class="correct_item">
            <?= htmlReady(implode(' | ', $exercise->correctAnswers())) ?>
        </span>
    </div>
<? endif ?>

<!-- end correct_lt_exercise -->
