<? setlocale(LC_NUMERIC, NULL) ?>

<h1>
    <?= sprintf(_vips('Aufgabenblatt &bdquo;%s&ldquo;'), htmlReady($test_title)) ?>
</h1>

<? if ($overall_uncorrected_solutions > 0) : ?>
    <p style="font-weight: bold;">
        <a href="<?= vips_link('solutions/edit_solution', ['assignment_id' => $assignment_id, 'exercise_id' => $first_uncorrected_solution['exercise_id'], 'solver_id' => $first_uncorrected_solution['solver_id'], 'single_solver' => $first_uncorrected_solution['solver_type'] == 'single', 'view' => $view]) ?>" title="<?= _vips('Korrigieren Sie hier die nächste unkorrigierte Lösung') ?>">
            <?= sprintf(n_vips('Es muss noch %d Lösung korrigiert werden.', 'Es müssen noch %d Lösungen korrigiert werden.', $overall_uncorrected_solutions), $overall_uncorrected_solutions) ?>
        </a>
    </p>
<? endif ?>

<p>
    <?= _vips("Klicken Sie auf die Schaltfläche &bdquo;Autokorrektur&ldquo;, um alle Aufgaben automatisch korrigieren zu lassen. Die bereits manuell durchgeführten Korrekturen werden durch diese Aktion nicht überschrieben.") ?><br>

    <?= vips_link_button(_vips('Autokorrektur'), vips_url('solutions/autocorrect_solutions', compact('assignment_id', 'expand', 'view')), ['title' => _vips('automatisch korrigieren')]) ?>
</p>

