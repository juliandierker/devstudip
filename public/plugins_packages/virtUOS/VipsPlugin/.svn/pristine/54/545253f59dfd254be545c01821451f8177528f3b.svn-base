<!-- start print_pl_exercise -->

<? if ($print_correction && $solution->commented_solution != '') : ?>
    <div class="label_text">
        <?= _vips('Kommentiertes Programm:') ?>
    </div>

    <div class="vips_output">
        <pre><?= htmlReady($solution->commented_solution) ?></pre>
    </div>
<? elseif (isset($response) && $response[0] != '') : ?>
    <div class="label_text">
        <?= _vips('Programm') ?>:
    </div>

    <div class="vips_output">
        <pre><?= htmlReady($response[0]) ?></pre>
    </div>
<? else : ?>
    <div class="vips_output" style="min-height: 30em;">
        <pre><?= htmlReady($exercise->task['template']) ?></pre>
    </div>
<? endif ?>

<? if ($show_solution) : ?>
    <div class="label_text">
        <?= _vips('Musterlösung') ?>:
    </div>

    <div class="vips_output">
        <pre><?= htmlReady(str_replace('m_l_', '', $exercise->task['answers'][0]['text'])) ?></pre>
    </div>
<? endif ?>

<!-- end print_pl_exercise -->
