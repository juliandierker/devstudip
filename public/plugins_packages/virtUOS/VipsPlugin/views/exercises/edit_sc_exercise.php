<div class="label_text">
    <?= _vips('Antwortalternativen') ?>
</div>

<div class="dynamic_list">
    <? foreach ($exercise->task as $j => $task): ?>
        <div class="dynamic_list dynamic_row" style="border-bottom: 1px dotted grey;">
            <? foreach ($task['answers'] as $i => $answer): ?>
                <? $size = substr_count($answer['text'], "\n") == 0 ? 'small' : 'large'; ?>

                <div class="dynamic_row mc_row">
                    <label class="dynamic_counter size_toggle size_<?= $size ?> undecorated" style="padding: 1ex; white-space: nowrap;">
                        <div class="input_container">
                        <input type="text" class="character_input small_input size-l" <?= $size == 'small' ? 'name="answer['.$j.']['.$i.']"' : '' ?> value="<?= htmlReady($answer['text']) ?>">
                        <div class="large_input">
                        <textarea class="character_input wysiwyg size-l" <?= $size == 'large' ? 'name="answer['.$j.']['.$i.']"' : '' ?>><?= vips_wysiwyg_ready($answer['text']) ?></textarea>
                        </div>
                        </div>
                        <?= Assets::img(vips_image_url('expand.svg'), ['title' => _vips('Eingabefeld vergrößern'), 'class' => 'textarea_toggle small_input']) ?>
                        <?= Assets::img(vips_image_url('collapse.svg'), ['title' => _vips('Eingabefeld verkleinern'), 'class' => 'textarea_toggle large_input']) ?>
                    </label>

                    <label class="undecorated" style="padding: 1ex;">
                        <input type="radio" name="correct[<?= $j ?>]" value="<?= $i ?>"<? if ($answer['score'] == 1): ?> checked<? endif ?>>
                        <?= _vips('richtig') ?>
                    </label>

                    <a href="#" class="delete_dynamic_row">
                        <?= Icon::create('trash', 'clickable', ['title' => _vips('Antwort löschen')]) ?>
                    </a>
                </div>
            <? endforeach ?>

            <div class="dynamic_row mc_row template">
                <label class="dynamic_counter size_toggle size_small undecorated" style="padding: 1ex; white-space: nowrap;">
                    <input type="text" class="character_input small_input" data-name="answer[<?= $j ?>]">
                    <textarea class="character_input large_input"></textarea>
                    <?= Assets::img(vips_image_url('expand.svg'), ['title' => _vips('Eingabefeld vergrößern'), 'class' => 'textarea_toggle small_input']) ?>
                    <?= Assets::img(vips_image_url('collapse.svg'), ['title' => _vips('Eingabefeld verkleinern'), 'class' => 'textarea_toggle large_input']) ?>
                </label>

                <label class="undecorated" style="padding: 1ex;">
                    <input type="radio" name="correct[<?= $j ?>]" data-value>
                    <?= _vips('richtig') ?>
                </label>

                <a href="#" class="delete_dynamic_row">
                    <?= Icon::create('trash', 'clickable', ['title' => _vips('Antwort löschen')]) ?>
                </a>
            </div>

            <?= vips_button(_vips('Antwort hinzufügen'), 'add_answer', ['class' => 'add_dynamic_row']) ?>
            <?= vips_button(_vips('Antwortblock löschen'), 'del_group', ['class' => 'delete_dynamic_row']) ?>
        </div>
    <? endforeach ?>

    <div class="dynamic_list dynamic_row template" style="border-bottom: 1px dotted grey;">
        <div class="dynamic_row mc_row template">
            <label class="dynamic_counter size_toggle size_small undecorated" style="padding: 1ex; white-space: nowrap;">
                <input type="text" class="character_input small_input" data-name=":answer">
                <textarea class="character_input large_input"></textarea>
                <?= Assets::img(vips_image_url('expand.svg'), ['title' => _vips('Eingabefeld vergrößern'), 'class' => 'textarea_toggle small_input']) ?>
                <?= Assets::img(vips_image_url('collapse.svg'), ['title' => _vips('Eingabefeld verkleinern'), 'class' => 'textarea_toggle large_input']) ?>
            </label>

            <label class="undecorated" style="padding: 1ex;">
                <input type="radio" data-name="correct" data-value=":value">
                <?= _vips('richtig') ?>
            </label>

            <a href="#" class="delete_dynamic_row">
                <?= Icon::create('trash', 'clickable', ['title' => _vips('Antwort löschen')]) ?>
            </a>
        </div>

        <?= vips_button(_vips('Antwort hinzufügen'), 'add_answer', ['class' => 'add_dynamic_row']) ?>
        <?= vips_button(_vips('Antwortblock löschen'), 'del_group', ['class' => 'delete_dynamic_row']) ?>
    </div>

    <?= vips_button(_vips('Antwortblock hinzufügen'), 'add_group', ['class' => 'add_dynamic_row']) ?>
</div>

<div class="smaller">
    <?= _vips('Leere Antwortalternativen werden automatisch gelöscht.') ?>
</div>
