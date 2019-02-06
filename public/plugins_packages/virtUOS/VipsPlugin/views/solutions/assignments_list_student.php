<? setlocale(LC_NUMERIC, NULL) ?>

<table class="default">
    <caption>
        <?= _vips('Aufgabenblätter') ?>
    </caption>

    <thead>
        <tr class="sortable">
            <th style="width: 40%;" class="<?= vips_sort_class($sort === 'title', $desc) ?>">
                <a href="<?= vips_link('solutions', ['sort' => 'title', 'desc' => $sort === 'title' && !$desc]) ?>">
                    <?= _vips('Titel') ?>
                </a>
            </th>
            <th style="width: 20%;" class="<?= vips_sort_class($sort === 'start', $desc) ?>">
                <a href="<?= vips_link('solutions', ['sort' => 'start', 'desc' => $sort === 'start' && !$desc]) ?>">
                    <?= _vips('Start') ?>
                </a>
            </th>
            <th style="width: 20%;" class="<?= vips_sort_class($sort === 'end', $desc) ?>">
                <a href="<?= vips_link('solutions', ['sort' => 'end', 'desc' => $sort === 'end' && !$desc]) ?>">
                    <?= _vips('Ende') ?>
                </a>
            </th>
            <th colspan="3" style="width: 5%; text-align: right;">
                <?= _vips('Punkte') ?>
            </th>
            <th style="width: 10%; text-align: right;">
                <?= _vips('Prozent') ?>
            </th>
            <th class="actions">
                <?= _vips('Aktion') ?>
            </th>
        </tr>
    </thead>

    <tbody>
        <? foreach ($assignments as $ass): ?>
            <tr>
                <td>
                    <? if ($ass['released'] > 0): ?>
                        <a href="<?= vips_link('solutions/student_assignment_solutions', ['assignment_id' => $ass['id']]) ?>">
                            <?= vips_test_icon($ass['type']) ?>
                            <?= htmlReady($ass['title']) ?>
                        </a>
                    <? else: ?>
                        <span style="color: grey;">
                            <?= vips_test_icon($ass['type'], 'inactive') ?>
                            <?= htmlReady($ass['title']) ?>
                        </span>
                    <? endif ?>
                </td>
                <td>
                    <?= date('d.m.Y, H:i', strtotime($ass['start'])) ?>
                </td>
                <td>
                    <? if (!vips_test_unlimited($ass)) : ?>
                        <?= date('d.m.Y, H:i', strtotime($ass['end'])) ?>
                    <? endif ?>
                </td>
                <td style="text-align: right;">
                    <? if ($ass['released'] > 0): ?>
                        <?= $ass['reached_points'] ?>
                    <? else: ?>
                        &ndash;
                    <? endif ?>
                </td>
                <td style="text-align: center;">
                    /
                </td>
                <td style="text-align: right;">
                    <?= $ass['max_points'] ?>
                </td>
                <td style="text-align: right;">
                    <? if ($ass['released'] > 0 && $ass['max_points'] != 0) : ?>
                        <?= sprintf('%.1f %%', round(100 * $ass['reached_points'] / $ass['max_points'], 1)) ?>
                    <? else : ?>
                        &ndash;
                    <? endif ?>
                </td>
                <td class="actions">
                    <? if ($ass['type'] != 'exam' || $ass['released'] == 2): ?>
                        <a href="<?= vips_link('export/print_assignment', ['assignment_id' => $ass['id']]) ?>" target="_blank">
                            <?= Icon::create('print', 'clickable', ['title' => _vips('Drucken')]) ?>
                        </a>
                    <? else: ?>
                        <?= Icon::create('print', 'inactive', ['title' => _vips('Drucken')]) ?>
                    <? endif ?>
                </td>
            </tr>
        <? endforeach ?>
    </tbody>

    <tfoot>
        <tr>
            <td colspan="3"></td>
            <td style="text-align: right;">
                <?= $sum_reached_points ?>
            </td>
            <td style="text-align: center;">
                /
            </td>
            <td style="text-align: right;">
                <?= $sum_max_points ?>
            </td>
            <td style="text-align: right;">
                <? if ($sum_max_points != 0) : ?>
                    <?= sprintf('%.1f %%', round(100 * $sum_reached_points / $sum_max_points, 1)) ?>
                <? else : ?>
                    &ndash;
                <? endif ?>
            </td>
            <td>
            </td>
        </tr>
    </tfoot>
</table>

<? setlocale(LC_NUMERIC, 'C') ?>
