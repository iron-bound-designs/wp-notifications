<?php
/**
 * Queue interface.
 *
 * @author Iron Bound Designs
 * @since  1.0
 */

namespace IronBound\WP_Notifications\Queue;

use IronBound\WP_Notifications\Notification;
use IronBound\WP_Notifications\Strategy\Strategy;

/**
 * Interface Queue
 * @package IronBound\WP_Notifications\Queue
 */
interface Queue {

	/**
	 * Process a batch of notifications.
	 *
	 * @since 1.0
	 *
	 * @param Notification[] $notifications
	 * @param Strategy       $strategy
	 *
	 * @throws \Exception
	 */
	public function process( array $notifications, Strategy $strategy );
}