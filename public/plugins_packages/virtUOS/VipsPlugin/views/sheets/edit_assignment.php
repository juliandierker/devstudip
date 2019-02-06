<form class="default width-1200" action="<?= vips_link('sheets/store_assignment') ?>" data-secure method="POST">
    <input type="hidden" name="assignment_id" value="<?= $assignment_id ?>">

    <?= vips_accept_button(_vips('Speichern'), 'store', ['style' => 'display: none;']) ?>

    <fieldset>
        <legend>
            <?= _vips('Grunddaten') ?>
        </legend>

        <label>
            <span class="required"><?= _vips('Titel') ?></span>
            <input type="text" name="assignment_name" class="character_input size-l" value="<?= htmlReady($test->title) ?>" required>
        </label>

        <label>
            <?= _vips('Beschreibung') ?>
            <textarea name="assignment_description" class="character_input size-l wysiwyg"><?= vips_wysiwyg_ready($test->description) ?></textarea>
        </label>

        <div style="margin-top: 1em;">
            <? foreach ($assignment_types as $type => $entry) : ?>
                <label class="undecorated">
                    <input type="radio" class="assignment_type" name="assignment_type" value="<?= $type ?>" <?= $assignment->type == $type ? 'checked' : '' ?>>
                    <?= htmlReady($entry['name']) ?>
                </label>
            <? endforeach ?>
        </div>

        <label class="undecorated" id="start_date">
            <div class="label_text">
                <span class="required"><?= _vips('Startzeitpunkt') ?></span>
            </div>

            <input type="text" name="start_date" class="has-date-picker size-s" value="<?= date('d.m.Y', strtotime($assignment->start)) ?>" required>
            <input type="text" name="start_time" class="has-time-picker size-s" value="<?= date('H:i', strtotime($assignment->start)) ?>" required>
        </label>

        <? $required = $assignment->type !== 'selftest' ? 'required' : '' ?>

        <label class="undecorated" id="end_date">
            <div class="label_text">
                <span class="<?= $required ?>"><?= _vips('Endzeitpunkt') ?></span>
            </div>

            <input type="text" name="end_date" class="has-date-picker size-s" value="<?= $assignment->isUnlimited() ? '' : date('d.m.Y', strtotime($assignment->end)) ?>" <?= $required ?>>
            <input type="text" name="end_time" class="has-time-picker size-s" value="<?= $assignment->isUnlimited() ? '' : date('H:i', strtotime($assignment->end)) ?>" <?= $required ?>>
        </label>

        <? $disabled = $assignment->type !== 'exam' ? 'disabled' : '' ?>

        <label id="exam_length" style="<?= $disabled ? 'display: none;' : '' ?>">
            <span class="required"><?= _vips('Dauer in Minuten') ?></span>
            <input type="number" name="exam_length" value="<?= htmlReady($assignment->options['duration']) ?>" <?= $disabled ?> required>
        </label>

        <input id="options-toggle" type="checkbox" value="on">
        <label class="caption" for="options-toggle">
            <?= Icon::create('arr_1down', 'clickable', ['class' => 'toggle-open']) ?>
            <?= Icon::create('arr_1right', 'clickable', ['class' => 'toggle-closed']) ?>
            <?= _vips('Weitere Einstellungen') ?>
        </label>

        <label class="undecorated">
            <div class="label_text">
                <?= _vips('Block') ?>
            </div>

            <select name="assignment_block" style="max-width: 22.7em;">
                <option value="0">
                    <?= _vips('Keinem Block zuweisen') ?>
                </option>
                <? foreach ($blocks as $block): ?>
                    <option value="<?= $block->id ?>" <?= $assignment->block_id == $block->id ? 'selected' : '' ?>>
                        <?= htmlReady($block->name) ?>
                    </option>
                <? endforeach ?>
            </select>
            <?= _vips('oder') ?>
            <input type="text" name="assignment_block_name" class="character_input" style="max-width: 22.7em;" placeholder="<?= _vips('Neuen Block anlegen') ?>">
        </label>

        <label>
            <? $selected[$assignment->options['evaluation_mode']] = 'selected' ?>
            <?= _vips('Falsche Antworten in Multiple- und Single-Choice-Aufgaben') ?>

            <select name="evaluation_mode">
                <option value="0" <?= $selected[0] ?>>
                    <?= _vips('&hellip; geben keinen Punktabzug') ?>
                </option>
                <option value="1" <?= $selected[1] ?>>
                    <?= _vips('&hellip; geben Punktabzug (Gesamtpunktzahl Aufgabe mind. 0)') ?>
                </option>
                <option value="2" <?= $selected[2] ?>>
                    <?= _vips('&hellip; geben Punktabzug (negative Punkte für eine Aufgabe möglich)') ?>
                </option>
                <option value="3" <?= $selected[3] ?>>
                    <?= _vips('&hellip; führen zur Bewertung der Aufgabe mit 0 Punkten') ?>
                </option>
            </select>
        </label>

        <label>
            <?= _vips('Notizen (für Teilnehmer unsichtbar)') ?>
            <textarea name="assignment_notes" class="character_input"><?= htmlReady($assignment->options['notes']) ?></textarea>
        </label>

        <? if ($assignment->type == 'exam'): ?>
            <label>
                <?= _vips('IP-Zugriffsbereich (optional)') ?>
                <input type="text" name="ip_range" value="<?= htmlReady($assignment->options['ip_range']) ?>">
            </label>

            <label>
                <?= _vips('Beispiele:') ?>
                <table>
                    <tr>
                        <td><b>123.456.789.321</b></td>
                        <td><?= _vips('gibt nur diese IP frei.') ?></td>
                    </tr>
                    <tr>
                        <td><b>123.456.789</b></td>
                        <td><?= _vips('gibt alle IPs frei, die so beginnen.') ?></td>
                    </tr>
                    <tr>
                        <td><b>123.456.7-123.456.9 </b></td>
                        <td><?= sprintf(_vips('gibt alle IPs frei, die mit %s123.456.7%s, %s123.456.8%s oder %s123.456.9%s beginnen.'), '<b>', '</b>', '<b>', '</b>', '<b>', '</b>') ?></td>
                    </tr>
                    <tr>
                        <td class="smaller" colspan="2">
                            <?= _vips('Außerdem können Mischformen aller genannten Fälle eingetragen werden (durch Komma oder Leerzeichen getrennt).') ?>
                        </td>
                    </tr>
                </table>
            </label>
        <? endif ?>
    </fieldset>

    <table class="default" id="exercises">
        <thead>
            <th colspan="2">
                <?= _vips('Aufgaben') ?>
            </th>
            <th class="actions">
                <?= _vips('Aktionen') ?>
            </th>
        </thead>

        <tbody id="list" class="dynamic_list">
            <?= $this->render_partial('sheets/list_exercises') ?>
        </tbody>
    </table>

    <footer>
        <?= vips_accept_button(_vips('Speichern'), 'store') ?>
        <? if ($assignment_id): ?>
            <?= vips_link_button(_vips('Neue Aufgabe erstellen'), vips_url('sheets/add_exercise_dialog', compact('assignment_id')), ['data-dialog' => 'size=auto']) ?>
        <? else: ?>
            <?= vips_button(_vips('Neue Aufgabe erstellen'), 'none', ['disabled' => '', 'title' => _vips('Aufgaben können erst nach dem Speichern der Grunddaten hinzugefügt werden.')]) ?>
        <? endif ?>
    </footer>
