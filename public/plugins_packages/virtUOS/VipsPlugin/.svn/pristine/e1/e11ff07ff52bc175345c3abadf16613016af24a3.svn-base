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
        <item type="choice-multiple">
            <choices>
                <choice type="yes">
                    <?= vips_xml_encode($exercise->task['choices'][1]) ?>
                </choice>
                <choice type="no">
                    <?= vips_xml_encode($exercise->task['choices'][0]) ?>
                </choice>
                <choice type="none">
                    <?= _vips('keine Antwort') ?>
                </choice>
            </choices>
            <answers>
                <? foreach ($exercise->task['answers'] as $answer): ?>
                    <answer score="<?= (int) $answer['score'] ?>">
                        <?= vips_xml_encode($answer['text']) ?>
                    </answer>
                <? endforeach ?>
            </answers>
            <? if ($exercise->options['feedback'] != ''): ?>
                <feedback>
                    <?= vips_xml_encode($exercise->options['feedback']) ?>
                </feedback>
            <? endif ?>
        </item>
    </items>
</exercise>
