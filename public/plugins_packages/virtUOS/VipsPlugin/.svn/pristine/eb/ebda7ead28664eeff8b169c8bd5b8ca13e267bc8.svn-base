<!-- start print_rh_exercise -->

<table class="default">
    <colgroup>
        <? if ($show_solution) : ?>
            <col style="width: 30%;"><? /* group */ ?>
            <col style="width: 30%;"><? /* answer field or student solution */ ?>
            <col style="width: 30%;"><? /* correct solution */ ?>
        <? else : ?>
            <col style="width: 45%;"><? /* group */ ?>
            <col style="width: 45%;"><? /* answer field or student solution */ ?>
        <? endif ?>
        <? if ($print_correction): ?>
            <col style="width: 10%;"><? /* correction */ ?>
        <? endif ?>
    </colgroup>

    <thead>
        <tr>
            <th>
                <?= _vips('vorgegebener Text') ?>
            </th>

            <th>
                <?= _vips('zugeordnete Antwort') ?>
            </th>

            <? if ($show_solution) : ?>
                <th>
                    <?= _vips('richtige Antwort') ?>
                </th>
            <? endif ?>

            <? if ($print_correction): ?>
                <th>
                    <?= _vips('korrekt') ?>
                </th>
            <? endif ?>
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
                    <?= $i + 1 ?>.
                    <?= formatReady($group) ?>
                </td>

                <td>
                    <? if ($student_answer !== null): ?>
                        <?= formatReady($student_answer['text']) ?>
                    <? else : ?>
                        ____________________
                    <? endif ?>
                </td>

                <? if ($show_solution) : ?>
                    <td>
                        <?= formatReady($correct_answer['text']) ?>
                    </td>
                <? endif ?>

                <? if ($print_correction): ?>
                    <td>
                        <? if ($student_answer !== null && $student_answer['group'] == $i) : ?>
                            <?= Icon::create('accept', 'status-green', ['title' => _vips('richtig')]) ?>
                        <? else : ?>
                            <?= Icon::create('decline', 'status-red', ['title' => _vips('falsch')]) ?>
                        <? endif ?>
                    </td>
                <? endif ?>
            </tr>
        <? endforeach ?>
    </tbody>
</table>

<div class="label_text">
    <? if ($print_correction): ?>
        <?= _vips('Nicht zugeordnete Antworten:') ?>
    <? else: ?>
        <?= _vips('Antwortmöglichkeiten:') ?>
    <? endif ?>
</div>

<ol>
    <? foreach ($exercise->task['answers'] as $answer): ?>
        <? if ($response[$answer['id']] === null || $response[$answer['id']] == -1): ?>
            <li>
                <?= formatReady($answer['text']) ?>

                <? if ($print_correction): ?>
                    <? if ($answer['group'] == -1): ?>
                        <?= Icon::create('accept', 'status-green', ['title' => _vips('richtig')]) ?>
                    <? else: ?>
                        <?= Icon::create('decline', 'status-red', ['title' => _vips('falsch')]) ?>
                    <? endif ?>
                <? endif ?>
            </li>
        <? endif ?>
    <? endforeach ?>
</ol>

<!-- end print_rh_exercise -->
