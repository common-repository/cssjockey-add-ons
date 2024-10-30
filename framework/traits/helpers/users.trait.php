<?php

/**
 * Trait cjwpbldr_helpers_users
 */
trait cjwpbldr_helpers_users {

    /**
     * @return string[]
     */
    public function getWPUserRoles() {
        global $wp_roles;
        if( ! isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles();
        }

        return $wp_roles->get_names();
    }

    /**
     * @return mixed
     */
    public function userRoles() {
        global $wp_roles;
        $return = $wp_roles->get_names();

        /*$options = get_option( 'wp_envato_user_roles' );
        $return = [];

        if( is_array( $options ) && ! empty( $options ) ) {
            foreach( $options as $role_key => $role ) {
                $return[ $role_key ] = $role['name'];
            }
        }*/

        return $return;
    }

    /**
     * @param $user_var
     *
     * @return array|bool
     */
    public function userRole( $user_var ) {
        global $wpdb;
        if( ! $user_var ) {
            return false;
        }
        if( is_numeric( $user_var ) ) {
            $user_data = get_userdata( $user_var );

            return $user_data->roles;
        } elseif( substr_count( $user_var, '@' ) ) {
            $column = 'user_email';
        } else {
            $column = 'user_login';
        }
        $user = $wpdb->get_row( "SELECT * FROM $wpdb->users WHERE {$column} = '{$user_var}'", ARRAY_A );
        $user_data = get_userdata( $user['ID'] );

        return ( ! is_null( $user_data->roles )) ? $user_data->roles : [];
    }

    /**
     * @return array
     */
    public function usersTableFields() {
        $users_table_fields = ['ID', 'user_login', 'user_pass', 'user_pass_confirmation', 'user_nicename', 'user_email', 'user_url', 'user_registered', 'user_activation_key', 'user_status', 'display_name'];

        return $users_table_fields;
    }

