<form class="default" action="<?= vips_link('sheets/assign_block') ?>" method="POST">
    <? foreach ($assignment_ids as $assignment_id): ?>
        <input type="hidden" name="assignment_ids[]" value="<?= $assignment_id ?>">
    <? endforeach ?>

    <label>
        <?= _vips('Block auswählen') ?>

        <select name="block_id">
            <option value="0">
                <?= _vips('Keinem Block zuweisen') ?>
            </option>
            <? foreach ($blocks as $block): ?>
                <option value="<?= $block->id ?>">
                    <?= htmlReady($block->name) ?>
                </option>
            <? endforeach ?>
        </select>
    </label>

    <footer data-dialog-button>
        <?= vips_button(_vips('Zuweisen'), 'assign_block') ?>
    </footer>
</form>
