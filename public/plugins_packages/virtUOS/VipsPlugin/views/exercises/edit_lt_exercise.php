<div class="label_text">
    <?= _vips('Antwortalternativen') ?>
</div>

<div class="dynamic_list">
    <? foreach ($exercise->task['answers'] as $i => $answer): ?>
        <div class="dynamic_row mc_row">
            <label class="dynamic_counter undecorated" style="padding: 1ex;">
                <input class="character_input" name="answer[<?= $i ?>]" type="text" value="<?= htmlReady($answer['text']) ?>">
            </label>
            <label class="undecorated" style="padding: 1ex;">
                <input type="radio" name="correct[<?= $i ?>]" value="1"<? if ($answer['score'] == 1): ?> checked<? endif ?>>
                <?= _vips('richtig') ?>
            </label>
            <label class="undecorated" style="padding: 1ex;">
                <input type="radio" name="correct[<?= $i ?>]" value="0.5"<? if ($answer['score'] == 0.5): ?> checked<? endif ?>>
                <?= _vips('teils richtig') ?>
            </label>
            <label class="undecorated" style="padding: 1ex;">
                <input type="radio" name="correct[<?= $i ?>]" value="0"<? if ($answer['score'] == 0): ?> checked<? endif ?>>
                <?= _vips('falsch') ?>
            </label>

            <a href="#" class="delete_dynamic_row">
                <?= Icon::create('trash', 'clickable', ['title' => _vips('Antwort löschen')]) ?>
            </a>
        </div>
    <? endforeach ?>

    <div class="dynamic_row mc_row template">
        <label class="dynamic_counter undecorated" style="padding: 1ex;">
            <input class="character_input" data-name="answer" type="text">
        </label>
        <label class="undecorated" style="padding: 1ex;">
            <input type="radio" data-name="correct" value="1">
            <?= _vips('richtig') ?>
        </label>
        <label class="undecorated" style="padding: 1ex;">
            <input type="radio" data-name="correct" value="0.5">
            <?= _vips('teils richtig') ?>
        </label>
        <label class="undecorated" style="padding: 1ex;">
            <input type="radio" data-name="correct" value="0" checked>
            <?= _vips('falsch') ?>
        </label>

        <a href="#" class="delete_dynamic_row">
            <?= Icon::create('trash', 'clickable', ['title' => _vips('Antwort löschen')]) ?>
        </a>
    </div>

    <?= vips_button(_vips('Antwort hinzufügen'), 'add_answer', ['class' => 'add_dynamic_row']) ?>
</div>

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
    <?= _vips('Ähnlichkeitsberechnung') ?>

    <? if ($exercise->task['compare']): ?>
        <? $checked[$exercise->task['compare']] = 'selected' ?>
    <? endif ?>

    <select name="answer_distance">
        <option value="">
            <?= _vips('keine') ?>
        </option>
        <option value="levenshtein" <?= $checked['levenshtein'] ?>>
            <?= _vips('Levenshtein-Distanz') ?>
        </option>
        <option value="soundex" <?= $checked['soundex'] ?>>
            <?= _vips('Soundex-Aussprache') ?>
        </option>
    </select>
</label>
