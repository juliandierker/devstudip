<? if ($count == 0 && !isset($search_filter)): ?>
    <?= MessageBox::info(_vips('Es sind leider keine Aufgabenbl�tter mit Zugriffsberechtigung vorhanden.'), [
            _vips('Auf dieser Seite finden Sie eine �bersicht �ber alle Aufgabenbl�tter in Vips, auf die Sie Zugriff haben.')
        ]) ?>
<? else: ?>
    <form class="default" action="<?= vips_link('pool/tests') ?>">
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
            <select name="assignment_type" style="height: 32px;">
                <option value=""<?= $search_filter['assignment_type'] == '' ? ' selected' : '' ?>>
                    <?= _vips('Beliebiger Modus') ?>
                </option>
                <? foreach ($assignment_types as $type => $entry): ?>
                    <option value="<?= $type ?>"<?= $search_filter['assignment_type'] == $type ? ' selected' : '' ?>>
                        <?= htmlReady($entry['name']) ?>
                    </option>
                <? endforeach ?>
            </select>
        </label>

        <footer style="margin: 1em 0;">
            <?= vips_button(_vips('Suchen'), 'start_search', ['title' => _vips('Suche starten')]) ?>
            <?= vips_button(_vips('Zur�cksetzen'), 'reset_search', ['title' => _vips('Suche zur�cksetzen')]) ?>
        </footer>
    </form>

    <? if ($count): ?>
        <?= $this->render_partial('pool/list_tests') ?>
    <? else: ?>
        <?= MessageBox::info(_vips('Mit den aktuellen Sucheinstellungen sind keine Aufgabenbl�tter mit Zugriffsberechtigung vorhanden. ' .
                                   'Sie k�nnen die Suchfilter anpassen, um wieder Ergebnisse zu erhalten.')) ?>
    <? endif ?>
<? endif ?>
