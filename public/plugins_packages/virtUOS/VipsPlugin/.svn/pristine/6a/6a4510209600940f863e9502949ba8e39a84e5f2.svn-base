<?= "\xEF\xBB\xBF" ?>
<? setlocale(LC_NUMERIC, NULL) ?>
"<?= _vips('Vorname') ?>"<??>
;"<?= _vips('Nachname') ?>"<??>
;"<?= _vips('Matrikelnr.') ?>"<??>
<? foreach ($items as $category => $list) : ?>
<? foreach ($list as $item) : ?>
;"<?= vips_csv_encode($item['name']) ?>"<??>
<? endforeach ?>
<? endforeach ?>
;"<?= _vips('Summe') ?>"<??>
;"<?= _vips('Note') ?>"<??>
;"<?= _vips('ECTS') ?>"<??>

<? if ($display == 'points') : ?>
"<?= _vips('Maximalpunktzahl:') ?>"<??>
<? else : ?>
"<?= _vips('Gewichtung:') ?>"<??>
<? endif ?>
;<??>
;<??>
<? foreach ($items as $category => $list) : ?>
<? foreach ($list as $item) : ?>
<? if ($display == 'points') : ?>
;"<?= sprintf('%.1f', $item['points']) ?>"<??>
<? elseif ($display == 'weighting') : ?>
;"<?= sprintf('%.1f%%', $item['weighting']) ?>"<??>
<? endif ?>
<? endforeach ?>
<? endforeach ?>
<? if ($display == 'points') : ?>
;"<?= sprintf('%.1f', $overall['points']) ?>"<??>
<? elseif ($display == 'weighting') : ?>
;"<?= sprintf('%.1f%%', $overall['weighting']) ?>"<??>
<? endif ?>
;;<??>

<? foreach ($participants as $p) : ?>
"<?= vips_csv_encode($p['forename']) ?>"<??>
;"<?= vips_csv_encode($p['surname']) ?>"<??>
;"<?= vips_csv_encode($p['stud_id']) ?>"<??>
<? foreach ($items as $category => $list) : ?>
<? foreach ($list as $item) : ?>
<? if ($display == 'points') : ?>
;"<?= sprintf('%.1f', $p['items'][$category][$item['id']]['points']) ?>"<??>
<? elseif ($display == 'weighting') : ?>
<? if (isset($p['items'][$category][$item['id']]['weighting'])) : ?>
;"<?= sprintf('%.1f%%', $p['items'][$category][$item['id']]['weighting']) ?>"<??>
<? else : ?>
;<??>
<? endif ?>
<? endif ?>
<? endforeach ?>
<? endforeach ?>
<? if ($display == 'points') : ?>
;"<?= sprintf('%.1f', $p['overall']['points']) ?>"<??>
<? elseif ($display == 'weighting') : ?>
;"<?= sprintf('%.1f%%', $p['overall']['weighting']) ?>"<??>
<? endif ?>
;"<?= $p['grade'] ?>"<??>
;"<?= $p['ects'] ?>"<??>

<? endforeach ?>
<? setlocale(LC_NUMERIC, 'C') ?>
