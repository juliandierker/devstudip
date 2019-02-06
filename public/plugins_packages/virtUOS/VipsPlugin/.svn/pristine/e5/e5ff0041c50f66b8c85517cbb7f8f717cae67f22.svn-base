<div class="label_text">
    <?= _vips('Antwortalternativen') ?>
</div>

<div class="dynamic_list">
    <? foreach ($exercise->task['answers'] as $i => $answer): ?>
        <? $size = substr_count($answer['text'], "\n") == 0 ? 'small' : 'large'; ?>

        <div class="dynamic_row mc_row">
            <label class="dynamic_counter size_toggle size_<?= $size ?> undecorated" style="padding: 1ex; white-space: nowrap;">
                <input type="text" class="character_input small_input" <?= $size == 'small' ? 'name="answer['.$i.']"' : '' ?> value="<?= htmlReady($answer['text']) ?>">
                <textarea class="character_input large_input" <?= $size == 'large' ? 'name="answer['.$i.']"' : '' ?>><?= htmlReady($answer['text']) ?></textarea>
                <?= Assets::img(vips_image_url('expand.svg'), ['title' => _vips('Eingabefeld vergrößern'), 'class' => 'textarea_toggle small_input']) ?>
                <?= Assets::img(vips_image_url('collapse.svg'), ['title' => _vips('Eingabefeld verkleinern'), 'class' => 'textarea_toggle large_input']) ?>
            </label>

            <label class="undecorated" style="padding: 1ex;">
                <input type="checkbox" name="correct[<?= $i ?>]" value="1"<? if ($answer['score']): ?> checked<? endif ?>>
                <?= _vips('richtig') ?>
            </label>

            <a href="#" class="delete_dynamic_row">
                <?= Icon::create('trash', 'clickable', ['title' => _vips('Antwort löschen')]) ?>
            </a>
        </div>
    <? endforeach ?>

    <div class="dynamic_row mc_row template">
        <label class="dynamic_counter size_toggle size_small undecorated" style="padding: 1ex; white-space: nowrap;">
            <input type="text" class="character_input small_input" data-name="answer">
            <textarea class="character_input large_input"></textarea>
            <?= Assets::img(vips_image_url('expand.svg'), ['title' => _vips('Eingabefeld vergrößern'), 'class' => 'textarea_toggle small_input']) ?>
            <?= Assets::img(vips_image_url('collapse.svg'), ['title' => _vips('Eingabefeld verkleinern'), 'class' => 'textarea_toggle large_input']) ?>
        </label>

        <label class="undecorated" style="padding: 1ex;">
            <input type="checkbox" data-name="correct" value="1">
            <?= _vips('richtig') ?>
        </label>

        <a href="#" class="delete_dynamic_row">
            <?= Icon::create('trash', 'clickable', ['title' => _vips('Antwort löschen')]) ?>
        </a>
    </div>

    <?= vips_button(_vips('Antwort hinzufügen'), 'add_answer', ['class' => 'add_dynamic_row']) ?>
</div>

<div class="smaller">
    <?= _vips('Leere Antwortalternativen werden automatisch gelöscht.') ?>
</div>