    /**
     * @param       $var
     * @param array $additional_fields
     *
     * @return array
     */
    public function userInfo( $var, $additional_fields = [] ) {
        global $wpdb;
        $return = [];

        $exclude_strings = ['user_pass', 'session', 'wp_', 'meta-box', 'screen_layout'];
        if( is_numeric( $var ) ) {
            $column = 'ID';
        } elseif( substr_count( $var, '@' ) ) {
            $column = 'user_email';
        } else {
            $column = 'user_login';
        }
        $user_data = $wpdb->get_row( "SELECT * FROM $wpdb->users WHERE {$column} = '{$var}'", ARRAY_A );
        if( empty( $user_data ) && is_numeric( $var ) && strlen( $var ) > 9 ) {
            $user_data_by_mobile = $this->getUserByMeta( 'user_mobile', $var, 'info' );
            $user_data = $wpdb->get_row( "SELECT * FROM $wpdb->users WHERE ID = '{$user_data_by_mobile['ID']}'", ARRAY_A );
        }

        if( ! is_array( $user_data ) ) {
            return [];
        }

        foreach( $user_data as $key => $value ) {
            $return_value = (is_serialized( $value )) ? unserialize( $value ) : $value;
            if( is_numeric( $return_value ) ) {
                $return_value = (int) $return_value;
            }
            $return[ $key ] = $return_value;
        }
        $user_meta = $wpdb->get_results( "SELECT * FROM $wpdb->usermeta WHERE user_id = '{$user_data['ID']}'", ARRAY_A );
        if( is_array( $user_meta ) ) {
            foreach( $user_meta as $meta ) {
                $return[ $meta['meta_key'] ] = (is_serialized( $meta['meta_value'] )) ? @unserialize( $meta['meta_value'] ) : $meta['meta_value'];
                //$return[$meta['meta_key']] = $meta['meta_value'];
            }
        }

        $first_name = (isset( $return['first_name'] ) && $return['first_name'] != '') ? $return['first_name'] : '';
        $last_name = (isset( $return['last_name'] ) && $return['last_name'] != '') ? ' ' . $return['last_name'] : '';
        $return['display_name'] = ($first_name != '') ? $first_name . $last_name : $return['nickname'];
        $return['posts_link_url'] = get_author_posts_url( $return['ID'], $return['nickname'] );
        $return['gravatar'] = $this->gravatar( $return['ID'] );
        $return['default_avatar'] = $this->root_url . '/framework/html/images/default-avatar.png';
        $return['user_avatar'] = (isset( $return['user_avatar'] ) && $return['user_avatar'] != '') ? $return['user_avatar'] : $return['gravatar'];
        //$return['user_avatar'] = $return['default_avatar'];

        // crop user avatar is not square
        /*if( ! strpos( $return['user_avatar'], 'gravatar' ) ) {
            list( $width, $height, $type, $attr ) = getimagesize( $return['user_avatar'] );
            if( $width !== $height ) {
                $cropped = $this->imageResize( $return['user_avatar'], array(256, 256), true );
                update_user_meta( $return['ID'], 'default_avatar', $cropped );
                update_user_meta( $return['ID'], 'user_avatar', $cropped );
                $return['user_avatar'] = get_user_meta( $return['ID'], 'user_avatar', true );
                $return['default_avatar'] = get_user_meta( $return['ID'], 'user_avatar', true );
            }
        }*/

        $return['last_login'] = (isset( $return['last_login'] )) ? $this->wpDate( $return['last_login'] ) : '';
        $return['access_token'] = (isset( $return['X-Authorization'] )) ? $return['X-Authorization'] : '';
        $return['access_token_expires'] = (isset( $return['X-Authorization-Expires'] )) ? $return['X-Authorization-Expires'] : '';
        $wp_user_data = get_userdata( $return['ID'] );
        $return['roles'] = $wp_user_data->roles;

        foreach( $return as $key => $value ) {
            if( is_string( $value ) && $value != '' ) {
                $return[ $key ] = urldecode( $value );
            }
        }

        if( isset( $return['X-Authorization-Expires'] ) && time() > $return['X-Authorization-Expires'] ) {
            unset( $return['cjwpbldr_auth_token'] );
            delete_user_meta( $return['ID'], 'X-Authorization' );
            delete_user_meta( $return['ID'], 'X-Authorization-Expires' );
        }
        unset( $return['X-Authorization'] );
        unset( $return['X-Authorization-Expires'] );

        foreach( array_keys( $return ) as $key ) {
            foreach( $exclude_strings as $string ) {
                if( substr_count( $key, $string ) > 0 ) {
                    unset( $return[ $key ] );
                }
            }
        }

        $fields = [];
        foreach( $this->user_info_fields as $f_key => $f_value ) {
            if( isset( $return[ $f_value ] ) ) {
                $fields[ $f_value ] = $return[ $f_value ];
            }
        }

        if( in_array( 'all', $additional_fields ) ) {
            return $return;
        }

        if( is_array( $additional_fields ) && ! empty( $additional_fields ) ) {
            foreach( $additional_fields as $key => $additional_field_key ) {
                $fields[ $additional_field_key ] = (isset( $return[ $additional_field_key ] )) ? $return[ $additional_field_key ] : '';
            }
        }

        $return = $fields;
        if( is_array( $additional_fields ) && ! in_array( 'access_token', $additional_fields ) ) {
            unset( $return['access_token'] );
        }

        return $return;
    }

    /**
     * @param       $request
     * @param array $fields
     *
     * @return bool
     */
    public function getApiUserInfo( $request, $fields = [] ) {
        $token = $request->get_header( 'x_authorization' );
        if( $token == '' ) {
            $token = $request->get_header( 'x_authorization_token' );
        }
        $fields = array_merge( $fields, ['access_token'] );
        $user_info = $this->getUserByToken( $token, $fields );
        $return = ( ! empty( $user_info )) ? $user_info : false;

        return $return;
    }

    /**
     * @param $request
     * @param $permission
     *
     * @return bool
     */
    public function apiUserCan( $request, $permission ) {
        $token = $request->get_header( 'x_authorization' );
        if( $token == '' ) {
            $token = $request->get_header( 'x_authorization_token' );
        }
        $personal_token = get_option( 'cjwpbldr_api_token', '' );
        if( $token == $personal_token ) {
            return true;
        }
        $user_info = $this->getUserByMeta( 'X-Authorization', $token, 'data' );
        if( ! empty( $user_info ) ) {

            return user_can( $user_info['ID'], $permission );
        }

        return false;
    }

    /**
     * @param $request
     *
     * @return bool
     */
    public function meOrAdmin( $request ) {
        if( $this->apiUserCan( $request, 'manage_options' ) ) {
            return true;
        }
        if( ! empty( $this->getApiUserInfo( $request ) ) ) {
            return true;
        }

        return false;
    }

