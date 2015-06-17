<?php
/**
 * Used to send purchase notification.
 *
 * @author Iron Bound Designs
 * @since  1.0
 */

namespace IronBound\WP_Notifications;

use IronBound\WP_Notifications\Strategy\Strategy;
use IronBound\WP_Notifications\Template\Factory;
use IronBound\WP_Notifications\Template\Manager;

/**
 * Class Notification
 * @package IronBound\WP_Notifications
 */
class Notification implements \Serializable {

	/**
	 * @var Strategy
	 */
	protected $strategy;

	/**
	 * @var \WP_User
	 */
	private $recipient;

	/**
	 * @var string
	 */
	private $message;

	/**
	 * @var string
	 */
	private $subject;

	/**
	 * @var array
	 */
	private $data_sources = array();

	/**
	 * @var array
	 */
	private $tags = array();

	/**
	 * @var Manager
	 */
	private $manager;

	/**
	 * Constructor.
	 *
	 * @since 1.0
	 *
	 * @param \WP_User $recipient
	 * @param Manager  $manager
	 * @param string   $message Message to be sent. Template tags are not replaced. May contain HTML.
	 * @param string   $subject
	 */
	public function __construct( \WP_User $recipient, Manager $manager, $message, $subject ) {
		$this->recipient = $recipient;
		$this->message   = $message;
		$this->manager   = $manager;
		$this->subject   = $subject;

		$this->tags = $this->generate_rendered_tags();
	}

	/**
	 * Generate the rendered forms of the tags.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	protected function generate_rendered_tags() {

		$data_sources   = $this->data_sources;
		$data_sources[] = $this->recipient;

		/**
		 * Filter the data sources used for rendering the template tags.
		 *
		 * @param array    $data_sources
		 * @param \WP_User $recipient
		 */
		$data_sources = apply_filters( 'itepbo_notification_data_sources', $data_sources, $this->recipient );

		$tags     = $this->manager->render_tags( $data_sources );
		$rendered = array();

		foreach ( $tags as $tag => $value ) {
			$rendered[ '{' . $tag . '}' ] = $value;
		}

		return $rendered;
	}

	/**
	 * Add a data source.
	 *
	 * @since 1.0
	 *
	 * @param \Serializable $source
	 */
	public function add_data_source( \Serializable $source ) {
		$this->data_sources[] = $source;
	}

	/**
	 * Set the send strategy.
	 *
	 * @since 1.0
	 *
	 * @param Strategy $strategy
	 */
	public function set_strategy( Strategy $strategy ) {
		$this->strategy = $strategy;
	}

	/**
	 * Send the notification.
	 *
	 * @since 1.0
	 *
	 * @return bool
	 *
	 * @throws \Exception
	 */
	public function send() {

		if ( is_null( $this->strategy ) ) {
			throw new \LogicException( "No strategy has been set." );
		}

		return $this->strategy->send( $this->recipient, $this->message, $this->subject, $this->tags );
	}

	/**
	 * Has this notification already been set.
	 *
	 * This is used by Queue processors in case of timeouts.
	 *
	 * @since 1.0
	 *
	 * @return boolean
	 */
	public function was_sent() {
		return false;
	}

	/**
	 * Get the recipient for this notification.
	 *
	 * @since 1.0
	 *
	 * @return \WP_User
	 */
	public function get_recipient() {
		return $this->recipient;
	}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * String representation of object
	 * @link http://php.net/manual/en/serializable.serialize.php
	 * @return string the string representation of the object or null
	 *
	 * @codeCoverageIgnore
	 */
	public function serialize() {
		$data = array(
			'recipient'    => $this->recipient->ID,
			'message'      => $this->message,
			'subject'      => $this->subject,
			'strategy'     => get_class( $this->strategy ),
			'manager'      => $this->manager->get_type(),
			'data_sources' => serialize( $this->data_sources )
		);

		return serialize( $data );
	}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Constructs the object
	 * @link http://php.net/manual/en/serializable.unserialize.php
	 *
	 * @param string $serialized <p>
	 *                           The string representation of the object.
	 *                           </p>
	 *
	 * @return void
	 *
	 * @codeCoverageIgnore
	 */
	public function unserialize( $serialized ) {
		$data = unserialize( $serialized );

		$this->recipient    = get_user_by( 'id', $serialized['recipient'] );
		$this->message      = $data['message'];
		$this->manager      = Factory::make( $data['manager'] );
		$this->tags         = $this->generate_rendered_tags();
		$this->data_sources = unserialize( $data['data_sources'] );

		$strategy_class = $data['strategy'];

		if ( $strategy_class && $strategy_class instanceof Strategy ) {
			$this->strategy = new $strategy_class();
		}
	}
}