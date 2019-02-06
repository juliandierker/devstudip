<div class="breadcrumb width-1200">
    <? if (!$exercise->id):  /* creating a new exercise */ ?>
        <a href="<?= vips_link('sheets/edit_assignment', ['assignment_id' => $assignment_id]) ?>">
            &laquo; <?= _vips('ohne Speichern zurück zum Aufgabenblatt') ?>
        </a>
    <? else:  /* editing an existing regular exercise */ ?>
        <? /* previous exercise */ ?>
        <div style="display: inline-block; width: 33%;">
            <? if (isset($prev_exercise_id)): ?>
                <a href="<?= vips_link('sheets/edit_exercise', ['assignment_id' => $assignment_id, 'exercise_id' => $prev_exercise_id]) ?>">
                    <?= Icon::create('arr_1left') ?>
                    <?=_vips('vorige Aufgabe bearbeiten')?>
                </a>
             <? endif ?>
        </div>

        <? /* exercise overview */ ?>
        <div style="display: inline-block; text-align: center; width: 33%;">
            <a href="<?= vips_link('sheets/edit_assignment', ['assignment_id' => $assignment_id]) ?>">
                &bull; <?=_vips('Zurück zum Aufgabenblatt')?> &bull;
            </a>
        </div>

        <? /* next exercise */ ?>
        <div style="display: inline-block; text-align: right; width: 33%;">
            <? if (isset($next_exercise_id)): ?>
                <a href="<?= vips_link('sheets/edit_exercise', ['assignment_id' => $assignment_id, 'exercise_id' => $next_exercise_id]) ?>">
                    <?=_vips('nächste Aufgabe bearbeiten')?>
                    <?= Icon::create('arr_1right') ?>
                </a>
            <? endif ?>
        </div>
    <? endif ?>
</div>

<form class="default width-1200" action="<?= vips_link('sheets/store_exercise') ?>" data-secure method="POST">
    <input type="hidden" name="exercise_type" value="<?= $exercise->type ?>">
    <? if ($exercise->id) : ?>
        <input type="hidden" name="exercise_id" value="<?= $exercise->id ?>">
    <?endif ?>
    <input type="hidden" name="assignment_id" value="<?= $assignment_id ?>">

    <?= vips_accept_button(_vips('Speichern'), 'store_exercise', ['style' => 'display: none;']) ?>

    <fieldset>
        <legend>
            <?= htmlReady($exercise->getTypeName()) ?>
        </legend>

        <label>
            <span class="required"><?= _vips('Titel') ?></span>
            <input type="text" name="exercise_name" class="character_input size-l" value="<?= htmlReady($exercise->title) ?>" required>
        </label>

        <? if ($exercise->type !== 'cloze_exercise') : ?>
            <label>
                <?= _vips('Frage / Aussage') ?>
                <textarea name="exercise_question" class="character_input size-l wysiwyg" rows="<?= vips_textarea_size($exercise->description) ?>"><?= vips_wysiwyg_ready($exercise->description) ?></textarea>
            </label>
        <? endif ?>

        <?= $exercise->getEditTemplate($assignment)->render(compact('assignment_id')) ?>

        <input id="options-toggle" type="checkbox" value="on">
        <label class="caption" for="options-toggle">
            <?= Icon::create('arr_1down', 'clickable', ['class' => 'toggle-open']) ?>
            <?= Icon::create('arr_1right', 'clickable', ['class' => 'toggle-closed']) ?>
            <?= _vips('Weitere Einstellungen') ?>
        </label>

        <label>
            <?= _vips('Hinweise zur Bearbeitung der Aufgabe') ?>
            <textarea name="exercise_hint" class="character_input size-l"><?= htmlReady($exercise->options['hint']) ?></textarea>
        </label>

        <? if ($assignment->type == 'selftest') : ?>
            <label>
                <?= _vips('Automatisches Feedback bei falscher Antwort') ?>
                <textarea name="mistake_comment" class="character_input size-l"><?= htmlReady($exercise->options['feedback']) ?></textarea>
            </label>
        <? else : ?>
            <label>
                <input type="checkbox" name="exercise_comment" value="1" <?= $exercise->options['comment'] ? 'checked' : '' ?>>
                <?= _vips('Eingabe eines Kommentars durch Studierende erlauben') ?>
            </label>
        <? endif ?>
    </fieldset>

    <footer>
        <?= vips_accept_button(_vips('Speichern'), 'store_exercise') ?>
    </footer>
</form>
