<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>

<task lang="de" xmlns="urn:proforma:task:v0.9.4">
    <description>
        <h1>
            <?= vips_xml_encode($exercise->title) ?>
        </h1>

        <?= vips_qti_format($exercise->description) ?>

        <? if ($exercise->options['hint'] != ''): ?>
        <p>
            <?= vips_qti_format($exercise->options['hint']) ?>
        </p>
        <? endif ?>
    </description>
    <proglang version="SWI-Prolog 5.10.1">
        prolog
    </proglang>
    <files>
        <? if ($exercise->task['template'] != ''): ?>
            <file id="answerDefault" class="template" filename="answerDefault.pl" type="embedded">
                <?= vips_xml_encode($exercise->task['template']) ?>
            </file>
        <? endif ?>

        <? if ($exercise->task['input'] != ''): ?>
            <file id="queryDefault" class="inputdata" filename="queryDefault.pl" type="embedded">
                <?= vips_xml_encode($exercise->task['input']) ?>
            </file>
        <? endif ?>

        <? $test = $exercise->task['test'] ?>
        <? if (preg_match('/^%([ELD])/m', $test, $matches)): ?>
            <? $test = preg_replace('/^%[ELD].*/m', '', $test) ?>
            <? $mode = $matches[1] ?>
        <? endif ?>
        <? if (preg_match('/^(%=\w)/m', $test, $matches)): ?>
            <? $test = preg_replace('/^%=\w.*/m', '', $test) ?>
            <? $similarity_mode = $matches[1] ?>
        <? endif ?>
        <? foreach ($exercise->task['answers'] as $index => $solution): ?>
            <file id="m_l_<?= $index ?>" class="internal" filename="m_l_<?= $index ?>.pl" type="embedded">
                <?= vips_xml_encode($solution['text']) ?>
            </file>
        <? endforeach ?>

        <? if ($test != ''): ?>
            <file id="test" class="internal" filename="test.pl" type="embedded">
                <?= vips_xml_encode($test) ?>
            </file>
        <? endif ?>
    </files>
    <model-solutions>
        <? foreach ($exercise->task['answers'] as $index => $solution): ?>
            <model-solution id="m_s_<?= $index ?>" filename="m_s_<?= $index ?>.pl" type="embedded">
                <?= vips_xml_encode(preg_replace('/\bm_l_/', '', $solution['text'])) ?>
            </model-solution>
        <? endforeach ?>
    </model-solutions>
    <tests>
        <? foreach ($exercise->task['answers'] as $index => $solution['text']): ?>
            <test id="test_<?= $index ?>" validity="<?= $solution['score'] ?>">
                <title>
                    Test
                </title>
                <test-type>
                    <? if ($mode === 'E'): ?>
                        prolog-similarity
                    <? elseif ($mode === 'L'): ?>
                        prolog-eval
                    <? elseif ($mode === 'D'): ?>
                        prolog-diff
                    <? else: ?>
                        prolog-eval-similarity
                    <? endif ?>
                </test-type>
                <test-configuration>
                    <filerefs>
                        <fileref refid="answerDefault">
                        </fileref>
                        <fileref refid="m_l_<?= $index ?>">
                        </fileref>
                        <fileref refid="test">
                        </fileref>
                    </filerefs>
                    <? if ($similarity_mode): ?>
                        <test-meta-data>
                            <?= $similarity_mode ?>
                        </test-meta-data>
                    <? endif ?>
                </test-configuration>
            </test>
        <? endforeach ?>
    </tests>
</task>
