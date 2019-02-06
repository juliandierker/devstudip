<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>

<test xmlns="urn:vips:test:v1.0" id="test-<?= $assignment->id ?>" type="<?= $assignment->type ?>"
      start="<?= date('c', strtotime($assignment->start)) ?>"
      <? if (!$assignment->isUnlimited()): ?>
          end="<?= date('c', strtotime($assignment->end)) ?>"
      <? endif ?>
      >
    <title>
        <?= vips_xml_encode($assignment->test->title) ?>
    </title>
    <description>
        <?= vips_xml_encode($assignment->test->description) ?>
    </description>
    <? if ($assignment->options['notes'] != ''): ?>
        <notes>
            <?= vips_xml_encode($assignment->options['notes']) ?>
        </notes>
    <? endif ?>
    <exercises>
        <? foreach ($assignment->test->exercises as $exercise): ?>
            <?= $exercise->getXMLTemplate($assignment)->render() ?>
        <? endforeach ?>
    </exercises>
</test>
