<?php

use mp_ssv_general\base\BaseFunctions;

require_once 'inc/template-tags.php';
require_once 'cards-text-widget.php';
require_once 'birthday-widget.php';

if (!isset($content_width)) {
    $content_width = 1700;
}
add_editor_style('css/' . get_current_blog_id() . '_materialize.css');

function mp_ssv_theme_setup()
{
    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    $args = array(
        'default-image' => get_template_directory_uri() . '/images/banner.jpg',
        'width'         => 2048,
        'height'        => 1000,
        'flex-width'    => true,
        'flex-height'   => true,
    );
    add_theme_support('custom-header', $args);
    set_post_thumbnail_size(1920, 480, true);
    add_image_size('ssv-banner-xl', 1920, 480, true);
    add_image_size('ssv-banner-l', 1700, 425, true);
    add_image_size('ssv-banner-m', 1200, 300, true);
    add_image_size('ssv-banner-s', 600, 150, true);
    register_nav_menus(
        array(
            'primary'        => __('Primary Menu', 'mp-ssv'),
            'mobile_primary' => __('Primary Mobile Menu', 'mp-ssv'),
            'mobile_profile' => __('Profile Mobile Menu', 'mp-ssv'),
        )
    );
    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        )
    );
    add_theme_support('tabs');
    add_theme_support('materialize');
    add_theme_support('ssv-material');
}

add_action('after_setup_theme', 'mp_ssv_theme_setup');

function mp_ssv_custom_image_sizes($sizes)
{
    return array_merge(
        $sizes,
        array(
            'ssv-banner-xl' => 'Banner XL',
            'ssv-banner-l'  => 'Banner L',
            'ssv-banner-m'  => 'Banner M',
            'ssv-banner-s'  => 'Banner S',
        )
    );
}

add_filter('image_size_names_choose', 'mp_ssv_custom_image_sizes');

function mp_ssv_enqueue_scripts()
{
    wp_enqueue_script('materialize', get_theme_root_uri() . '/ssv-material/js/materialize.js', array('jquery'));
    if (is_customize_preview()) {
        //Uses Generated CSS
    } else {
        if(file_exists(__DIR__ . '/css/' . get_current_blog_id() . '_materialize.css')) {
            wp_enqueue_style('materialize', get_theme_root_uri() . '/ssv-material/css/' . get_current_blog_id() . '_materialize.css');
        } else {
            wp_enqueue_style('materialize', get_theme_root_uri() . '/ssv-material/css/materialize.css');
        }
    }
    wp_enqueue_style('material_icons', 'https://fonts.googleapis.com/icon?family=Material+Icons');
    if (is_404()) {
        wp_enqueue_script('bb8', get_theme_root_uri() . '/ssv-material/js/BB8.js', array('jquery'));
        wp_enqueue_style('bb8', get_theme_root_uri() . '/ssv-material/css/BB8.css');
    } else {
        wp_enqueue_script('material_boxed_link', get_theme_root_uri() . '/ssv-material/js/material-boxed-link.js', array('jquery'));
        wp_enqueue_script('materialize_init', get_theme_root_uri() . '/ssv-material/js/init.js', array('materialize', 'jquery'), '1.1');
        wp_localize_script('materialize_init', 'theme_vars', ['slider_interval' => get_theme_mod('slider_interval', 6000)]);
    }
}

add_action('wp_enqueue_scripts', 'mp_ssv_enqueue_scripts');

function mp_special_nav_menu_class($classes, $item, $args)
{
    if (in_array('current-menu-item', $classes) || in_array('current_page_item', $classes) || in_array('current-menu-ancestor', $classes) || in_array('current-menu-parent', $classes)) {
        $classes[] = 'menu-item-active ';
    }
    if (in_array('menu-item-has-children', $classes) && strpos($args->theme_location, 'mobile') === false) {
        $classes[]   = 'dropdown-button';
        $item->title = $item->title . '<i class="material-icons right">arrow_drop_down</i>';
    }
    $classes[] = 'waves-effect';
    return $classes;
}

add_filter('nav_menu_css_class', 'mp_special_nav_menu_class', 10, 3);

