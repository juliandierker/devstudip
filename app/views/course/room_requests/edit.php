<? use Studip\Button, Studip\LinkButton; ?>

<form method="post" name="room_request"
      action="<?= $this->controller->link_for('edit/' . $course_id, $params) ?>"
    <?= Request::isXhr() ? 'data-dialog="size=big"' : '' ?> class="default">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset>
        <legend><?= _('Raumanfragen bearbeiten / erstellen') ?></legend>

        <?= $this->render_partial('course/room_requests/_form.php'); ?>
    </fieldset>

    <footer data-dialog-button>
        <?= Button::createAccept(_('Speichern und zurück zur Übersicht'), 'save_close', array('title' => _('Speichern und zurück zur Übersicht'))) ?>
        <?= Button::create(_('Übernehmen'), 'save', array('title' => _('Änderungen speichern'))) ?>
        <?= LinkButton::createCancel(_('Abbrechen'), $controller->link_for('index/' . $course_id), array('title' => _('Abbrechen'))) ?>
    </footer>
</form>
