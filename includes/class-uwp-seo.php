<?php

defined( 'ABSPATH' ) || exit;

class UsersWP_Seo {

    public function __construct() {

    }

    public function profile_options($settings) {

       $settings[] = array(
           'title' => __( 'Profile SEO', 'userswp' ),
           'type'  => 'title',
           'id'    => 'profile_seo_options',
           'advanced'  => true,
       );

        $settings[] = array(
            'id' => 'profile_seo_meta_separator',
            'name' => __( 'Title separator', 'userswp' ),
            'type' => 'radio',
            'default' => self::get_default_sep(),
            'class' => 'uwp-seo-meta-separator',
            'desc' 	=> __('Choose the symbol to use as your title separator. This will display, for instance, between your user profile title and site name. Symbols are shown in the size they will appear in the search results.', 'uwp-groups'),
            'desc_tip' => true,
            'advanced'  => false,
            'placeholder' => '',
            'options' => array(
                '-' => '-',
                '|' => '|',
                '>' => '>',
                '<' => '<',
                '~' => '~',
                ':' => ':',
                '*' => '*',
            ),
        );

        $settings[] = array(
            'id' => 'profile_seo_meta_title',
            'name' => __( 'Meta Title', 'userswp' ),
            'type' => 'text',
            'default' => '',
            'class' => 'large-text',
            'desc' 	=> __('Available SEO tags:', 'uwp-groups') . ' '.self::get_seo_tags(true),
            'desc_tip' => false,
            'advanced'  => false,
            'placeholder' => $this->get_default_meta_title(),
        );

        $settings[] = array(
            'id' => 'profile_seo_meta_description',
            'name' => __( 'Meta Description', 'userswp' ),
            'type' => 'textarea',
            'default' => '',
            'desc' 	=> __( 'Enter the meta description to use for the page.', 'userswp' ),
            'desc_tip' => true,
            'advanced'  => false,
            'placeholder' => $this->get_default_meta_description(),
            'custom_desc' => __('Available SEO tags:', 'uwp-groups') . ' '.self::get_seo_tags(true),
        );

       $settings[] = array( 'type' => 'sectionend', 'id' => 'profile_seo_options' );

        return $settings;
    }

    public static function get_default_sep() {
        return '|';
    }

    public static function get_seo_tags( $inline = true ) {

        $tags = array(
            '[#site_name#]',
            '[#user_name#]',
            '[#display_name#]',
            '[#first_name#]',
            '[#last_name#]',
            '[#email#]',
            '[#user_bio#]',
            '[#sep#]',
        );

        $tags = apply_filters('uwp_seo_tags',$tags);

        if(!$inline) {
            return  $tags;
        }

        return '<code>' . implode( '</code> <code>', $tags ) . '</code>';
    }

    public function get_default_meta_title() {
        return '[#site_name#] [#sep#] [#user_name#]';
    }

    public function get_default_meta_description() {
        return '[#user_bio#]';
    }

    public function replace_tags($string) {

        if(is_uwp_profile_page()) {
            $displayed_user = uwp_get_displayed_user();
            $displayed_user_id = !empty($displayed_user->ID) ? $displayed_user->ID : 0;

            $site_name = get_bloginfo('name');

            $first_name = !empty($displayed_user->first_name) ? $displayed_user->first_name :'';
            $last_name = !empty($displayed_user->last_name) ? $displayed_user->last_name :'';
            $user_name = !empty($displayed_user->user_login) ? $displayed_user->user_login :'';
            $display_name = !empty($displayed_user->display_name) ? $displayed_user->display_name :'';
            $user_email = !empty($displayed_user->user_email) ? $displayed_user->user_email :'';

            $user_bio = get_user_meta($displayed_user_id, 'description', true);

            $meta_separator = uwp_get_option('profile_seo_meta_separator');
            $sep = !empty($meta_separator) ? $meta_separator : self::get_default_sep();

            $string = str_replace('[#site_name#]', $site_name, $string);

            $string = str_replace('[#user_name#]', $user_name, $string);
            $string = str_replace('[#display_name#]', $display_name, $string);
            $string = str_replace('[#first_name#]', $first_name, $string);
            $string = str_replace('[#last_name#]', $last_name, $string);
            $string = str_replace('[#email#]', $user_email, $string);

            $string = str_replace('[#user_bio#]', $user_bio, $string);
            $string = str_replace('[#sep#]', $sep, $string);
        }

        return $string;
    }

    public function get_meta_title() {
        $meta_title = uwp_get_option('profile_seo_meta_title');
        $meta_title = !empty($meta_title) ? $meta_title : $this->get_default_meta_title();
        $meta_title = $this->replace_tags($meta_title);

        return $meta_title;
    }

    public function get_meta_description() {

        $meta_description = uwp_get_option('profile_seo_meta_description');
        $meta_description = !empty($meta_description) ? $meta_description : $this->get_default_meta_description();
        $meta_description = $this->replace_tags($meta_description);

        return $meta_description;
    }

    public function output_title($title) {

        if(is_uwp_profile_page()) {
            $title = $this->get_meta_title();
        }

        return $title;
    }

    public function output_description() {

        if(is_uwp_profile_page()) {
            $description = $this->get_meta_description();
            echo '<meta name="description" content="' . $description . '" />';
        }
    }

    public static function has_yoast() {
        return defined( 'WPSEO_VERSION' );
    }

    public static function has_yoast_14() {
        return ( self::has_yoast() && version_compare( WPSEO_VERSION, '14.0', '>=' ) );
    }

    public function get_title($title) {
        if(is_uwp_profile_page()) {
            $title = $this->get_meta_title();
        }

        return $title;
    }

    public function get_description($description) {
        if(is_uwp_profile_page()) {
            $description = $this->get_meta_description();
        }

        return $description;
    }

    public function get_opengraph_url($url) {
        if(is_uwp_profile_page()) {
            $displayed_user = uwp_get_displayed_user();
            $displayed_user_id = !empty($displayed_user->ID) ? $displayed_user->ID : 0;
            $url = uwp_build_profile_tab_url($displayed_user_id);
        }

        return $url;
    }
}
