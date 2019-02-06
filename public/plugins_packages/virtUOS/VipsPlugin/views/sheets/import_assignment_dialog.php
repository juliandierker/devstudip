<form class="default" action="<?= vips_link('sheets/import_test') ?>" method="POST" enctype="multipart/form-data">
    <h4>
        <?= _vips('Aufgabenblatt aus Textdatei importieren') ?>
    </h4>

    <label>
        <?= _vips('Datei:') ?>
        <input type="file" name="import_file" style="min-width: 40em;">
    </label>

    <footer data-dialog-button>
        <?= vips_accept_button(_vips('Importieren'), 'import') ?>
    </footer>
</form>
