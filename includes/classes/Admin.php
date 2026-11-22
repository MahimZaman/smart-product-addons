<?php

namespace SmartProductAddons;

defined('ABSPATH') || exit;

class Admin
{
    public function register()
    {
        add_action('init', [$this, 'register_post_types']);
        add_action('add_meta_boxes', [$this, 'options_metabox']);
        add_action('admin_enqueue_scripts', [$this, 'admin_scripts']);
        add_action('wp_ajax_create_new_field', [$this, 'create_new_field']);
        add_action('wp_ajax_load_all_fields', [$this, 'load_all_fields']);
        add_action('save_post', [$this, 'save_rules']);
        add_action('wp_ajax_reorder_serialized_array', [$this, 'spa_reorder_serialized_fields']);
        add_action('wp_ajax_delete_field_by_key', [$this, 'delete_field_by_key']);
        add_action('wp_ajax_load_field_conditions', [$this, 'load_field_conditions']);
        add_action('wp_ajax_save_field_condition', [$this, 'save_field_condition']);
        add_action('wp_ajax_show_field_dependents', [$this, 'show_field_dependents']);
        add_action('wp_ajax_load_field_dependents', [$this, 'load_field_dependents']);
        add_filter('post_row_actions', [$this, 'spa_add_duplicate_post_link'], 10, 2);
        add_action('admin_action_spa_duplicate_post', [$this, 'spa_handle_duplicate_post']);
    }

    public function load_field_dependents()
    {
        $key = sanitize_text_field($_POST['key']);
        $option = sanitize_text_field($_POST['option']);
        $fieldData = isset($_POST['fieldData']) ? maybe_unserialize(wp_unslash($_POST['fieldData'])) : [];
        $showIfChecked = $_POST['showIfChecked'];
        $hideIfChecked = $_POST['hideIfChecked'];
        if (!isset($fieldData[$key]) || !isset($fieldData[$key]['options'][$option])) wp_send_json_error(__('Something went wrong', SPA_TEXTDOMAIN));
        $fieldData[$key]['options'][$option]['dependents']['showIfChecked'] = $showIfChecked;
        $fieldData[$key]['options'][$option]['dependents']['hideIfChecked'] = $hideIfChecked;

        wp_send_json_success([
            'fieldData' => serialize($fieldData),
        ]);
    }

    public function show_field_dependents()
    {
        $key = sanitize_text_field($_POST['key']);
        $option = sanitize_text_field($_POST['option']);
        $fieldData = isset($_POST['fieldData']) ? maybe_unserialize(wp_unslash($_POST['fieldData'])) : [];

        if (!isset($fieldData[$key]) || !isset($fieldData[$key]['options'][$option])) wp_send_json_error(__('Something went wrong', SPA_TEXTDOMAIN));

        $sicOptionHTML = '';
        $hicOptionHTML = '';
        $showIfChecked = isset($fieldData[$key]['options'][$option]['dependents']['showIfChecked']) ? $fieldData[$key]['options'][$option]['dependents']['showIfChecked'] : [];
        $hideIfChecked = isset($fieldData[$key]['options'][$option]['dependents']['hideIfChecked']) ? $fieldData[$key]['options'][$option]['dependents']['hideIfChecked'] : [];
        foreach ($fieldData as $field_key => $field_value) {
            if ($field_value['type'] == 'checkbox') {
                $selectOptions = $field_value['options'];
                foreach ($selectOptions as $selectOption) {
                    if (isset($selectOption['value'])) {
                        $sicOptionHTML .= '<option value="' . $selectOption['value'] . '" ' . (in_array($selectOption['value'], $showIfChecked) ? 'selected' : '') . '>' . $selectOption['value'] . '</option>';
                    }
                }
                foreach ($selectOptions as $selectOption) {
                    if (isset($selectOption['value'])) {
                        $hicOptionHTML .= '<option value="' . $selectOption['value'] . '" ' . (in_array($selectOption['value'], $hideIfChecked) ? 'selected' : '') . '>' . $selectOption['value'] . '</option>';
                    }
                }
            }
        }


        /* Single Dependents */

        // $showIfChecked = isset($fieldData[$key]['options'][$option]['dependents']['showIfChecked']) ? $fieldData[$key]['options'][$option]['dependents']['showIfChecked'] : [];
        // $hideIfChecked = isset($fieldData[$key]['options'][$option]['dependents']['hideIfChecked']) ? $fieldData[$key]['options'][$option]['dependents']['hideIfChecked'] : [];

        // $selectOptions = $fieldData[$key]['options'];
        // $sicOptionHTML = '';
        // foreach ($selectOptions as $selectOption) {
        //     $sicOptionHTML .= '<option value="' . $selectOption['value'] . '" ' . (in_array($selectOption['value'], $showIfChecked) ? 'selected' : '') . '>' . $selectOption['value'] . '</option>';
        // }
        // $hicOptionHTML = '';
        // foreach ($selectOptions as $selectOption) {
        //     $hicOptionHTML .= '<option value="' . $selectOption['value'] . '" ' . (in_array($selectOption['value'], $hideIfChecked) ? 'selected' : '') . '>' . $selectOption['value'] . '</option>';
        // }

        wp_send_json_success([
            'showIfChecked' => $sicOptionHTML,
            'hideIfChecked' => $hicOptionHTML,
            'title'         => $fieldData[$key]['options'][$option]['value'],
            'data'          => $showIfChecked,
        ]);
    }

