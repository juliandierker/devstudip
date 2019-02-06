<a href="<?= vips_link('sheets/show_exercise', ['assignment_id' => $assignment_id, 'exercise_id' => $item->exercise_id, 'solver_id' => $solver_id]) ?>">
    <div class="sidebar_exercise_label">
        <?= sprintf(_vips('Aufgabe %d'), $item->position) ?>
    </div>
    <div class="sidebar_exercise_points">
        <?= sprintf(_vips('%g Punkte'), $item->points) ?>
    </div>
    <div class="sidebar_exercise_state">
        <? if ($solution): ?>
            <?= Icon::create('accept', 'status-green', ['title' => _vips('Aufgabe bearbeitet')]) ?>
        <? endif ?>
    </div>
</a>
