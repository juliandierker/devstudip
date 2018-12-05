<h3><?= $rule->getName() ?></h3>
<? if ($rule->isFCFSallowed()) : ?>
    <label for="enable_FCFS">
    <input <?=($rule->prio_exists ? 'disabled' : '')?> type="checkbox" id="enable_FCFS"  name="enable_FCFS" value="1" <?= (!is_null($rule->getDistributionTime()) && !$rule->getDistributionTime() ? "checked" : ""); ?>>
    <?=_("Automatische Platzverteilung (Windhund-Verfahren)")?>
    <?=($rule->prio_exists ? tooltipicon(_("Es existieren bereits Anmeldungen für die automatische Platzverteilung.")) : '')?>
    </label>
<? endif ?>
<label for="start" class="caption">
    <span id="losvf_title"><?= _('Zeitpunkt der automatischen Platzverteilung (Los-Verfahren)') ?>:</span>
</label>
<label class="col-1">
    <?= _('Datum') ?>
    <input type="text" name="distributiondate" id="distributiondate"
        class="size-s no-hint" placeholder="tt.mm.jjjj"
        value="<?= $rule->getDistributionTime() ? date('d.m.Y', $rule->getDistributionTime()) : '' ?>"/>
</label>

<label class="col-1">
    <?= _('Uhrzeit') ?>
    <input type="text" name="distributiontime" id="distributiontime"
        class="size-s no-hint" placeholder="ss:mm"
        value="<?= $rule->getDistributionTime() ? date('H:i', $rule->getDistributionTime()) : '23:59' ?>"/>
</label>

<label  class="col-1">
    <span id="rule_info"> <?= MessageBox::info('Wenn das Windhundverfahren nicht erw�nscht ist, einfach das H�kchen wegnehmen. F�r das Losverfahren ist ein Datum und eine Uhrzeit notwendig') ?>
</label>

<script>
    $("#enable_FCFS").ready(
        function(){
            $("#distributiondate").prop('checked', true);
            $("#distributiondate").prop('disabled', true);
            $("#distributiontime").prop('disabled', true);
            $("#rule_info").attr('disabled', false);
            $("#losvf_title").css("color", "grey");

            $("#enable_FCFS").change(
                function(){
                    if ($(this).is(':checked')) {
                        $("#distributiondate").prop('disabled', true);
                        $("#distributiontime").prop('disabled', true);
                        $("#rule_info").attr('disabled', false);
                        $("#losvf_title").css("color", "grey");  
                    } else {
                        $("#rule_info").remove();
                        $("#distributiondate").prop('disabled', false);
                        $("#distributiontime").prop('disabled', false);
                        $("#losvf_title").css("color", "#333333");
                        $('#distributiondate').datepicker();
                        $('#distributiondate').datepicker().css("top", "0%");
                        $('#distributiontime').timepicker();
                        $('#distributiondate').timepicker().css("top", "0%");

                    }
                }
            );       
        }
    );
    
    
</script>