    public function spa_handle_duplicate_post()
    {

        if (empty($_GET['post'])) {
            wp_die(__('No post to duplicate.', 'spa'));
        }

        $post_id = absint($_GET['post']);

        if (! wp_verify_nonce($_GET['_wpnonce'], 'spa_duplicate_post_' . $post_id)) {
            wp_die(__('Security check failed.', 'spa'));
        }

        $post = get_post($post_id);

        if (! $post || $post->post_type !== 'spa_options') {
            wp_die(__('Invalid post.', 'spa'));
        }

        // Create new post array
        $new_post_args = array(
            'post_title'   => $post->post_title . ' (Copy)',
            'post_content' => $post->post_content,
            'post_status'  => 'publish',
            'post_type'    => $post->post_type,
            'post_author'  => get_current_user_id(),
        );

        $new_post_id = wp_insert_post($new_post_args);

        if (! $new_post_id) {
            wp_die(__('Failed to duplicate.', 'spa'));
        }

        // Copy ALL post meta
        $meta = get_post_meta($post_id);

        foreach ($meta as $meta_key => $meta_values) {
            foreach ($meta_values as $meta_value) {
                add_post_meta(
                    $new_post_id,
                    $meta_key,
                    maybe_unserialize($meta_value)
                );
            }
        }

        // Redirect to edit screen of duplicated post
        wp_safe_redirect(
            admin_url('post.php?action=edit&post=' . $new_post_id)
        );
        exit;
    }

    public function spa_add_duplicate_post_link($actions, $post)
    {

        // Change this to your CPT slug
        if ($post->post_type !== 'spa_options') {
            return $actions;
        }

        if (current_user_can('edit_posts')) {
            $actions['duplicate'] = sprintf(
                '<a href="%s" title="%s">%s</a>',
                wp_nonce_url(
                    admin_url('admin.php?action=spa_duplicate_post&post=' . $post->ID),
                    'spa_duplicate_post_' . $post->ID
                ),
                esc_attr__('Duplicate this item', 'spa'),
                esc_html__('Duplicate', 'spa')
            );
        }

        return $actions;
    }

    public function save_field_condition()
    {
        $fieldKey = sanitize_text_field($_POST['fieldKey']);
        $fieldData = isset($_POST['fieldData']) ? maybe_unserialize(wp_unslash($_POST['fieldData'])) : [];
        $conditions = isset($_POST['conditions']) ? maybe_unserialize(wp_unslash($_POST['conditions'])) : [];
        $relation = sanitize_text_field($_POST['relation']);

        if (!$fieldData[$fieldKey]) wp_send_json_error(__('No fields found', SPA_TEXTDOMAIN));

        $fieldData[$fieldKey]['conditions'] = $conditions;
        $fieldData[$fieldKey]['relation'] = $relation;

        wp_send_json_success([
            'fieldData' => serialize($fieldData)
        ]);
    }

