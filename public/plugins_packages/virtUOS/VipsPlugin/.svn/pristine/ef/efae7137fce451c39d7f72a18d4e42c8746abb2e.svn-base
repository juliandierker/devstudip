<!-- start print_tb_exercise -->

<? if ($print_correction && $solution->commented_solution != '') : ?>
    <div class="label_text">
        <?= _vips('Kommentierte Studentenlösung:') ?>
    </div>

    <?= formatReady($solution->commented_solution) ?>
<? elseif (isset($response) && $response[0] != '') : ?>
    <div class="label_text">
        <?= _vips('Lösung des Studenten:') ?>
    </div>

    <?= htmlReady($response[0], true, true) ?>
<? else : ?>
    <div style="min-height: 30em;">
        <?= htmlReady($exercise->task['template'], true, true) ?>
    </div>
<? endif ?>

<? if ($exercise->options['file_upload'] && $solution->files): ?>
    <div class="label_text">
        <?= _vips('Hochgeladene Dateien:') ?>
    </div>

    <ul>
        <? foreach ($solution->files as $file): ?>
            <li>
                <?= htmlReady($file->name) ?>
            </li>
        <? endforeach ?>
    </ul>
<? endif ?>

<? if ($show_solution && $exercise->task['answers'][0]['text'] != '') : ?>
    <div class="label_text">
        <?= _vips('Musterlösung:') ?>
    </div>

    <?= formatReady($exercise->task['answers'][0]['text']) ?>
<? endif ?>

<!-- end print_tb_exercise -->
