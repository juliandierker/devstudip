<!-- start solve_pl_exercise -->

<? /* default answer */ ?>
<label class="display_toggle" style="display: none;">
    <?= _vips('Vorbelegung') ?>:
    <textarea name="answer_default" class="monospace size-l" readonly rows="20"><?= htmlReady($exercise->task['template']) ?></textarea>
    <?= vips_button(_vips('Vorbelegung ausblenden'), 'edit_answer', ['class' => 'vips_display_toggle']) ?>
</label>

<? /* student answer */ ?>
<label class="display_toggle">
    <?= _vips('Lösung') ?>:
    <textarea name="answer[0]" class="monospace size-l" rows="20"><?= htmlReady($response[0] ?: $exercise->task['template']) ?></textarea>
    <? if ($exercise->task['template'] != ''): ?>
        <?= vips_button(_vips('Vorbelegung anzeigen'), 'force_default_answer', ['class' => 'vips_display_toggle']) ?>
    <? endif ?>

    <?= vips_button(_vips('Herunterladen'), 'pl_exercise_mode_download', ['title' => _vips('Programmcode herunterladen')]) ?>
    <input type="file" name="pl_userfile" title="<?= _vips('Lokale Datei ins Fenster laden') ?>">
    <?= vips_button(_vips('Datei hochladen'), 'pl_exercise_mode_upload') ?>
</label>


<? /* test query */ ?>
<label>
    <?= _vips('Testanfrage') ?>
    <input type="text" name="pl_query" class="monospace" value="<?= htmlReady($exercise->task['input']) ?>">
    <input type="hidden" name="pl_count" value="<?= $pl_count ?>">
    <?= vips_button(_vips('Query'), 'pl_exercise_mode_query') ?>
    <?= vips_button(_vips('Query Next'), 'pl_exercise_mode_querynext') ?>
</label>

<? /* prolog output */ ?>
<? if ($pl_out): ?>
    <label>
        <?= _vips('Prolog-Ausgabe') ?>
        <div class="vips_output">
            <pre><?= htmlReady($pl_out) ?></pre>
        </div>
    </label>
<? endif ?>

<!-- end solve_pl_exercise -->
