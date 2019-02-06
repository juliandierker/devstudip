<form class="default" action="<?= vips_link('admin/store_block') ?>" method="POST">
    <? if ($block): ?>
        <input type="hidden" name="block_id" value="<?= $block->id ?>">
    <? endif ?>

    <label>
        <span class="required"><?= _vips('Blockname') ?></span>
        <input type="text" name="block_name" required value="<?= htmlReady($block->name) ?>">
    </label>

    <label style="margin-top: 1em;">
        <input type="checkbox" name="block_visible" value="1" <?= !$block || $block->visible ? 'checked' : '' ?>>
        <?= _vips('Aufgabenblätter für Teilnehmer sichtbar') ?>
        <?= tooltipIcon(_vips('Wenn Aufgabenblätter nur in anderen Tools (z.B. Courseware) verwendet werden, können sie in Vips unsichtbar gemacht werden.')) ?>
    </label>

    <footer data-dialog-button>
        <?= vips_accept_button(_vips('Speichern'), 'store_block') ?>
    </footer>
</form>
