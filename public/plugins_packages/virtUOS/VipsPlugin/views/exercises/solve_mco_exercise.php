<!-- start solve_mco_exercise -->

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
                <? foreach ($exercise->task['choices'] + [-1 => _vips('keine Antwort')] as $val => $label): ?>
                    <label class="undecorated" style="padding: 1ex;">
                        <input type="radio" name="answer[<?= $key ?>]" value="<?= $val ?>"
                            <? if (!isset($response[$key]) && $val == -1 || $response[$key] === "$val"): ?>checked<? endif ?>>
                        <?= htmlReady($label) ?>
                    </label>
                <? endforeach ?>
            </td>
        </tr>
    <? endforeach ?>
</table>

<?= $this->render_partial('exercises/evaluation_mode_info') ?>

<!-- end solve_mco_exercise -->