    /**
     * @param     $user_email
     * @param int $size
     *
     * @return mixed
     */
    public function gravatar( $user_email, $size = 150 ) {
        //$gravatar = get_avatar( $user_email, $size, $this->default_avatar_url);
        $args = get_avatar_data( $user_email, [
            'size' => 150,
            'default' => $this->default_avatar_url,
            'rating' => 'G',
        ] );

        return $args['url'];
        /*preg_match( "/src='(.*?)'/i", $gravatar, $matches );
        $return = ( ! empty( $matches ) && is_array( $matches )) ? $matches[1] : null;

        return $return;*/
    }

    /**
     * @param        $meta_key
     * @param        $meta_value
     * @param string $type
     * @param array  $additional_fields
     *
     * @return array|object|void|null
     */
    public function getUserByMeta( $meta_key, $meta_value, $type = 'meta', $additional_fields = [] ) {
        global $wpdb;
        $user_meta = $wpdb->get_row( "SELECT * FROM $wpdb->usermeta WHERE meta_key = '{$meta_key}' AND meta_value = '{$meta_value}'" );

        if( $type == 'meta' ) {
            return $user_meta;
        }

        if( is_null( $user_meta ) ) {
            return [];
        } else {
            return $this->userInfo( $user_meta->user_id, $additional_fields );
        }
    }

    /**
     * @param array $params
     *
     * @return mixed
     */
    public function getUsers( $params = [] ) {
        if( ! isset( $params['page'] ) ) {
            $params['page'] = 1;
        }
        $params['page'] = ($params['page'] == 0) ? 1 : $params['page'];
        $page = (isset( $params['page'] )) ? $params['page'] : 1;
        $number = get_option( 'posts_per_page' );
        $paged = (get_query_var( 'paged' )) ? get_query_var( 'paged' ) : $page;
        $offset = ($paged - 1) * $number;
        $query_vars = [
            'orderby' => 'registered',
            'order' => 'ASC',
        ];
        $params['query_vars'] = (gettype( $params['query_vars'] ) == 'string') ? json_decode( $params['query_vars'], 1 ) : $params['query_vars'];
        if( isset( $params['query_vars'] ) && is_array( $params['query_vars'] ) && ! empty( $params['query_vars'] ) ) {
            foreach( $params['query_vars'] as $key => $query_var_value ) {
                $query_vars[ $key ] = $query_var_value;
            }
        }
        $query_vars['number'] = $number;
        $query_vars['offset'] = $offset;
        $return['query_vars'] = $query_vars;
        $total_query_vars = $query_vars;
        unset( $total_query_vars['number'] );
        unset( $total_query_vars['offset'] );
        $total_users = get_users( $total_query_vars );
        $total_users_count = count( $total_users );
        $return['total'] = $total_users_count;
        $users = get_users( $query_vars );
        $return['found'] = count( $users );
        $total_pages = ceil( $total_users_count / $number );
        $current_page = (int) $paged;
        $next_page = ($current_page < $total_pages) ? $current_page + 1 : 0;
        $prev_page = ($current_page <= $total_pages && $current_page > 0) ? $current_page - 1 : 0;
        $return['pagination']['total_pages'] = $total_pages;
        $return['pagination']['current_page'] = $current_page;
        $return['pagination']['next_page'] = $next_page;
        $return['pagination']['prev_page'] = $prev_page;
        $return['users'] = [];
        $additional_fields = (isset( $params['fields'] ) && ! empty( $params['fields'] )) ? $params['fields'] : [];
        if( ! empty( $users ) ) {
            $return['users'] = [];
            foreach( $users as $key => $user ) {
                $return['users'][ $key ] = $this->userInfo( $user->ID, $additional_fields, ['access_token'] );
            }
        }
        $links['pagination']['next'] = $this->apiUrl() . 'users?page=' . $next_page;
        $links['pagination']['prev'] = $this->apiUrl() . 'users?page=' . $prev_page;

        return $return;
    }

    /**
     * @param        $meta_key
     * @param string $meta_value
     * @param string $type
     *
     * @return array|object|null
     */
    public function getUsersByMeta( $meta_key, $meta_value = '', $type = 'meta' ) {
        global $wpdb;
        if( $meta_value != '' ) {
            $results = $wpdb->get_results( "SELECT * FROM $wpdb->usermeta WHERE meta_key = '{$meta_key}' AND meta_value = '{$meta_value}'" );
        } else {
            $results = $wpdb->get_results( "SELECT * FROM $wpdb->usermeta WHERE meta_key = '{$meta_key}'" );
        }
        if( $type == 'meta' ) {
            return $results;
        }
        if( ! empty( $results ) ) {
            $return = [];
            foreach( $results as $key => $result ) {
                $return[] = $this->userInfo( $result->user_id );
            }

            return $return;
        } else {
            return [];
        }
    }

