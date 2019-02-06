<? setlocale(LC_NUMERIC, NULL) ?>

<? if ($assignment->type == 'exam' && !vips_has_status('tutor')) : ?>
    <?= $this->render_partial('exercises/exam_timer', ['current_time' => time(), 'hand_in_time' => $studentAbgabe]) ?>

    <div style="text-align: center">
        <?= _vips('Abgabezeitpunkt:') ?>
        <font style="color: red; font-weight: bold;"><?= sprintf(_vips('%s Uhr'), date('H:i', $studentAbgabe)) ?></font>
    </div>
<? endif ?>

<? /* navigation */ ?>
<div class="breadcrumb width-1200">
    <? /* previous exercise */ ?>
    <div style="display: inline-block; width: 33%;">
        <? if (isset($prev_exercise_id)) : ?>
            <a href="<?= vips_link('sheets/show_exercise', ['assignment_id' => $assignment_id, 'exercise_id' => $prev_exercise_id, 'solver_id' => $solver_id]) ?>">
                <?= Icon::create('arr_1left') ?>
                <? if ($show_solution || vips_has_status('tutor')) : ?>
                    <?=_vips('vorige Aufgabe')?>
                <? else : ?>
                    <?=_vips('ohne Speichern zur vorigen Aufgabe')?>
                <? endif ?>
            </a>
        <? endif ?>
    </div>

    <? /* exercise overview */ ?>
    <div style="display: inline-block; text-align: center; width: 33%;">
        <a href="<?= vips_link('sheets/list_assignments_stud', ['assignment_id' => $assignment_id, 'solver_id' => $solver_id]) ?>">
            &bull; <?=_vips('Zurück zum Aufgabenblatt')?> &bull;
        </a>
    </div>

    <? /* next exercise */ ?>
    <div style="display: inline-block; text-align: right; width: 33%;">
        <? if (isset($next_exercise_id)) : ?>
            <a href="<?= vips_link('sheets/show_exercise', ['assignment_id' => $assignment_id, 'exercise_id' => $next_exercise_id, 'solver_id' => $solver_id]) ?>">
                <? if ($show_solution || vips_has_status('tutor')) : ?>
                    <?=_vips('nächste Aufgabe')?>
                <? else : ?>
                    <?=_vips('ohne Speichern zur nächsten Aufgabe')?>
                <? endif ?>
                <?= Icon::create('arr_1right') ?>
            </a>
        <? endif ?>
    </div>
</div>

<? if ($show_solution) : ?>
    <form class="default">
        <!-- show feedback for selftest -->
        <?= $this->render_partial('exercises/correct_exercise') ?>

        <fieldset>
            <legend>
                <?= sprintf(_vips('Bewertung der Aufgabe &bdquo;%s&ldquo;'), htmlReady($exercise->title)) ?>
            </legend>

            <? if ($solution->corrector_comment != '') : ?>
                <label>
                    <?= _vips('Anmerkungen zur Lösung:') ?>
                    <div class="vips_output">
                        <?= formatReady($solution->corrector_comment) ?>
                    </div>
                </label>
            <? endif ?>

            <div class="description">
                <?= sprintf(_vips('Erreichte Punkte: %s von %s'), '<b>' . (float) $reached_points . '</b>', (float) $max_points) ?>
            </div>
        </fieldset>
    </form>
<? else : ?>
    <!-- solve and submit exercise -->
    <form class="default width-1200" name="jsfrm" action="<?= vips_link('sheets/submit_exercise') ?>" data-secure method="POST" enctype="multipart/form-data">
        <input type="hidden" name="solver_id" value="<?= $solver_id ?>">
        <input type="hidden" name="exercise_id" value="<?= $exercise_id ?>">
        <input type="hidden" name="assignment_id" value="<?= $assignment_id ?>">
        <input type="hidden" name="forced" value="0">

        <fieldset>
            <legend>
                <?= $exercise_position ?>.
                <?= htmlReady($exercise->title) ?>
                <div style="float: right; margin-right: 1ex;">
                    <?= sprintf(n_vips('(%s Punkt)', '(%s Punkte)', $max_points), (float) $max_points) ?>
                </div>
            </legend>

            <div class="description">
                <?= formatReady($exercise->description) ?>
            </div>

            <?= $this->render_partial('exercises/show_exercise_hint') ?>

            <?= $exercise_template->render() ?>

            <? if ($exercise->options['comment']) : ?>
                <label style="margin-top: 1em;">
                    <?= _vips('Bemerkungen zur Lösung (optional)') ?>
                    <textarea name="student_comment"><?= htmlReady($solution->student_comment) ?></textarea>
                </label>
            <? endif ?>
        </fieldset>

        <footer>
            <?= vips_accept_button(_vips('Abschicken'), 'submit_exercise', $exercise->itemCount() ? [] : ['disabled' => 'disabled']) ?>
        </footer>
    </form>
<? endif ?>

<? setlocale(LC_NUMERIC, 'C') ?>
