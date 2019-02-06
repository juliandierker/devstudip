<!-- start solve_cloze_exercise -->

<div>
<? foreach (explode('[[]]', formatReady($exercise->task['text'])) as $blank => $text): ?>
<?= $text ?>
<? if (isset($exercise->task['answers'][$blank])) : ?>
<? if ($exercise->task['select']): ?>
<select name="answer[<?= $blank ?>]">
    <? foreach ($exercise->task['answers'][$blank] as $option): ?>
        <option value="<?= htmlReady($option['text']) ?>" <?= trim($option['text']) === $response[$blank] ? ' selected' : ''?>><?= htmlReady($option['text']) ?></option>
    <? endforeach ?>
</select>
<? else: ?>
<input type="text" class="character_input" name="answer[<?= $blank ?>]" style="width: <?= vips_input_size($exercise->task['answers'][$blank]) ?>em;" value="<?= htmlReady($response[$blank]) ?>"><??>
<? endif ?>
<? endif ?>
<? endforeach ?>
</div>

<!-- end solve_cloze_exercise -->
