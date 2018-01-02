<?php

namespace InternetPixels\EventManager\Entities;

/**
 * Class EventListenerEntity
 * @package InternetPixels\EventManager\Entities
 */
class EventListenerEntity {

	/**
	 * @var string
	 */
	private $eventKey;

	/**
	 * @var
	 */
	private $callback;

	/**
	 * @var int
	 */
	private $priority = 100;

	/**
	 * @return string
	 */
	public function getEventKey(): string {
		return $this->eventKey;
	}

	/**
	 * @param string $eventKey
	 */
	public function setEventKey( string $eventKey ) {
		$this->eventKey = $eventKey;
	}

	/**
	 * @return mixed
	 */
	public function getCallback() {
		return $this->callback;
	}

	/**
	 * @param mixed $callback
	 */
	public function setCallback( $callback ) {
		$this->callback = $callback;
	}

	/**
	 * @return int
	 */
	public function getPriority(): int {
		return $this->priority;
	}

	/**
	 * @param int $priority
	 */
	public function setPriority( int $priority ) {
		$this->priority = $priority;
	}

}