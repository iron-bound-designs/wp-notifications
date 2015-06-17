<?php
/**
 * Base strategy for sending notifications.
 *
 * @author Iron Bound Designs
 * @since  1.0
 */

namespace IronBound\WP_Notifications\Strategy;

/**
 * Interface Base
 * @package IronBound\WP_Notifications\Strategy
 */
interface Strategy {

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
	public function send( \WP_User $recipient, $message, $subject, array $template_parts );

	/**
	 * Get the suggested number of times a notification with this strategy can be sent per PHP request.
	 *
	 * @since 1.0
	 *
	 * @return int
	 */
	public function get_suggested_rate();
}