    public function load_field_conditions()
    {
        $fieldKey = sanitize_text_field($_POST['fieldKey']);
        $fieldData = isset($_POST['fieldData']) ? maybe_unserialize(wp_unslash($_POST['fieldData'])) : [];
        $currentField = isset($fieldData[$fieldKey]) ? $fieldData[$fieldKey] : [];
        ob_start();
        if (!empty($currentField['conditions'])) {
            foreach ($currentField['conditions'] as $condition):
?>
                <div class="spaConditionRow">
                    <div class="spaConditionColumn">
                        <div class="spaConditionGroup">
                            <label class="spaLabel"><?php _e('Field', SPA_TEXTDOMAIN); ?></label>
                            <input class="spaConditionField" type="text" value="<?php echo $condition['field'] ?>">
                        </div>
                    </div>
                    <div class="spaConditionColumn">
                        <div class="spaConditionGroup">
                            <label class="spaLabel"><?php _e('Condition', SPA_TEXTDOMAIN); ?></label>
                            <select class="spaCompareCondition">
                                <option value="equal" <?php selected('equal', $condition['compare']) ?>>Is equal</option>
                                <option value="not-equal" <?php selected('not-equal', $condition['compare']) ?>>Not equal</option>
                                <option value="empty" <?php selected('empty', $condition['compare']) ?>>Is empty</option>
                                <option value="not-empty" <?php selected('not-empty', $condition['compare']) ?>>Not empty</option>
                            </select>
                        </div>
                    </div>
                    <div class="spaConditionColumn">
                        <div class="spaConditionGroup">
                            <label class="spaLabel"><?php _e('Value', SPA_TEXTDOMAIN); ?></label>
                            <input class="spaCompareValue" type="text" value="<?php echo $condition['value']; ?>">
                        </div>
                    </div>
                    <span class="spaRemoveCondition"><i class="ri-close-line"></i></span>
                    <span class="spaCorelateCondition <?php echo $condition['corelate'] == 'yes' ? 'active' : '' ?>"><i class="ri-links-line"></i></span>
                </div>
            <?php
            endforeach;
        } else {
            ?>
            <div class="spaConditionRow">
                <div class="spaConditionColumn">
                    <div class="spaConditionGroup">
                        <label class="spaLabel"><?php _e('Field', SPA_TEXTDOMAIN); ?></label>
                        <input class="spaConditionField" type="text">
                    </div>
                </div>
                <div class="spaConditionColumn">
                    <div class="spaConditionGroup">
                        <label class="spaLabel"><?php _e('Condition', SPA_TEXTDOMAIN); ?></label>
                        <select class="spaCompareCondition">
                            <option value="equal">Is equal</option>
                            <option value="not-equal">Not equal</option>
                            <option value="empty">Is empty</option>
                            <option value="not-empty">Not empty</option>
                        </select>
                    </div>
                </div>
                <div class="spaConditionColumn">
                    <div class="spaConditionGroup">
                        <label class="spaLabel"><?php _e('Value', SPA_TEXTDOMAIN); ?></label>
                        <input class="spaCompareValue" type="text">
                    </div>
                </div>
                <span class="spaRemoveCondition"><i class="ri-close-line"></i></span>
                <span class="spaCorelateCondition"><i class="ri-links-line"></i></span>
            </div>
<?php
        }
        $html = ob_get_clean();

        wp_send_json_success(array(
            'data' => $html,
            'relation' => $currentField['relation']
        ));

        wp_die(); //  This doesn't allow printing a 0 at the end of the output. 
    }

    public function delete_field_by_key()
    {
        $key = sanitize_text_field($_POST['key']);
        $fieldData = maybe_unserialize(wp_unslash($_POST['fieldData']));
        unset($fieldData[$key]);
        wp_send_json_success(array(
            'serialized' => serialize($fieldData),
            'key' => $key
        ));
    }

