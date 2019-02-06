<label>
    <?= _vips('Musterlösung') ?>
    <input type="text" name="answer_0" value="<?= htmlReady($exercise->task['answers'][0]['text']) ?>">
</label>

<div class="label_text">
    <?= _vips('Folgende Rechenarten, Konstanten und Funktionen werden unterstützt:') ?>
</div>

+, -, *, /, ^, e, pi,
acos(), asin(), atan(),
cos(), cosh(), ln(), log(),
sin(), sinh(), sqrt(), tan()

<div class="label_text">
    <?= _vips('Eigene Variablen deklarieren (optional)')?>
</div>

<div class="dynamic_list">
    <? foreach ($exercise->task['variables'] as $i => $variable): ?>
        <div class="dynamic_row mc_row">
            <label class="dynamic_counter undecorated" style="padding: 1ex;">
                <?= _vips('Variable') ?>
                <input type="text" name="var[<?= $i ?>]" value="<?= htmlReady($variable['name']) ?>">
            </label>

            <label class="undecorated">
                <?= _vips('Minimum') ?>
                <input type="number" name="min[<?= $i ?>]" value="<?= htmlReady($variable['min']) ?>">
            </label>

            <label class="undecorated">
                <?= _vips('Maximum') ?>
                <input type="number" name="max[<?= $i ?>]" value="<?= htmlReady($variable['max']) ?>">
            </label>

            <a href="#" class="delete_dynamic_row">
                <?= Icon::create('trash', 'clickable', ['title' => _vips('Variable löschen')]) ?>
            </a>
        </div>
    <? endforeach ?>

    <div class="dynamic_row mc_row template">
        <label class="dynamic_counter undecorated" style="padding: 1ex;">
            <?= _vips('Variable') ?>
            <input type="text" data-name="var">
        </label>

        <label class="undecorated">
            <?= _vips('Minimum') ?>
            <input type="number" data-name="min" value="-100">
        </label>

        <label class="undecorated">
            <?= _vips('Maximum') ?>
            <input type="number" data-name="max" value="100">
        </label>

        <a href="#" class="delete_dynamic_row">
            <?= Icon::create('trash', 'clickable', ['title' => _vips('Variable löschen')]) ?>
        </a>
    </div>

    <?= vips_button(_vips('Variable hinzufügen'), 'add_variable', ['class' => 'add_dynamic_row']) ?>
</div>
