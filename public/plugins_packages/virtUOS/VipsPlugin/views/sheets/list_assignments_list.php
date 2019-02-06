<form action="" method="POST">
    <table class="default">
        <caption>
            <?= htmlReady($title) ?>

            <? if ($block->id): ?>
                <? if (!$block->visible): ?>
                    <?= _vips('(für Teilnehmer unsichtbar)') ?>
                <? endif ?>

                <span class="actions">
                    <a href="<?= vips_link('admin/edit_block', ['block_id' => $block->id]) ?>" data-dialog="size=auto">
                        <?= Icon::create('edit', 'clickable', ['title' => _vips('Block bearbeiten')]) ?>
                    </a>
                    <a href="<?= vips_link('admin/delete_block', ['block_id' => $block->id]) ?>"
                       data-confirm="<?= htmlReady(sprintf(_vips('Wollen Sie wirklich den Block "%s" löschen?'), $title)) ?>">
                        <?= Icon::create('trash', 'clickable', ['title' => _vips('Block löschen')]) ?>
                    </a>
                </span>
            <? endif?>
        </caption>

        <thead>
            <tr class="sortable">
                <th style="width: 20px;">
                    <input type="checkbox" data-proxyfor=".batch_select_<?= $i ?>" data-activates=".batch_action_<?= $i ?>">
                </th>
                <th style="width: 40%;" class="<?= vips_sort_class($sort === 'title', $desc) ?>">
                    <a href="<?= vips_link('sheets', ['sort' => 'title', 'desc' => $sort === 'title' && !$desc]) ?>">
                        <?= _vips('Titel') ?>
                    </a>
                </th>
                <th style="width: 15%;" class="<?= vips_sort_class($sort === 'start', $desc) ?>">
                    <a href="<?= vips_link('sheets', ['sort' => 'start', 'desc' => $sort === 'start' && !$desc]) ?>">
                        <?= _vips('Start') ?>
                    </a>
                </th>
                <th style="width: 15%;" class="<?= vips_sort_class($sort === 'end', $desc) ?>">
                    <a href="<?= vips_link('sheets', ['sort' => 'end', 'desc' => $sort === 'end' && !$desc]) ?>">
                        <?= _vips('Ende') ?>
                    </a>
                </th>
                <th style="width: 10%;" class="<?= vips_sort_class($sort === 'type', $desc) ?>">
                    <a href="<?= vips_link('sheets', ['sort' => 'type', 'desc' => $sort === 'type' && !$desc]) ?>">
                        <?= _vips('Modus') ?>
                    </a>
                </th>
                <th style="width: 10%;">
                    <?= _vips('Block') ?>
                </th>
                <th class="actions">
                    <?= _vips('Aktionen') ?>
                </th>
            </tr>
        </thead>

        <tbody>
            <? foreach ($assignments as $assignment) : ?>
                <tr>
                    <? $halted = $assignment->isRunning() && !$assignment->active ?>
                    <? $style = $halted ? 'color: red;' : '' ?>
                    <td>
                        <input class="batch_select_<?= $i ?>" type="checkbox" name="assignment_ids[]" value="<?= $assignment->id ?>">
                    </td>
                    <td style="<?= $style ?>">
                        <a href="<?= vips_link('sheets/edit_assignment', ['assignment_id' => $assignment->id]) ?>">
                            <?= $assignment->getTypeIcon() ?>
                            <?= htmlReady($assignment->test->title) ?>
                        </a>
                        <? if ($halted): ?>
                            (<?= _vips('unterbrochen') ?>)
                        <? endif ?>
                    </td>
                    <td style="<?= $style ?>">
                        <?= date('d.m.Y, H:i', strtotime($assignment->start)) ?>
                    </td>
                    <td style="<?= $style ?>">
                        <? if (!$assignment->isUnlimited()) : ?>
                            <?= date('d.m.Y, H:i', strtotime($assignment->end)) ?>
                        <? endif ?>
                    </td>
                    <td>
                        <?= htmlReady($assignment->getTypeName()) ?>
                    </td>
                    <td>
                        <?= htmlReady($block->id ? $title : $assignment->block->name) ?>
                    </td>
                    <td class="actions">
                        <? if ($assignment->isRunning()): ?>
                            <a href="<?= vips_link('sheets/stopgo_assignment', ['assignment_id' => $assignment->id]) ?>">
                                <? if (!$assignment->active) : ?>
                                    <?= Icon::create('play', 'clickable', ['title' => _vips('Fortsetzen')]) ?>
                                <? else : ?>
                                    <?= Icon::create('pause', 'clickable', ['title' => _vips('Anhalten')]) ?>
                                <? endif ?>
                            </a>
                        <? elseif (!$assignment->isFinished()) : ?>
                            <a href="<?= vips_link('sheets/start_assignment', ['assignment_id' => $assignment->id]) ?>">
                                <?= Icon::create('play', 'clickable', ['title' => _vips('Starten')]) ?>
                            </a>
                        <? endif ?>

                        <a href="<?= vips_link('sheets/list_assignments_stud', ['assignment_id' => $assignment->id]) ?>">
                            <?= Icon::create('community', 'clickable', ['title' => _vips('Studentensicht anzeigen')]) ?>
                        </a>
                        <a href="<?= vips_link('solutions/assignment_solutions', ['assignment_id' => $assignment->id]) ?>">
                            <?= Icon::create('accept', 'clickable', ['title' => _vips('Aufgaben korrigieren')]) ?>
                        </a>
                        <a href="<?= vips_link('export/print_student_overview', ['assignment_id' => $assignment->id]) ?>">
                            <?= Icon::create('print', 'clickable', ['title' => _vips('Drucken')]) ?>
                        </a>
                        <a href="<?= vips_link('sheets/copy_assignment', ['assignment_id' => $assignment->id]) ?>">
                            <?= Icon::create('file+add', 'clickable', ['title' => _vips('Kopieren')]) ?>
                        </a>
                        <a href="<?= vips_link('sheets/delete_assignment', ['assignment_id' => $assignment->id]) ?>">
                            <?= Icon::create('trash', 'clickable', ['title' => _vips('Löschen')]) ?>
                        </a>
                    </td>
                </tr>
            <? endforeach ?>
        </tbody>

        <? if (count($assignments)): ?>
            <tfoot>
                <tr>
                    <td colspan="7">
                        <?= vips_button(_vips('Block zuweisen'), 'assign_block', [
                                'class' => 'batch_action_' . $i,
                                'formaction' => vips_url('sheets/assign_block_dialog'),
                                'data-dialog' => 'size=auto'
                            ]) ?>
                        <?= vips_button(_vips('Verschieben'), 'move_assignments', [
                                'class' => 'batch_action_' . $i,
                                'formaction' => vips_url('sheets/move_assignments_dialog'),
                                'data-dialog' => 'size=auto'
                            ]) ?>
                        <?= vips_button(_vips('Löschen'), 'delete_assignments', [
                                'class' => 'batch_action_' . $i,
                                'formaction' => vips_url('sheets/delete_assignments'),
                                'data-confirm' => _vips('Wollen Sie wirklich die ausgewählten Aufgabenblätter löschen?')
                            ]) ?>
                    </td>
                </tr>
            </tfoot>
        <? endif ?>
    </table>
</form>
