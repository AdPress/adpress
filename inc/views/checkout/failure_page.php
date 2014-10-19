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
            <h3 id="adpress-icon-request_sent">' . __('There was a problem', 'wp-adpress') . '</h3>
        </div>
        <p>
        ' . __( 'There was an issue while processing your purchase', 'wp-adpress' ) . '
        </p>
    </div>
</div>
';
