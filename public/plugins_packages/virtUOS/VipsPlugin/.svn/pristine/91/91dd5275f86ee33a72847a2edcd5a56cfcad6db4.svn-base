<? setlocale(LC_NUMERIC, NULL) ?>

<? if (count($assignments) == 0): ?>
    <?= MessageBox::info(_vips('Es wurden noch keine Aufgabenblätter eingerichtet.')) ?>
<? endif ?>

<? foreach ($assignments as $assignment): ?>
    <h2>
        <?= htmlReady($assignment->test->title) ?>
    </h2>

    <div style="margin: 1ex 0;">
        <?= formatReady($assignment->test->description) ?>
    </div>

    <? if (vips_has_status('tutor') || $assignment->type !== 'exam' || $assignment->checkIPAccess($ip_address)): ?>
        <? if ($assignment->isUnlimited()) : ?>
            <?= _vips('Start:') ?>
            <?= date('d.m.Y, H:i', strtotime($assignment->start)) ?>
        <? else: ?>
            <?= _vips('Zeitraum:') ?>
            <?= date('d.m.Y, H:i', strtotime($assignment->start)) ?> &ndash;
            <?= date('d.m.Y, H:i', strtotime($assignment->end)) ?>
        <? endif ?>

        <? if ($assignment->type === 'exam'): ?>
            <br>
            <? if ($remainingMinutes = max(0, getRemainingMinutes($solver_id, $assignment->id))): ?>
                <b><?= sprintf(_vips('Sie haben noch %s Minuten Zeit.'), $remainingMinutes) ?></b>
            <? else: ?>
                <b><?= _vips('Ihre Bearbeitungszeit ist abgelaufen.') ?></b>
            <? endif ?>
        <? endif ?>

        <div style="float: right;">
            <a href="<?= vips_link('export/print_assignment', ['assignment_id' => $assignment->id, 'user_id' => $solver_id]) ?>" target="_blank">
                <?= Icon::create('print', 'clickable', ['title' => _vips('Drucken')]) ?>
            </a>
        </div>

        <? if (count($assignment->test->exercise_refs)): ?>
            <table class="default dynamic_list">
                <thead>
                    <tr>
                        <th style="width: 2em;">
                        </th>
                        <th style="width: 50%;">
                            <?= _vips("Aufgaben") ?>
                        </th>
                        <th style="width: 15%;">
                            <?= _vips("Abgabedatum") ?>
                        </th>
                        <th style="width: 15%;">
                            <?= _vips("Teilnehmer") ?>
                        </th>
                        <th style="width: 10%; text-align: center;">
                            <?= _vips("bearbeitet") ?>
                        </th>
                        <th style="width: 10%; text-align: center;">
                            <?= _vips("max. Punkte") ?>
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <? foreach ($assignment->test->exercise_refs as $exercise_ref): ?>
                        <? $exercise = $exercise_ref->exercise ?>
                        <? $solution = $assignment->getSolution($solver_id, $exercise->id) ?>
                        <tr>
                            <td class="dynamic_counter" style="text-align: right;">
                            </td>
                            <td>
                                <a href="<?= vips_link('sheets/show_exercise', ['assignment_id' => $assignment->id, 'exercise_id' => $exercise->id, 'solver_id' => $solver_id]) ?>">
                                    <?= htmlReady($exercise->title) ?>
                                </a>
                            </td>
                            <td>
                                <? if ($solution): ?>
                                    <?= date('d.m.Y, H:i', strtotime($solution->time)) ?>
                                <? endif ?>
                            </td>
                            <td>
                                <? if ($solution): ?>
                                    <?= htmlReady(get_fullname($solution->user_id, 'no_title')) ?>
                                <? endif ?>
                            </td>
                            <td style="text-align: center;">
                                <? if ($solution): ?>
                                    <?= Icon::create('accept', 'status-green', ['title' => _vips('ja')]) ?>
                                <? else : ?>
                                    <?= Icon::create('decline', 'status-red', ['title' => _vips('nein')]) ?>
                                <? endif ?>
                            </td>
                            <td style="text-align: center;">
                                <?= (float) $exercise_ref->points ?>
                            </td>
                        </tr>
                    <? endforeach ?>
                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="5">
                            <? if ($assignment->type === 'selftest'): ?>
                                <?= vips_link_button(_vips('Lösungen dieses Blatts löschen'), vips_url('sheets/delete_solutions', ['assignment_id' => $assignment->id])) ?>
                            <? endif ?>
                        </td>
                        <td style="text-align: center;">
                            <?= $assignment->test->getTotalPoints() ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        <? else : ?>
            <?= MessageBox::info(_vips('keine Aufgaben gefunden')) ?>
        <? endif ?>
    <? else : ?>
        <?= MessageBox::error(sprintf(_vips('Sie haben mit Ihrer IP-Adresse &bdquo;%s&ldquo; keinen Zugriff!'), htmlReady($ip_address))) ?>
    <? endif ?>
<? endforeach ?>

<? setlocale(LC_NUMERIC, 'C') ?>
