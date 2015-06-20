<?php
/**
 * Process a queue of notifications using WP Cron.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 *
 * @copyright   Copyright (c) 2015, Iron Bound Designs, Inc.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License v2 or later
 */

namespace IronBound\WP_Notifications\Queue;

use IronBound\WP_Notifications\Contract;
use IronBound\WP_Notifications\Strategy\Strategy;

/**
 * Class WP_Cron
 * @package IronBound\WP_Notifications\Queue
 */
class WP_Cron implements Queue {

	const CRON_ACTION = 'ibd-wp-notifications-cron-notification';

	/**
	 * @var string
	 */
	protected $bucket;

	/**
	 * Constructor.
	 *
	 * @since 1.0
	 *
	 * @param string $bucket
	 */
	public function __construct( $bucket ) {
		if ( ! has_action( self::CRON_ACTION, array( $this, 'cron_callback' ) ) ) {
			add_action( self::CRON_ACTION, array( $this, 'cron_callback' ) );
		}

		$this->bucket = $bucket;
	}

	/**
	 * Process a batch of notifications.
	 *
	 * @since 1.0
	 *
	 * @param Contract[] $notifications
	 * @param Strategy   $strategy
	 *
	 * @throws \Exception
	 */
	public function process( array $notifications, Strategy $strategy ) {

		$hash = uniqid();

		$queues          = get_option( $this->bucket, array() );
		$queues[ $hash ] = array(
			'notifications' => $notifications,
			'strategy'      => $strategy
		);

		update_option( $this->bucket, $queues );

		self::cron_callback( $hash );
	}

	/**
	 * Get the timestamp of the next event time.
	 *
	 * @since 1.0
	 *
	 * @return int
	 */
	protected static function get_next_event_time() {
		return time() + ( 2 * 60 );
	}

	/**
	 * Callback function given to WP Cron.
	 *
	 * @since 1.0
	 *
	 * @param string $hash
	 */
	public function cron_callback( $hash ) {

		$queues = get_option( $this->bucket, array() );

		if ( ! isset( $queues[ $hash ] ) ) {
			return;
		}

		$queue = $queues[ $hash ];

		$notifications = $queue['notifications'];

		/**
		 * @var Strategy $strategy
		 */
		$strategy = $queue['strategy'];

		$rate = $strategy->get_suggested_rate();

		if ( count( $notifications ) > $rate ) {
			$to_process = array_slice( $notifications, 0, $rate );
		} else {
			$to_process = $notifications;
		}

		/**
		 * @var Contract $notification
		 */
		foreach ( $to_process as $key => $notification ) {

			/*
			 * On the off chance that we timeout while processing the notifications,
			 * double check that this notification hasn't already been sent.
			 */
			if ( $notification->was_sent() ) {
				unset( $notifications[ array_search( $notification, $notifications ) ] );

				continue;
			}

			try {

				$notification->set_strategy( $strategy );

				if ( $notification->send() ) {
					unset( $notifications[ array_search( $notification, $notifications ) ] );
				}

			}
			catch ( \Exception $e ) {

			}
		}

		if ( empty( $notifications ) ) {
			unset( $queues[ $hash ] );
		} else {
			$queues[ $hash ]['notifications'] = $notifications;

			// we pass along a garbage uniquid to prevent WP Cron from denying our event since it is less than 10 minutes since the last
			wp_schedule_single_event( self::get_next_event_time(), self::CRON_ACTION, array( $hash, uniqid() ) );
		}

		update_option( $this->bucket, $queues );
	}
}