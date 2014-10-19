<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
    die( '-1' );
}

echo '
<div class="wrap" id="adpress">
    <h2>' . __( 'Purchase Ad', 'wp-adpress' ) . '</h2>
    <div class="c-block" style="width:600px"> 
        <div class="c-head">
            <h3 id="adpress-icon-request_sent">' . __('Your request is sent', 'wp-adpress') . '</h3>
        </div>
        <p>
        ' . __( 'Thank you for purchasing with <a href="http://wpadpress.com">AdPress.</a>', 'wp-adpress' ) . '
        </p>
        <p>
        <strong>' . __( 'An Administrator may need to check your request before going live.', 'wp-adpress') . '</strong>
        </p>
        <p>
        ' . __('You can now <a href="admin.php?page=adpress-client">purchase more Ads</a> or <a href="admin.php?page=adpress-purchases">check your requests status</a>.', 'wp-adpress') . '
        </p>
    </div>
</div>
';
