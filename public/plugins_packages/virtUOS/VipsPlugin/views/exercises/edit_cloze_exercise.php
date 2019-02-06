<label>
    <?= _vips('Lückentext') ?> <?= tooltipIcon($tooltip, false, true) ?>
    <? $cloze_text = $exercise->getClozeText() ?>
    <textarea name="cloze_text" class="character_input size-l wysiwyg" rows="<?= vips_textarea_size($cloze_text) ?>"><?= vips_wysiwyg_ready($cloze_text) ?></textarea>
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
    <input type="checkbox" name="answer_distance" value="ignorecase" <?= $exercise->task['compare'] == 'ignorecase' ? ' checked' : '' ?>>
    <?= _vips('Groß-/Kleinschreibung bei Auswertung ignorieren') ?>
</label>

<label>
    <input type="checkbox" name="choose_item" value="1" <?= $exercise->task['select'] ? ' checked' : '' ?>>
    <?= _vips('Antwortmodus: Antwort aus Liste auswählen') ?>
</label>
