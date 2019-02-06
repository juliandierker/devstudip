<exercise id="exercise-<?= $exercise->id ?>"<? if ($exercise->options['lang']): ?> lang="<?= vips_rfc5646_language_tag($exercise->options['lang']) ?>"<? endif ?><? if ($exercise->options['comment']): ?> feedback="true"<? endif ?>>
    <title>
        <?= vips_xml_encode($exercise->title) ?>
    </title>
    <? if ($exercise->options['hint'] != ''): ?>
        <hint>
            <?= vips_xml_encode($exercise->options['hint']) ?>
        </hint>
    <? endif ?>
    <items>
        <item type="<?= $exercise->task['select'] ? 'cloze-select' : 'cloze-input' ?>">
            <description>
                <? foreach (explode('[[]]', $exercise->task['text']) as $blank => $text): ?>
                    <text><?= vips_xml_encode($text) ?></text>
                    <? if (isset($exercise->task['answers'][$blank])): ?>
                        <answers>
                            <? foreach ($exercise->task['answers'][$blank] as $answer): ?>
                                <answer score="<?= $answer['score'] ?>"><?= vips_xml_encode($answer['text']) ?></answer>
                            <? endforeach ?>
                        </answers>
                    <? endif ?>
                <? endforeach ?>
            </description>
            <? if ($exercise->task['compare']): ?>
                <evaluation-hints>
                    <similarity type="<?= vips_xml_encode($exercise->task['compare']) ?>"/>
                </evaluation-hints>
            <? endif ?>
            <? if ($exercise->options['feedback'] != ''): ?>
                <feedback>
                    <?= vips_xml_encode($exercise->options['feedback']) ?>
                </feedback>
            <? endif ?>
        </item>
    </items>
</exercise>
