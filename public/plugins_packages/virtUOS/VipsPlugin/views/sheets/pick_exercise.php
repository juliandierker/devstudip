<? if (count($search_result)): ?>
    <table class="default">
        <thead>
            <tr>
                <th>
                    <?= _vips('Aufgabe') ?>
                </th>
                <th>
                    <?= _vips('Aufgabenblatt') ?>
                </th>
                <th>
                    <?= _vips('Veranstaltung') ?>
                </th>
                <th>
                    <?= _vips('Semester') ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <? foreach ($search_result as $exercise): ?>
                <tr>
                    <td>
                        <label class="undecorated">
                            <input type="checkbox" name="exercise_ids[<?= $exercise['id'] ?>]" value="<?= $exercise['assignment_id'] ?>">
                            <a href="<?= vips_link('sheets/preview_exercise', [
                                                        'assignment_id' => $exercise['assignment_id'],
                                                        'exercise_id' => $exercise['id']]) ?>"
                               data-dialog="id=vips_preview;size=600x400" target="_blank">
                                <?= Icon::create('question-circle', 'clickable', ['title' => _vips('Vorschau anzeigen')]) ?>
                            </a>
                            <?= htmlReady($exercise['title']) ?>
                        </label>
                    </td>
                    <td>
                        <?= htmlReady($exercise['test_name']) ?>
                    </td>
                    <td>
                        <?= htmlReady($exercise['course_name']) ?>
                    </td>
                    <td>
                        <?= htmlReady($exercise['sem_name']) ?>
                    </td>
                </tr>
            <? endforeach ?>
        </tbody>
    </table>
<? else: ?>
    <?= MessageBox::info(_vips('Keine Aufgaben gefunden.')) ?>
<? endif ?>
