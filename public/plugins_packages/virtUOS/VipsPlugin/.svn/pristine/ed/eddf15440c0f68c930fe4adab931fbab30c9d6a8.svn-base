<? setlocale(LC_NUMERIC, NULL) ?>

<table class="default">
    <caption>
        <?= $display == 'points' ? _vips('Punkteübersicht') : _vips('Notenübersicht') ?>
    </caption>

    <colgroup>
        <col>

        <? if (count($items['practices']) > 0) : ?>
            <col style="border-left: 1px dotted gray;">
        <? endif ?>
        <? if (count($items['practices']) > 1) : ?>
            <col span="<?= count($items['practices']) - 1 ?>">
        <? endif ?>

        <? if (count($items['blocks']) > 0) : ?>
            <col style="border-left: 1px dotted gray;">
        <? endif ?>
        <? if (count($items['blocks']) > 1) : ?>
            <col span="<?= count($items['blocks']) - 1 ?>">
        <? endif ?>

        <? if (count($items['exams']) > 0) : ?>
            <col style="border-left: 1px dotted gray;">
        <? endif ?>
        <? if (count($items['exams']) > 1) : ?>
            <col span="<?= count($items['exams']) - 1 ?>">
        <? endif ?>

        <col style="border-left: 1px dotted gray;">
        <col span="2">
    </colgroup>

    <thead>
        <tr>
            <th><? /* participant */ ?></th>

            <? if (count($items['practices']) > 0) : ?>
                <th colspan="<?= count($items['practices']) ?>" class="smaller">
                    <?= _vips('Übungsblätter') ?>
                </th>
            <? endif ?>

            <? if (count($items['blocks']) > 0) : ?>
                <th colspan="<?= count($items['blocks']) ?>" class="smaller">
                    <?= _vips('Blöcke') ?>
                </th>
            <? endif ?>

            <? if (count($items['exams']) > 0) : ?>
                <th colspan="<?= count($items['exams']) ?>" class="smaller">
                    <?= _vips('Klausuren') ?>
                </th>
            <? endif ?>

            <th colspan="3"><? /* sum, grade */ ?></th>
        </tr>

        <tr class="sortable">
            <th class="<?= vips_sort_class($sort === 'name', $desc) ?>">
                <a href="<?= vips_link('solutions/participants_overview', ['display' => $display, 'sort' => 'name', 'desc' => $sort === 'name' && !$desc]) ?>">
                    <?= _vips('Teilnehmer') ?>
                </a>
            </th>

            <? foreach ($items as $category => $list) : ?>
                <? foreach ($list as $item) : ?>
                    <th class="gradebook_header">
                        <span title="<?= htmlReady($item['tooltip']) ?>">
                            <?= htmlReady($item['name']) ?>
                        </span>
                    </th>
                <? endforeach ?>
            <? endforeach ?>

            <th class="<?= vips_sort_class($sort === 'sum', $desc) ?>" style="text-align: right;">
                <a href="<?= vips_link('solutions/participants_overview', ['display' => $display, 'sort' => 'sum', 'desc' => $sort !== 'sum' || !$desc]) ?>">
                    <?= _vips('Summe') ?>
                </a>
            </th>

            <th colspan="2" class="<?= vips_sort_class($sort === 'grade', $desc) ?>" style="text-align: right;" title="<?= _vips('deutsche Note (ECTS-Note)') ?>">
                <a href="<?= vips_link('solutions/participants_overview', ['display' => $display, 'sort' => 'grade', 'desc' => $sort !== 'grade' || !$desc]) ?>">
                    <?= _vips('Note') ?>
                </a>
            </th>
        </tr>

        <tr style="background-color: #D1D1D1;">
            <td class="smaller">
                <? if ($display == 'points') : ?>
                    <?= _vips('Maximalpunktzahl:') ?>
                <? elseif ($display == 'weighting') : ?>
                    <?= _vips('Gewichtung:') ?>
                <? endif ?>
            </td>

            <? foreach ($items as $category => $list) : ?>
                <? foreach ($list as $item) : ?>
                    <td class="smaller" style="text-align: right; white-space: nowrap;">
                        <? if ($display == 'points' && isset($item['points'])) : ?>
                            <?= $item['points'] ?>
                        <? elseif ($display == 'weighting' && isset($item['weighting'])) : ?>
                            <?= sprintf('%s %%', round($item['weighting'], 1)) ?>
                        <? else : ?>
                            &ndash;
                        <? endif ?>
                    </td>
                <? endforeach ?>
            <? endforeach ?>

            <td class="smaller" style="text-align: right; white-space: nowrap;">
                <? if ($display == 'points') : ?>
                    <?= $overall['points'] ?>
                <? elseif ($display == 'weighting') : ?>
                    <?= sprintf('%s %%', $overall['weighting']) ?>
                <? endif ?>
            </td>

            <td colspan="2"></td>
        </tr>
    </thead>

    <tbody>
        <? /* each participant */ ?>
        <? foreach ($participants as $p) : ?>
            <? // create tooltip for sum column
               $tooltip = ['points' => null, 'weighting' => null];
               foreach (['practices', 'blocks', 'exams'] as $category) {
                   if (count($items[$category]) > 0) {
                       $tooltip['points'][]    = max(0, $p['overall']['points_'.$category]);
                       $tooltip['weighting'][] = max(0, $p['overall']['weighting_'.$category]);
                   }
               }

               if (isset($tooltip['points'])) {
                   $overall_tooltip['points']    = sprintf(_vips('%s Punkte'), implode(' + ', $tooltip['points']));
                   $overall_tooltip['weighting'] = sprintf(_vips('%s Prozent'), implode(' + ', $tooltip['weighting']));
               }
            ?>

            <tr>
                <td>
                    <?= htmlReady($p['name']) ?>
                </td>

                <? foreach ($items as $category => $list) : ?>
                    <? foreach ($list as $item) : ?>
                        <td style="text-align: right; white-space: nowrap;">
                            <? $percent = $p['items'][$category][$item['id']]['percent']; ?>
                            <? if ($display == 'points') : ?>
                                <span<? if (isset($percent)) : ?> title="<?= sprintf('%s %%', $percent) ?>"<? endif ?>>
                                    <? if (isset($p['items'][$category][$item['id']]['points'])) : ?>
                                        <?= sprintf(_vips('%.1f'), $p['items'][$category][$item['id']]['points']) ?>
                                    <? else : ?>
                                        &ndash;
                                    <? endif ?>
                                </span>
                            <? elseif ($display == 'weighting') : ?>
                                <span<? if (isset($percent)) : ?> title="<?= sprintf(_vips('absolut: %s %%'), $percent) ?>"<? endif ?>>
                                    <? if (isset($p['items'][$category][$item['id']]['weighting'])) : ?>
                                        <?= sprintf('%.1f %%', $p['items'][$category][$item['id']]['weighting']) ?>
                                    <? else : ?>
                                        &ndash;
                                    <? endif ?>
                                </span>
                            <? endif ?>
                        </td>
                    <? endforeach ?>
                <? endforeach ?>

                <td style="text-align: right; white-space: nowrap;">
                    <span title="<?= $overall_tooltip[$display] ?>">
                        <? if ($display == 'points') : ?>
                            <?= sprintf('%.1f', $p['overall']['points']) ?>
                        <? elseif ($display == 'weighting') : ?>
                            <?= sprintf('%.1f %%', $p['overall']['weighting']) ?>
                        <? endif ?>
                    </span>
                </td>

                <td style="text-align: right;">
                    <?= $p['grade'] ?>
                </td>

                <td>
                    <?= $p['ects'] ? '('.$p['ects'].')' : '' ?>
                </td>
            </tr>
        <? endforeach ?>
    </tbody>
</table>

<? setlocale(LC_NUMERIC, 'C') ?>
