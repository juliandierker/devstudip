<?= "\xEF\xBB\xBF" ?>
<? setlocale(LC_NUMERIC, NULL) ?>
"<?= _vips('Titel') ?>"<??>
;"<?= _vips('Aufgabe') ?>"<??>
;"<?= _vips('Item') ?>"<??>
;"<?= _vips('erreichbare Punkte') ?>"<??>
;"<?= _vips('durchschn. Punkte') ?>"<??>
;"<?= _vips('korrekte Lösungen') ?>"<??>

<? foreach ($assignments as $assignment): ?>
<? if (count($assignment['exercises'])): ?>
"<?= vips_csv_encode($assignment['title']) ?>"<??>
;<??>
;<??>
;"<?= sprintf('%.1f', $assignment['points']) ?>"<??>
;"<?= sprintf('%.1f', $assignment['average']) ?>"<??>
;<??>

<? foreach ($assignment['exercises'] as $exercise): ?>
"<?= vips_csv_encode($assignment['title']) ?>"<??>
;"<?= vips_csv_encode($exercise['position'] . '. ' . $exercise['name']) ?>"<??>
;<??>
;"<?= sprintf('%.1f', $exercise['points']) ?>"<??>
;"<?= sprintf('%.1f', $exercise['average']) ?>"<??>
;"<?= sprintf('%.1f%%', $exercise['correct'] * 100) ?>"<??>

<? if (count($exercise['items']) > 1): ?>
<? foreach ($exercise['items'] as $index => $item): ?>
"<?= vips_csv_encode($assignment['title']) ?>"<??>
;"<?= vips_csv_encode($exercise['position'] . '. ' . $exercise['name']) ?>"<??>
;"<?= sprintf(_vips('Item %d'), $index + 1) ?>"<??>
;"<?= sprintf('%.1f', $exercise['points'] / count($exercise['items'])) ?>"<??>
;"<?= sprintf('%.1f', $item) ?>"<??>
;"<?= sprintf('%.1f%%', $exercise['items_c'][$index] * 100) ?>"<??>

<? endforeach ?>
<? endif ?>
<? endforeach ?>
<? endif ?>
<? endforeach ?>
<? setlocale(LC_NUMERIC, 'C') ?>
