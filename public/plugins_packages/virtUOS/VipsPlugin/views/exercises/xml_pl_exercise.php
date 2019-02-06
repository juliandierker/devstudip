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
        <item type="program-prolog">
            <answers>
                <? if ($exercise->task['template'] != ''): ?>
                    <answer score="0" default="true">
                        <?= vips_xml_encode($exercise->task['template']) ?>
                    </answer>
                <? endif ?>
                <answer score="1">
                    <?= vips_xml_encode($exercise->getPrologText()) ?>
                </answer>
            </answers>
            <evaluation-hints>
                <input-data type="prolog-query">
                    <?= vips_xml_encode($exercise->task['input']) ?>
                </input-data>
            </evaluation-hints>
            <? if ($exercise->options['feedback'] != ''): ?>
                <feedback>
                    <?= vips_xml_encode($exercise->options['feedback']) ?>
                </feedback>
            <? endif ?>
        </item>
    </items>
</exercise>
