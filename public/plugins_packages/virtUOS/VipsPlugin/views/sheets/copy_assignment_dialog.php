<form class="default" action="<?= vips_link('sheets/copy_assignment') ?>" method="POST">
    <label>
        <?= _vips('Vorhandenes Aufgabenblatt kopieren') ?>

        <select name="assignment_id">
            <? foreach ($assignments as $entry): ?>
                <? $current = htmlReady($entry['course_name'].' ('.$entry['sem_name'].')') ?>
                <? if ($optgroup != $current): ?>
                    <? if ($optgroup != ''): ?>
                        </optgroup>
                    <? endif ?>
                    <? $optgroup = $current ?>
                    <optgroup label="<?= htmlReady($optgroup) ?>">
                <? endif ?>
                <option value="<?= $entry['id'] ?>">
                    <?= htmlReady($entry['title']) ?>
                </option>
            <? endforeach ?>
            <? if ($optgroup != ''): ?>
                </optgroup>
            <? endif ?>
        </select>
    </label>

    <footer data-dialog-button>
        <?= vips_accept_button(_vips('Kopieren'), 'copy') ?>
    </footer>
</form>
