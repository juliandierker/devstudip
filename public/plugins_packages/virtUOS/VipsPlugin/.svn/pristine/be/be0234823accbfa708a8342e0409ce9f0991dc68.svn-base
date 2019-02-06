<div class="label_text">
    <?= _vips('Antwortmöglichkeiten') ?>
</div>

<? foreach ($exercise->task[0]['answers'] as $key => $answer): ?>
    <div class="mc_row">
        <input type="radio" name="correct[0]" value="<?= $key ?>"<? if ($answer['score'] == 1): ?> checked<? endif ?>>
        <input type="text" class="character_input size-s" name="answer[0][<?= $key ?>]" value="<?= htmlReady($answer['text']) ?>">
    </div>
<? endforeach ?>
