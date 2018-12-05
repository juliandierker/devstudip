<? use Studip\Button, Studip\LinkButton; ?>
<? $perm = MvvPerm::get($abschnitt) ?>
<h3>
    <? if ($abschnitt->isNew()) : ?>
    <?= sprintf(_('Einen neuen Studiengangteil-Abschnitt für die Version "%s" anlegen.'),
            htmlReady($version->getDisplayName())) ?>
    <? else : ?>
    <?= sprintf(_('Studiengangteil-Abschnitt "%s" der Version "%s" bearbeiten.'),
            htmlReady($this->abschnitt->name),
            htmlReady($this->version->getDisplayName())) ?>
    <? endif; ?>
</h3>
<form class="default" action="<?= $controller->url_for('/abschnitt', $abschnitt->id) ?>" method="post">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset>
        <legend><?= _('Grunddaten') ?></legend>
        <label><?= _('Name') ?>
            <?= MvvI18N::input('name', $abschnitt->name, ['maxlength' => '254'])->checkPermission($abschnitt) ?>
        </label>
        <label>
            <?= _('Kommentar') ?>
            <?= MvvI18N::textarea('kommentar', $abschnitt->kommentar, ['class' => 'add_toolbar resizable ui-resizable'])->checkPermission($abschnitt) ?>
        <label><?= _('Kreditpunkte') ?>
            <input <?= $perm->disable('kp') ?> type="text" name="kp" id="kp" value="<?= htmlReady($abschnitt->kp) ?>" size="3" maxlength="2">
        </label>
        <label><?= _('Zwischenüberschrift') ?>
            <?= MvvI18N::input('ueberschrift', $abschnitt->ueberschrift, ['maxlength' => '254'])->checkPermission($abschnitt) ?>
        </label>
    </fieldset>
    <input type="hidden" name="version_id" value="<?= $this->version->id ?>">
    <footer data-dialog-button >
        <? if ($abschnitt->isNew()) : ?>
        <?= Button::createAccept(_('Anlegen'), 'store', array('title' => _('Abschnitt anlegen'))) ?>
        <? else : ?>
        <?= Button::createAccept(_('Übernehmen'), 'store', array('title' => _('Änderungen übernehmen'))) ?>
        <? endif; ?>
        <?= LinkButton::createCancel(_('Abbrechen'), $cancel_url, array('title' => _('zurück zur Übersicht'))) ?>
    </footer>
</form>
