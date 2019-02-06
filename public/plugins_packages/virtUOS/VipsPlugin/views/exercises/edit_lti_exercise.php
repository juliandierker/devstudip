<label>
    <?= _vips('URL zum Starten der LTI-Aufgabe') ?>
    <?= tooltipIcon(_vips('Hier k�nnen Sie ein externes Tool einbinden, sofern es den LTI-Standard (Version 1.1) unterst�tzt. Die Betreiber dieses Tools m�ssen Ihnen eine URL und Zugangsdaten (Consumer-Key und Consumer-Secret) mitteilen.')) ?>
    <input type="text" name="launch_url" value="<?= htmlReady($exercise->task['launch_url']) ?>">
</label>

<label>
    <?= _vips('Consumer-Key der LTI-Aufgabe') ?>
    <input type="text" name="consumer_key" value="<?= htmlReady($exercise->task['consumer_key']) ?>">
</label>

<label>
    <?= _vips('Consumer-Secret der LTI-Aufgabe') ?>
    <input type="text" name="consumer_secret" value="<?= htmlReady($exercise->task['consumer_secret']) ?>">
</label>

<label>
    <input type="checkbox" name="send_lis_person" value="1" <?= $exercise->task['send_lis_person'] ? ' checked' : '' ?>>
    <?= _vips('Nutzerdaten an LTI-Tool senden') ?>
    <?= tooltipIcon(_vips('Nutzerdaten d�rfen nur das externe Tool gesendet werden, wenn es keine Datenschutzbedenken gibt. Mit Setzen des Hakens best�tigen Sie, dass die �bermittlung der Daten zul�ssig ist.')) ?>
</label>

<label>
    <?= _vips('Zus�tzliche LTI-Parameter') ?>
    <?= tooltipIcon(_vips('Ein Wert pro Zeile, Beispiel: Review:Chapter=1.2.56')) ?>
    <textarea name="custom_parameters"><?= htmlReady($exercise->task['custom_parameters']) ?></textarea>
</label>
