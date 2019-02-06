<? setlocale(LC_NUMERIC, NULL) ?>

<form class="default" action="<?= vips_link('admin/store_weight') ?>" method="POST">
    <table class="default collapsable">
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
                <th style="width: 15%;" class="<?= vips_sort_class($sort === 'start', $desc) ?>">
                    <a href="<?= vips_link('solutions', ['sort' => 'start', 'desc' => $sort === 'start' && !$desc]) ?>">
                        <?= _vips('Start') ?>
                    </a>
                </th>
                <th style="width: 15%;" class="<?= vips_sort_class($sort === 'end', $desc) ?>">
                    <a href="<?= vips_link('solutions', ['sort' => 'end', 'desc' => $sort === 'end' && !$desc]) ?>">
                        <?= _vips('Ende') ?>
                    </a>
                </th>
                <th style="width: 5%; text-align: center;">
                    <?= _vips('korrigiert') ?>
                </th>
                <th style="width: 5%; text-align: center;">
                    <?= _vips('Freigabe') ?>
                </th>
                <th style="width: 5%; text-align: right;">
                    <?= _vips('Punkte') ?>
                </th>
                <? if ($has_grades): ?>
                    <th style="width: 10%; text-align: right;">
                        <?= _vips('Gewichtung') ?>
                    </th>
                <? endif ?>
                <th style="width: 5%;" class="actions">
                    <?= _vips('Aktionen') ?>
                </th>
            </tr>
        </thead>

        <? foreach ($blocks as $block) :?>
            <tbody>
                <? if (count($blocks) > 1 || $block->id): ?>
                    <tr class="header-row">
                        <th class="toggle-indicator" colspan="6">
                            <a class="toggler">
                                <? if ($block->id): ?>
                                    <?= _vips('Block:') ?>
                                <? endif ?>
                                <?= htmlReady($block->name) ?>
                                <? if ($block->id && !$block->visible): ?>
                                    <?= _vips('(für Teilnehmer unsichtbar)') ?>
                                <? endif ?>
                            </a>
                        </th>
                        <? if ($has_grades): ?>
                            <th class="dont-hide" style="text-align: right;">
                                <? if ($block->id): ?>
                                    <input type="text" style="text-align: right; width: 4em;" name="block_weight[<?= $block->id ?>]" value="<?= $block->weight ?>"> %
                                <? endif ?>
                            </th>
                        <? endif ?>
                        <th class="actions">
                        </th>
                    </tr>
                <? endif ?>
                <? foreach ($assignments as $ass): ?>
                    <? if ($ass['assignment']->block_id == $block->id): ?>
                        <tr>
                            <td>
                                <a href="<?= vips_link('solutions/assignment_solutions', ['assignment_id' => $ass['id']]) ?>">
                                    <?= vips_test_icon($ass['type']) ?>
                                    <?= htmlReady($ass['title']) ?>
                                </a>
                            </td>
                            <td>
                                <?= date('d.m.Y, H:i', strtotime($ass['start'])) ?>
                            </td>
                            <td>
                                <? if (!vips_test_unlimited($ass)) : ?>
                                    <?= date('d.m.Y, H:i', strtotime($ass['end'])) ?>
                                <? endif ?>
                            </td>

                            <td style="text-align: center;">
                                <? if (!isset($ass['uncorrected_solutions'])) : ?>
                                    &ndash;
                                <? elseif ($ass['uncorrected_solutions'] == 0) : ?>
                                    <?= Icon::create('accept', 'status-green', ['title' => _vips('ja')]) ?>
                                <? else : ?>
                                    <?= Icon::create('decline', 'status-red', ['title' => _vips('nein')]) ?>
                                <? endif ?>
                            </td>

                            <td style="text-align: center;">
                                <? if ($ass['released'] == 1) : ?>
                                    <?= _vips('Punkte') ?>
                                <? elseif ($ass['released'] == 2) : ?>
                                    <?= _vips('Korrekturen') ?>
                                <? else : ?>
                                    &ndash;
                                <? endif ?>
                            </td>
                            <td style="text-align: right;">
                                <?= $ass['max_points'] ?>
                            </td>
                            <? if ($has_grades): ?>
                                <td style="text-align: right;">
                                    <? if (!$block->id): ?>
                                        <input type="text" style="text-align: right; width: 4em;" name="assignment_weight[<?= $ass['id'] ?>]" value="<?= $ass['assignment']->weight ?>"> %
                                    <? endif ?>
                                </td>
                            <? endif ?>
                            <td class="actions">
                                <a href="<?= vips_link('sheets/edit_assignment', ['assignment_id' => $ass['id']]) ?>">
                                    <?= Icon::create('edit', 'clickable', ['title' => _vips('Bearbeiten')]) ?>
                                </a>
                                <a href="<?= vips_link('export/print_student_overview', ['assignment_id' => $ass['id']]) ?>">
                                    <?= Icon::create('print', 'clickable', ['title' => _vips('Drucken')]) ?>
                                </a>
                            </td>
                        </tr>
                    <? endif ?>
                <? endforeach ?>
            </tbody>
        <? endforeach ?>

        <tfoot>
            <tr>
                <td colspan="5"></td>
                <td style="padding-right: 5px; text-align: right;">
                    <?= $sum_max_points ?>
                </td>
                <? if ($has_grades): ?>
                    <td colspan="2" style="text-align: center;">
                        <?= vips_accept_button(_vips('Speichern'), 'store_weight') ?>
                    </td>
                <? else: ?>
                    <td></td>
                <? endif ?>
            </tr>
        </tfoot>
    </table>
</form>

<? setlocale(LC_NUMERIC, 'C') ?>
