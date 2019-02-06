<!-- start correct_mco_exercise -->

<div class="label_text">
    <?= _vips('Antworten:') ?>
</div>

<table style="margin: 1ex;">
    <? foreach ($exercise->task['answers'] as $key => $entry): ?>
        <tr>
            <td>
                <?= formatReady($entry['text']) ?>
            </td>

            <td style="white-space: nowrap;">
                <? if ($response[$key] == $entry['score']): ?>
                    <?= Icon::create('accept', 'status-green', ['class' => 'correction_marker', 'title' => _vips('richtig')]) ?>
                <? elseif ($response[$key] != -1): ?>
                    <?= Icon::create('decline', 'status-red', ['class' => 'correction_marker', 'title' => _vips('falsch')]) ?>
                <? endif ?>

                <? foreach ($exercise->task['choices'] + [-1 => _vips('keine Antwort')] as $val => $label): ?>
                    <span class="<?= $entry['score'] == $val ? 'correct_item' : 'mc_item' ?>">
                        <? if ($response[$key] === "$val"): ?>
                            <?= Assets::img(vips_image_url('choice_checked.svg'), ['style' => 'margin-left: 1ex;']) ?>
                        <? else: ?>
                            <?= Assets::img(vips_image_url('choice_unchecked.svg'), ['style' => 'margin-left: 1ex;']) ?>
                        <? endif ?>
                        <?= htmlReady($label) ?>
                    </span>
                <? endforeach ?>
            </td>
        </tr>
    <? endforeach ?>
</table>

<?= $this->render_partial('exercises/evaluation_mode_info', ['correct' => true]) ?>

<!-- end correct_mco_exercise -->
