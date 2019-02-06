<!-- start character_picker -->

<? if ($active_character_set == NULL) {
    $first_character_set = reset($available_character_sets);
    $active_character_set = $first_character_set['name'];
} ?>

<div id="character_picker_header">
    <? if (count($available_character_sets) > 1) : ?>
        <select name="character_set" onchange="jQuery('#character_picker div[id^=character_set_]').hide(); jQuery('#character_set_' + jQuery(this).val()).show();">
            <? foreach ($available_character_sets as $set) : ?>
                <option value="<?= $set['name'] ?>"<? if ($active_character_set == $set['name']) : ?> selected<? endif ?>>
                    <?= htmlReady($set['title']) ?>
                </option>
            <? endforeach ?>
        </select>
    <? else : ?>
        <?= htmlReady($available_character_sets[$character_set]['title']) ?>
    <? endif ?>
</div>

<? foreach ($available_character_sets as $set) : ?>
    <div id="character_set_<?= $set['name'] ?>" style="<? if ($active_character_set != $set['name']) : ?>display: none;<? endif ?>">
        <? foreach ($set['characters'] as $block) : ?>
            <span class="block">
                <? if (isset($block['title'])) : ?>
                    <span class="block_title">
                        <?= $block['title'] ?>
                    </span>
                <? endif ?>

                <? foreach ($block['chars'] as $hex => $description) : ?>
                    <button id="<?= $hex ?>" type="button" title="<?= $description ?>" onclick="insertAtCaret(this);">
                        &#x<?= implode(';&#x', explode('+', $hex)) ?>;
                    </button>
                <? endforeach ?>
            </span>
        <? endforeach ?>

        <? if (isset($set['optional'])) : ?>
            <span id="optional_characters_hidden" onclick="jQuery('#optional_characters_hidden, #optional_characters_shown').toggle();" style="cursor: pointer;">
                &#x25b8;&nbsp;<?= _vips('mehr') ?>
            </span>
            <span id="optional_characters_shown" style="display: none;">
                <? foreach ($set['optional'] as $block) : ?>
                    <span class="block">
                        <? if (isset($block['title'])) : ?>
                            <span class="block_title">
                                <?= $block['title'] ?>
                            </span>
                        <? endif ?>

                        <? foreach ($block['chars'] as $hex => $description) : ?>
                            <button id="<?= $hex ?>" type="button" title="<?= $description ?>" onclick="insertAtCaret(this);">
                                &#x<?= implode(';&#x', explode('+', $hex)) ?>;
                            </button>
                        <? endforeach ?>
                    </span>
                <? endforeach ?>

                <span onclick="jQuery('#optional_characters_hidden, #optional_characters_shown').toggle();" style="cursor: pointer;">
                    &#x25c2;&nbsp;<?= _vips('weniger') ?>
                </span>
            </span>
        <? endif ?>
    </div>
<? endforeach ?>

<!-- end character_picker -->
