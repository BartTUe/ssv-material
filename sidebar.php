<?php
/**
 * The template for the sidebar containing the main widget area
 *
 * @package    Moridrin
 * @subpackage SSV
 * @since      SSV 1.0
 */
?>

<?php if (is_active_sidebar('sidebar')) : ?>
    <aside id="secondary" class="sidebar col s12 <?= is_dynamic_sidebar() ? 'col m4 l3' : '' ?>" role="complementary">
        <div class="widget-area <?= is_admin_bar_showing() ? 'wpadminbar' : '' ?>">
            <?php dynamic_sidebar('sidebar'); ?>
        </div>
    </aside><!-- .sidebar .widget-area -->
<?php endif; ?>
