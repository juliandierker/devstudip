<? setlocale(LC_NUMERIC, NULL) ?>

<? if (vips_has_status('tutor')) : ?>

    <? /* breadcrumb navigation */ ?>
    <div class="breadcrumb width-1200">
        <? /* overview */ ?>
        <a href="<?= vips_link('solutions/assignment_solutions', ['assignment_type' => $assignment_type, 'assignment_id' => $assignment_id, 'view' => $view]) ?>">
            <?= htmlReady($assignment_title) ?>
        </a>

        &nbsp;&rtrif;&nbsp;

        <? /* previous solver */ ?>
        <? if (isset($prev_solver) && !empty($prev_solver)) : ?>
            <a href="<?= vips_link('solutions/edit_solution', ['assignment_id' => $assignment_id, 'exercise_id' => $exercise_id, 'solver_id' => $prev_solver['id'], 'single_solver' => $prev_solver['type'] == 'group' ? 0 : 1, 'view' => $view]) ?>" title="<?= _vips('voriger Teilnehmer') ?>">
                <b>&larr;</b>
            </a>
        <? /* more solvers do exist but they did not solve this exercise */ ?>
        <? elseif (isset($prev_solver)) : ?>
            <span class="quiet" title="<?= _vips('keiner der vorhergehenden Teilnehmer hat diese Aufgabe bearbeitet') ?>">
                <b>&larr;</b>
            </span>
        <? endif ?>

        <? /* overview */ ?>
        <a href="<?= vips_link('solutions/assignment_solutions', ['assignment_type' => $assignment_type, 'assignment_id' => $assignment_id, 'expand' => $solver_id, 'view' => $view]) ?>#<?= $solver_id ?>">
            <?= htmlReady($solver_name) ?>
        </a>

        <? /* next solver */ ?>
        <? if (isset($next_solver) && !empty($next_solver)) : ?>
            <a href="<?= vips_link('solutions/edit_solution', ['assignment_id' => $assignment_id, 'exercise_id' => $exercise_id, 'solver_id' => $next_solver['id'], 'single_solver' => $next_solver['type'] == 'group' ? 0 : 1, 'view' => $view]) ?>" title="<?= _vips('nächster Teilnehmer') ?>">
                <b>&rarr;</b>
            </a>
        <? /* more solvers do exist but they did not solve this exercise */ ?>
        <? elseif (isset($next_solver)) : ?>
            <span class="quiet" title="<?= _vips('keiner der nachfolgenden Teilnehmer hat diese Aufgabe bearbeitet') ?>">
                <b>&rarr;</b>
            </span>
        <? endif ?>

        &nbsp;&rtrif;&nbsp;

        <? /* previous exercise */ ?>
        <? if (isset($prev_exercise)) : ?>
            <a href="<?= vips_link('solutions/edit_solution', ['assignment_id' => $assignment_id, 'exercise_id' => $prev_exercise['id'], 'solver_id' => $solver_id, 'single_solver' => $single_solver, 'view' => $view]) ?>" title="<?= _vips('vorige Aufgabe') ?>">
                <b>&larr;</b>
            </a>
        <? /* more exercises do exist but the solver did not solve any */ ?>
        <? elseif ($exercise_position > 1) : ?>
            <span class="quiet" title="<?= _vips('der Teilnehmer hat keine der vorhergehenden Aufgaben bearbeitet') ?>">
                <b>&larr;</b>
            </span>
        <? endif ?>

        <? /* exercise name */ ?>
        <?= htmlReady($exercise_name) ?>

        <? /* next exercise */ ?>
        <? if (isset($next_exercise)) : ?>
            <a href="<?= vips_link('solutions/edit_solution', ['assignment_id' => $assignment_id, 'exercise_id' => $next_exercise['id'], 'solver_id' => $solver_id, 'single_solver' => $single_solver, 'view' => $view]) ?>" title="<?= _vips('nächste Aufgabe') ?>">
                <b>&rarr;</b>
            </a>
        <? /* more exercises do exist but the solver did not solve any */ ?>
        <? elseif ($exercise_position < $number_of_exercises) : ?>
            <span class="quiet" title="<?= _vips('der Teilnehmer hat keine der nachfolgenden Aufgaben bearbeitet') ?>">
                <b>&rarr;</b>
            </span>
        <? endif ?>
    </div>

    <form class="default width-1200" action="<?= vips_link('solutions/store_correction') ?>" data-secure method="POST">
        <input type="hidden" name="solution_id" value="<?= $solution_id ?>">
        <input type="hidden" name="single_solver" value="<?= $single_solver ?>">
        <input type="hidden" name="solver_id" value="<?= $solver_id ?>">
        <input type="hidden" name="view" value="<?= $view ?>">
        <input type="hidden" name="max_points" value="<?= (float) $max_points ?>">

        <?= $this->render_partial('exercises/correct_exercise') ?>

        <fieldset>
            <legend>
                <?= sprintf(_vips('Bewertung zur Aufgabe &bdquo;%s&ldquo;'), htmlReady($exercise_name)) ?>
                <?= sprintf($single_solver ? _vips('von %s') : _vips('von Gruppe &bdquo;%s&ldquo;'), htmlReady($solver_name)) ?>
            </legend>

            <label>
                <?= _vips('Anmerkung zur Lösung') ?>
                <textarea name="corrector_comment" class="character_input size-l wysiwyg"><?= vips_wysiwyg_ready($corrector_comment) ?></textarea>
            </label>

            <? if ($corrector_comment != '' && !Studip\Markup::isHtml($corrector_comment)): ?>
                <label>
                    <?= _vips('Textvorschau') ?>
                    <div class="vips_output">
                        <?= formatReady($corrector_comment) ?>
                    </div>
                </label>
            <? endif ?>

            <label>
                <span class="required"><?= sprintf(_vips('Vergebene Punkte (von %s)'), (float) $max_points) ?></span>
                <input name="reached_points" type="text" class="size-s" pattern="[0-9,.]+" data-message="<?= _vips('Bitte geben Sie eine Zahl ein') ?>"
                       value="<?= isset($reached_points) ? (float) $reached_points : '' ?>" required>
            </label>
        </fieldset>

        <footer>
            <?= vips_accept_button(_vips('Speichern'), 'store_solution') ?>
        </footer>
    </form>

