<form action="<?= vips_link('pool/delete_tests') ?>" method="POST">
    <input type="hidden" name="sort" value="<?= $sort ?>">
    <input type="hidden" name="desc" value="<?= $desc ?>">
    <input type="hidden" name="page" value="<?= $page ?>">
    <input type="hidden" name="search_filter[search_string]" value="<?= htmlReady($search_filter['search_string']) ?>">
    <input type="hidden" name="search_filter[assignment_type]" value="<?= htmlReady($search_filter['assignment_type']) ?>">

    <table class="default">
        <caption>
            <?= _vips('Aufgabenblätter') ?>
            <div class="actions">
                <?= sprintf(n_vips('%d Aufgabenblatt', '%d Aufgabenblätter', $count), $count) ?>
            </div>
        </caption>

        <thead>
            <tr class="sortable">
                <th style="width: 20px;">
                    <input type="checkbox" data-proxyfor=".batch_select" data-activates=".batch_action">
                </th>

                <th style="width: 35%;" class="<?= vips_sort_class($sort === 'title', $desc) ?>">
                    <a href="<?= vips_link('pool/tests' , ['sort' => 'title', 'desc' => $sort === 'title' && !$desc, 'search_filter' => $search_filter]) ?>">
                        <?= _vips('Titel') ?>
                    </a>
                </th>

                <th style="width: 15%;" class="<?= vips_sort_class($sort === 'Nachname', $desc) ?>">
                    <a href="<?= vips_link('pool/tests', ['sort' => 'Nachname', 'desc' => $sort === 'Nachname' && !$desc, 'search_filter' => $search_filter]) ?>">
                        <?= _vips('Autor') ?>
                    </a>
                </th>

                <th style="width: 10%;" class="<?= vips_sort_class($sort === 'created', $desc) ?>">
                    <a href="<?= vips_link('pool/tests', ['sort' => 'created', 'desc' => $sort === 'created' && !$desc, 'search_filter' => $search_filter])?>">
                        <?= _vips('Datum') ?>
                    </a>
                </th>

                <th style="width: 20%;" class="<?= vips_sort_class($sort === 'Name', $desc) ?>">
                    <a href="<?= vips_link('pool/tests', ['sort' => 'Name', 'desc' => $sort === 'Name' && !$desc, 'search_filter' => $search_filter])?>">
                        <?= _vips('Veranstaltung') ?>
                    </a>
                </th>

                <th style="width: 10%;" class="<?= vips_sort_class($sort === 'start_time', $desc) ?>">
                    <a sem_name href="<?= vips_link('pool/tests', ['sort' => 'start_time', 'desc' => $sort === 'start_time' && !$desc, 'search_filter' => $search_filter])?>">
                        <?= _vips('Semester') ?>
                    </a>
                </th>

                <th class="actions">
                    <?= _vips('Aktionen') ?>
                </th>
            </tr>
        </thead>

        <tbody>
            <? foreach ($tests as $test): ?>
                <tr>
                    <td>
                        <input class="batch_select" type="checkbox" name="assignment_ids[]" value="<?= $test['assignment_id'] ?>">
                    </td>

                    <td>
                        <a href="<?= vips_link('pool/placeholder', ['action' => 'edit_assignment', 'assignment_id' => $test['assignment_id']]) ?>">
                            <?= vips_test_icon($test['type']) ?>
                            <?= htmlReady($test['title']) ?>
                        </a>
                    </td>

                    <td>
                        <? if (isset($test['Nachname']) || isset($test['Vorname'])): ?>
                            <?= htmlReady($test['Nachname'] . ', ' . $test['Vorname']) ?>
                        <? endif ?>
                    </td>

                    <td>
                        <?= date('d.m.Y, H:i', strtotime($test['created'])) ?>
                    </td>

                    <td>
                        <a href="<?= URLHelper::getLink('seminar_main.php', ['cid' => $test['course_id']]) ?>">
                            <?= htmlReady($test['Name']) ?>
                        </a>
                    </td>

                    <td>
                        <?= htmlReady($test['sem_name']) ?>
                    </td>

                    <td class="actions">
                        <a href="<?= vips_link('pool/placeholder', ['action' => 'list_assignments_stud', 'assignment_id' => $test['assignment_id']]) ?>">
                            <?= Icon::create('community', 'clickable', ['title' => _vips('Studentensicht anzeigen')]) ?>
                        </a>

                        <a href="<?= vips_link('pool/placeholder', ['action' => 'print_student_overview', 'assignment_id' => $test['assignment_id']]) ?>">
                            <?= Icon::create('print', 'clickable', ['title' => _vips('Drucken')]) ?>
                        </a>

                        <a href="<?= vips_link('pool/placeholder', ['action' => 'copy_assignment', 'assignment_id' => $test['assignment_id']]) ?>">
                            <?= Icon::create('file+add', 'clickable', ['title' => _vips('Kopieren')]) ?>
                        </a>

                        <a href="<?= vips_link('pool/placeholder', ['action' => 'delete_assignment', 'assignment_id' => $test['assignment_id']]) ?>">
                            <?= Icon::create('trash', 'clickable', ['title' => _vips('Löschen')]) ?>
                        </a>
                    </td>
                </tr>
            <? endforeach ?>
        </tbody>

        <tfoot>
            <tr>
                <td colspan="4">
                    <?= vips_button(_vips('Ausgewählte löschen'), 'delete_selected',
                                    ['class' => 'batch_action', 'data-confirm' => _vips('Wollen Sie wirklich die ausgewählten Aufgabenblätter löschen?')]) ?>
                </td>
                <td colspan="3" class="actions">
                    <?= vips_page_chooser(vips_url('pool/tests', ['page' => '%d', 'sort' => $sort, 'desc' => $desc, 'search_filter' => $search_filter]), $count, $page) ?>
                </td>
            </tr>
        </tfoot>
    </table>
</form>
