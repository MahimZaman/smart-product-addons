<?php

defined('ABSPATH') || exit;

?>

<div class="containerFluid">
    <input type="hidden" value="<?php echo get_the_ID() ?>" id="spaRuleId">
    <input type="hidden" value='<?php echo serialize(get_post_meta(get_the_ID(), 'spa_get_fields', true)) ?>' name="spa_fields" id="spaFieldsInput">
    <div class="optionsHeader">
        <div class="spaProgressLoader">
            <span class="spaDottedLoader"></span>
            <span class="spaDottedLoaderText"><?php _e('Loading...', 'smart-product-addons'); ?></span>
        </div>
        <button class="spaButton spaButtonPrimary spaAddField" type="button"><?php _e('Add Field', 'smart-product-addons'); ?> <i class="ri-add-line"></i></button>
    </div>
    <div class="ruleFields"></div>
</div>

<!-- Modal for adding fields -->
<div class="spaFieldModal">
    <div class="spaModalInner">
        <button type="button" class="spaCloseModal">
            <i class="ri-close-line"></i>
        </button>
        <div class="spaModalForm">
            <div class="spaFormGroup">
                <label for="spaFieldType" class="spaLabel"><?php _e('Field type:', 'smart-product-addons'); ?></label>
                <select name="spa_field_type" class="spaSelectField" id="spaFieldType">
                    <option value=""><?php _e('Select a field', 'smart-product-addons'); ?></option>
                    <option value="text"><?php _e('Text', 'smart-product-addons'); ?></option>
                    <option value="select"><?php _e('Select Dropdown', 'smart-product-addons'); ?></option>
                    <option value="checkbox"><?php _e('Checkbox', 'smart-product-addons'); ?></option>
                    <option value="radio"><?php _e('Radio Button', 'smart-product-addons'); ?></option>
                    <option value="textarea"><?php _e('Textarea', 'smart-product-addons'); ?></option>
                    <option value="image-button"><?php _e('Image Button', 'smart-product-addons'); ?></option>
                </select>
            </div>

            <div class="spaFormGroup">
                <label for="spaFieldLabel" class="spaLabel"><?php _e('Field label:', 'smart-product-addons'); ?></label>
                <input type="text" name="spa_field_label" id="spaFieldLabel" class="spaTextField">
            </div>

            <div class="spaFormGroup">
                <label for="spaFieldName" class="spaLabel"><?php _e('Name:'); ?></label>
                <input type="text" name="spa_field_name" id="spaFieldName" class="spaTextField">
            </div>

            <div class="spaFormGroup spaOptionContainer spaFieldDetails" id="fieldOptions">
                <label class="spaLabel"><?php _e('Options:', 'smart-product-addons'); ?></label>
                <div class="spaOptionRows">
                    <div class="spaOptionRow">
                        <div class="spaOptionColumn">
                            <label for="" class="spaLabel"><?php _e('Label', 'smart-product-addons'); ?></label>
                            <input type="text" class="spaTextField spaSelectOptionLabel">
                        </div>
                        <div class="spaOptionColumn">
                            <label for="" class="spaLabel"><?php _e('Value', 'smart-product-addons'); ?></label>
                            <input type="text" class="spaTextField spaSelectOptionValue">
                        </div>
                        <div class="spaOptionColumn">
                            <label for="" class="spaLabel"><?php _e('Price', 'smart-product-addons'); ?></label>
                            <input type="number" class="spaTextField spaSelectOptionPrice" placeholder="Ex: 10.99">
                        </div>
                        <div class="spaOptionColumn">
                            <div class="spaFlex spaAlignCenter spaGap-1">
                                <button type="button" class="spaButtonIcon spaDeleteRow"><i class="ri-delete-bin-fill"></i></button>
                            </div>
                        </div>
                        <div class="spaChildOptionRows">
                            <button type="button" class="spaAddChildOption"><?php _e('Add Child', SPA_TEXTDOMAIN); ?></button>
                        </div>
                    </div>
                </div>
                <div class="spaOptionAction">
                    <button type="button" class="spaAddOption"><?php _e('Add Option +'); ?></button>
                </div>
            </div>

            <div class="spaFormGroup spaFieldDetails spaOptionContainer" id="textareaFieldOptions">
                <label class="spaLabel"><?php _e('Settings:', 'smart-product-addons'); ?></label>
                <div class="spaFormGroup">
                    <label for="spaTextareaRows" class="spaLabel"><?php _e('Rows:'); ?></label>
                    <input type="number" id="spaTextareaRows" class="spaTextField" placeholder="Ex: 5">
                </div>
                <div class="spaFormGroup">
                    <label for="spaTextareaCharacterLimits" class="spaLabel"><?php _e('Character Limits:'); ?></label>
                    <input type="number" id="spaTextareaCharacterLimits" class="spaTextField" placeholder="Ex: 100">
                </div>
                <div class="spaFormGroup">
                    <label for="spaTextareaPrice" class="spaLabel"><?php _e('Price:'); ?></label>
                    <input type="number" id="spaTextareaPrice" class="spaTextField" placeholder="Ex: 10.99">
                </div>
            </div>

            <div class="spaFormGroup spaFieldDetails spaOptionContainer" id="textFieldOptions">
                <label class="spaLabel"><?php _e('Settings:', 'smart-product-addons'); ?></label>
                <div class="spaFormGroup">
                    <label for="spaTextPrice" class="spaLabel"><?php _e('Price:'); ?></label>
                    <input type="number" id="spaTextPrice" class="spaTextField" placeholder="Ex: 10.99">
                </div>
            </div>

            <div class="spaFormGroup spaOptionContainer spaFieldDetails" id="imageFieldOptions">
                <label class="spaLabel"><?php _e('Options:', 'smart-product-addons'); ?></label>
                <div class="spaOptionRows">
                    <div class="spaOptionRow">
                        <div class="spaOptionColumn">
                            <label for="" class="spaLabel"><?php _e('Upload Image', 'smart-product-addons'); ?></label>
                            <img class="spaImagePreview" src="" />
                            <input type="hidden" value="" class="spaImageOptionUrl">
                            <div class="spaImageBtns">
                                <button type="button" class="spaUploadImage spaSmallButton"><?php _e('Upload', 'smart-product-addons'); ?></button>
                                <button type="button" class="spaRemoveImage spaSmallButton"><?php _e('Remove', 'smart-product-addons'); ?></button>
                            </div>
                        </div>
                        <div class="spaOptionColumn">
                            <label for="" class="spaLabel"><?php _e('Label', 'smart-product-addons'); ?></label>
                            <input type="text" class="spaTextField spaImageOptionLabel">
                        </div>
                        <div class="spaOptionColumn">
                            <label for="" class="spaLabel"><?php _e('Price', 'smart-product-addons'); ?></label>
                            <input type="number" class="spaTextField spaImageOptionPrice" placeholder="Ex: 10.99">
                        </div>
                        <div class="spaOptionColumn">
                            <div class="spaFlex spaAlignCenter spaGap-1">
                                <button type="button" class="spaButtonIcon spaDeleteRow"><i class="ri-delete-bin-fill"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="spaOptionAction">
                    <button type="button" class="spaAddImageOption"><?php _e('Add Option +'); ?></button>
                </div>
            </div>

            <div class="spaNotice"></div>
            <button type="button" class="spaSubmitFields spaButton" data-edit="not-edit"><?php _e('Save Field', 'smart-product-addons'); ?></button>
        </div>
    </div>
