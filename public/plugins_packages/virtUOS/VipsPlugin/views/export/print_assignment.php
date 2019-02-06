<h1><?= htmlReady($assignment->test->title) ?></h1>

<h4><?= formatReady($assignment->test->description) ?></h4>

<p>
    <?= _vips('Beginn') ?>: <?= date('d.m.Y, H:i', strtotime($assignment->start)) ?><br>
    <? if (!$assignment->isUnlimited()): ?>
        <?= _vips('Ende') ?>: <?= date('d.m.Y, H:i', strtotime($assignment->end)) ?>
    <? endif ?>
</p>

<p>
    <?= _vips('Kurs') ?>: <?= htmlReady($assignment->course->name) ?><br>
    <?= _vips('Nummer') ?>: <?= htmlReady($assignment->course->veranstaltungsnummer) ?><br>
    <?= _vips('Semester') ?>: <?= htmlReady($assignment->course->start_semester->name) ?><br>
    <?= _vips('Dozenten') ?>: <?= htmlReady(join(', ', $lecturers)) ?>
</p>

<div style="height: 3em;"></div>

<tt>
    <? if (isset($students)): ?>
        <?= _vips('Name') ?>: <?= htmlReady(join(', ', $students)) ?><br>
    <? else :?>
        <?= _vips('Name') ?>: ________________________________________<br>
    <? endif ?>
    <? if ($assignment->type == 'exam'): ?>
        <br>
        <? if (isset($stud_ids)): ?>
            <?= _vips('Matrikelnummer') ?>: <?= htmlReady(join(', ', $stud_ids)) ?>
        <? else :?>
            <?= _vips('Matrikelnummer') ?>: ______________________________
        <? endif ?>
    <? endif ?>
</tt>

<div style="height: 2em;"></div>

<!-- end print_assignment_header -->

<? foreach ($exercises_data as $exercise): ?>
    <?= $this->render_partial('exercises/print_exercise', $exercise) ?>
<? endforeach ?>

<? setlocale(LC_NUMERIC, NULL) ?>
<? if ($print_correction): ?>
    <hr style="width: 100%;">

    <h2><?=_vips('Gesamtpunktzahl')?></h2>

    <table>
        <tr>
            <td>
                <?=_vips('Erreichte Punkte')?>:
            </td>
            <td style="font-weight: bold;">
                <?= (float) $reached_points ?>
            </td>
        </tr>

        <tr>
            <td>
                <?=_vips('Maximal erreichbare Punkte')?>:
            </td>
            <td style="font-weight: bold;">
                <?= (float) $max_points ?>
            </td>
        </tr>
    </table>
<? endif ?>
<? setlocale(LC_NUMERIC, 'C') ?>

<p style="page-break-after: always;"></p>
