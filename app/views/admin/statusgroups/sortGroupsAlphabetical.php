<form method="post" action="<?= $controller->url_for("admin/statusgroups/sortGroupsAlphabetical/{$group->id}") ?>" class="default">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset>
        <legend>
            <?= _('Unterruppe alphabetisch sortieren') ?>
        </legend>

        <section>
            <?= sprintf(_('Untergruppen wirklich alphabetisch sortieren?'), htmlReady($group->name)) ?>
        </section>
    </fieldset>

    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Sortieren'), 'confirm') ?>
        <?= Studip\LinkButton::createCancel(_('Abbrechen'), $controller->url_for('admin/statusgroups')) ?>
    </footer>
</form>