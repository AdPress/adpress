<?php
// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}
if (is_plugin_active_for_network(ADPRESS_BASENAME)) {
    // Page view
    $view = '';
    // Array of Blogs Id
    $blogs = wp_adpress_mu::list_blogs();
    // Old Blog id
    global $blog_id;
    $old_id = $blog_id;
    // Loop
    // TODO: redo all restore_current_blog switches
    foreach ($blogs as $blogid) {
        switch_to_blog($blogid);
        if (count(wp_adpress_campaigns::list_campaigns('active'))) {
            $av_view = new wp_adpress_available(true);
            $blog = new wp_adpress_mu($blogid);
            $view .= '<h3>Blog: <a href="' . $blog->blogurl . '">' . $blog->blogname . '</a></h3>';
            $view .= $av_view->view;
        }
    }
    // Restore the current blog
    switch_to_blog($old_id);
} else {
    $av_view = new wp_adpress_available();
    $view = $av_view->view;
}

?>
<div class="wrap" id="adpress" style="width:600px;">
    <div id="adpress-icon-available" class="icon32"><br></div>
    <h2><?php _e('Available Ads', 'wp-adpress'); ?></h2>
    <?php echo $view; ?>
</div>