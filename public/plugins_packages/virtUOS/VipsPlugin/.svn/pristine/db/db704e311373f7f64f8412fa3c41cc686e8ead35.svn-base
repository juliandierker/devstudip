<fieldset>
    <legend>
        <?= $exercise_position ?>.
        <?= htmlReady($exercise->title) ?>
    </legend>

    <div class="description">
        <?= formatReady($exercise->description) ?>
    </div>

    <?= $this->render_partial('exercises/show_exercise_hint') ?>

    <?= $exercise_template->render() ?>

    <? if ($exercise->options['comment'] && $solution->student_comment != '') : ?>
        <label>
            <?= _vips('Bemerkungen zur Lösung') ?>
            <div class="vips_output">
                <?= htmlReady($solution->student_comment, true, true) ?>
            </div>
        </label>
    <? endif ?>
</fieldset>
