<label>
    <?= _vips('Vorgegebene Lösung (optional)') ?>
    <textarea name="answer_default" class="monospace size-l" rows="<?= vips_textarea_size($exercise->task['template']) ?>"><?= htmlReady($exercise->task['template']) ?></textarea>
</label>

<label>
    <?= _vips('Musterlösung') ?>
    <textarea name="answer_0" class="monospace size-l" rows="<?= vips_textarea_size($exercise->getPrologText()) ?>"><?= htmlReady($exercise->getPrologText()) ?></textarea>
</label>

<label>
    <?= _vips('Vorgegebene Anfrage') ?>
    <input type="text" name="query_default" class="monospace" value="<?= htmlReady($exercise->task['input']) ?>">
</label>
