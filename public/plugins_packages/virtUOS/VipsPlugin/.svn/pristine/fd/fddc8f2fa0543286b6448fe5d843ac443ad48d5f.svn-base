<? setlocale(LC_NUMERIC, NULL) ?>

<? if (count($assignments)) : ?>
    <table class="default">
        <caption>
            <?= _vips('Statistik der Aufgabenblätter') ?>
        </caption>

        <thead>
            <tr>
                <th>
                    <?= _vips('Titel / Aufgabe') ?>
                </th>
                <th style="text-align: right;">
                    <?= _vips('erreichbare Punkte') ?>
                </th>
                <th style="text-align: right;">
                    <?= _vips('durchschn. Punkte') ?>
                </th>
                <th style="text-align: right;">
                    <?= _vips('korrekte Lösungen') ?>
                </th>
            </tr>
        </thead>

        <tbody>
            <? foreach ($assignments as $assignment): ?>
                <? if (count($assignment['exercises'])): ?>
                    <tr style="font-weight: bold;">
                        <td style="width: 70%;">
                            <a href="<?= vips_link('sheets/edit_assignment', ['assignment_id' => $assignment['id']]) ?>">
                                <?= vips_test_icon($assignment['type']) ?>
                                <?= htmlReady($assignment['title']) ?>
                            </a>
                        </td>
                        <td style="text-align: right;">
                            <?= sprintf('%.1f', $assignment['points']) ?>
                        </td>
                        <td style="text-align: right;">
                            <?= sprintf('%.1f', $assignment['average']) ?>
                        </td>
                        <td>
                        </td>
                    </tr>

                    <? foreach ($assignment['exercises'] as $exercise): ?>
                        <tr>
                            <td style="width: 70%; padding-left: 2em;">
                                <a href="<?= vips_link('sheets/edit_exercise', ['assignment_id' => $assignment['id'], 'exercise_id' => $exercise['id']]) ?>">
                                    <?= $exercise['position'] ?>. <?= htmlReady($exercise['name']) ?>
                                </a>
                            </td>
                            <td style="text-align: right;">
                                <?= sprintf('%.1f', $exercise['points']) ?>
                            </td>
                            <td style="text-align: right;">
                                <?= sprintf('%.1f', $exercise['average']) ?>
                            </td>
                            <td style="text-align: right;">
                                <?= sprintf('%.1f %%', $exercise['correct'] * 100) ?>
                            </td>
                        </tr>

                        <? if (count($exercise['items']) > 1): ?>
                            <? foreach ($exercise['items'] as $index => $item): ?>
                                <tr>
                                    <td style="width: 70%; padding-left: 4em;">
                                        <?= sprintf(_vips('Item %d'), $index + 1) ?>
                                    </td>
                                    <td style="text-align: right;">
                                        <?= sprintf('%.1f', $exercise['points'] / count($exercise['items'])) ?>
                                    </td>
                                    <td style="text-align: right;">
                                        <?= sprintf('%.1f', $item) ?>
                                    </td>
                                    <td style="text-align: right;">
                                        <?= sprintf('%.1f %%', $exercise['items_c'][$index] * 100) ?>
                                    </td>
                                </tr>
                            <? endforeach ?>
                        <? endif ?>
                    <? endforeach ?>
                <? endif ?>
            <? endforeach ?>
        </tbody>
    </table>
<? endif ?>

<? setlocale(LC_NUMERIC, 'C') ?>
