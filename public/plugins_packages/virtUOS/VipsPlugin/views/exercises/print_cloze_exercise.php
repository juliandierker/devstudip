<!-- start print_cloze_exercise -->

<div style="margin-bottom: 1em; <?= isset($response) ? '' : 'line-height: 200%;' ?>">

<? foreach (explode('[[]]', formatReady($exercise->task['text'])) as $blank => $text): ?>
<?= $text ?>
<? if (isset($exercise->task['answers'][$blank])) : ?>
<? if (isset($response)) : ?>
<span style="font-style: italic; text-decoration: underline;">&nbsp;&nbsp;&nbsp;<?= htmlReady($response[$blank]) ?>&nbsp;&nbsp;&nbsp;</span><??>
<? if ($print_correction): ?>
<? if ($results[$blank]['points'] == 1) :  /* correct answer */ ?>
<?= Icon::create('accept', 'status-green', ['title' => _vips('richtig')]) ?><??>
<? else :  /* wrong answer */ ?>
<?= Icon::create('decline', 'status-red', ['title' => _vips('falsch')]) ?><??>
<? endif  /* right or wrong answer */ ?>
<? endif  /* print_correction */ ?>
<? elseif ($exercise->task['select']) : ?>
<? foreach ($exercise->task['answers'][$blank] as $index => $option) : ?>
<?= $index ? ' | ' : '' ?>
<?= Assets::img(vips_image_url('choice_unchecked.svg'), ['style' => 'vertical-align: text-bottom;']) ?>
 <span style="border-bottom: 1px dotted black;"><?= htmlReady($option['text']) ?></span>
<? endforeach ?>
<? else : ?>
<?= str_repeat('__', vips_input_size($exercise->task['answers'][$blank])) ?>
<? endif ?>
<? if ($show_solution) :  /* sample solution */ ?>
<span style="border: 1px dashed green; padding: 0px 3px; font-size: smaller;">(<?= htmlReady(implode('/', $exercise->correctAnswers($blank))) ?>)</span><??>
<? endif  /* if show solution */ ?>
<? endif  /* blank or not */ ?>
<? endforeach ?>

</div>

<!-- end print_cloze_exercise -->
