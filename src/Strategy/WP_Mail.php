<?php
/**
 * Send notifications using WP Mail.
 *
 * @author Iron Bound Designs
 * @since  1.0
 *
 * @copyright   Copyright (c) 2015, Iron Bound Designs, Inc.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License v2 or later
 */

namespace IronBound\WP_Notifications\Strategy;

/**
 * Class WP_Mail
 * @package IronBound\WP_Notifications\Strategy
 */
class WP_Mail implements Strategy {

	/**
	 * Send the notification.
	 *
	 * @since 1.0
	 *
	 * @param \WP_User $recipient
	 * @param string   $message        May contain HTML. Template parts aren't replaced.
	 * @param string   $subject
	 * @param array    $template_parts Array of template parts to their values.
	 *
	 * @return bool
	 *
	 * @throws \Exception
	 */
	public function send( \WP_User $recipient, $message, $subject, array $template_parts ) {
		$message = str_replace( array_keys( $template_parts ), array_values( $template_parts ), $message );
		$subject = str_replace( array_keys( $template_parts ), array_values( $template_parts ), $subject );

		$name        = get_option( 'blogname' );
		$admin_email = get_option( 'admin_email' );

		return wp_mail( $recipient->user_email, $subject, $message, array(
			'Content-Type: text/html; charset=UTF-8',
			"From: $name <$admin_email>"
		) );
	}

	/**
	 * Get the suggested number of times a notification with this strategy can be sent per PHP request.
	 *
	 * @since 1.0
	 *
	 * @return int
	 */
	public function get_suggested_rate() {
		return 10;
	}
}