<table class="default">
    <caption>
        <?= _vips('Lösungen der Teilnehmer') ?>

        <span class="actions">
            <form action="<?= vips_link('solutions/assignment_solutions') ?>" method="POST">
                <input type="hidden" name="assignment_id" value="<?= $assignment_id ?>">

                <label>
                    <?= _vips('Anzeigefilter:') ?>

                    <? if ($test_type != 'exam') : ?>
                        <select name="view" onChange="this.form.submit();">
                            <option value="" <?= !$view ? 'selected' : '' ?>>
                                <?= _vips('Studenten mit abgegebenen Lösungen') ?>
                            </option>
                            <option value="todo" <?= $view == 'todo' ? 'selected' : '' ?>>
                                <?= _vips('Studenten mit unkorrigierten Lösungen') ?>
                            </option>
                            <option value="all" <?= $view == 'all' ? 'selected' : '' ?>>
                                <?= _vips('alle Studenten') ?>
                            </option>
                        </select>
                    <? else :  /* exams */ ?>
                        <select name="view" onChange="this.form.submit();">
                            <option value="" <?= !$view ? 'selected' : '' ?>>
                                <?= _vips('Studenten, die die Klausur beendet haben') ?>
                            </option>
                            <option value="working" <?= $view == 'working' ? 'selected' : '' ?>>
                                <?= _vips('Studenten, die die Klausur gerade bearbeiten') ?>
                            </option>
                            <option value="pending" <?= $view == 'pending' ? 'selected' : '' ?>>
                                <?= _vips('Studenten, die die Klausur noch nicht begonnen haben') ?>
                            </option>
                        </select>
                    <? endif ?>
                </label>

                <noscript>
                    <?= vips_button(_vips('Anzeigen'), 'anzeigen') ?>
                </noscript>
            </form>
        </span>
    </caption>
    <thead>
        <tr>
            <th></th>
            <th>
                <? if ($expand != 'all') : ?>
                    <a href="<?= vips_link('solutions/assignment_solutions', ['assignment_id' => $assignment_id, 'view' => $view, 'expand' => 'all']) ?>" onclick="display_all_exercises(event);">
                        <?= Icon::create('arr_1right', 'clickable', ['class' => 'arrow_all', 'title' => _vips('Aufgaben aller Teilnehmer anzeigen')]) ?>
                        <?= Icon::create('arr_1down', 'clickable', ['class' => 'arrow_all', 'title' => _vips('Aufgaben aller Teilnehmer verstecken'), 'style' => 'display: none;']) ?>
                        <?=_vips('Teilnehmer') ?>
                    </a>
                <? else : ?>
                    <a href="<?= vips_link('solutions/assignment_solutions', ['assignment_id' => $assignment_id, 'view' => $view]) ?>" onclick="display_all_exercises(event);">
                        <?= Icon::create('arr_1right', 'clickable', ['class' => 'arrow_all', 'title' => _vips('Aufgaben aller Teilnehmer anzeigen'), 'style' => 'display: none;']) ?>
                        <?= Icon::create('arr_1down', 'clickable', ['class' => 'arrow_all', 'title' => _vips('Aufgaben aller Teilnehmer verstecken')]) ?>
                        <?= _vips('Teilnehmer') ?>
                    </a>
                <? endif ?>
            </th>
            <th style="text-align: center;">
                <?=_vips("Nachricht")?>
            </th>
            <th style="text-align: center;">
                <?=_vips("Punkte")?>
            </th>
            <th style="text-align: center;">
                <?= _vips("Prozent") ?>
            </th>
            <th style="text-align: center;">
                <?= _vips("unkorrigierte Lösungen") ?>
            </th>
            <th style="text-align: center;">
                <?= _vips("unbearbeitete Aufgaben") ?>
            </th>
        </tr>
    </thead>

    <tbody>
        <? $i = 0; ?>
        <? foreach ($solvers as $solver) : ?>
            <? /* reached points, uncorrected solutions and unanswered exercises */ ?>
            <? $reached_points = 0; ?>
            <? $uncorrected_solutions = 0; ?>
            <? $unanswered_exercises = count($exercises); ?>
            <? if (isset($solutions[$solver['id']]) && is_array($solutions[$solver['id']])) : ?>
                <? foreach ($solutions[$solver['id']] as $solution) : ?>
                    <? $reached_points += $solution['points']; ?>
                    <? if (!$solution['corrected']) : ?>
                        <? $uncorrected_solutions++; ?>
                    <? endif ?>
                    <? $unanswered_exercises--; ?>
                <? endforeach ?>
            <? endif ?>

            <tr id="row_<?= $solver['id'] ?>" style="vertical-align: top; border-top: 1px dotted black;" class="solution <?= $expand == $solver['id'] || $expand == 'all' ? '' : 'solution-closed' ?>">
                <td style="text-align: right;">
                    <a name="<?= $solver['id'] ?>">
                        <?= ++$i ?>.
                    </a>
                </td>

                <? /* single solvers */ ?>
                <? if ($solver['type'] == 'single') : ?>
                    <td>
                        <? if ($expand != $solver['id'] && $expand != 'all') : ?>
                            <a href="<?= vips_link('solutions/assignment_solutions', ['assignment_id' => $assignment_id, 'view' => $view, 'expand' => $solver['id']]) ?>#<?= $solver['id'] ?>" onclick="display_exercises(event, '<?= $solver['id'] ?>');">
                                <?= Icon::create('arr_1right', 'clickable', ['class' => 'solution-open', 'title' => _vips('Aufgaben anzeigen')]) ?>
                                <?= Icon::create('arr_1down', 'clickable', ['class' => 'solution-close', 'title' => _vips('Aufgaben verstecken')]) ?>
                                <?= htmlReady($solver['name']) ?>
                            </a>
                        <? else : ?>
                            <a href="<?= vips_link('solutions/assignment_solutions', ['assignment_id' => $assignment_id, 'view' => $view]) ?>#<?= $solver['id'] ?>" onclick="display_exercises(event, '<?= $solver['id'] ?>');">
                                <?= Icon::create('arr_1right', 'clickable', ['class' => 'solution-open', 'title' => _vips('Aufgaben anzeigen')]) ?>
                                <?= Icon::create('arr_1down', 'clickable', ['class' => 'solution-close', 'title' => _vips('Aufgaben verstecken')]) ?>
                                <?= htmlReady($solver['name']) ?>
                            </a>
                        <? endif ?>

                        <? /* running info */ ?>
                        <? if ($test_type == 'exam' && $view === 'working') : ?>
                            <? $ip        = $solver['running_info']['ip'] ?>
                            <? $start     = $solver['running_info']['start'] ?>
                            <? $remaining = $solver['running_info']['remaining'] ?>
                            <div class="smaller">
                                <?= _vips('IP-Adresse') ?>: <?= htmlReady($ip) ?> (<?= htmlReady(gethostbyaddr($ip)) ?>)<br>
                                <?= _vips('Start') ?>: <span title="<?= strftime('%A, %d.%m.%Y', $start) ?>"><?= sprintf(_vips('%s Uhr'), date('H:i', $start)) ?></span><? if ($remaining > 0) : ?> (<?= sprintf(n_vips('noch %d Minute', 'noch %d Minuten', $remaining), $remaining) ?>)<? endif ?>
                            </div>
                        <? endif ?>
                    </td>

                    <? /* send mail */ ?>
                    <td style="text-align: center;">
                        <? if (isset($solver['user_name'])) : ?>
                            <a href="<?= URLHelper::getLink('dispatch.php/messages/write', ['rec_uname' => $solver['user_name']]) ?>" data-dialog>
                                <?= Icon::create('mail', 'clickable', ['title' => sprintf(_vips("eine Nachricht an %s schreiben"), htmlReady($solver['name']))]) ?>
                            </a>
                        <? endif ?>
                    </td>

                <? /* groups */ ?>
                <? elseif ($solver['type'] == 'group') : ?>
                    <td colspan="2">
                        <table style="width: 100%;">
                            <tr>
                                <td style="border-style: none; padding: 0px; width: 84%;">
                                    <? if ($expand != $solver['id'] && $expand != 'all') : ?>
                                        <a href="<?= vips_link('solutions/assignment_solutions', ['assignment_id' => $assignment_id, 'view' => $view, 'expand' => $solver['id']]) ?>#<?= $solver['id'] ?>" onclick="display_exercises(event, '<?= $solver['id'] ?>');">
                                            <?= Icon::create('arr_1right', 'clickable', ['class' => 'solution-open', 'title' => _vips('Aufgaben anzeigen')]) ?>
                                            <?= Icon::create('arr_1down', 'clickable', ['class' => 'solution-close', 'title' => _vips('Aufgaben verstecken')]) ?>
                                            <?= htmlReady($solver['name']) ?>:
                                        </a>
                                    <? else : ?>
                                        <a href="<?= vips_link('solutions/assignment_solutions', ['assignment_id' => $assignment_id, 'view' => $view]) ?>#<?= $solver['id'] ?>" onclick="display_exercises(event, '<?= $solver['id'] ?>');">
                                            <?= Icon::create('arr_1right', 'clickable', ['class' => 'solution-open', 'title' => _vips('Aufgaben anzeigen')]) ?>
                                            <?= Icon::create('arr_1down', 'clickable', ['class' => 'solution-close', 'title' => _vips('Aufgaben verstecken')]) ?>
                                            <?= htmlReady($solver['name']) ?>:
                                        </a>
                                    <? endif ?>
                                </td>
                                <td style="border-style: none; padding: 0px; width: 16%;">
                                    <? for ($receivers = []; list(, $member) = each($solver['members']); $receivers[] = $member['user_name']); ?>
                                    <a href="<?= URLHelper::getLink('dispatch.php/messages/write', ['rec_uname' =>  $receivers]) ?>" data-dialog>
                                        <?= Icon::create('mail', 'clickable', ['title' => sprintf(_vips("eine Nachricht an alle Mitglieder der Gruppe %s schreiben"), htmlReady($solver['name']))]) ?>
                                    </a>
                                </td>
                            </tr>

                            <? /* all members in group */ ?>
                            <? foreach ($solver['members'] as $member) : ?>
                                <tr>
                                    <td class="smaller" style="border-style: none; padding: 0 0 0 18px; width: 84%;">
                                        <?= htmlReady($member['name']) ?>
                                    </td>
                                    <td style="border-style: none; padding: 0px; width: 16%;">
                                        <a href="<?= URLHelper::getLink('dispatch.php/messages/write', ['rec_uname' => $member['user_name']]) ?>" data-dialog>
                                            <?= Icon::create('mail', 'clickable', ['title' => sprintf(_vips("eine Nachricht an %s schreiben"), htmlReady($member['name']))]) ?>
                                        </a>
                                    </td>
                                </tr>
                            <? endforeach ?>
                        </table>
                    </td>

                <? endif ?>

                <? /* reached points */ ?>
                <td style="text-align: center;">
                    <?= $reached_points ?> / <?= $overall_max_points ?>
                </td>

                <? /* percent */ ?>
                <td style="text-align: center;">
                    <? if ($overall_max_points != 0) : ?>
                        <?= sprintf('%.1f %%', round($reached_points / $overall_max_points * 100, 1)) ?>
                    <? else : ?>
                        &ndash;
                    <? endif ?>
                </td>

                <? /* uncorrected solutions */ ?>
                <td style="text-align: center;">
                    <? if ($uncorrected_solutions != 0) : ?>
                        <?= $uncorrected_solutions ?>
                    <? else : ?>
                        &ndash;
                    <? endif ?>
                </td>

                <? /* unanswered exercises */ ?>
                <td style="text-align: center;">
                    <? if ($unanswered_exercises != 0) : ?>
                        <?= $unanswered_exercises ?>
                    <? else : ?>
                        &ndash;
                    <? endif ?>
                </td>
            </tr>

            <tr>
                <td></td>
                <td colspan="6">
                    <table style="width: 100%;">
                        <tr class="smaller">
                            <? $col_count = 0; ?>
                            <? foreach ($exercises as $exercise) : ?>
                                <td style="width: 20%; border-style: none; padding: 0 0 3px 0; vertical-align: top;">
                                    <? if (isset($solutions[$solver['id']][$exercise['id']])) : ?>
                                        <a href="<?= vips_link('solutions/edit_solution', ['assignment_id' => $assignment_id, 'exercise_id' => $exercise['id'], 'solver_id' => $solver['id'], 'single_solver' => $solver['type'] == 'single', 'view' => $view]) ?>">
                                            <? if (isset($solutions[$solver['id']][$exercise['id']]['corrector_id'])) : ?>
                                                <span class="solution-corrected">
                                                    <?= htmlReady($exercise['title']) ?>
                                                </span>
                                            <? elseif ($solutions[$solver['id']][$exercise['id']]['corrected']) : ?>
                                                <span class="solution-autocorrected">
                                                    <?= htmlReady($exercise['title']) ?>
                                                </span>
                                            <? else : ?>
                                                <span class="solution-uncorrected">
                                                    <?= htmlReady($exercise['title']) ?>
                                                </span>
                                            <? endif ?>
                                        </a>
                                    <? else : ?>
                                        <span class="solution-none">
                                            <?= htmlReady($exercise['title']) ?>
                                        </span>
                                    <? endif ?>
                                    <br/>

                                    <? /* reached / max points */ ?>
                                    <? $max_points = $exercises[$exercise['id']]['points'] ?>
                                    <? if (isset($solutions[$solver['id']][$exercise['id']])) : ?>
                                        <? $points = doubleval($solutions[$solver['id']][$exercise['id']]['points']) /* => 0 if null */ ?>
                                        <? $title  = sprintf(n_vips('%s von %s Punkt', '%s von %s Punkten', $max_points), $points, $max_points) ?>
                                        <? if ($points > $max_points || $points < 0) : ?>
                                            <span style="font-size: smaller; cursor: help; color: red;" title="<?= htmlReady($title) ?>">
                                                (<?= $points ?>/<?= $max_points ?>)
                                            </span>
                                        <? else : ?>
                                            <span style="font-size: smaller; cursor: help;" title="<?= htmlReady($title) ?>">
                                                (<?= $points ?>/<?= $max_points ?>)
                                            </span>
                                        <? endif ?>
                                    <? else : ?>
                                        <span class="solution-none" style="font-size: smaller;">
                                            (0/<?= $max_points ?>)
                                        </span>
                                    <? endif ?>
                                </td>
                                <? if (++$col_count % 5 == 0): ?>
                                    </tr>
                                    <tr class="smaller">
                                <? endif ?>
                            <? endforeach ?>
                        </tr>
                    </table>
                </td>
            </tr>
        <? endforeach ?>
    </tbody>
</table>

<script>
    function display_exercises(event, solver_id) {
        jQuery('#row_' + solver_id).toggleClass('solution-closed');

        jQuery(document.body).trigger('sticky_kit:recalc');
        event.preventDefault();
    }

    function display_all_exercises(event) {
        if (jQuery('.arrow_all').first().css('display') != 'none') {
            jQuery('.arrow_all').toggle();
            jQuery('.solution').removeClass('solution-closed');
        } else {
            jQuery('.arrow_all').toggle();
            jQuery('.solution').addClass('solution-closed');
        }

        jQuery(document.body).trigger('sticky_kit:recalc');
        event.preventDefault();
    }
</script>

<? setlocale(LC_NUMERIC, 'C') ?>
