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
        <item type="lti-tool">
            <external-link uri="<?= vips_xml_encode($exercise->task['launch_url']) ?>">
                <? foreach (['consumer_key', 'consumer_secret', 'custom_parameters', 'send_lis_person'] as $name): ?>
                    <param name="<?= $name ?>"><?= vips_xml_encode($exercise->task[$name]) ?></param>
                <? endforeach ?>
            </external-link>
            <? if ($exercise->options['feedback'] != ''): ?>
                <feedback>
                    <?= vips_xml_encode($exercise->options['feedback']) ?>
                </feedback>
            <? endif ?>
        </item>
    </items>
</exercise>
