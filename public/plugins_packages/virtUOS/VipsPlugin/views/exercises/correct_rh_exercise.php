<!-- start correct_rh_exercise -->

<table class="default" style="margin-top: 1em;">
    <thead>
        <tr>
            <th>
                <?= _vips('vorgegebener Text') ?>
            </th>

            <th>
                <?= _vips('zugeordnete Antwort') ?>
            </th>

            <th>
                <?= _vips('richtige Antwort') ?>
            </th>
        </tr>
    </thead>

    <tbody>
        <? foreach ($exercise->task['groups'] as $i => $group) : ?>
            <?
            $correct_answer = $student_answer = null;

            foreach ($exercise->task['answers'] as $answer) {
                if ($answer['group'] == $i) {
                    $correct_answer = $answer;
                }
                if (isset($response[$answer['id']]) && $response[$answer['id']] == $i) {
                    $student_answer = $answer;
                }
            }
            ?>
            <tr style="vertical-align: top;">
                <td>
                    <?= formatReady($group) ?>
                </td>

                <td>
                    <? if ($student_answer !== null): ?>
                        <?= formatReady($student_answer['text']) ?>

                        <? if ($student_answer['group'] == $i): ?>
                            <?= Icon::create('accept', 'status-green', ['class' => 'correction_marker', 'title' => _vips('richtig')]) ?>
                        <? else: ?>
                            <?= Icon::create('decline', 'status-red', ['class' => 'correction_marker', 'title' => _vips('falsch')]) ?>
                        <? endif ?>
                    <? endif ?>
                </td>

                <td class="correct_item">
                    <?= formatReady($correct_answer['text']) ?>
                </td>
            </tr>
        <? endforeach ?>
    </tbody>
</table>

<div class="label_text">
    <?= _vips('Nicht zugeordnete Antworten:') ?>
</div>

<? foreach ($exercise->task['answers'] as $answer): ?>
    <? if ($response[$answer['id']] === null || $response[$answer['id']] == -1): ?>
        <div style="display: inline-block;" class="<?= $answer['group'] == -1 ? 'correct_item' : 'mc_item' ?>">
            <?= formatReady($answer['text']) ?>

            <? if ($answer['group'] == -1): ?>
                <?= Icon::create('accept', 'status-green', ['style' => 'vertical-align: text-bottom;', 'title' => _vips('richtig')]) ?>
            <? else: ?>
                <?= Icon::create('decline', 'status-red', ['style' => 'vertical-align: text-bottom;', 'title' => _vips('falsch')]) ?>
            <? endif ?>
        </div>
    <? endif ?>
<? endforeach ?>

<!-- end correct_rh_exercise -->
