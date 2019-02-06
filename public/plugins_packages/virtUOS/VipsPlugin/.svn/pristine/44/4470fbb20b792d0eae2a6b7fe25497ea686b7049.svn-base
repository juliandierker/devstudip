<? setlocale(LC_NUMERIC, NULL) ?>
<? $use_weighting = $overall['weighting'] > 0; ?>

<table class="default">
    <caption>
        <?= _vips('Note') ?>
    </caption>

    <thead>
        <tr>
            <th>
                <?= _vips('Titel') ?>
            </th>
            <th colspan="3" style="text-align: center; width: 1%;">
                <?= _vips('Punkte') ?>
            </th>
            <th style="text-align: right;">
                <?= _vips('Prozent') ?>
            </th>
            <? if ($use_weighting) : ?>
                <th style="text-align: right;">
                    <?= _vips('Gewichtung') ?>
                </th>
                <th style="text-align: right;">
                    <?= _vips('gewichtete Prozent') ?>
                </th>
            <? endif ?>
        </tr>
    </thead>

    <? /* here, $participants contains only one entry */ ?>
    <? foreach ($participants as $me) : ?>

        <tbody>
            <? foreach (['practices', 'blocks', 'exams'] as $category) : ?>
                <? foreach ($items[$category] as $item) : ?>
                    <? /* skip unfinished or unreleased tests */ ?>
                    <? if ($item['hidden']) continue ?>

                    <? $reached_points   = $me['items'][$category][$item['id']]['points'] ?>
                    <? $percent          = $me['items'][$category][$item['id']]['percent'] ?>
                    <? $weighted_percent = $me['items'][$category][$item['id']]['weighting'] ?>

                    <tr>
                        <td>
                            <?= htmlReady($item['name']) ?>
                        </td>

                        <td style="text-align: right;">
                            <? if (isset($reached_points)) : ?>
                                <?= $reached_points ?>
                            <? else : ?>
                                &ndash;
                            <? endif ?>
                        </td>

                        <td style="text-align: center;">
                            /
                        </td>

                        <td style="text-align: right;">
                            <?= $item['points'] ?>
                        </td>

                        <td style="text-align: right;">
                            <? if (isset($percent)) : ?>
                                <?= sprintf('%.1f %%', $percent) ?>
                            <? else : ?>
                                &ndash;
                            <? endif ?>
                        </td>

                        <? if ($use_weighting) : ?>
                            <td style="text-align: right;">
                                <?= sprintf('%.1f %%', $item['weighting']) ?>
                            </td>

                            <td style="text-align: right;">
                                <? if (isset($weighted_percent)) : ?>
                                    <?= sprintf('%.1f %%', $weighted_percent) ?>
                                <? else : ?>
                                    &ndash;
                                <? endif ?>
                            </td>
                        <? endif ?>
                    </tr>
                <? endforeach ?>
            <? endforeach ?>
        </tbody>

        <tfoot>
            <? /* show overall percent and final grade if all tests are expired and released */ ?>
            <? if ($compute_grade) : ?>
                <tr>
                    <td colspan="<?= $use_weighting ? 6 : 4 ?>">
                        <?= _vips('Prozent, gesamt') ?>
                    </td>
                    <td style="text-align: right;">
                        <?= sprintf('%.1f %%', $me['overall']['weighting']) ?>
                    </td>
                </tr>

                <tr style="font-weight: bold;">
                    <td colspan="<?= $use_weighting ? 7 : 5 ?>" style="text-align: center;">
                        <?= _vips('Note:') ?> <?= $me['grade'] ?> (<?= $me['ects'] ?>)
                    </td>
                </tr>

                <? if (!empty($me['grade_comment'])) : ?>
                    <tr>
                        <td colspan="<?= $use_weighting ? 7 : 5 ?>" style="text-align: center;">
                            <?= htmlReady($me['grade_comment']) ?>
                        </td>
                    </tr>
                <? endif ?>

            <? /* else show notice that not all assignments are expired */ ?>
            <? else : ?>
                <tr style="font-size: smaller; font-weight: bold;">
                    <td colspan="<?= $use_weighting ? 7 : 5 ?>">
                        <?= _vips('In diesem Kurs sind noch nicht alle Aufgabenblätter beendet und freigegeben. Daher kann die Note nicht berechnet werden.') ?>
                    </td>
                </tr>
            <? endif ?>
        </tfoot>

    <? endforeach ?>
</table>

<? setlocale(LC_NUMERIC, 'C') ?>
