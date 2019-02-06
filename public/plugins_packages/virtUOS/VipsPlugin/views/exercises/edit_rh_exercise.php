<table class="default" style="margin-top: 1em;">
    <thead>
        <tr>
            <th style="width: 50%;">
                <?= _vips('Vorgegebener Text') ?>
            </th>
            <th style="width: 50%;">
                <?= _vips('Zuzuordnender Text') ?>
            </th>
        </tr>
    </thead>

    <tbody class="dynamic_list">
        <? foreach ($exercise->task['groups'] as $i => $group): ?>
            <? foreach ($exercise->task['answers'] as $answer): ?>
                <? if ($answer['group'] == $i): ?>
                    <? $size = substr_count($group, "\n") + substr_count($answer['text'], "\n") == 0 ? 'small' : 'large' ?>

                    <tr class="dynamic_row size_toggle size_<?= $size ?>">
                        <td class="dynamic_counter" style="padding-right: 1ex; white-space: nowrap;">
                            <div class="input_container">
                            <input type="text" class="character_input small_input size-l" <?= $size == 'small' ? 'name="default['.$i.']"' : '' ?> value="<?= htmlReady($group) ?>">
                            <div class="large_input">
                            <textarea class="character_input wysiwyg size-l" <?= $size == 'large' ? 'name="default['.$i.']"' : '' ?>><?= htmlReady($group) ?></textarea>
                            </div>
                            </div>
                        </td>
                        <td style="white-space: nowrap;">
                            <div class="input_container">
                            <input type="text" class="character_input small_input size-l" <?= $size == 'small' ? 'name="answer['.$i.']"' : '' ?> value="<?= htmlReady($answer['text']) ?>">
                            <div class="large_input">
                            <textarea class="character_input wysiwyg size-l" <?= $size == 'large' ? 'name="answer['.$i.']"' : '' ?>><?= htmlReady($answer['text']) ?></textarea>
                            </div>
                            </div>
                            <input type="hidden" name="id[<?= $i ?>]" value="<?= $answer['id'] ?>">
                            <?= Assets::img(vips_image_url('expand.svg'), ['title' => _vips('Eingabefeld vergrößern'), 'class' => 'textarea_toggle small_input']) ?>
                            <?= Assets::img(vips_image_url('collapse.svg'), ['title' => _vips('Eingabefeld verkleinern'), 'class' => 'textarea_toggle large_input']) ?>

                            <a href="#" class="delete_dynamic_row" style="padding: 1ex;">
                                <?= Icon::create('trash', 'clickable', ['title' => _vips('Zuordnung Löschen')]) ?>
                            </a>
                        </td>
                    </tr>
                <? endif ?>
            <? endforeach ?>
        <? endforeach ?>

        <tr class="dynamic_row size_toggle size_small template">
            <td class="dynamic_counter" style="white-space: nowrap;">
                <input type="text" class="character_input small_input" style="width: 90%;" data-name="default">
                <textarea class="character_input large_input" style="width: 90%;"></textarea>
            </td>
            <td style="white-space: nowrap;">
                <input type="text" class="character_input small_input" style="width: 90%;" data-name="answer">
                <textarea class="character_input large_input" style="width: 90%;"></textarea>
                <input type="hidden" data-name="id" value="">
                <?= Assets::img(vips_image_url('expand.svg'), ['title' => _vips('Eingabefeld vergrößern'), 'class' => 'textarea_toggle small_input']) ?>
                <?= Assets::img(vips_image_url('collapse.svg'), ['title' => _vips('Eingabefeld verkleinern'), 'class' => 'textarea_toggle large_input']) ?>

                <a href="#" class="delete_dynamic_row" style="padding: 1ex;">
                    <?= Icon::create('trash', 'clickable', ['title' => _vips('Zuordnung Löschen')]) ?>
                </a>
            </td>
        </tr>

        <tr>
            <th colspan="2">
                <?= vips_button(_vips('Zuordnung hinzufügen'), 'add_pairs', ['class' => 'add_dynamic_row']) ?>
            </th>
        </tr>
    </tbody>
</table>

<div class="label_text">
    <?= _vips('Distraktoren (optional)') ?>
    <?= tooltipIcon(_vips('Weitere Antworten, die keinem Text zugeordnet werden dürfen.')) ?>
</div>

<div class="dynamic_list">
    <? foreach ($exercise->task['answers'] as $answer): ?>
        <? if ($answer['group'] == -1): ?>
            <? $size = substr_count($answer['text'], "\n") == 0 ? 'small' : 'large' ?>

            <div class="dynamic_row mc_row">
                <label class="dynamic_counter size_toggle size_<?= $size ?> undecorated" style="padding: 1ex; white-space: nowrap;">
                    <input type="text" class="character_input small_input" <?= $size == 'small' ? 'name="_answer[]"' : '' ?> value="<?= htmlReady($answer['text']) ?>">
                    <textarea class="character_input large_input" <?= $size == 'large' ? 'name="_answer[]"' : '' ?>><?= htmlReady($answer['text']) ?></textarea>
                    <input type="hidden" name="_id[]" value="<?= $answer['id'] ?>">
                    <?= Assets::img(vips_image_url('expand.svg'), ['title' => _vips('Eingabefeld vergrößern'), 'class' => 'textarea_toggle small_input']) ?>
                    <?= Assets::img(vips_image_url('collapse.svg'), ['title' => _vips('Eingabefeld verkleinern'), 'class' => 'textarea_toggle large_input']) ?>
                </label>

                <a href="#" class="delete_dynamic_row">
                    <?= Icon::create('trash', 'clickable', ['title' => _vips('Distraktor löschen')]) ?>
                </a>
            </div>
        <? endif ?>
    <? endforeach ?>

    <div class="dynamic_row mc_row template">
        <label class="dynamic_counter size_toggle size_small undecorated" style="padding: 1ex; white-space: nowrap;">
            <input type="text" class="character_input small_input" name="_answer[]">
            <textarea class="character_input large_input"></textarea>
            <input type="hidden" name="_id[]" value="">
            <?= Assets::img(vips_image_url('expand.svg'), ['title' => _vips('Eingabefeld vergrößern'), 'class' => 'textarea_toggle small_input']) ?>
            <?= Assets::img(vips_image_url('collapse.svg'), ['title' => _vips('Eingabefeld verkleinern'), 'class' => 'textarea_toggle large_input']) ?>
        </label>

        <a href="#" class="delete_dynamic_row">
            <?= Icon::create('trash', 'clickable', ['title' => _vips('Distraktor löschen')]) ?>
        </a>
    </div>

    <?= vips_button(_vips('Distraktor hinzufügen'), 'add_false_answer', ['class' => 'add_dynamic_row']) ?>
</div>