    public function spa_reorder_serialized_fields()
    {
        if (! current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        // ✅ Required data
        if (empty($_POST['order']) || empty($_POST['serialized'])) {
            wp_send_json_error(['message' => 'Missing data']);
        }

        $order      = (array) $_POST['order'];
        $serialized = wp_unslash($_POST['serialized']);

        // ⚠️ SAFE unserialize
        $data = @unserialize($serialized);
        if (! is_array($data)) {
            wp_send_json_error(['message' => 'Invalid serialized payload']);
        }

        $map = [];

        foreach ($data as  $key => $item) {
            $map[$key] = $item;
        }

        $reordered = [];

        foreach ($order as $id) {
            if (isset($map[$id])) {
                $reordered[] = $map[$id];
            }
        }

        if (count($reordered) !== count($map)) {
            wp_send_json_error(['message' => 'Reorder mismatch']);
        }

        wp_send_json_success([
            'serialized' => serialize($reordered)
        ]);
    }

    public function save_rules($post_id)
    {
        if (isset($_POST['spa_fields'])) {
            update_post_meta($post_id, 'spa_get_fields', maybe_unserialize(wp_unslash($_POST['spa_fields'])));
        }

        if (
            !isset($_POST['spaConditionNonce']) ||
            !wp_verify_nonce($_POST['spaConditionNonce'], 'spaConditionNonce')
        ) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        update_post_meta(
            $post_id,
            'spaConditionType',
            sanitize_text_field($_POST['spaConditionType'] ?? 'products')
        );

        if (sanitize_text_field($_POST['spaConditionType']) == 'products') {
            update_post_meta(
                $post_id,
                'spaSelectedProducts',
                array_map('intval', $_POST['spaSelectedProducts'] ?? [])
            );
        } else {
            update_post_meta(
                $post_id,
                'spaSelectedProducts',
                []
            );
        }

        if (sanitize_text_field($_POST['spaConditionType']) == 'categories') {
            update_post_meta(
                $post_id,
                'spaSelectedCategories',
                array_map('intval', $_POST['spaSelectedCategories'] ?? [])
            );
        } else {
            update_post_meta(
                $post_id,
                'spaSelectedCategories',
                []
            );
        }
    }

    public function load_all_fields()
    {
        $fields = !empty($_POST['fieldData']) ? maybe_unserialize(wp_unslash($_POST['fieldData'])) : [];
        $conditionFields = $fields;
        if (!empty($fields)) {
            foreach ($fields as $key => $field) {
                require SPA_PATH . 'includes/templates/SingleField.php';
            }
        }
        wp_die();
    }

    public function create_new_field()
    {

        $fieldData = $_POST['fieldData'];
        $postId = sanitize_text_field($_POST['postId']);
        $fields = get_post_meta($postId, 'spa_get_fields', true) ? get_post_meta($postId, 'spa_get_fields', true) : [];
        $isEdit = $_POST['isEdit'];
        if ($isEdit != 'not-edit') {
            $fields[$isEdit] = $fieldData;
        } else {
            $fields[] = $fieldData;
        }
        update_post_meta($postId, 'spa_get_fields', $fields);
        wp_send_json_success(serialize($fields));
    }

    public function admin_scripts()
    {
        // ENQUEUE MEDIA
        wp_enqueue_media();

        // ENQUEUE CSS FILES
        wp_enqueue_style(
            'spaSelect2Css',
            'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
        );


        wp_enqueue_style('spa-remixicon', 'https://cdn.jsdelivr.net/npm/remixicon@4.7.0/fonts/remixicon.css', [], SPA_VER);
        wp_enqueue_style('spa-admin', SPA_URL . 'assets/admin/css/main.css', [], SPA_VER);

        // ENQUEUE JS FILES
        wp_enqueue_script(
            'spaSelect2Js',
            'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ['jquery'],
            SPA_VER,
            true
        );
        wp_enqueue_script('spa-admin', SPA_URL . 'assets/admin/js/main.js', ['jquery'], SPA_VER, true);

        // LOCALIZE PHP VARIABLE TO JS
        wp_localize_script('spa-admin', 'spaLocal', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('spa_admin_nonce'),
        ]);
    }

    public function register_post_types()
    {
        $labels = array(
            'name'               => __('Smart Product Addons', 'smart-product-addons'),
            'singular_name'      => __('Smart Product Addon', 'smart-product-addons'),
            'add_new_item'       => __('Add New Options', 'smart-product-addons'),
            'edit_item'          => __('Edit Rule', 'smart-product-addons'),
            'new_item'           => __('New Option', 'smart-product-addons'),
            'all_items'          => __('All Options', 'smart-product-addons'),
            'menu_name'          => __('Smart Product Addons', 'smart-product-addons')
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'has_archive'        => false,
            'rewrite'            => array(
                'slug' => 'spa_options',
                'with_front' => false
            ),
            'supports'           => array('title'),
            'show_in_rest'       => true,
            'menu_icon'          => 'dashicons-excerpt-view'
        );

        register_post_type('spa_options', $args);
    }

    public function options_metabox()
    {
        add_meta_box('spa-options', __('Options', 'smart-product-addons'), [$this, 'render_options'], 'spa_options', 'normal', 'high');
        add_meta_box('spa-templates', __('Templates', 'smart-product-addons'), [$this, 'render_templates'], 'spa_options', 'side', 'default');
    }

    public function render_templates($post)
    {
        require_once SPA_PATH . 'includes/templates/TemplateMetabox.php';
    }

    public function render_options()
    {
        require_once SPA_PATH . 'includes/templates/OptionFieldsMetabox.php';
    }
}
