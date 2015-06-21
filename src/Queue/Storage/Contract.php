<?php
/**
 * Storage contract.
 *
 * @author Iron Bound Designs
 * @since  1.0
 */

namespace IronBound\WP_Notifications\Queue\Storage;
use IronBound\WP_Notifications\Contract as Notification;
use IronBound\WP_Notifications\Strategy\Strategy;

/**
 * Interface Contract
 * @package IronBound\WP_Notifications\Queue\Storage
 */
interface Contract {

	/**
	 * Store a set of notifications.
	 *
	 * @since 1.0
	 *
	 * @param string         $queue_id
	 * @param Notification[] $notifications
	 * @param Strategy       $strategy
	 *
	 * @return bool
	 */
	public function store_notifications( $queue_id, array $notifications, Strategy $strategy );

	/**
	 * Get a set of notifications.
	 *
	 * @since 1.0
	 *
	 * @param string $queue_id
	 *
	 * @return Notification[]|null Null if invalid queue_id
	 */
	public function get_notifications( $queue_id );

	/**
	 * Get the strategy for a set of notifications.
	 *
	 * @since 1.0
	 *
	 * @param string $queue_id
	 *
	 * @return Strategy|null Null if invalid queue_id
	 */
	public function get_notifications_strategy( $queue_id );

	/**
	 * Clear a set of notifications.
	 *
	 * @since 1.0
	 *
	 * @param string $queue_id
	 *
	 * @return bool
	 */
	public function clear_notifications( $queue_id );
}