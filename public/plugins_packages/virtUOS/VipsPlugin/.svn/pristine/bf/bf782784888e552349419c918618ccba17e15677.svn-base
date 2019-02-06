<? setlocale(LC_NUMERIC, NULL) ?>

<? foreach ($test->exercise_refs as $i => $exercise_ref): ?>
    <? $exercise = $exercises[$i] ?>

    <tr id="item_<?= $exercise->id ?>">
        <td class="vips_drag dynamic_counter" style="width: 2em; text-align: right;">
            <!-- position -->
        </td>
        <td title="<?= _vips('Aufgabentyp').': '.htmlReady($exercise->getTypeName()) ?>">
            <!-- exercise title -->
            <a href="<?= vips_link('sheets/edit_exercise', ['assignment_id' => $assignment_id, 'exercise_id' => $exercise->id]) ?>">
                <?= htmlReady($exercise->title) ?>
            </a>
        </td>
        <td class="actions" style="width: 300px; white-space: nowrap;">
            <!-- max points -->
            <label class="undecorated" style="margin-right: 1em;">
                <?= _vips('Punkte') ?>:
                <input name="exercise_points[<?= $exercise->id ?>]" type="text" style="width: 4em;" value="<?= (float) $exercise_ref->points ?>" required>
            </label>

            <!-- arrows for position change -->
            <? if ($exercise_ref->position > 1): ?>
                <a href="<?= vips_link('sheets/move_exercise', ['direction' => 'down', 'exercise_id' => $exercise->id, 'exercise_position' => $exercise_ref->position, 'assignment_id' => $assignment_id]) ?>">
                    <?= Icon::create('arr_2up', 'sort', ['title' => _vips('nach oben verschieben')]) ?>
                </a>
            <? else: ?>
                <?= Assets::img('blank.gif', ['width' => 16, 'height' => 16]) ?>
            <? endif ?>

            <? if ($exercise_ref->position < count($exercises)): ?>
                <a href="<?= vips_link('sheets/move_exercise', ['direction' => 'up', 'exercise_id' => $exercise->id, 'exercise_position' => $exercise_ref->position, 'assignment_id' => $assignment_id]) ?>">
                    <?= Icon::create('arr_2down', 'sort', ['title' => _vips('nach unten verschieben')]) ?>
                </a>
            <? else: ?>
                <?= Assets::img('blank.gif', ['width' => 16, 'height' => 16]) ?>
            <? endif ?>

            <!-- display button -->
            <a href="<?= vips_link('sheets/show_exercise', ['assignment_id' => $assignment_id, 'exercise_id' => $exercise->id]) ?>">
                <?= Icon::create('community', 'clickable', ['title' => _vips('Studentensicht anzeigen')]) ?>
            </a>

            <!-- copy button -->
            <a href="<?= vips_link('sheets/copy_exercise', ['assignment_id' => $assignment_id, 'exercise_id' => $exercise->id]) ?>" onclick="duplicateExercise(event, <?= $exercise->id ?>);">
                <?= Icon::create('assessment+add', 'clickable', ['title' => _vips('Aufgabe kopieren')]) ?>
            </a>

            <!-- delete button -->
            <a href="<?= vips_link('sheets/delete_exercise', ['exercise_id' => $exercise->id, 'assignment_id' => $assignment_id]) ?>"
                onclick="deleteExercise(event, <?= $exercise->id ?>, '<?= jsReady($exercise->title, 'inline-single') ?>');">
                <?= Icon::create('trash', 'clickable', ['title' => _vips('Aufgabe löschen')]) ?>
            </a>
        </td>
    </tr>
<? endforeach ?>

<? setlocale(LC_NUMERIC, 'C') ?>