</div>

<div class="spaFieldConditionRules">
    <div class="spaConditionHead">
        <h2 class="spaConditionHeading"><?php _e('Conditions', SPA_TEXTDOMAIN); ?></h2>
        <span class="spaConditionClose">
            <i class="ri-close-line"></i>
        </span>
    </div>
    <div class="spaConditionBody">
        <div class="spaConditions">
            Loading...
        </div>
        <button class="spaAddCondition" type="button"><?php _e('Add Condition', SPA_TEXTDOMAIN); ?></button>
    </div>
    <div class="spaConditionActions">
        <select id="spaConditionRelation">
            <option value="AND">AND</option>
            <option value="OR">OR</option>
        </select>
        <button type="button" class="spaSaveCondition"><?php _e('Save Condition', SPA_TEXTDOMAIN); ?></button>
    </div>
</div>

<div class="spaDependentRules">
    <div class="spaDependentsHead">
        <h2 class="spaDependentsHeading"><?php _e('Dependents', SPA_TEXTDOMAIN); ?> - <span class="spaDependentOption"></span></h2>
        <span class="spaDependentsClose">
            <i class="ri-close-line"></i>
        </span>
    </div>
    <div class="spaDependentsBody">
        <div class="spaDependents">
            <div class="spaFormGroup">
                <label for="spaShowIfChecked" class="spaLabel"><?php _e('Show if checked', SPA_TEXTDOMAIN); ?></label>
                <select id="spaShowIfChecked" multiple>
                    <option value=""><?php echo __('Select options', SPA_TEXTDOMAIN); ?></option>
                </select>
                <small class="spaInstruction"><?php _e('Write options separated by comma. Ex: Option A,Option B,Option C'); ?></small>
            </div>
            <div class="spaFormGroup">
                <label for="spaHideIfChecked" class="spaLabel"><?php _e('Hide if checked', SPA_TEXTDOMAIN); ?></label>
                <select id="spaHideIfChecked" multiple>
                    <option value=""><?php echo __('Select options', SPA_TEXTDOMAIN); ?></option>
                </select>
                <small class="spaInstruction"><?php _e('Write options separated by comma. Ex: Option A,Option B,Option C'); ?></small>
            </div>
        </div>
    </div>
    <div class="spaDependentsActions">
        <button type="button" class="spaSaveDependents spaButtonWithLoader">
            <span class="spaButtonText"><?php _e('Save Dependents', SPA_TEXTDOMAIN); ?></span>
            <span class="spaButtonLoader"><span class="spaBtnLoader spaLoaderSmall spaLoaderWhite"></span></span>
        </button>
    </div>
</div>