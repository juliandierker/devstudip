<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
</head>
<body onload="document.ltiLaunchForm.submit();">
<form name="ltiLaunchForm" method="POST" action="<?= htmlReady($launch_url) ?>">
    <? foreach ($launch_data as $key => $value): ?>
        <input type="hidden" name="<?= htmlReady($key) ?>" value="<?= htmlReady($value) ?>">
    <? endforeach ?>

    <input type="hidden" name="oauth_signature" value="<?= $signature ?>">
    <noscript>
        <?= vips_button(_vips('Aufgabe anzeigen'), 'launch') ?>
    </noscript>
</form>
</body>
</html>