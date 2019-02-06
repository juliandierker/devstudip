<form class="default" action="<?= vips_link('groups/store_group') ?>" method="POST">
    <? if ($group) : ?>
        <input type="hidden" name="group_id" value="<?= $group->id ?>">
    <? endif ?>

    <label>
        <span class="required"><?= _vips('Gruppenname') ?></span>
        <input type="text" name="group_name" required value="<?= htmlReady($group->name) ?>">
    </label>

    <label style="margin-top: 1em;">
        <span class="required"><?= _vips('Gruppengröße (mind. 1)') ?></span>
        <input type="number" name="group_size" required value="<?= htmlReady($group->size) ?>">
    </label>

    <footer data-dialog-button>
        <?= vips_accept_button(_vips('Speichern'), 'store_group') ?>
    </footer>
</form>
