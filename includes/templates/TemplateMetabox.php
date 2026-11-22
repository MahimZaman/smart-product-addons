<?php
defined('ABSPATH') || exit;
wp_nonce_field('spaConditionNonce', 'spaConditionNonce');

$conditionType     = get_post_meta($post->ID, 'spaConditionType', true) ?: 'products';
$selectedProducts  = (array) get_post_meta($post->ID, 'spaSelectedProducts', true);
$selectedCategories = (array) get_post_meta($post->ID, 'spaSelectedCategories', true);

$products = wc_get_products([
    'limit' => -1,
    'status' => 'publish',
]);

$categories = get_terms([
    'taxonomy'   => 'product_cat',
    'hide_empty' => false,
]);
?>
<div class="spaConditionWrapper">

    <p>
        <label>
            <input type="radio" name="spaConditionType" value="products" <?php checked($conditionType, 'products'); ?>>
            <?php _e('Choose Products', SPA_TEXTDOMAIN) ?>
        </label>
        &nbsp;&nbsp;
        <label>
            <input type="radio" name="spaConditionType" value="categories" <?php checked($conditionType, 'categories'); ?>>
            <?php _e('Choose Categories', SPA_TEXTDOMAIN) ?>
        </label>
    </p>

    <div id="spaProductsField" class="spaConditionField" <?php if ($conditionType !== 'products') echo 'style="display:none"'; ?>>
        <select id="spaProductsSelect" name="spaSelectedProducts[]" class="spaSelectField" multiple>
            <?php foreach ($products as $product): ?>
                <option value="<?php echo esc_attr($product->get_id()); ?>"
                    <?php selected(in_array($product->get_id(), $selectedProducts)); ?>>
                    <?php echo esc_html($product->get_name()); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div id="spaCategoriesField" class="spaConditionField" <?php if ($conditionType !== 'categories') echo 'style="display:none"'; ?>>
        <select id="spaCategoriesSelect" name="spaSelectedCategories[]" class="spaSelectField" multiple>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo esc_attr($category->term_id); ?>"
                    <?php selected(in_array($category->term_id, $selectedCategories)); ?>>
                    <?php echo esc_html($category->name); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>