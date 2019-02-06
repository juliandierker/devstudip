<? if (count($groups) === 0): ?>
    <?= MessageBox::info(_vips('Es wurden noch keine Gruppen angelegt.')) ?>
<? else: ?>
    <? foreach ($groups as $group): ?>
        <table class="default">
            <caption>
                <?= htmlReady($group->name) ?>
                <span class="actions">
                    <?= _vips('Größe:') ?> <?= $group->size ?>
                </span>
            </caption>

            <thead>
                <tr>
                    <th>
                        <?= _vips('Teilnehmer') ?>
                    </th>

                    <th class="actions">
                        <? if (vips_has_status('tutor')) :?>
                            <? if ($group->size > count($group->current_members)): ?>
                                <?= MultiPersonSearch::get('group' . $group->id)
                                        ->setExecuteURL(vips_url('groups/assign_participants', ['group_id' => $group->id]))
                                        ->setSearchObject(new PermissionSearch('user_in_sem', '', 'user_id', ['seminar_id' => $group->course_id, 'sem_perm' => ['autor']]))
                                        ->setDefaultSelectableUser($selectable_users)
                                        ->setDefaultSelectedUser($unselectable_users)
                                        ->render() ?>
                            <? endif ?>

                            <a href="<?= vips_link('groups/edit_group_dialog', ['group_id' => $group->id]) ?>" data-dialog="size=auto" title="<?= _vips('Gruppe bearbeiten') ?>">
                                <?= Icon::create('edit') ?>
                            </a>
                            <a href="<?= vips_link('groups/delete_group', ['group_id' => $group->id]) ?>" title="<?= _vips('Gruppe löschen') ?>"
                               <? if (count($group->current_members)): ?>
                                   data-confirm="<?= htmlReady(sprintf(_vips('Es befinden sich noch Teilnehmer in der Gruppe "%s". Wollen Sie die Gruppe trotzdem löschen?'), $group->name)) ?>"
                               <? endif ?>>
                                <?= Icon::create('trash') ?>
                            </a>
                        <? elseif ($settings->selfassign && $group->size > count($group->current_members)): ?>
                            <a href="<?= vips_link('groups/assign_participant', ['group_id' => $group->id]) ?>" title="<?= _vips('In diese Gruppe eintragen') ?>">
                                <?= Icon::create('community+add') ?>
                            </a>
                        <? endif ?>
                    </th>
                </tr>
            </thead>

            <tbody class="dynamic_list">
                <? foreach ($group->current_members as $member) : ?>
                    <tr>
                        <td class="dynamic_counter">
                            <a href="<?= URLHelper::getLink('dispatch.php/profile', ['username' => $member->user->username]) ?>">
                                <?= htmlReady($member->user->Nachname.', '.$member->user->Vorname) ?>
                            </a>
                        </td>
                        <td class="actions">
                            <? if (vips_has_status('tutor') || $settings->selfassign && $member->user_id === $user_id) :?>
                                <a href="<?= vips_link('groups/delete_participant', ['group_id' => $group->id, 'user_id' => $member->user_id]) ?>"
                                   title="<?= vips_has_status('tutor') ? _vips('Teilnehmer aus der Gruppe austragen') : _vips('Aus dieser Gruppe austragen') ?>">
                                    <?= Icon::create('trash') ?>
                                </a>
                            <? endif ?>
                        </td>
                    </tr>
                <? endforeach?>
            </tbody>
        </table>
    <? endforeach ?>
<? endif ?>
