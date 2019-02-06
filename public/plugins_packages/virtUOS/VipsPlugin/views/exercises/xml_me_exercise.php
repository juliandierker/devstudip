<exercise id="exercise-<?= $exercise->id ?>"<? if ($exercise->options['comment']): ?> feedback="true"<? endif ?>>
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
        <item type="math-expression">
            <answers>
                <? foreach ($exercise->task['answers'] as $answer): ?>
                    <answer score="<?= (float) $answer['score'] ?>">
                        <?= vips_xml_encode($answer['text']) ?>
                    </answer>
                <? endforeach ?>
            </answers>
            <evaluation-hints>
                <? foreach ($exercise->task['variables'] as $variable): ?>
                    <input-data type="variable" name="<?= vips_xml_encode($variable['name']) ?>">
                        <?= vips_xml_encode($variable['min']) ?>:<?= vips_xml_encode($variable['max']) ?>
                    </input-data>
                <? endforeach ?>
            </evaluation-hints>
            <? if ($exercise->options['feedback'] != ''): ?>
                <feedback>
                    <?= vips_xml_encode($exercise->options['feedback']) ?>
                </feedback>
            <? endif ?>
        </item>
    </items>
</exercise>
