<!-- start correct_cloze_exercise -->

<div>
<? foreach (explode('[[]]', formatReady($exercise->task['text'])) as $blank => $text) : ?>
<?= $text ?>
<? if (isset($exercise->task['answers'][$blank])) : ?>
<? if ($results[$blank]['points'] == 1) :  /* correct answer */ ?>
<span style="color: green; white-space: pre;"><?= htmlReady($response[$blank]) ?></span><??>
<?= Icon::create('accept', 'status-green', ['title' => _vips('richtig')]) ?><??>
<? else : ?>
<? if ($results[$blank]['points'] == 0.5) :  /* partly correct answer */ ?>
<span style="color: magenta; white-space: pre;"><?= htmlReady($response[$blank]) ?></span><??>
<?= Icon::create('decline', 'status-yellow', ['title' => _vips('fast richtig')]) ?><??>
<? elseif (!vips_has_status('tutor') || !$korrektur || $results[$blank]['safe']):  /* wrong answer */ ?>
<span style="color: red; white-space: pre;"><?= htmlReady($response[$blank]) ?></span><??>
<?= Icon::create('decline', 'status-red', ['title' => _vips('falsch')]) ?><??>
<? else :  /* wrong answer */ ?>
<span style="color: red; white-space: pre;"><?= htmlReady($response[$blank]) ?></span><??>
<?= Icon::create('question', 'status-red', ['title' => _vips('unbekannte Antwort')]) ?><??>
<? endif ?>
<span class="correct_item"><?= htmlReady(implode(' | ', $exercise->correctAnswers($blank))) ?></span><??>
<? endif ?>
<? endif ?>
<? endforeach ?>
</div>

<?= $this->render_partial('exercises/evaluation_mode_info', ['correct' => true, 'evaluation_mode' => false]) ?>

<!-- end correct_cloze_exercise -->
