<? $resources = get_object_vars($userRooms) ?>
<? if (sizeof($resources) > 0) : ?>
    <table class="collapsable default">
        <thead> 
            <caption>
            </caption>
            <colgroup>
                <col style="width: 70%">
                <col style="width: 15%">
                <col style="width: 15%">
            </colgroup>
        </thead> 
        <tr>
            <th><?= htmlReady("Resource") ?></th>
            <th><?= htmlReady("Berechtigungsart") ?></th>
            <th><?= htmlReady("Rechtezuweisung") ?></th> 
        </tr> 
        <tbody class = "collapsed"> 
             
            <? foreach ($resources as $key => $value): ?>
                <tr class="table_header header-row">
                    <th class="toggle-indicator">               
                        <a class="toggler"><b><?= htmlReady($key) ?></b></a>
                    </th>
                    <th></th>
                    <th></th>
                </tr>       
                <? foreach ($value as $targetRoom): ?>
                    <tr> 
                        <td ><?= ($targetRoom[3]) ?></td>
                            <? if (!$targetRoom[1]) : ?>
                                    <td><?= htmlReady("keine") ?> </td>
                                <? else: ?>
                                    <td><?= htmlReady($targetRoom[1]) ?> </td>
                                <? endif ?>
                            <? if ($targetRoom[1]) : ?>
                                <td> <?= htmlReady("direkt") ?> </td>
                            <? else: ?>
                                <td><?= htmlReady("indirekt") ?> </td>
                            <? endif ?>                          
                    </tr>
                <? endforeach ?>  
        </tbody>
            <?endforeach?>
    </table>
    <? else: ?>
        <?=PageLayout::postMessage(MessageBox::info(_('Für diesen Nutzer wurden noch keine Resourcenrechte vergeben'))) ?>
<? endif ?>

<script> 
    $('#rights_filter').ready(function($) {
        $('#rights_filter').change(function() {
            var direct = $('.direct');
            var indirect = $('.indirect');
            if ($('#rights_filter').prop('checked') == true) {
                indirect.hide();
                console.log("test");
            }
            else {
                indirect.show();
            }
        });
    });
</script>