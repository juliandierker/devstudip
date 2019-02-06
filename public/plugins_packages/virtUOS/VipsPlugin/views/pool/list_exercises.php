<form action="<?= vips_link('pool/delete_exercises') ?>" method="POST">
    <input type="hidden" name="sort" value="<?= $sort ?>">
    <input type="hidden" name="desc" value="<?= $desc ?>">
    <input type="hidden" name="page" value="<?= $page ?>">
    <input type="hidden" name="search_filter[search_string]" value="<?= htmlReady($search_filter['search_string']) ?>">
    <input type="hidden" name="search_filter[exercise_type]" value="<?= htmlReady($search_filter['exercise_type']) ?>">

    <table class="default">
        <caption>
            <?= _vips('Aufgaben') ?>
            <div class="actions">
                <?= sprintf(n_vips('%d Aufgabe', '%d Aufgaben', $count), $count) ?>
            </div>
        </caption>

        <thead>
            <tr class="sortable">
                <th style="width: 20px;">
                    <input type="checkbox" data-proxyfor=".batch_select" data-activates=".batch_action">
                </th>

                <th style="width: 35%;" class="<?= vips_sort_class($sort === 'title', $desc) ?>">
                    <a href="<?= vips_link('pool' , ['sort' => 'title', 'desc' => $sort === 'title' && !$desc, 'search_filter' => $search_filter]) ?>">
                        <?= _vips('Titel') ?>
                    </a>
                </th>

                <th style="width: 10%;" class="<?= vips_sort_class($sort === 'type', $desc) ?>">
                    <a href="<?= vips_link('pool' , ['sort' => 'type', 'desc' => $sort === 'type' && !$desc, 'search_filter' => $search_filter]) ?>">
                        <?= _vips('Aufgabentyp') ?>
                    </a>
                </th>

                <th style="width: 15%;" class="<?= vips_sort_class($sort === 'Nachname', $desc) ?>">
                    <a href="<?= vips_link('pool', ['sort' => 'Nachname', 'desc' => $sort === 'Nachname' && !$desc, 'search_filter' => $search_filter]) ?>">
                        <?= _vips('Autor') ?>
                    </a>
                </th>

                <th style="width: 10%;" class="<?= vips_sort_class($sort === 'created', $desc) ?>">
                    <a href="<?= vips_link('pool', ['sort' => 'created', 'desc' => $sort === 'created' && !$desc, 'search_filter' => $search_filter])?>">
                        <?= _vips('Datum') ?>
                    </a>
                </th>

                <th style="width: 20%;" class="<?= vips_sort_class($sort === 'test_title', $desc) ?>">
                    <a href="<?= vips_link('pool', ['sort' => 'test_title', 'desc' => $sort === 'test_title' && !$desc, 'search_filter' => $search_filter])?>">
                        <?= _vips('Aufgabenblatt') ?>
                    </a>
                </th>

                <th class="actions">
                    <?= _vips('Aktionen') ?>
                </th>
            </tr>
        </thead>

        <tbody>
            <? foreach ($exercises as $exercise): ?>
                <tr>
                    <td>
                        <input class="batch_select" type="checkbox" name="exercise_ids[<?= $exercise['id'] ?>]" value="<?= $exercise['assignment_id'] ?>">
                    </td>

                    <td>
                        <a href="<?= vips_link('pool/placeholder', ['action' => 'edit_exercise', 'assignment_id' => $exercise['assignment_id'], 'exercise_id' => $exercise['id']]) ?>">
                            <?= htmlReady($exercise['title']) ?>
                        </a>
                    </td>

                    <td>
                        <?= htmlReady($exercise_types[$exercise['type']]['name']) ?>
                    </td>

                    <td>
                        <? if (isset($exercise['Nachname']) || isset($exercise['Vorname'])): ?>
                            <?= htmlReady($exercise['Nachname'] . ', ' . $exercise['Vorname']) ?>
                        <? endif ?>
                    </td>

                    <td>
                        <?= date('d.m.Y, H:i', strtotime($exercise['created'])) ?>
                    </td>

                    <td>
                        <a href="<?= vips_link('pool/placeholder', ['action' => 'edit_assignment', 'assignment_id' => $exercise['assignment_id']]) ?>">
                            <?= htmlReady($exercise['test_title']) ?>
                        </a>
                    </td>

                    <td class="actions">
                        <a href="<?= vips_link('pool/placeholder', ['action' => 'show_exercise', 'assignment_id' => $exercise['assignment_id'], 'exercise_id' => $exercise['id']]) ?>">
                            <?= Icon::create('community', 'clickable', ['title' => _vips('Studentensicht anzeigen')]) ?>
                        </a>

                        <a href="<?= vips_link('pool/placeholder', ['action' => 'copy_exercise', 'assignment_id' => $exercise['assignment_id'], 'exercise_id' => $exercise['id']]) ?>">
                            <?= Icon::create('assessment+add', 'clickable', ['title' => _vips('Aufgabe kopieren')]) ?>
                        </a>

                        <a href="<?= vips_link('pool/placeholder', ['action' => 'delete_exercise', 'assignment_id' => $exercise['assignment_id'], 'exercise_id' => $exercise['id']]) ?>">
                            <?= Icon::create('trash', 'clickable', ['title' => _vips('Aufgabe löschen')]) ?>
                        </a>
                    </td>
                </tr>
            <? endforeach ?>
        </tbody>

        <tfoot>
            <tr>
                <td colspan="4">
                    <?= vips_button(_vips('Ausgewählte löschen'), 'delete_selected',
                                    ['class' => 'batch_action', 'data-confirm' => _vips('Wollen Sie wirklich die ausgewählten Aufgaben löschen?')]) ?>
                </td>
                <td colspan="3" class="actions">
                    <?= vips_page_chooser(vips_url('pool', ['page' => '%d', 'sort' => $sort, 'desc' => $desc, 'search_filter' => $search_filter]), $count, $page) ?>
                </td>
            </tr>
        </tfoot>
    </table>
</form>
