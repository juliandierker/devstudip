<form class="default" action="<?= vips_link('sheets/edit_exercise') ?>" method="POST">
    <input type="hidden" name="assignment_id" value="<?= $assignment_id ?>">

    <label>
        <?= _vips('Aufgabentyp auswählen') ?>

        <select name="new_exercise_type">
            <? foreach ($exercise_types as $uri => $entry): ?>
                <option value="<?= $uri ?>"<?= $exercise_type == $uri ? ' selected' : '' ?>>
                    <?= htmlReady($entry['name']) ?>
                </option>
            <? endforeach ?>
        </select>
    </label>

    <footer data-dialog-button>
        <?= vips_button(_vips('Erstellen'), 'create_exercise') ?>
    </footer>
</form>
