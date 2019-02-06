<?= "\xEF\xBB\xBF" ?>
<? setlocale(LC_NUMERIC, NULL) ?>
"<?= _vips('Teilnehmer') ?>"<??>
<? foreach ($exercises as $exercise) : ?>
;"<?= vips_csv_encode($exercise['title']) ?>"<??>
<? endforeach ?>

<? foreach ($solvers as $solver) : ?>
"<?= vips_csv_encode($solver['name']) ?>"<??>
<? foreach ($exercises as $exercise) : ?>
;"<?= $solutions[$solver['id']][$exercise['id']]['points'] ?>"<??>
<? endforeach ?>

<? endforeach ?>
<? setlocale(LC_NUMERIC, 'C') ?>
