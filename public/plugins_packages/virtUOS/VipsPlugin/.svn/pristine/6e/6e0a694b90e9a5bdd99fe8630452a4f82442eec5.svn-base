<form class="default" action="<?= vips_link('sheets/copy_exercise') ?>" method="POST">
    <input type="hidden" name="assignment_id" value="<?= $assignment_id ?>">

    <h4>
        <?= _vips('Aufgaben aus einem anderen Aufgabenblatt kopieren') ?>
    </h4>

    <!-- semester picker -->
    <? foreach ($semesters as $semester): ?>
        <div id="sem_<?= $semester->beginn ?>">
            <?= $this->render_partial('sheets/pick_semester', compact('semester')) ?>
        </div>
    <? endforeach ?>

    <!-- search input -->
    <div style="margin-top: 1em;">
        <input type="text" name="search" id="search" placeholder="<?= _vips('Aufgaben suchen') ?>" style="margin-right: 1em; width: 20em;">
        <?= vips_button(_vips('Suchen'), 'search_exercise', ['onclick' => 'triggerSearch(event);']) ?>
        <?= vips_button(_vips('Neue Suche'), 'clear_search', ['onclick' => 'resetSearch(event);']) ?>
    </div>

    <div id="search_results"></div>

    <footer data-dialog-button>
        <?= vips_button(_vips('Kopieren'), 'copy_exercise') ?>
    </footer>
</form>

<script>
    function triggerSearch(event) {
        jQuery('#search_results').load('<?= vips_url('sheets/search_exercise_ajax') ?>', jQuery('#search').serialize());
        event.preventDefault();
    }

    function resetSearch(event) {
        jQuery('#search_results').empty();
        event.preventDefault();
    }
</script>
