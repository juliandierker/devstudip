<exercise id="exercise-<?= $exercise->id ?>"<? if ($exercise->options['lang']): ?> lang="<?= vips_rfc5646_language_tag($exercise->options['lang']) ?>"<? endif ?><? if ($exercise->options['comment']): ?> feedback="true"<? endif ?>>
    <title>
        <?= vips_xml_encode($exercise->title) ?>
    </title>
    <description>
        <?= vips_xml_encode($exercise->description) ?>
    </description>
    <? if ($exercise->options['hint'] != ''): ?>
        <hint>
            <?= vips_xml_encode($exercise->options['hint']) ?>
        </hint>
    <? endif ?>
    <items>
        <item type="text-line">
            <answers>
                <? foreach ($exercise->task['answers'] as $answer): ?>
                    <answer score="<?= (float) $answer['score'] ?>">
                        <?= vips_xml_encode($answer['text']) ?>
                    </answer>
                <? endforeach ?>
            </answers>
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
