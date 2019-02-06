<? $rows = $exercise->options['file_upload'] ? 10 : 20 ?>

<? if ($solution->commented_solution): ?>
    <label>
        <input type="checkbox" name="show_original_solution" value="1" onChange="jQuery('.solution-toggle').toggle();">
        <?= _vips('Ursprüngliche Lösung anzeigen (ohne Anmerkungen)') ?>
    </label>

    <label class="solution-toggle" style="display: none;">
        <?= _vips('Ursprüngliche Lösung') ?>

        <? if (!vips_has_status('tutor') || !$korrektur): ?>
            <div class="vips_output">
                <?= htmlReady($response[0], true, true) ?>
            </div>
        <? else: ?>
            <textarea readonly name="answer[0]" class="size-l" rows="<?= $rows ?>"><?= htmlReady($response[0]) ?></textarea>
        <? endif ?>
    </label>
<? endif ?>

<label class="solution-toggle">
    <?= $solution->commented_solution ? _vips('Kommentierte Lösung') : _vips('Lösung') ?>

    <? if (!vips_has_status('tutor') || !$korrektur): ?>
        <div class="vips_output">
            <? if ($solution->commented_solution): ?>
                <?= formatReady($solution->commented_solution) ?>
            <? else: ?>
                <?= htmlReady($response[0], true, true) ?>
            <? endif ?>
        </div>
    <? else: ?>
        <textarea name="commented_solution" class="character_input size-l wysiwyg" rows="<?= $rows ?>"><?= vips_wysiwyg_ready($solution->commented_solution ?: $response[0]) ?></textarea>

        <? if ($solution->commented_solution): ?>
            <?= vips_button(_vips('Zurücksetzen'), 'delete_commented_solution', ['title' => _vips('Kommentierte Lösung löschen')]) ?>

            <? if (!Studip\Markup::isHtml($solution->commented_solution)): ?>
                <div class="label_text">
                    <?= _vips('Textvorschau') ?>
                </div>
                <div class="vips_output">
                    <?= formatReady($solution->commented_solution) ?>
                </div>
            <? endif ?>
        <? endif ?>
    <? endif ?>
</label>

<? if ($exercise->options['file_upload'] && $solution->files): ?>
    <table class="default">
        <caption>
            <?= _vips('Hochgeladene Dateien') ?>
        </caption>

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
                <th style="width: 20%;">
                    <?= _vips('Datum') ?>
                </th>
            </tr>
        </thead>

        <tbody>
            <? foreach ($solution->files as $file): ?>
                <tr>
                    <td>
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
                </tr>
            <? endforeach ?>
        </tbody>

        <? if (class_exists('ZipArchive') && count($solution->files) > 1): ?>
            <tfoot>
                <tr>
                    <td colspan="4">
                        <?= vips_link_button(_vips('Alle Dateien herunterladen'), vips_url('sheets/relay/download_zip', ['exercise_id' => $exercise->id, 'assignment_id' => $solution->assignment_id, 'solver_id' => $solution->user_id])) ?>
                    </td>
                </tr>
            </tfoot>
        <? endif ?>
    </table>
<? endif ?>

<? if ($exercise->task['answers'][0]['text'] != ''): ?>
    <div class="label_text">
        <?= _vips('Musterlösung') ?>
    </div>
    <div class="vips_output">
        <?= formatReady($exercise->task['answers'][0]['text']) ?>
    </div>
<? endif ?>
