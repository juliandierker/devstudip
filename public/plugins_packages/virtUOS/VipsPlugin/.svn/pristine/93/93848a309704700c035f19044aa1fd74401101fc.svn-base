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
        <? foreach ($exercise->task as $group => $task): ?>
            <item type="choice-single">
                <answers>
                    <? foreach ($task['answers'] as $answer): ?>
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
        <? endforeach ?>
    </items>
</exercise>
