<?php
defined('ABSPATH') || exit;
?>
<div class="spaSingleField" data-id="<?php echo $key; ?>" data-name="<?php echo $field['name']; ?>">
    <div class="spaFieldLabel">
        <span class="spaFieldDrag">
            <i class="ri-draggable"></i>
        </span>
        <?php echo $field['label']; ?>
        <div>
            <small class="spaFieldKey">
                <i class="ri-file-copy-line"></i>
                <span><?php echo $field['name']; ?></span>
            </small>
        </div>
        <?php if ($field['type'] === 'text' || $field['type'] === 'textarea'): ?>
            <ul class="spaFieldBadges">
                <?php if (isset($field['options'][0]['price'])): ?>
                    <li class="spaFieldBadge"><?php echo '<b>Price:</b> ' . wc_price($field['options'][0]['price']) ?></li>
                <?php endif; ?>
                <?php if (isset($field['options'][0]['rows'])): ?>
                    <li class="spaFieldBadge"><?php echo '<b>Rows:</b> ' . $field['options'][0]['rows'] ?></li>
                <?php endif; ?>
                <?php if (isset($field['options'][0]['characters'])): ?>
                    <li class="spaFieldBadge"><?php echo '<b>Character Limit:</b> ' . $field['options'][0]['characters'] ?></li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>
        <span class="spaFieldType"><?php echo $field['type']; ?></span>
        <span class="spaFieldActions">
            <button type="button" class="spaEditField" data-edit="<?php echo $key; ?>" data-fields='<?php echo json_encode($field) ?>'><i class="ri-edit-line"></i></button>
            <button type="button" class="spaFieldCondition" data-key="<?php echo $key; ?>"><i class="ri-flow-chart"></i></button>
            <button type="button" class="spaDeleteField" data-delete="<?php echo $key; ?>"><i class="ri-delete-bin-line"></i></button>
        </span>
    </div>
    <?php if (!empty($field['options'])): ?>
        <ul class="spaFieldOptions">
            <?php if ($field['type'] == 'select' || $field['type'] == 'checkbox' || $field['type'] == 'radio'): ?>
                <?php foreach ($field['options'] as $option_key => $option): ?>
                    <li class="spaFieldList">
                        <div class="spaFieldItem">
                            <?php echo $option['label'] . '(' . $option['value'] . ') +' . wc_price($option['price']) ?>
                            <?php if ($field['type'] === 'checkbox'): ?>
                                <button type="button" class="spaFieldDependents" data-key="<?php echo $key; ?>" data-option="<?php echo $option_key; ?>"><?php _e('Dependents', SPA_TEXTDOMAIN); ?></button>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($option['children'])): ?>
                            <ul class="spaChildFieldOptions">
                                <?php foreach ($option['children'] as $optionChildren): ?>
                                    <li>
                                        <?php if ($optionChildren['image']): ?>
                                            <img src="<?php echo $optionChildren['image']; ?>" alt="<?php echo $optionChildren['label']; ?>">
                                        <?php endif; ?>
                                        <span><?php echo $optionChildren['label']; ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            <?php elseif ($field['type'] == 'image-button'): ?>
                <?php foreach ($field['options'] as $option): ?>
                    <li class="spaImageFieldOption">
                        <?php if ($option['image']): ?>
                            <img src="<?php echo $option['image']; ?>" alt="<?php echo $option['label']; ?>">
                        <?php endif; ?>
                        <span><?php echo $option['label'] . ' +' . wc_price($option['price']); ?></span>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    <?php endif; ?>
</div>