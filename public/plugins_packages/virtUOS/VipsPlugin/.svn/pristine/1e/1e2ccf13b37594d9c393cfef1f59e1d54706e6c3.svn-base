<!-- start solve_tb_exercise -->
<? $rows = $exercise->options['file_upload'] ? 10 : 20 ?>

<? /* default answer */ ?>
<label class="display_toggle" style="display: none;">
    <?= _vips('Vorbelegung') ?>:
    <textarea name="answer_default" class="size-l" readonly rows="<?= $rows ?>"><?= htmlReady($exercise->task['template']) ?></textarea>
    <?= vips_button(_vips('Vorbelegung ausblenden'), 'edit_answer', ['class' => 'vips_display_toggle']) ?>
</label>

<? /* student answer */ ?>
<label class="display_toggle">
    <?= _vips('Antwort:') ?>
    <textarea name="answer[0]" class="character_input size-l" rows="<?= $rows ?>"><?= htmlReady($response[0] ?: $exercise->task['template']) ?></textarea>
    <? if ($exercise->task['template'] != ''): ?>
        <?= vips_button(_vips('Vorbelegung anzeigen'), 'force_default_answer', ['class' => 'vips_display_toggle']) ?>
    <? endif ?>
</label>

<? if ($exercise->options['file_upload']): ?>
    <table class="default">
        <caption>
            <?= _vips('Hochgeladene Dateien') ?>
            (<?= sprintf(_vips('max. %s MB'), vips_file_upload_limit() / 1048576) ?>)
        </caption>

        <? if ($solution->files): ?>
            <thead>
                <tr>
                    <th style="width: 50%;">
                        <?= _vips('Name') ?>
                    </th>
                    <th style="width: 10%;">
                        <?= _vips('Größe') ?>
                    </th>
                    <th style="width: 20%;">
                        <?= _vips('Autor') ?>
                    </th>
                    <th style="width: 15%;">
                        <?= _vips('Datum') ?>
                    </th>
                    <th class="actions">
                        <?= _vips('Aktionen') ?>
                    </th>
                </tr>
            </thead>

            <tbody>
                <? foreach ($solution->files as $file): ?>
                    <tr>
                        <td>
                            <input type="hidden" name="file_ids[]" value="<?= $file->id ?>">
                            <a href="<?= vips_link('sheets/relay/download', ['exercise_id' => $exercise->id, 'assignment_id' => $solution->assignment_id, 'solver_id' => $solution->user_id, 'file_id' => $file->id]) ?>">
                                <?= Icon::create('file', 'clickable', ['title' => _vips('Datei herunterladen')]) ?>
                                <?= htmlReady($file->name) ?>
                            </a>
                        </td>
                        <td>
                            <?= sprintf('%.1f KB', $file->size / 1024) ?>
                        </td>
                        <td>
                            <?= htmlReady(get_fullname($file->user_id, 'no_title')) ?>
                        </td>
                        <td>
                            <?= date('d.m.Y, H:i', strtotime($file->created)) ?>
                        </td>
                        <td class="actions">
                            <a href="#" onclick="jQuery(this).closest('tr').remove(); return false;">
                                <?= Icon::create('trash', 'clickable', ['title' => _vips('Datei löschen')]) ?>
                            </a>
                        </td>
                    </tr>
                <? endforeach ?>
            </tbody>
        <? endif ?>

        <tfoot>
            <tr>
                <td colspan="5">
                    <input type="file" name="upload" title="<?= _vips('Datei als Lösung hochladen') ?>">
                </td>
            </tr>
        </tfoot>
    </table>
<? endif ?>
<!-- end solve_tb_exercise -->