    /**
     * @param $user_id
     * @param $data
     *
     * @return array
     */
    public function updateUserInfo( $user_id, $data ) {
        global $wpdb;
        $prefix = (is_multisite()) ? $wpdb->base_prefix : $wpdb->prefix;
        $table_name = $prefix . 'users';
        $user_table_fields = [];
        $user_data = [];
        $exclude_users_table_fields = ['ID', 'user_pass', 'user_login', 'user_pass_confirmation'];
        foreach( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
            $user_table_fields[] = $column_name;
            if( ! in_array( $column_name, $exclude_users_table_fields ) ) {
                if( isset( $data[ $column_name ] ) ) {
                    $user_data[ $column_name ] = $data[ $column_name ];
                }
            }
        }
        if( is_array( $user_data ) ) {
            $user_data['ID'] = $user_id;
            wp_update_user( $user_data );
        }
        foreach( $data as $meta_key => $meta_value ) {
            if( ! in_array( $meta_key, $user_table_fields ) ) {
                update_user_meta( $user_id, $meta_key, $meta_value );
            }
        }

        return $this->userInfo( $user_id );
    }

    /**
     * @param       $user_id
     * @param       $string
     * @param array $user_info
     *
     * @return mixed
     */
    public function processUserVariables( $user_id, $string, $user_info = [] ) {
        if( ! empty( $user_info ) ) {
            $user_info = $this->userInfo( $user_id );
        }
        if( ! empty( $user_info ) ) {
            foreach( $user_info as $key => $value ) {
                $value = (is_serialized( $value )) ? unserialize( $value ) : $value;
                if( ! $this->containsStrArray( $value ) ) {
                    $value = (is_array( $value )) ? implode( '|', $value ) : $value;
                }
                $string = str_replace( '%%' . $key . '%%', $value, $string );
            }
        }
        $string = $this->replaceBetween( $string, '%%', '%%', '' );
        $string = str_replace( '%%', '', $string );

        return $string;
    }

    /**
     * @return array
     */
    public function getRoles() {
        global $wp_roles;
        $role_names = $wp_roles->role_names;
        $custom_roles = [
            'everyone' => __( 'Everyone', 'wp-builder-locale' ),
            'visitor' => __( 'Visitor', 'wp-builder-locale' ),
        ];
        $return = array_merge( $custom_roles, $role_names );

        return $return;
    }

    /**
     * @param        $user_login
     * @param string $separator
     *
     * @return array|string|string[]|null
     */
    public function uniqueUsername( $user_login, $separator = '' ) {
        global $wpdb;
        if( $this->isValidEmail( $user_login ) ) {
            $user_login = explode( '@', $user_login );
            $user_login = $user_login[0];
        }
        $user_login = preg_replace( "/-$/", "", preg_replace( '/[^a-z0-9]+/i', "-", $user_login ) );
        $query = $wpdb->get_row( "SELECT * FROM $wpdb->users WHERE user_login = '{$user_login}'", ARRAY_A );

        if( is_null( $query ) ) {
            return $user_login;
        } else {
            preg_match( '/(.+)-([0-9]+)$/', $user_login, $match );
            $new_user_login = isset( $match[2] ) ? $match[1] . $separator . ($match[2] + 1) : $user_login . $separator . count( $query );
            if( is_null( $query ) ) {
                return $new_user_login;
            } else {
                return $this->uniqueUsername( $new_user_login, $separator );
            }
        }
    }

    /**
     * @param string $key_value
     * @param string $name_value
     *
     * @return array
     */
    public function usersArray( $key_value = 'ID', $name_value = 'display_name' ) {
        $users = get_users( ['orderby' => 'display_name'] );
        $return = [];
        if( is_array( $users ) && ! empty( $users ) ) {
            foreach( $users as $key => $user ) {
                $user_info = $this->userInfo( $user->ID );
                $return[ $user_info[ $key_value ] ] = $user_info[ $name_value ];
            }
        }

        return $return;
    }

    /**
     * @return mixed
     */
    public function userRolesArray() {
        global $wp_roles;
        $roles = $wp_roles->role_names;
        if( is_array( $roles ) && ! empty( $roles ) ) {
            foreach( $roles as $key => $value ) {
                $return[ $key ] = $value;
            }
        }
        $return['everyone'] = __( 'Everyone', 'wp-builder-locale' );
        $return['visitor'] = __( 'Visitor', 'wp-builder-locale' );

        return $return;
    }

}
