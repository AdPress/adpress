<?php
// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}

// Array of Blogs Id
$blogs = wp_adpress_mu::list_blogs();
// Execute Command
if (isset($_GET['cmd']) && isset($_GET['blogid']) && isset($_GET['id'])) {
    switch_to_blog((int)$_GET['blogid']);
    wp_adpress_ads::command($_GET['cmd'], (int)$_GET['id']);
    restore_current_blog();
}
?>
<div class="wrap" id="adpress">
    <div id="adpress-icon-runningads" class="icon32"><br></div>
    <h2><?php _e('Running Ads', 'wp-adpress'); ?></h2>
    <?php foreach ($blogs as $blogid) { ?>
    <?php
    switch_to_blog($blogid);
    $ads_running = new wp_adpress_ads_running(true);
    $blog = new wp_adpress_mu($blogid);
    ?>
    <div id="requests-table" style="margin-bottom: 25px;">

        <h3>Blog:  <a href="<?php echo $blog->blogurl; ?>"><?php echo $blog->blogname; ?></a></h3>
        <div class="tablenav top">
            <div class="tablenav-pages">
                <div class="displaying-num">
                    <?php echo $ads_running->count; printf(_n(' Ad', ' Ads', $ads_running->count, 'wp-adpress'));?>
                </div>
            </div>
            <br class="clear"/>
        </div>
        <?php echo $ads_running->view; ?>
    </div>
    <?php } ?>
    <?php
    restore_current_blog();
    ?>
</div>