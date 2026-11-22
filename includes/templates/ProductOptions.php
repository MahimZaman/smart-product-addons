<?php
defined('ABSPATH') || exit;
?>
<div class="spaProductOptionsContainer">
    <div id="spaProductOptions" data-base="<?php echo $product->get_price(); ?>">
        <?php
        if (!empty($fields)) {
            foreach ($fields as $field) {
                $type = $field['type'];
                $label = $field['label'];
                $fieldId = $field['name'];
                if ($type === 'image-button'):
        ?>
                    <div class="spaFieldGroup" data-condition='<?php echo isset($field['conditions']) ? json_encode($field['conditions']) : "" ?>' data-relation='<?php echo isset($field['relation']) ? $field['relation'] : "" ?>'>
                        <h3 class="spaFieldTitle"><?php _e($label, SPA_TEXTDOMAIN); ?></h3>
                        <div class="spaImageFieldOptions">
                            <label for="<?php echo $fieldId . '_option_none'; ?>" class="spaImageFieldOption">
                                <input type="radio" name="<?php echo $fieldId; ?>" id="<?php echo $fieldId . '_option_none'; ?>" value="">
                                <div class="spaOptionImage">
                                    <span class="spaImageNone"><i class="ri-prohibited-line"></i></span>
                                    <span class="spaImageOptionChecked">
                                        <i class="ri-check-line"></i>
                                        <i class="ri-add-line"></i>
                                    </span>
                                </div>
                                <span class="spaImageFieldToolTip"><?php echo __('None', SPA_TEXTDOMAIN); ?></span>
                            </label>
                            <?php if (isset($field['options'])): ?>
                                <?php foreach ($field['options'] as $fieldKey => $fieldOption): ?>
                                    <label for="<?php echo $fieldId . '_option_' . $fieldKey; ?>" class="spaImageFieldOption">
                                        <input type="radio" name="<?php echo $fieldId; ?>" id="<?php echo $fieldId . '_option_' . $fieldKey; ?>" data-price="<?php echo $fieldOption['price'] ?>" value="<?php echo __($fieldOption['label'], SPA_TEXTDOMAIN); ?>">
                                        <div class="spaOptionImage">
                                            <img src="<?php echo $fieldOption['image'] ?>" alt="<?php echo __($fieldOption['label'], SPA_TEXTDOMAIN); ?>">
                                            <span class="spaImageOptionChecked">
                                                <i class="ri-check-line"></i>
                                                <i class="ri-add-line"></i>
                                            </span>
                                        </div>
                                        <span class="spaImageFieldToolTip"><?php echo __($fieldOption['label'], SPA_TEXTDOMAIN); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php
                elseif ($type === 'radio'):
                ?>
                    <div class="spaFieldGroup" data-condition='<?php echo isset($field['conditions']) ? json_encode($field['conditions']) : "" ?>' data-relation='<?php echo isset($field['relation']) ? $field['relation'] : "" ?>'>
                        <h3 class="spaFieldTitle"><?php _e($label, SPA_TEXTDOMAIN); ?></h3>
                        <?php
                        if (!empty($field['options'])):
                            foreach ($field['options'] as $option_key => $option_value):
                        ?>
                                <label for="<?php echo $fieldId . "_option_" . $option_key; ?>" class="spaRadioField">
                                    <input type="radio" name="<?php echo $fieldId; ?>" id="<?php echo $fieldId . "_option_" . $option_key; ?>" value="<?php echo $option_value['value'] ?>" data-price="<?php echo $option_value['price']; ?>">
                                    <div class="spaRadioFieldLabel">
                                        <span class="spaRadioFieldIcon"></span>
                                        <span class="spaRadioFieldLabelText">
                                            <?php echo __($option_value['label'], SPA_TEXTDOMAIN); ?>
                                        </span>
                                        <small>+<?php echo wc_price($option_value['price']); ?></small>
                                    </div>
                                    <?php if (isset($option_value['children'])): ?>
                                        <div class="spaRadioChildren">
                                            <div class="spaImageFieldOptions">
                                                <?php foreach ($option_value['children'] as $childKey => $childValue): ?>
                                                    <label for="<?php echo $fieldId . '_child_' . $childKey; ?>" class="spaImageFieldOption">
                                                        <input type="radio" name="<?php echo $fieldId . "_child"; ?>" id="<?php echo $fieldId . '_child_' . $childKey; ?>" value="<?php echo __($childValue['label'], SPA_TEXTDOMAIN); ?>">
                                                        <div class="spaOptionImage">
                                                            <img src="<?php echo $childValue['image'] ?>" alt="<?php echo __($childValue['label'], SPA_TEXTDOMAIN); ?>">
                                                            <span class="spaImageOptionChecked">
                                                                <i class="ri-check-line"></i>
                                                                <i class="ri-add-line"></i>
                                                            </span>
                                                        </div>
                                                        <span class="spaImageFieldToolTip"><?php echo __($childValue['label'], SPA_TEXTDOMAIN); ?></span>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </label>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </div>
                <?php
                elseif ($type === 'checkbox'):
                ?>
                    <div class="spaFieldGroup" data-condition='<?php echo isset($field['conditions']) ? json_encode($field['conditions']) : "" ?>' data-relation='<?php echo isset($field['relation']) ? $field['relation'] : "" ?>'>
                        <h3 class="spaFieldTitle"><?php _e($label, SPA_TEXTDOMAIN); ?></h3>
                        <?php
                        if (!empty($field['options'])):
                            foreach ($field['options'] as $option_key => $option_value):
                        ?>
                                <label for="<?php echo $fieldId . "_option_" . $option_key; ?>" class="spaCheckboxField">
                                    <input type="checkbox" name="<?php echo $fieldId; ?>" id="<?php echo $fieldId . "_option_" . $option_key; ?>" value="<?php echo $option_value['value'] ?>" data-price="<?php echo $option_value['price']; ?>" data-dependents='<?php echo json_encode($option_value['dependents'] ?? ''); ?>'>
                                    <div class="spaCheckboxLabel">
                                        <span class="spaCheckBoxIcon">
                                            <i class="ri-check-line"></i>
                                            <i class="ri-add-line"></i>
                                        </span>
                                        <span class="spaCheckboxLabelText">
                                            <?php echo __($option_value['label'], SPA_TEXTDOMAIN); ?>
                                        </span>
                                        <small>+<?php echo wc_price($option_value['price']); ?></small>
                                    </div>
                                    <?php if (isset($option_value['children'])): ?>
                                        <div class="spaCheckboxChildren">
                                            <div class="spaImageFieldOptions">
                                                <?php foreach ($option_value['children'] as $childKey => $childValue): ?>
                                                    <label for="<?php echo $fieldId . '_child_' . $childKey; ?>" class="spaImageFieldOption">
                                                        <input type="radio" name="<?php echo $fieldId . "_child"; ?>" id="<?php echo $fieldId . '_child_' . $childKey; ?>" value="<?php echo __($childValue['label'], SPA_TEXTDOMAIN); ?>">
                                                        <div class="spaOptionImage">
                                                            <img src="<?php echo $childValue['image'] ?>" alt="<?php echo __($childValue['label'], SPA_TEXTDOMAIN); ?>">
                                                            <span class="spaImageOptionChecked">
                                                                <i class="ri-check-line"></i>
                                                                <i class="ri-add-line"></i>
                                                            </span>
                                                        </div>
                                                        <span class="spaImageFieldToolTip"><?php echo __($childValue['label'], SPA_TEXTDOMAIN); ?></span>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </label>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </div>
                <?php
                elseif ($type === 'select'):
                ?>
                    <div class="spaFieldGroup" data-condition='<?php echo isset($field['conditions']) ? json_encode($field['conditions']) : "" ?>' data-relation='<?php echo isset($field['relation']) ? $field['relation'] : "" ?>'>
                        <h3 class="spaFieldTitle"><?php _e($label, SPA_TEXTDOMAIN); ?></h3>
                        <select class="spaSelectField" name="<?php echo $fieldId; ?>" id="<?php echo $fieldId; ?>">
                            <option value=""><?php _e('Choose an option', SPA_TEXTDOMAIN); ?></option>
                            <?php
                            if (!empty($field['options'])):
                                foreach ($field['options'] as $option_key => $option_value):
                            ?>
                                    <option value="<?php echo $option_value['value']; ?>" data-price="<?php echo (float) $option_value['price'] ?>"><?php echo $option_value['label'] . ($option_value['price'] == 0 ? '' : ' +' . wc_price($option_value['price'])); ?></option>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
                <?php
                elseif ($type === 'text'):
                ?>
                    <div class="spaFieldGroup" data-condition='<?php echo isset($field['conditions']) ? json_encode($field['conditions']) : "" ?>' data-relation='<?php echo isset($field['relation']) ? $field['relation'] : "" ?>'>
                        <h3 class="spaFieldTitle"><?php _e($label, SPA_TEXTDOMAIN); ?></h3>
                        <input type="text" data-label="<?php echo $label; ?>" name="<?php echo $fieldId; ?>" id="<?php echo $fieldId ?>" class="spaTextField" data-price="<?php echo $field['options'][0]['price'] ?>">
                        <small class="spaFieldPrice">+<?php echo wc_price($field['options'][0]['price']); ?></small>
                    </div>
                <?php
                elseif ($type === 'textarea'):
                ?>
                    <div class="spaFieldGroup" data-condition='<?php echo isset($field['conditions']) ? json_encode($field['conditions']) : "" ?>' data-relation='<?php echo isset($field['relation']) ? $field['relation'] : "" ?>'>
                        <h3 class="spaFieldTitle"><?php _e($label, SPA_TEXTDOMAIN); ?></h3>
                        <textarea type="text" data-label="<?php echo $label; ?>" name="<?php echo $fieldId; ?>" id="<?php echo $fieldId ?>" class="spaMessageField" data-price="<?php echo $field['options'][0]['price'] ?>" rows="<?php echo $field['options'][0]['rows'] ?>" maxlength="<?php echo isset($field['options'][0]['characters']) ? $field['options'][0]['characters'] : 200; ?>"></textarea>
                        <small class="spaFieldPrice">+<?php echo wc_price($field['options'][0]['price']); ?></small>
                    </div>
        <?php
                endif;
            }
        }
        ?>
    </div>

    <div id="spaContentRight">
        <div class="spaContentRightSticky">
            <h2 class="spaOrderSummary"><?php _e('Order Summary', SPA_TEXTDOMAIN); ?></h2>
            <div id="spaPriceReview">
                <table class="spaPriceTable">
                    <tbody></tbody>
                </table>
            </div>