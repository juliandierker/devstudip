<? if (count($courses)) : ?>
    <h3>
        <?= _vips('Bitte wählen Sie den Kurs, in dem Sie die Klausur schreiben möchten:') ?>
    </h3>

    <table class="default">
        <thead>
            <tr>
                <th style="width: 5%;"></th>
                <th style="width: 75%;"><?= _vips('Name') ?></th>
                <th style="width: 20%;"><?= _vips('Inhalt') ?></th>
            </tr>
        </thead>

        <tbody>
            <? foreach ($courses as $course_id => $course_name) : ?>
                <? $nav = $vips_plugin->getIconNavigation($course_id, NULL, NULL) ?>
                <tr>
                    <td>
                        <?= CourseAvatar::getAvatar($course_id)->getImageTag(Avatar::SMALL) ?>
                    </td>
                    <td>
                        <a href="<?= URLHelper::getLink($nav->getURL(), ['cid' => $course_id]) ?>">
                            <?= htmlReady($course_name) ?>
                        </a>
                    </td>
                    <td>
                        <a href="<?= URLHelper::getLink($nav->getURL(), ['cid' => $course_id]) ?>">
                            <?= $nav->getImageTag() ?>
                        </a>
                    </td>
                </tr>
            <? endforeach ?>
        </tbody>
    </table>
<? else : ?>
    <? /* this should never be shown, but can be reached directly by URL */ ?>
    <?= MessageBox::info(_vips('Zur Zeit laufen keine Klausuren.')) ?>
<? endif ?>
