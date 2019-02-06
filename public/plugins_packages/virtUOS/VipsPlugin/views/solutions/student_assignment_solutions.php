<? setlocale(LC_NUMERIC, NULL) ?>

<? if (!count($exercise_array)) : ?>
    <?= MessageBox::info(_vips('Es wurde keine Aufgabe korrigiert oder die Korrekturen wurden nicht freigegeben')) ?>
<? else : ?>
    <table class="default dynamic_list">
        <caption>
            <?= sprintf(_vips('Ergebnisse des Aufgabenblatts &bdquo;%s&ldquo;'), htmlReady($assignment->test->title)) ?>
        </caption>

        <thead>
            <tr>
                <th style="width: 2em;">
                </th>

                <th style="width: 65%;">
                    <?= _vips('Aufgaben') ?>
                </th>

                <th style="width: 20%; text-align: center;">
                    <?= _vips('erreichte Punkte') ?>
                </th>

                <th style="width: 15%; text-align: center;">
                    <?= _vips('max. Punkte') ?>
                </th>
            </tr>
        </thead>

        <tbody>
            <? foreach ($exercise_array as $exercise) : ?>
                <tr>
                    <td class="dynamic_counter" style="text-align: right;">
                    </td>
                    <td>
                        <? if ($exercise['solution']) : ?>
                            <? if ($released == 2 && $exercise['solution']->corrected) : ?>
                                <a href="<?= vips_link('solutions/edit_solution', ['assignment_id' => $assignment->id, 'exercise_id' => $exercise['exercise']->id]) ?>">
                                    <?= htmlReady($exercise['exercise']->title) ?>
                                </a>
                            <? else : ?>
                                <span style="color: grey;">
                                    <?= htmlReady($exercise['exercise']->title) ?>
                                    <?= _vips('(nicht freigegeben)') ?>
                                </span>
                            <? endif ?>
                            <? if ($released > 0 && $exercise['solution']->corrector_comment) : ?>
                                <?= tooltipIcon(formatReady($exercise['solution']->corrector_comment), false, true) ?>
                            <? endif ?>
                        <? else : ?>
                            <span class="quiet">
                                <?= htmlReady($exercise['exercise']->title) ?>
                                <?= _vips('(nicht abgegeben)') ?>
                            </span>
                        <? endif ?>
                    </td>
                    <td style="text-align: center;">
                        <? if ($released > 0) : ?>
                            <?= (float) $exercise['solution']->points ?>
                        <? endif ?>
                    </td>
                    <td style="text-align: center;">
                        <?= (float) $exercise['exercise_ref']['points'] ?>
                    </td>
                </tr>
            <? endforeach ?>
        </tbody>

        <tfoot>
            <tr style="font-weight: bold;">
                <td>
                </td>

                <td>
                    <?= _vips('Gesamtpunktzahl:') ?>
                </td>

                <td style="text-align: center;">
                    <?= $sum_reached_points ?>
                </td>

                <td style="text-align: center;">
                    <?= $sum_max_points ?>
                </td>
            </tr>
        </tfoot>
    </table>
<? endif ?>

<? setlocale(LC_NUMERIC, 'C') ?>
