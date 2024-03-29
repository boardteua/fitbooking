<?php

if (!class_exists('ACF_Field_User')) :

    class ACF_Field_User extends ACF_Field
    {

        /**
         * Initializes the field type.
         *
         * @date    5/03/2014
         * @param void
         * @return    void
         * @since    5.0.0
         *
         */
        function initialize()
        {

            // Props.
            $this->name = 'user';
            $this->label = __("User", 'acf');
            $this->category = 'relational';
            $this->defaults = array(
                'role' => '',
                'multiple' => 0,
                'allow_null' => 0,
                'return_format' => 'array',
            );

            // Register filter variations.
            acf_add_filter_variations('acf/fields/user/query', array('name', 'key'), 1);
            acf_add_filter_variations('acf/fields/user/result', array('name', 'key'), 2);
            acf_add_filter_variations('acf/fields/user/search_columns', array('name', 'key'), 3);

            // Add AJAX query.
            add_action('wp_ajax_acf/fields/user/query', array($this, 'ajax_query'));
            add_action('wp_ajax_nopriv_acf/fields/user/query', array($this, 'ajax_query'));
        }

        /**
         * Renders the field settings HTML.
         *
         * @date    23/01/13
         * @param array $field The ACF field.
         * @return    void
         * @since    3.6.0
         *
         */
        function render_field_settings($field)
        {

            acf_render_field_setting($field, array(
                'label' => __('Filter by role', 'acf'),
                'instructions' => '',
                'type' => 'select',
                'name' => 'role',
                'choices' => acf_get_user_role_labels(),
                'multiple' => 1,
                'ui' => 1,
                'allow_null' => 1,
                'placeholder' => __("All user roles", 'acf'),
            ));

            acf_render_field_setting($field, array(
                'label' => __('Allow Null?', 'acf'),
                'instructions' => '',
                'name' => 'allow_null',
                'type' => 'true_false',
                'ui' => 1,
            ));

            acf_render_field_setting($field, array(
                'label' => __('Select multiple values?', 'acf'),
                'instructions' => '',
                'name' => 'multiple',
                'type' => 'true_false',
                'ui' => 1,
            ));

            acf_render_field_setting($field, array(
                'label' => __('Return Format', 'acf'),
                'instructions' => '',
                'type' => 'radio',
                'name' => 'return_format',
                'choices' => array(
                    'array' => __("User Array", 'acf'),
                    'object' => __("User Object", 'acf'),
                    'id' => __("User ID", 'acf'),
                ),
                'layout' => 'horizontal',
            ));
        }

        /**
         * Renders the field input HTML.
         *
         * @date    23/01/13
         * @param array $field The ACF field.
         * @return    void
         * @since    3.6.0
         *
         */
        function render_field($field)
        {

            // Change Field into a select.
            $field['type'] = 'select';
            $field['ui'] = 1;
            $field['ajax'] = 1;
            $field['choices'] = array();

            // Populate choices.
            if ($field['value']) {

                // Clean value into an array of IDs.
                $user_ids = array_map('intval', acf_array($field['value']));

                // Find users in database (ensures all results are real).
                $users = acf_get_users(array(
                    'include' => $user_ids
                ));

                // Append.
                if ($users) {
                    foreach ($users as $user) {
                        $field['choices'][$user->ID] = $this->get_result($user, $field);
                    }
                }
            }

            // Render.
            acf_render_field($field);
        }

        /**
         * Returns the result text for a fiven WP_User object.
         *
         * @date    1/11/2013
         * @param WP_User $user The WP_User object.
         * @param array $field The ACF field related to this query.
         * @param    (int|string) $post_id The post_id being edited.
         * @return    string
         * @since    5.0.0
         *
         */
        function get_result($user, $field, $post_id = 0)
        {

            // Get user result item.
            $item = acf_get_user_result($user);

            // Default $post_id to current post being edited.
            $post_id = $post_id ? $post_id : acf_get_form_data('post_id');

            /**
             * Filters the result text.
             *
             * @date    21/5/19
             * @param array $args The query args.
             * @param array $field The ACF field related to this query.
             * @param    (int|string) $post_id The post_id being edited.
             * @since    5.8.1
             *
             */
            return apply_filters("acf/fields/user/result", $item['text'], $user, $field, $post_id);
        }

        /**
         * Filters the field value after it is loaded from the database.
         *
         * @date    23/01/13
         * @param mixed $value The field value.
         * @param mixed $post_id The post ID where the value is saved.
         * @param array $field The field array containing all settings.
         * @return    mixed
         * @since    3.6.0
         *
         */
        function load_value($value, $post_id, $field)
        {

            // Add compatibility for version 4.
            if ($value === 'null') {
                return false;
            }
            return $value;
        }

        /**
         * Filters the field value after it is loaded from the database but before it is returned to the front-end API.
         *
         * @date    23/01/13
         * @param mixed $value The field value.
         * @param mixed $post_id The post ID where the value is saved.
         * @param array $field The field array containing all settings.
         * @return    mixed
         * @since    3.6.0
         *
         */
        function format_value($value, $post_id, $field)
        {

            // Bail early if no value.
            if (!$value) {
                return false;
            }

            // Clean value into an array of IDs.
            $user_ids = array_map('intval', acf_array($value));

            // Find users in database (ensures all results are real).
            $users = acf_get_users(array(
                'include' => $user_ids
            ));

            // Bail early if no users found.
            if (!$users) {
                return false;
            }

            // Format values using field settings.
            $value = array();
            foreach ($users as $user) {

                // Return object.
                if ($field['return_format'] == 'object') {
                    $item = $user;

                    // Return array.
                } elseif ($field['return_format'] == 'array') {
                    $item = array(
                        'ID' => $user->ID,
                        'user_firstname' => $user->user_firstname,
                        'user_lastname' => $user->user_lastname,
                        'nickname' => $user->nickname,
                        'user_nicename' => $user->user_nicename,
                        'display_name' => $user->display_name,
                        'user_email' => $user->user_email,
                        'user_url' => $user->user_url,
                        'user_registered' => $user->user_registered,
                        'user_description' => $user->user_description,
                        'user_avatar' => get_avatar($user->ID),
                    );

                    // Return ID.
                } else {
                    $item = $user->ID;
                }

                // Append item
                $value[] = $item;
            }

            // Convert to single.
            if (!$field['multiple']) {
                $value = array_shift($value);
            }

            // Return.
            return $value;
        }

        /**
         * Filters the field value before it is saved into the database.
         *
         * @date    23/01/13
         * @param mixed $value The field value.
         * @param mixed $post_id The post ID where the value is saved.
         * @param array $field The field array containing all settings.
         * @return    mixed
         * @since    3.6.0
         *
         */
        function update_value($value, $post_id, $field)
        {

            // Bail early if no value.
            if (empty($value)) {
                return $value;
            }

            // Format array of values.
            // - ensure each value is an id.
            // - Parse each id as string for SQL LIKE queries.
            if (acf_is_sequential_array($value)) {
                $value = array_map('acf_idval', $value);
                $value = array_map('strval', $value);

                // Parse single value for id.
            } else {
                $value = acf_idval($value);
            }

            // Return value.
            return $value;
        }

        /**
         * Callback for the AJAX query request.
         *
         * @date    24/10/13
         * @param void
         * @return    void
         * @since    5.0.0
         *
         */
        function ajax_query()
        {

            // Modify Request args.
            if (isset($_REQUEST['s'])) {
                $_REQUEST['search'] = $_REQUEST['s'];
            }
            if (isset($_REQUEST['paged'])) {
                $_REQUEST['page'] = $_REQUEST['paged'];
            }

            // Add query hooks.
            add_action('acf/ajax/query_users/init', array($this, 'ajax_query_init'), 10, 2);
            add_filter('acf/ajax/query_users/args', array($this, 'ajax_query_args'), 10, 3);
            add_filter('acf/ajax/query_users/result', array($this, 'ajax_query_result'), 10, 3);
            add_filter('acf/ajax/query_users/search_columns', array($this, 'ajax_query_search_columns'), 10, 4);

            // Simulate AJAX request.
            acf_get_instance('ACF_Ajax_Query_Users')->request();
        }

        /**
         * Runs during the AJAX query initialization.
         *
         * @date    9/3/20
         * @param array $request The query request.
         * @param ACF_Ajax_Query $query The query object.
         * @return    void
         * @since    5.8.8
         *
         */
        function ajax_query_init($request, $query)
        {

            // Require field.
            if (!$query->field) {
                $query->send(new WP_Error('acf_missing_field', __('Error loading field.', 'acf'), array('status' => 404)));
            }
        }

        /**
         * Filters the AJAX query args.
         *
         * @date    9/3/20
         * @param array $args The query args.
         * @param array $request The query request.
         * @param ACF_Ajax_Query $query The query object.
         * @return    array
         * @since    5.8.8
         *
         */
        function ajax_query_args($args, $request, $query)
        {

            // Add specific roles.
            if ($query->field['role']) {
                $args['role__in'] = acf_array($query->field['role']);
            }

            /**
             * Filters the query args.
             *
             * @date    21/5/19
             * @param array $args The query args.
             * @param array $field The ACF field related to this query.
             * @param    (int|string) $post_id The post_id being edited.
             * @since    5.8.1
             *
             */
            return apply_filters("acf/fields/user/query", $args, $query->field, $query->post_id);
        }

        /**
         * Filters the WP_User_Query search columns.
         *
         * @date    9/3/20
         * @param array $columns An array of column names to be searched.
         * @param string $search The search term.
         * @param WP_User_Query $WP_User_Query The WP_User_Query instance.
         * @return    array
         * @since    5.8.8
         *
         */
        function ajax_query_search_columns($columns, $search, $WP_User_Query, $query)
        {

            /**
             * Filters the column names to be searched.
             *
             * @date    21/5/19
             * @param array $columns An array of column names to be searched.
             * @param string $search The search term.
             * @param WP_User_Query $WP_User_Query The WP_User_Query instance.
             * @param array $field The ACF field related to this query.
             * @since    5.8.1
             *
             */
            return apply_filters("acf/fields/user/search_columns", $columns, $search, $WP_User_Query, $query->field);
        }

        /**
         * Filters the AJAX Query result.
         *
         * @date    9/3/20
         * @param array $item The choice id and text.
         * @param WP_User $user The user object.
         * @param ACF_Ajax_Query $query The query object.
         * @return    array
         * @since    5.8.8
         *
         */
        function ajax_query_result($item, $user, $query)
        {

            /**
             * Filters the result text.
             *
             * @date    21/5/19
             * @param string The result text.
             * @param WP_User $user The user object.
             * @param array $field The ACF field related to this query.
             * @param    (int|string) $post_id The post_id being edited.
             * @since    5.8.1
             *
             */
            $item['text'] = apply_filters("acf/fields/user/result", $item['text'], $user, $query->field, $query->post_id);
            return $item;
        }

        /**
         * Return an array of data formatted for use in a select2 AJAX response.
         *
         * @date    15/10/2014
         * @param array $args An array of query args.
         * @return    array
         * @since    5.0.9
         * @deprecated 5.8.9
         *
         */
        function get_ajax_query($options = array())
        {
            _deprecated_function(__FUNCTION__, '5.8.9');
            return array();
        }

        /**
         * Filters the WP_User_Query search columns.
         *
         * @date    15/10/2014
         * @param array $columns An array of column names to be searched.
         * @param string $search The search term.
         * @param WP_User_Query $WP_User_Query The WP_User_Query instance.
         * @return    array
         * @since    5.0.9
         * @deprecated 5.8.9
         *
         */
        function user_search_columns($columns, $search, $WP_User_Query)
        {
            _deprecated_function(__FUNCTION__, '5.8.9');
            return $columns;
        }
    }


// initialize
    acf_register_field_type('ACF_Field_User');

endif; // class_exists check