function mp_ssv_widgets_init()
{
    register_sidebar(
        array(
            'name'          => __('Sidebar', 'mp-ssv'),
            'id'            => 'sidebar',
            'description'   => __('Add widgets here to appear in your sidebar.', 'mp-ssv'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );
}

add_action('widgets_init', 'mp_ssv_widgets_init');

function mp_ssv_init_js()
{
}

add_action('wp_loaded', 'mp_ssv_init_js');

/**
 * @return string
 * @internal param null|WP_Query $query
 */
function mp_ssv_get_pagination()
{
    global $wp_query;
    $pageCount   = $wp_query->max_num_pages;
    $currentPage = (get_query_var('paged')) ? get_query_var('paged') : 1;
    paginate_links(); //TODO this is not used due to the custom styling of the pagination links.
    ob_start();
    ?>
    <ul class="pagination right">
        <?php
        if ($currentPage > 1) {
            ?>
            <li class="waves-effect"><a href="?paged=<?php echo esc_html($currentPage - 1) ?>"><i class="material-icons">chevron_left</i></a></li><?php
        } else {
            ?>
            <li class="disabled waves-effect"><i class="material-icons">chevron_left</i></li><?php
        }
        ?>
        <?php
        for ($i = 1; $i <= $pageCount; $i++) {
            if ($i != $currentPage) {
                ?>
                <li class="waves-effect"><a href="?paged=<?php echo esc_html($i) ?>"><?php echo esc_html($i) ?></a></li><?php
            } else {
                ?>
                <li class="active waves-effect"><span class="non-link"><?php echo esc_html($i) ?></span></li><?php
            }
        }
        if ($currentPage < $pageCount) {
            ?>
            <li class="waves-effect"><a href="?paged=<?php echo esc_html($currentPage + 1) ?>"><i class="material-icons">chevron_right</i></a></li><?php
        } else {
            ?>
            <li class="disabled waves-effect"><i class="material-icons">chevron_right</i></li><?php
        }
        ?>
    </ul>
    <?php
    return ob_get_clean();
}

function mp_ssv_customize_register($wp_customize)
{
    /** @var WP_Customize_Manager $wp_customize */
//    $wp_customize->add_section(
//        'mp_ssv',
//        array(
//            'title' => 'SSV',
//        )
//    );
    $wp_customize->add_setting(
        'icon_large',
        array(
            'sanitize_callback' => 'sanitize_url',
        )
    );
    $wp_customize->add_control(
        new WP_Customize_Cropped_Image_Control(
            $wp_customize,
            'icon_large',
            array(
                'label'       => 'Large Icon',
                'section'     => 'title_tagline',
                'flex_width'  => true,
                'flex_height' => true,
                'width'       => 600,
                'height'      => 292,
            )
        )
    );
    $wp_customize->add_setting(
        'navbar_logo'
    );
    $wp_customize->add_control(
        new WP_Customize_Image_Control(
            $wp_customize,
            'navbar_logo',
            array(
                'label'       => 'Navbar Logo',
                'section'     => 'title_tagline',
            )
        )
    );
    $wp_customize->add_setting(
        'welcome_message',
        array(
            'default' => '<h3>About the SSV Library</h3><p>The SSV Library started with the website for <a href="https://allterrain.nl/">All Terrain</a> for which a lot of functionality was needed in a format that would be easy enough for everyone to work with.</p>',
        )
    );
    $wp_customize->add_control(
        'welcome_message',
        array(
            'label'   => 'Welcome Message',
            'section' => 'title_tagline',
            'type'    => 'textarea',
        )
    );
    $wp_customize->add_setting(
        'footer_main',
        array(
            'default' => '<h3>About the SSV Library</h3><p>The SSV Library started with the website for <a href="https://allterrain.nl/">All Terrain</a> for which a lot of functionality was needed in a format that would be easy enough for everyone to work with.</p>',
        )
    );
    $wp_customize->add_control(
        'footer_main',
        array(
            'label'   => 'Footer Main',
            'section' => 'title_tagline',
            'type'    => 'textarea',
        )
    );
    $wp_customize->add_setting(
        'foorer_right',
        array(
            'default' => '<h3>Partners</h3><ul><li><a class="grey-text text-lighten-3 customize-unpreviewable" href="https://allterrain.nl/">All Terrain</a></li><li><a class="grey-text text-lighten-3 customize-unpreviewable" href="http://www.eshdavinci.nl">ESH Da Vinci</a></li><li><a class="grey-text text-lighten-3 customize-unpreviewable" href="https://www.facebook.com/survivalruneindhoven/">Survivalrun Eindhoven</a></li></ul>',
        )
    );
    $wp_customize->add_control(
        'foorer_right',
        array(
            'label'   => 'Footer Right',
            'section' => 'title_tagline',
            'type'    => 'textarea',
        )
    );
    $wp_customize->add_section( 'homepage_buttons' , array(
        'title'      => __( 'Homepage Buttons', 'ssv-material' ),
        'priority'   => 30,
    ));
    for ($i = 0; $i < 4; $i++) {
        $wp_customize->add_setting(
            'home_button_'.$i.'_enabled',
            array(
                'default' => false
            )
        );
        $wp_customize->add_control(
            'home_button_'.$i.'_enabled',
            array(
                'label'    => __( 'Enable homepage button ' . $i, 'ssv-material' ),
                'section'  => 'homepage_buttons',
                'settings' => 'home_button_'.$i.'_enabled',
                'type'     => 'checkbox',
            )
        );
        $wp_customize->add_setting(
            'home_button_' . $i . '_image'
        );
        $wp_customize->add_control(
            new WP_Customize_Cropped_Image_Control(
                $wp_customize,
                'home_button_' . $i . '_image',
                array(
                    'label'       => 'Homepage button #' . $i . ' image',
                    'section'     => 'homepage_buttons',
                    'flex_width'  => true,
                    'flex_height' => true,
                    'width'       => 485,
                    'height'      => 325,
                )
            )
        );
        $wp_customize->add_setting(
            'home_button_'.$i.'_title',
            array(
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => '',
            )
        );
        $wp_customize->add_control(
            'home_button_'.$i.'_title',
            array(
                'label'   => 'Homepage button #' . $i . ' title',
                'section' => 'homepage_buttons',
                'type'    => 'text',
            )
        );
        $wp_customize->add_setting(
            'home_button_'.$i.'_url',
            array(
                'sanitize_callback' => 'sanitize_url',
                'default' => '#'
            )
        );
        $wp_customize->add_control(
            'home_button_'.$i.'_url',
            array(
                'label'   => 'Homepage button #' . $i . ' url',
                'section' => 'homepage_buttons',
                'type'    => 'text',
            )
        );
        $wp_customize->add_setting(
            'slider_interval',
            array(
                'default' => '6000',
            )
        );
        $wp_customize->add_control(
            'slider_interval',
            array(
                'label'   => 'Slider interval',
                'section' => 'header_image',
                'type'    => 'number',
            )
        );

        $wp_customize->add_setting(
            'logo_on_home',
            array(
                'default' => 0,
            )
        );
        $wp_customize->add_control(
            'logo_on_home',
            array(
                'label'   => 'Show logo on Homepage',
                'section' => 'title_tagline',
                'type'    => 'checkbox',
            )
        );

        $wp_customize->add_setting(
            'site_title_position',
            array(
                'default' => 'under_header',
            )
        );
        $wp_customize->add_control(
            'site_title_position',
            array(
                'label'   => 'Site title position on Homepage',
                'section' => 'title_tagline',
                'type'    => 'select',
                'choices' => [
                        'on_header' => 'On header images',
                        'under_header' => 'Under header images',
                ]
            )
        );

        $wp_customize->add_setting(
            'slider_height',
            array(
                'default' => '450',
            )
        );
        $wp_customize->add_control(
            'slider_height',
            array(
                'label'   => 'Slider height on Homepage',
                'section' => 'header_image',
                'type'    => 'number',
            )
        );

        $wp_customize->add_setting(
            'slider_height_archives',
            array(
                'default' => '0',
            )
        );
        $wp_customize->add_control(
            'slider_height_archives',
            array(
                'label'   => 'Slider height on Archive pages',
                'description' => 'Set to 0 to use the default header instead of the slider on archives.',
                'section' => 'header_image',
                'type'    => 'number',
            )
        );

        $wp_customize->add_setting(
            'slider_overlay_color',
            array(
                'default' => 'black',
            )
        );
        $wp_customize->add_control(
            'slider_overlay_color',
            array(
                'label'   => 'Slider Overlay Color',
                'description' => 'The slider has a semy transparent overlay. The default color is black.',
                'section' => 'header_image',
                'type'    => 'select',
                'choices' => [
                        'black' => 'Black',
                        'primary' => 'Primary Color',
                        'accent' => 'Accent Color',
                ]
            )
        );

        $wp_customize->add_setting(
            'header_height',
            array(
                'default' => '250',
            )
        );
        $wp_customize->add_control(
            'header_height',
            array(
                'label'   => 'Default Header Image Height',
                'section' => 'header_image',
                'type'    => 'number',
            )
        );
    }

    mp_ssv_add_color_customizer($wp_customize, 'primary_color', 'Primary Color', '#005E38');
    mp_ssv_add_color_customizer($wp_customize, 'text_on_primary_color', 'Text On Primary Color', '#FFFFFF');
    mp_ssv_add_color_customizer($wp_customize, 'secondary_color', 'Secondary Color', '#26A69A');
    mp_ssv_add_color_customizer($wp_customize, 'text_on_secondary_color', 'Text On Secondary Color', '#FFFFFF');
    mp_ssv_add_color_customizer($wp_customize, 'link_color', 'Link Color', '#039BE5');
    mp_ssv_add_color_customizer($wp_customize, 'success_color', 'Success Color', '#4CAF50');
    mp_ssv_add_color_customizer($wp_customize, 'error_color', 'Error Color', '#F44336');
}

function mp_ssv_add_color_customizer($wp_customize, $name, $label, $default)
{
    /** @var WP_Customize_Manager $wp_customize */
    $wp_customize->add_setting(
        $name,
        array(
            'sanitize_callback' => 'sanitize_hex_color',
            'default'           => $default,
        )
    );
    $wp_customize->add_control(
        $name,
        array(
            'label'   => $label,
            'section' => 'colors',
            'type'    => 'color',
        )
    );
}

add_action('customize_register', 'mp_ssv_customize_register');

function mp_ssv_customize_preview_css()
{
    if (is_customize_preview()) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        if (!defined('FS_METHOD')) {
            define('FS_METHOD', 'direct');
        }
        require_once "compiling-source/scssphp/scss.inc.php";
        $scss = new \Leafo\ScssPhp\Compiler();
        $scss->setVariables(
            array(
                'header-text-color'       => '#' . get_theme_mod('header_textcolor', '#212121'),
                'primary-color'           => get_theme_mod('primary_color', '#005E38'),
                'text-on-primary-color'   => get_theme_mod('text_on_primary_color', '#FFFFFF'),
                'secondary-color'         => get_theme_mod('secondary_color', '#26A69A'),
                'text-on-secondary-color' => get_theme_mod('text_on_secondary_color', '#FFFFFF'),
                'link-color'              => get_theme_mod('link_color', '#039BE5'),
                'success-color'           => get_theme_mod('success_color', '#4CAF50'),
                'error-color'             => get_theme_mod('error_color', '#F44336'),
                'roboto-font-path'        => '/wp-content/themes/ssv-material/fonts/roboto/',
            )
        );
        $css = $scss->compile('@import "' . get_theme_file_path() . '/css/materialize"');
        echo '<style id="moridrin">';
        echo $css;
        echo '</style>';
    }
}

add_action('wp_head', 'mp_ssv_customize_preview_css');

/**
 * @throws Exception
 */
function mp_ssv_customize_save_css()
{
    if (!defined('FS_METHOD')) {
        define('FS_METHOD', 'direct');
    }
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once "compiling-source/scssphp/scss.inc.php";
    $scss = new \Leafo\ScssPhp\Compiler();
    $scss->setVariables(
        array(
            'header-text-color'       => '#' . get_theme_mod('header_textcolor', '#212121'),
            'primary-color'           => get_theme_mod('primary_color', '#005E38'),
            'text-on-primary-color'   => get_theme_mod('text_on_primary_color', '#FFFFFF'),
            'secondary-color'         => get_theme_mod('secondary_color', '#26A69A'),
            'text-on-secondary-color' => get_theme_mod('text_on_secondary_color', '#FFFFFF'),
            'link-color'              => get_theme_mod('link_color', '#039BE5'),
            'success-color'           => get_theme_mod('success_color', '#4CAF50'),
            'error-color'             => get_theme_mod('error_color', '#F44336'),
        )
    );
    $compiled = $scss->compile('@import "' . get_theme_file_path() . '/css/materialize"');

    WP_Filesystem();
    /** @var WP_Filesystem_Direct $wp_filesystem */
    global $wp_filesystem;
    $success = $wp_filesystem->put_contents(get_theme_file_path() . '/css/' . get_current_blog_id() . '_materialize.css', $compiled, FS_CHMOD_FILE);
    if (!$success) {
        throw new Exception('Could not save the css files.');
    }

    $jsonData = array(
        "short_name" => get_bloginfo(),
        "name"       => get_bloginfo('description'),
        "start_url"  => "/",
        "display"    => "standalone",
    );
    $success = $wp_filesystem->put_contents(get_theme_file_path() . '/manifest.json', json_encode($jsonData), FS_CHMOD_FILE);
    if (!$success) {
        throw new Exception('Could not save the manifest.');
    }
}

add_action('customize_save_after', 'mp_ssv_customize_save_css');

function mp_ssv_email_antispam($content)
{
    preg_match_all('/<([^<>]+)?[\"\'\?]((?:mailto:?:)?)([A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4})[\"\'\?]([^<>]+)?>/i', $content, $matches);
    foreach ($matches[0] as $key => $found) {
        $tags = explode(' ', $matches[1][$key]);
        $tag = str_replace('=', '', end($tags));
        $mailto = $matches[2][$key];
        $email = $matches[3][$key];
        $emailSplit = explode('@', $email);
        $antiSpamTags = str_replace('>', 'data-before-at="'.$emailSplit[0].'" data-after-at="'.$emailSplit[1].'" data-mailto="'.$mailto.'" data-anti-spam-tag="'.$tag.'">', $found);
        $antiSpam = str_replace($email, '[anti-spam-tag]', $antiSpamTags);
        $content = str_replace($found, $antiSpam, $content);
    }
    preg_match_all('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i', $content, $matches);
    foreach ($matches[0] as $key => $found) {
        $emailSplit = explode('@', $found);
        $antiSpam = '<span data-before-at="'.$emailSplit[0].'" data-after-at="'.$emailSplit[1].'">[anti-spam-tag]</spam>';
        $content = str_replace($found, $antiSpam, $content);
    }
    return $content;
}
add_action('the_content', 'mp_ssv_email_antispam', 100);


function custom_table_reset_password_mail_html($message, $key, $user_login, $user_data)
{
    $url = network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'https');

    ob_start();

?>

    <table style="width: 80%; margin: auto; border-collapse: collapse; border: 2px solid #005e38">
        <tbody>
            <tr>
                <td style="padding: 0;">

                    <table style="height: 64px; background-color: #005e38; width: 100%; border: none;">
                        <tbody>
                            <tr>
                                <td style="paddign: 0;">
                                    <img src="http://allterrain.nl/wp-content/themes/ssv-material/images/logo.svg" title="All Terrain" alt="All Terrain" style="height: 56px; width: 115px; margin-left: 20px; display: block;">
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <table style="margin-top: 1em; padding: 1em; margin-left: auto; margin-right: auto;">
                        <tbody>
                            <tr>
                                <td>
                                    <p style="width: 100%;">
                                        Someone has requested a password reset for the following account:<br><br>
                                        <?php echo(sprintf('<b>Username:</b> %s', $user_login) . '<br>') ?>
                                        <b>Website:</b> <a href="<?php echo(network_home_url('/'));?>"> <?php echo(network_home_url( '/' ));?> </a><br><br>
                                        If this was a mistake, just ignore this email and nothing will happen.<br><br>
                                        To reset your password, click the button below.<br><br>

                                        <table style="margin: auto;">
                                            <tbody>
                                                <tr>
                                                    <td style="background-color: #005E38; border: 1px none; border-radius: 5px; display: flex; align-items: center; padding: 0;">
                                                        <a href="<?php echo($url);?>" style="font-weight: bold; letter-spacing: normal; line-height: 100%; text-align: center; text-decoration: none; color: #FFFFFF; display: block; margin: 0; padding: 1em; height: 100%; width: 100%;">Reset Password</a><br>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="height: 2em;"> </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        If the button doesn't work, visit the url below via your browser:<br>
                                        <a href="<?php echo($url);?>"><?php echo($url);?></a><br>
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </td>
            </tr>
        </tbody>
    </table>
    
<?php

    return ob_get_clean();
}




add_filter('retrieve_password_message', 'custom_table_reset_password_mail_html', 10, 4);
