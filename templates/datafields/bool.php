<input type="hidden" name="<?= $name ?>[<?= $model->id ?>]" value="0">

<label>
    <input type="checkbox" name="<?= $name ?>[<?= $model->id ?>]"
           value="1" id="<?= $name ?>_<?= $model->id ?>"
           <? if ($value) echo 'checked'; ?>
           <? if ($model->is_required) echo 'required'; ?>>
   <span class="datafield_title <?= $model->is_required ? 'required' : '' ?>">
       <?= htmlReady($model->name) ?>
   </span>

   <? if ($tooltip): ?>
       <?= tooltipIcon($tooltip, $important ?: false) ?>
   <? endif; ?>
</label>
