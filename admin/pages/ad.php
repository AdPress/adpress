<?php
// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}

if (isset($_GET['blogid'])) {
    $blog = (int)$_GET['blogid'];
    switch_to_blog($blog);
    $view = new wp_adpress_adview();
    restore_current_blog();
} else {
    $view = new wp_adpress_adview();
}
?>
<div class="wrap" id="adpress">
    <?php echo $view->view; ?>
</div>