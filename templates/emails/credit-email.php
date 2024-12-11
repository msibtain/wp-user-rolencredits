<?php
/**
 * Payment Success Email.
 *
 * This template can be overridden by copying it to yourtheme/credits-for-woocommerce/emails/payment-success.php.
 */
defined( 'ABSPATH' ) || exit;
?>

<?php
/**
 * Add email header.
 * 
 * @since 1.0
 */
do_action( 'woocommerce_email_header', $email_heading, $email );
?>

<p>
	<?php
	/* translators: 1: user name 2: credited amount 3: blog name 4: credited date 5: balance amount */
	printf( wp_kses_post( __( 'Hi %1$s, <br>Your credit limit bill payment of <code>%2$s</code> on %3$s is successful. Your credit balance at <code>%4$s</code> is <code>%5$s</code>.', 'credits-for-woocommerce' ) ), esc_html( $custom_data['user_nicename'] ), $custom_data['new_credit'], esc_html( $custom_data['blogname'] ), $custom_data['date_created'], wp_kses_post( $custom_data['total_credit'] ) )
	?>
</p>

<p><?php esc_html_e( 'Thanks', 'credits-for-woocommerce' ); ?></p>

<?php
/**
 * Add email footer.
 * 
 * @since 1.0
 */
do_action( 'woocommerce_email_footer', $email );
?>
