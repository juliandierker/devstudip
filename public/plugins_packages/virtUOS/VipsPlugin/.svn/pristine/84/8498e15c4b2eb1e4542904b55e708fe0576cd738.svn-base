<? if ($count == 0 && !isset($search_filter)): ?>
    <?= MessageBox::info(_vips('Es sind leider keine Aufgaben mit Zugriffsberechtigung vorhanden.'), [
            _vips('Auf dieser Seite finden Sie eine Übersicht über alle Aufgaben in Vips, auf die Sie Zugriff haben.')
        ]) ?>
<? else: ?>
    <form class="default" action="<?= vips_link('pool') ?>">
        <label class="col-2">
            <?= QuickSearch::get('pool_search', $search)
                ->setAttributes(['autofocus' => ''])
                ->defaultValue(null, $search_filter['search_string'])
                ->fireJSFunctionOnSelect('function(id, name) { jQuery(this).val(jQuery("<div/>").html(name).text()); this.form.submit(); }')
                ->noSelectbox()
                ->render()
            ?>
        </label>

        <label class="col-2">
            <select name="exercise_type" style="height: 32px;">
                <option value=""<?= $search_filter['exercise_type'] == '' ? ' selected' : '' ?>>
                    <?= _vips('Alle Aufgabentypen') ?>
                </option>
                <? foreach ($exercise_types as $type => $entry): ?>
                    <option value="<?= $type ?>"<?= $search_filter['exercise_type'] == $type ? ' selected' : '' ?>>
                        <?= htmlReady($entry['name']) ?>
                    </option>
                <? endforeach ?>
            </select>
        </label>

        <footer style="margin: 1em 0;">
            <?= vips_button(_vips('Suchen'), 'start_search', ['title' => _vips('Suche starten')]) ?>
            <?= vips_button(_vips('Zurücksetzen'), 'reset_search', ['title' => _vips('Suche zurücksetzen')]) ?>
        </footer>
    </form>

    <? if ($count): ?>
        <?= $this->render_partial('pool/list_exercises') ?>
    <? else: ?>
        <?= MessageBox::info(_vips('Mit den aktuellen Sucheinstellungen sind keine Aufgaben mit Zugriffsberechtigung vorhanden. ' .
                                   'Sie können die Suchfilter anpassen, um wieder Ergebnisse zu erhalten.')) ?>
    <? endif ?>
<? endif ?>
