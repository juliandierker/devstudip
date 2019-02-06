<? if ($evaluation_mode): ?>
    <div class="smaller">
        <? if ($evaluation_mode == 1 && $exercise->itemCount() > 1) : ?>
            <?= _vips('Vorsicht: Falsche Antworten geben Punktabzug!') ?>
        <? elseif ($evaluation_mode == 2) : ?>
            <?= _vips('Vorsicht: Falsche Antworten geben Punktabzug! Die Gesamtpunktzahl kann dabei auch negativ werden.') ?>
        <? elseif ($evaluation_mode == 3 && $exercise->itemCount() > 1) : ?>
            <?= _vips('Vorsicht: Falsche Antworten führen zur Bewertung der Aufgabe mit 0 Punkten.') ?>
        <? endif ?>
    </div>
<? endif ?>

<? if ($correct): ?>
    <div class="smaller">
        <?= sprintf(_vips('Richtige Antworten %shervorgehoben%s.'), '<span class="correct_item">', '</span>') ?>
    </div>
<? endif ?>
