<form class="default" action="<?= vips_link('admin/store_grades') ?>" data-secure method="POST">
    <table class="default">
        <caption>
            <?= _vips('Notenverteilung') ?>
        </caption>
        <thead>
            <tr>
                <th>
                    <?=_vips('Note')?>
                </th>
                <th>
                    <?=_vips('Schwellwert')?>
                </th>
                <th>
                    <?=_vips('Kommentar')?>
                </th>
            </tr>
        </thead>

        <tbody>
            <? for ($i = 0; $i < count($grades); ++$i): ?>
                <? $class = $grade_settings && !$percentages[$i] ? 'quiet' : '' ?>
                <tr class="<?= $class ?>">
                    <td>
                        <?= htmlReady($grades[$i]) ?>
                    </td>
                    <td>
                        <input type="text" style="text-align: right; width: 4em;" name="percentage_<?= $i ?>" value="<?= $percentages[$i] ?>"> %
                    </td>
                    <td>
                        <input type="text" name="comment_<?= $i ?>" value="<?= htmlReady($comments[$i]) ?>" <?= $class ? 'disabled' : '' ?>>
                    </td>
                </tr>
            <? endfor ?>
        </tbody>

        <tfoot>
            <tr>
                <td class="smaller" colspan="3">
                    <?= _vips('Wenn Sie eine bestimmte Notenstufe nicht verwenden wollen, lassen Sie das Feld für den Schwellwert leer.') ?>
                </td>
            </tr>
        </tfoot>
    </table>

    <footer data-dialog-button>
        <?= vips_accept_button(_vips('Speichern'), 'save') ?>
    </footer>
</form>