</form>

<script>
    function createSortable() {
        jQuery('#list').sortable({
            axis: 'y',
            containment: 'parent',
            handle: '.vips_drag',
            helper: function(event, element) {
                element.children().width(function(index, width) {
                    return width;
                });

                return element;
            },
            tolerance: 'pointer',
            update: function(event, ui) {
                jQuery.post('<?= vips_url('sheets/move_exercise_ajax', compact('assignment_id')) ?>', jQuery('#list').sortable('serialize', ({ key: 'list[]'})));
            }

        });

        if (jQuery('#list').children().length) {
            jQuery('#exercises').show();
        } else {
            jQuery('#exercises').hide();
        }
    }

    function duplicateExercise(event, id) {
        jQuery('#list').load('<?= vips_url('sheets/copy_exercise_ajax', compact('assignment_id')) ?>', { exercise_id: id }, createSortable);
        event.preventDefault();
    }

    function deleteExercise(event, id, title) {
        STUDIP.Dialog.confirm('<?= _vips('Wollen Sie wirklich die Aufgabe "%s" löschen?') ?>'.replace('%s', title), function() {
            jQuery('#list').load('<?= vips_url('sheets/delete_exercise_ajax', compact('assignment_id')) ?>', { exercise_id: id }, createSortable);
        });
        event.preventDefault();
    }

    createSortable();
</script>
