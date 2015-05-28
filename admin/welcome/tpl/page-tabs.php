<?php
$selected = isset( $_GET['page'] ) ? $_GET['page'] : 'wpadpress-about';
?>
		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php echo $selected == 'wpadpress-new' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wpadpress-new' ), 'index.php' ) ) ); ?>">
				<?php _e( "What's New", 'wp-adpress' ); ?>
			</a>
			<a class="nav-tab <?php echo $selected == 'wpadpress-start' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wpadpress-start' ), 'index.php' ) ) ); ?>">
				<?php _e( 'Getting Started', 'wp-adpress' ); ?>
			</a>
			<a class="nav-tab <?php echo $selected == 'wpadpress-addons' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wpadpress-addons' ), 'index.php' ) ) ); ?>">
				<?php _e( 'Addons', 'wp-adpress' ); ?>
			</a>
			<a class="nav-tab <?php echo $selected == 'wpadpress-about' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wpadpress-about' ), 'index.php' ) ) ); ?>">
				<?php _e( 'About AdPress', 'wp-adpress' ); ?>
			</a>
		</h2>
