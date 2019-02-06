<? if ($solution->commented_solution): ?>
    <label>
        <input type="checkbox" name="show_original_solution" value="1" onChange="jQuery('.solution-toggle').toggle();">
        <?= _vips('Ursprüngliche Lösung anzeigen (ohne Anmerkungen)') ?>
    </label>

    <label class="solution-toggle" style="display: none;">
        <?= _vips('Ursprüngliche Lösung') ?>

        <? if (!vips_has_status('tutor') || !$korrektur): ?>
            <div class="vips_output">
                <pre><?= htmlReady($response[0]) ?></pre>
            </div>
        <? else: ?>
            <textarea readonly name="answer[0]" class="monospace size-l" rows="20"><?= htmlReady($response[0]) ?></textarea>
        <? endif ?>
    </label>
<? endif ?>

<label class="solution-toggle">
    <?= $solution->commented_solution ? _vips('Kommentierte Lösung') : _vips('Lösung') ?>

    <? if (!vips_has_status('tutor') || !$korrektur): ?>
        <div class="vips_output">
            <pre><?= htmlReady($solution->commented_solution ?: $response[0]) ?></pre>
        </div>
    <? else: ?>
        <textarea name="commented_solution" class="monospace size-l" rows="20"><?= htmlReady($solution->commented_solution ?: $response[0]) ?></textarea>

        <? if ($solution->commented_solution): ?>
            <?= vips_button(_vips('Zurücksetzen'), 'delete_commented_solution', ['title' => _vips('Kommentierte Lösung löschen')]) ?>
        <? endif ?>
        <?= vips_button(_vips('Herunterladen'), 'pl_exercise_mode_download', ['title' => _vips('Programmcode herunterladen')]) ?>
    <? endif ?>
</label>

<? if (vips_has_status('tutor') && $korrektur): ?>
    <? /* test query */ ?>
    <label>
        <?= _vips('Testanfrage') ?>
        <input type="text" name="pl_query" class="monospace" value="<?= htmlReady($exercise->task['input']) ?>">
    </label>

    <input type="hidden" name="pl_count" value="<?= $pl_count ?>">
    <?= vips_button(_vips('Query'), 'pl_exercise_mode_query') ?>
    <?= vips_button(_vips('Query Next'), 'pl_exercise_mode_querynext') ?>
    <?= vips_button(_vips('Eval'), 'pl_exercise_mode_eval') ?>

    <? if ($pl_out): ?>
        <label>
            <?= _vips('Prolog-Ausgabe') ?>

            <div class="vips_output">
                <pre><?= htmlReady($pl_out) ?></pre>
            </div>
        </label>
    <? endif ?>
<? endif ?>

<? if ($exercise->task['answers'][0]['text'] != ''): ?>
    <label>
        <?= _vips('Musterlösung') ?>

        <div class="vips_output">
            <? foreach ($exercise->task['answers'] as $answer): ?>
                <pre><?= htmlReady(str_replace('m_l_', '', $answer['text'])) ?></pre>
                <? if (!vips_has_status('tutor') || !$korrektur): ?>
                    <? break; ?>
                <? endif ?>
            <? endforeach ?>
        </div>
    </label>
<? endif ?>
