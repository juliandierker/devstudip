<? setlocale(LC_NUMERIC, NULL) ?>

<div style="margin-bottom: 3em; page-break-inside: avoid;">
    <div style="float: right; margin: 1em;">
        <?= sprintf(n_vips('(%s Punkt)', '(%s Punkte)', $max_points), (float) $max_points) ?>
    </div>

    <h3>
        <?= $exercise_position ?>.
        <?= htmlReady($exercise->title) ?>
    </h3>

    <? if ($exercise->description != '') : ?>
        <div style="margin-bottom: 1em;">
            <?= formatReady($exercise->description) ?>
        </div>
    <? endif ?>

    <?= $this->render_partial('exercises/show_exercise_hint') ?>

    <?= $exercise_template->render(compact('print_correction')) ?>

    <? if ($solution->student_comment != '') : ?>
        <div class="label_text">
            <?= _vips('Bemerkungen zur Lösung:') ?>
        </div>

        <?= htmlReady($solution->student_comment, true, true) ?>
    <? endif ?>

    <? if ($print_correction): ?>
        <? if ($solution->corrector_comment != ''): ?>
            <div class="label_text">
                <?= _vips('Anmerkung des Korrektors:')?>
            </div>

            <?= formatReady($solution->corrector_comment) ?>
        <? endif ?>

        <div class="label_text">
            <?= _vips('Erreichte Punkte:') ?>
            <?= (float) $reached_points ?> / <?= (float) $max_points ?>
        </div>
    <? endif ?>
</div>

<? setlocale(LC_NUMERIC, 'C') ?>
