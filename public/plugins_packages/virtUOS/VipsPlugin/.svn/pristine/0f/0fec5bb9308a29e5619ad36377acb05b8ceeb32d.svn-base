<form class="default" action="<?= vips_link('sheets/move_assignments') ?>" method="POST">
    <? foreach ($assignment_ids as $assignment_id): ?>
        <input type="hidden" name="assignment_ids[]" value="<?= $assignment_id ?>">
    <? endforeach ?>

    <label>
        <?= _vips('Veranstaltung auswählen') ?>

        <select name="course_id">
            <? foreach ($courses as $course): ?>
                <option value="<?= $course->id ?>">
                    <?= htmlReady($course->name) ?> (<?= htmlReady($course->start_semester->name) ?>)
                </option>
            <? endforeach ?>
        </select>
    </label>

    <footer data-dialog-button>
        <?= vips_button(_vips('Verschieben'), 'move_assignments') ?>
    </footer>
</form>
