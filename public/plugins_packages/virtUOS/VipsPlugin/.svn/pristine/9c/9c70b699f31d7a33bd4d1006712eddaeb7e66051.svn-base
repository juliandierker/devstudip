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
        <item type="matching">
            <choices>
                <? foreach ($exercise->task['groups'] as $group): ?>
                    <choice type="group">
                        <?= vips_xml_encode($group) ?>
                    </choice>
                <? endforeach ?>
            </choices>
            <answers>
                <? foreach ($exercise->task['answers'] as $answer): ?>
                    <answer score="1" correct="<?= $answer['group'] ?>">
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
