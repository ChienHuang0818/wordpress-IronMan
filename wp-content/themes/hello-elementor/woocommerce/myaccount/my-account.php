<?php
defined( 'ABSPATH' ) || exit;
?>

<section class="gym-account-wrap" aria-labelledby="accountTitle">
  <header class="gym-account-header">
    <h1 id="accountTitle"><?php echo esc_html__( 'My Account', 'woocommerce' ); ?></h1>
    <?php
    $current_user = wp_get_current_user();
    if ( $current_user && $current_user->exists() ) :
    ?>
      <p class="gym-account-greeting">
        <?php
        printf(
          /* translators: %s: display name */
          esc_html__( 'Welcome back, %s!', 'woocommerce' ),
          esc_html( $current_user->display_name )
        );
        ?>
      </p>
    <?php endif; ?>
  </header>

  <div class="gym-account-flex">
    <nav class="gym-account-tabs" aria-label="<?php echo esc_attr__( 'Account navigation', 'woocommerce' ); ?>">
      <?php
      do_action( 'woocommerce_account_navigation' );
      ?>
    </nav>

    <main class="gym-account-content" role="main">
      <?php
      do_action( 'woocommerce_account_content' );
      ?>
    </main>
  </div>
</section>
