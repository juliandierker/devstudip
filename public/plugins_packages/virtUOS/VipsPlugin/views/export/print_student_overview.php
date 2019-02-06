<!-- start export_student_overview -->
<h1>
    <?= htmlReady($assignment->test->title) ?>
</h1>

<p>
    <?= _vips('Drucken Sie hier das Aufgabenblatt aus. Entscheiden Sie sich für den Blankodruck mit oder ohne Musterlösung oder wählen Sie einen Studenten aus, dessen Lösung Sie drucken wollen.') ?>
</p>

<?= vips_link_button(_vips('Drucken (ohne Musterlösung)'), vips_url('export/print_assignment', ['assignment_id' => $assignment->id]), ['target' => '_blank']) ?>
<?= vips_link_button(_vips('Drucken (mit Musterlösung)'), vips_url('export/print_assignment', ['assignment_id' => $assignment->id, 'print_sample_solution' => 1]), ['target' => '_blank']) ?>

<? if (count($students)) : ?>
    <table class="default">
        <caption>
            <?= _vips('Mit Studentenlösung drucken') ?>
        </caption>
        <thead>
            <tr>
                <th style="width: 50%;">
                    <?= _vips('Teilnehmer') ?>
                </th>
                <th>
                    <label>
                        <input type="checkbox" data-proxyfor=".print_correction">
                        <?= _vips('Korrektur drucken') ?>
                    </label>
                </th>
                <th>
                    <label>
                        <input type="checkbox" data-proxyfor=".print_sample_solution">
                        <?= _vips('Musterlösung drucken') ?>
                    </label>
                </th>
                <th class="actions">
                    <?= _vips('Aktionen') ?>
                </th>
            </tr>
        </thead>

        <tbody>
            <? foreach ($students as $student) : ?>
                <tr>
                    <form action="<?= vips_link('export/print_assignment') ?>" method="POST" target="_blank">
                        <input type="hidden" name="assignment_id" value="<?= $assignment->id ?>">
                        <input type="hidden" name="user_id" value="<?= $student["id"] ?>">

                        <td>
                            <? if ($student["single_solver"]) : ?>
                                <a href="<?= URLHelper::getLink('dispatch.php/profile', ['username' => $student['username']]) ?>">
                                    <?= htmlReady($student['name']) ?>
                                </a>
                            <? else : ?>
                                <?= htmlReady($student["name"]) /* group name */ ?>:
                                <? $j = 0; $count = count($student['users']); ?>
                                <? foreach ($student['users'] as $student): ?>
                                    <? $sep = ++$j == $count ? '' : '; '; ?>
                                    <a href="<?= URLHelper::getLink('dispatch.php/profile', ['username' => $student['username']]) ?>">
                                        <?= htmlReady($student['name']).$sep ?>
                                    </a>
                                <? endforeach ?>
                            <? endif ?>
                        </td>

                        <td>
                            <label>
                                <input class="print_correction" type="checkbox" name="print_correction" value="1">
                                <?= _vips('Korrektur') ?>
                            </label>
                        </td>

                        <td>
                            <label>
                                <input class="print_sample_solution" type="checkbox" name="print_sample_solution" value="1">
                                <?= _vips('Musterlösung') ?>
                            </label>
                        </td>

                        <td class="actions">
                            <?= Icon::create('print', 'clickable', ['title' => _vips('Drucken')])->asInput(null, ['name' => 'print']) ?>
                        </td>
                    </form>
                </tr>
            <? endforeach ?>
        </tbody>

        <tfoot>
            <tr>
                <form action="<?= vips_link('export/print_all_assignments') ?>" method="POST" target="_blank">
                    <input type="hidden" name="assignment_id" value="<?= $assignment->id ?>">
                    <? foreach ($students as $student) : ?>
                        <input type="hidden" name="user_ids[]" value="<?= $student['id'] ?>">
                    <? endforeach ?>

                    <td style="padding: 5px;">
                        <b><?= _vips('Lösungen aller Teilnehmer drucken') ?></b>
                    </td>

                    <td style="padding: 5px;">
                        <label>
                            <input class="print_correction" type="checkbox" name="print_correction" value="1">
                            <?= _vips('Korrektur') ?>
                        </label>
                    </td>

                    <td style="padding: 5px;">
                        <label>
                            <input class="print_sample_solution" type="checkbox" name="print_sample_solution" value="1">
                            <?= _vips('Musterlösung') ?>
                        </label>
                    </td>

                    <td class="actions" style="padding: 5px;">
                        <?= Icon::create('print', 'clickable', ['title' => _vips('Drucken')])->asInput(null, ['name' => 'print']) ?>
                    </td>
                </form>
            </tr>
        </tfoot>
    </table>
<? else : ?>
    <?= MessageBox::info(_vips('Keine Studentenlösungen vorhanden.')) ?>
<? endif ?>