<? /* For students, show lecturer's correction without any possibility to modify it! */ ?>

<? else : ?>

    <div class="breadcrumb width-1200">
        <div style="display: inline-block; width: 33%;">
            <? if (isset($prev_exercise)) : ?>
                <a href="<?= vips_link('solutions/edit_solution', ['solver_id' => $solver_id, 'single_solver' => $single_solver, 'assignment_id' => $assignment_id, 'exercise_id' => $prev_exercise['id']]) ?>">
                    <?= Icon::create('arr_1left') ?>
                    <?= _vips('vorige Aufgabe') ?>
                </a>
            <? endif ?>
        </div>

        <div style="display: inline-block; text-align: center; width: 33%;">
            <a href="<?= vips_link('solutions/student_assignment_solutions', ['assignment_id' => $assignment_id]) ?>">
                &bull; <?=_vips('Übersicht der Korrekturen')?> &bull;
            </a>
        </div>

        <div style="display: inline-block; text-align: right; width: 33%;">
            <? if (isset($next_exercise)) : ?>
                <a href="<?= vips_link('solutions/edit_solution', ['solver_id' => $solver_id, 'single_solver' => $single_solver, 'assignment_id' => $assignment_id, 'exercise_id' => $next_exercise['id']]) ?>">
                    <?= _vips('nächste Aufgabe') ?>
                    <?= Icon::create('arr_1right') ?>
                </a>
            <? endif ?>
        </div>
    </div>

    <form class="default">
        <?= $this->render_partial('exercises/correct_exercise') ?>

        <fieldset>
            <legend>
                <?= sprintf(_vips('Bewertung der Aufgabe &bdquo;%s&ldquo;'), htmlReady($exercise_name)) ?>
            </legend>

            <? if ($corrector_comment != '') : ?>
                <label>
                    <? if (isset($corrector_user_name)) : ?>
                        <?= sprintf(_vips('Anmerkung des Korrektors (%s):'), '<a href="'.URLHelper::getLink('dispatch.php/messages/write', ['rec_uname' => $corrector_user_name]).'" title="'.sprintf(_vips('eine Nachricht an %s schreiben'), htmlReady($corrector_full_name)).'" data-dialog>'.htmlReady($corrector_full_name).'</a>') ?>
                    <? else : ?>
                        <?= _vips('Anmerkung des Korrektors:') ?>
                    <? endif ?>

                    <div class="vips_output">
                        <?= formatReady($corrector_comment) ?>
                    </div>
                </label>
            <? endif ?>

            <div>
                <?= sprintf(_vips('Erreichte Punkte: %s von %s'), '<b>' . (float) $reached_points . '</b>', (float) $max_points) ?>
            </div>
        </fieldset>
    </form>

<? endif ?>
<? setlocale(LC_NUMERIC, 'C') ?>
