<label>
    <?= _vips('Vorgegebener Text (optional)') ?>
    <textarea class="character_input size-l" name="answer_default" rows="<?= vips_textarea_size($exercise->task['template']) ?>"><?= htmlReady($exercise->task['template']) ?></textarea>
</label>

<label>
    <?= _vips('Musterlösung') ?>
    <textarea class="character_input size-l wysiwyg" name="answer_0" rows="<?= vips_textarea_size($exercise->task['answers'][0]['text']) ?>"><?= vips_wysiwyg_ready($exercise->task['answers'][0]['text']) ?></textarea>
</label>

<label>
    <?= _vips('Zeichenwähler') ?>

    <select name="character_picker">
        <option value="">
            <?= _vips('keiner') ?>
        </option>
        <? foreach ($available_character_sets as $set): ?>
            <option value="<?= $set['name'] ?>"<? if ($exercise->options['lang'] == $set['name']): ?> selected<? endif ?>>
                <?= htmlReady($set['title']) ?>
            </option>
        <? endforeach ?>
    </select>
</label>

<label>
    <input type="checkbox" name="answer_distance" value="levenshtein" <?= $exercise->task['compare'] == 'levenshtein' ? ' checked' : '' ?>>
    <?= _vips('Ähnlichkeitsberechnung über Levenshtein-Distanz') ?>
</label>

<label>
    <input type="checkbox" name="file_upload" value="1" <?= $exercise->options['file_upload'] ? ' checked' : '' ?>>
    <?= _vips('Hochladen von Dateien als Lösung erlauben') ?>
    <?= tooltipIcon(_vips('Hochgeladene Dateien können nicht automatisch bewertet werden.')) ?>
</label>
