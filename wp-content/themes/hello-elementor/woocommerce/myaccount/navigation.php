<?php
/**
 * My Account navigation - Gym Style (fixed)
 *
 * Override path:
 * yourtheme/woocommerce/myaccount/navigation.php
 */
defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_navigation' );

// 使用者資料與統計（未登入 → 0）
$user_id     = is_user_logged_in() ? get_current_user_id() : 0;
$order_count = 0;
$total_spent = 0;

if ( $user_id ) {
	// 防止 WooCommerce 被停用時出錯
	if ( function_exists( 'wc_get_customer_order_count' ) ) {
		$order_count = (int) wc_get_customer_order_count( $user_id );
	}
	if ( function_exists( 'wc_get_customer_total_spent' ) ) {
		$total_spent = (float) wc_get_customer_total_spent( $user_id );
	}
}
?>

<nav class="gym-account-navigation" aria-label="<?php echo esc_attr__( 'Account pages', 'woocommerce' ); ?>">
	<ul class="gym-nav-menu">
		<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
			<?php
			$icon      = '';
			$is_active = wc_is_current_account_menu_item( $endpoint );

			$classes = wc_get_account_menu_item_classes( $endpoint );
			?>
			<li class="gym-nav-item <?php echo esc_attr( $classes ); ?> <?php echo $is_active ? 'gym-nav-active' : ''; ?>">
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"
				   class="gym-nav-link"
				   <?php echo $is_active ? 'aria-current="page"' : ''; ?>>
					<span class="gym-nav-icon"><?php echo esc_html( $icon ); ?></span>
					<span class="gym-nav-label"><?php echo esc_html( $label ); ?></span>
					<?php if ( $is_active ) : ?>
						<span class="gym-nav-indicator"></span>
					<?php endif; ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
