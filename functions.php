<?php
/**
 * SSV functions and definitions
 *
 * @package    Moridrin
 * @subpackage SSV
 * @since      SSV 1.0
 */

require_once 'general/general.php';
require_once 'inc/template-tags.php';

function mp_ssv_theme_setup()
{
    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-header');
    set_post_thumbnail_size(1920, 480, true);
    add_image_size('ssv-banner-xl', 1920, 480, true);
    add_image_size('ssv-banner-l', 1700, 425, true);
    add_image_size('ssv-banner-m', 1200, 300, true);
    add_image_size('ssv-banner-s', 600, 150, true);
    register_nav_menus(
        array(
            'primary'        => __('Primary Menu', 'ssv'),
            'mobile_primary' => __('Primary Mobile Menu', 'ssv'),
            'mobile_profile' => __('Profile Mobile Menu', 'ssv'),
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
}

add_action('after_setup_theme', 'mp_ssv_theme_setup');

function mp_ssv_custom_image_sizes($sizes)
{
    return array_merge(
        $sizes,
        array(
            'ssv-banner-xl' => __('Banner XL'),
            'ssv-banner-l'  => __('Banner L'),
            'ssv-banner-m'  => __('Banner M'),
            'ssv-banner-s'  => __('Banner S'),
        )
    );
}

add_filter('image_size_names_choose', 'mp_ssv_custom_image_sizes');

function mp_ssv_enquire_scripts()
{
    wp_enqueue_script('materialize', get_theme_root_uri() . '/mp-ssv/js/materialize.js', array('jquery'));
    wp_enqueue_script('materialize_init', get_theme_root_uri() . '/mp-ssv/js/init.js', array('jquery'));
    wp_localize_script(
        'materialize_init',
        'materialize_init',
        array(
            'themeURL' => get_template_directory_uri(),
            'URL' => get_site_url(),
        )
    );
    wp_enqueue_style('materialize', get_theme_root_uri() . '/mp-ssv/css/materialize.css');
    wp_enqueue_style('material_icons', 'https://fonts.googleapis.com/icon?family=Material+Icons');
}

add_action('wp_enqueue_scripts', 'mp_ssv_enquire_scripts');

function mp_special_nav_menu_class($classes, $item, $args)
{
    if (in_array('current-menu-item', $classes) || in_array('current_page_item', $classes) || in_array('current-menu-ancestor', $classes) || in_array('current-menu-parent', $classes)) {
        $classes[] = 'active ';
    }
    if (in_array('menu-item-has-children', $classes) && strpos($args->theme_location, 'mobile') === false) {
        $classes[]   = 'dropdown-button';
        $item->title = $item->title . '<i class="material-icons right">arrow_drop_down</i>';
    }
    $classes[] = 'waves-effect';
    return $classes;
}

add_filter('nav_menu_css_class', 'mp_special_nav_menu_class', 10, 3);

function ssv_widgets_init()
{
    register_sidebar(
        array(
            'name'          => __('Sidebar', 'ssv'),
            'id'            => 'sidebar',
            'description'   => __('Add widgets here to appear in your sidebar.', 'ssv'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );
}

add_action('widgets_init', 'ssv_widgets_init');

function mp_ssv_init_js()
{
}

add_action('wp_loaded', 'mp_ssv_init_js');

/**
 * @param null|WP_Query $query
 *
 * @return string
 */
function mp_ssv_get_pagination()
{
    global $wp_query;
    $pageCount   = $wp_query->max_num_pages;
    $currentPage = (get_query_var('paged')) ? get_query_var('paged') : 1;
    ob_start();
    ?>
    <ul class="pagination right">
        <?php
        if ($currentPage > 1) {
            ?>
            <li class="waves-effect"><a href="?paged=<?= $currentPage - 1 ?>"><i class="material-icons">chevron_left</i></a></li><?php
        } else {
            ?>
            <li class="disabled waves-effect"><i class="material-icons">chevron_left</i></li><?php
        }
        ?>
        <?php
        for ($i = 1; $i <= $pageCount; $i++) {
            if ($i != $currentPage) {
                ?>
                <li class="waves-effect"><a href="?paged=<?= $i ?>"><?= $i ?></a></li><?php
            } else {
                ?>
                <li class="active waves-effect"><span class="non-link"><?= $i ?></span></li><?php
            }
        }
        if ($currentPage < $pageCount) {
            ?>
            <li class="waves-effect"><a href="?paged=<?= $currentPage + 1 ?>"><i class="material-icons">chevron_right</i></a></li><?php
        } else {
            ?>
            <li class="disabled waves-effect"><i class="material-icons">chevron_right</i></li><?php
        }
        ?>
    </ul>
    <?php
    return ob_get_clean();
}
