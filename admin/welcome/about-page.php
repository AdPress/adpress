			<div class="changelog">
				<h3><?php _e( 'Improved Order Editing', 'edd' );?></h3>

				<div class="feature-section">

					<img src="<?php echo EDD_PLUGIN_URL . 'assets/images/screenshots/order-details.png'; ?>" class="edd-welcome-screenshots"/>

					<h4><?php _e( 'Combined View and Edit Screens', 'edd' );?></h4>
					<p><?php _e( 'The View and Edit payment screens have been combined into a single, more efficient, user-friendly screen. Add or remove products to an order, adjust amounts, add notes, or resend purchase receipts all at one time from the same screen.', 'edd' );?></p>
					<p><?php _e( 'All data associated with a payment can now be edited as well, including the customer\'s billing address.', 'edd' );?></p>

					<h4><?php _e( 'Responsive and Mobile Friendly', 'edd' );?></h4>
					<p><?php _e( 'We have followed the introduction of a responsive Dashboard in WordPress 3.8 and made our own view/edit screen for orders fully responsive and easy to use on mobile devices.', 'edd' );?></p>

				</div>
			</div>

			<div class="changelog">
				<h3><?php _e( 'Per-Products Sales and Earnings Graphs', 'edd' );?></h3>

				<div class="feature-section">

					<img src="<?php echo EDD_PLUGIN_URL . 'assets/images/screenshots/product-earnings.png'; ?>" class="edd-welcome-screenshots"/>

					<h4><?php _e( 'See Earnings and Sales for Any Time Period','edd' );?></h4>
					<p><?php _e( 'With 1.9 we have introduced beautiful graphs for individual products that allows you to view earnings and sales over any time period. Easily see earnings / sales for monthly, yearly, quarterly, or any other date range for any product in your store.', 'edd' );?></p>

					<h4><?php _e( 'Easily Access Reports', 'edd' );?></h4>
					<p><?php printf( __( 'Per-product earnings / sales graphs can be accessed from <em>%s &rarr; Reports &rarr; %s</em> or from the <em>Stats</em> section of the Edit screen for any %s.', 'edd' ), edd_get_label_plural(), edd_get_label_plural(), edd_get_label_singular() );?></p>


				</div>
			</div>

			<div class="changelog">
				<h3><?php _e( 'Dramatically Improved Taxes', 'edd' );?></h3>

				<div class="feature-section">

					<img src="<?php echo EDD_PLUGIN_URL . 'assets/images/screenshots/product-tax.png'; ?>" class="edd-welcome-screenshots"/>

					<h4><?php _e( 'Mark Products Exclusive of Tax', 'edd' );?></h4>
					<p><?php _e( 'Products in your store can now be marked as exclusive of tax, meaning customers will never have to pay tax on these products during checkout.', 'edd' );?></p>

					<h4><?php _e( 'Re-written Tax API', 'edd' );?></h4>
					<p><?php _e( 'The tax system in EDD has been plagued with bugs since it was first introduced, so in 1.9 we have completely rewritten the entire system from the ground up to ensure it is reliable and bug free.', 'edd' );?></p>
					<p><?php _e( 'It can be difficult to completely delete an entire section of old code, but we are confident the rewrite will be worth every minute of the time spent on it.', 'edd' );?></p>
					<p><?php _e( 'We are determined to continue to provide you a reliable, easy system to sell your digital products. In order to do that, sometimes we just have to swallow our pride and start over.', 'edd' );?></p>

				</div>
			</div>

			<div class="changelog">
				<h3><?php _e( 'Better Support for Large Stores', 'edd' );?></h3>

				<div class="feature-section">

					<h4><?php _e( 'Live Search Product Drop Downs','edd' );?></h4>
					<p><?php _e( 'Every product drop down menu used in Easy Digital Downloads has been replaced with a much more performant version that includes a live Ajax search, meaning stores that have a large number of products will see a significant improvement for page load times in the WordPress Dashboard.', 'edd' );?></p>

					<h4><?php _e( 'Less Memory Intensive Log Pages', 'edd' ); ?></h4>
					<p><?php _e( 'The File Download log pages have long been memory intensive to load. By putting them through intensive memory load tests and query optimization, we were able to reduce the number of queries and amount of memory used by a huge degree, making these pages much, much faster..', 'edd' ); ?></p>

				</div>
			</div>

			<div class="changelog">
				<h3><?php _e( 'Additional Updates', 'edd' );?></h3>

				<div class="feature-section col three-col">
					<div>
						<h4><?php _e( 'Improved Product Creation / Editing', 'edd' );?></h4>
						<p><?php _e( 'The interface for creating / editing Download products has been dramatically improved by separating the UI out into sections that are easier to use and less cluttered.', 'edd' );?></p>

						<h4><?php _e( 'EDD_Graph Class', 'edd' );?></h4>
						<p><?php _e( 'Along with per-product earnings / sales graphs, we have introduced an EDD_Graph class that makes it exceptionally simple to generate your own custom graphs. Simply build an array of data and let the class work its magic.', 'edd' );?></p>
					</div>

					<div>
						<h4><?php _e( 'Payment Date Filters', 'edd' );?></h4>
						<p><?php _e( 'A new section has been added to the Payment History screen that allows you to filter payments by date, making it much easier to locate payments for a particular period.', 'edd' );?></p>

						<h4><?php _e( 'EDD_Email_Template_Tags Class', 'edd' );?></h4>
						<p><?php _e( 'A new API has been introduced for easily adding new template tags to purchase receipts and admin sale notifications.', 'edd' );?></p>
					</div>

					<div class="last-feature">
						<h4><?php _e( 'Resend Purchase Receipts in Bulk', 'edd' );?></h4>
						<p><?php _e( 'A new action has been added to the Bulk Actions menu in the Payment History screen that allows you to resend purchase receipt emails in bulk.' ,'edd' );?></p>

						<h4><?php _e( 'Exclude Products from Discounts','edd' );?></h4>
						<p><?php _e( 'Along with being able to assign discounts to specific products, you can also now exclude products from discount codes.', 'edd' );?></p>
					</div>
				</div>
			</div>
