<!-- start solve_yn_exercise -->

<div class="label_text">
    <?= _vips('Antwort:') ?>
</div>

<? foreach ($exercise->task[0]['answers'] as $key => $answer): ?>
    <label>
        <input type="radio" name="answer[0]" value="<?= $key ?>"<? if ($response[0] === "$key"): ?> checked<? endif ?>>
        <?= htmlReady($answer['text']) ?>
    </label>
<? endforeach ?>

<!-- end solve_yn_exercise -->
