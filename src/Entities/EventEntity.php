<?php

namespace InternetPixels\EventManager\Entities;

/**
 * Class EventEntity
 * @package InternetPixels\EventManager\Entities
 */
class EventEntity {

	/**
	 * @var string
	 */
	private $key;

	/**
	 * @var EventListenerEntity[]
	 */
	private $listeners = [];

	/**
	 * @var bool
	 */
	private $params = false;

	/**
	 * @return string
	 */
	public function getKey(): string {
		return $this->key;
	}

	/**
	 * @param string $key
	 */
	public function setKey( string $key ) {
		$this->key = $key;
	}

	/**
	 * @return array
	 */
	public function getListeners(): array {
		return $this->listeners;
	}

	/**
	 * @param EventListenerEntity $listener
	 */
	public function addListener( EventListenerEntity $listener ) {
		$this->listeners[] = $listener;
	}

	/**
	 * @return bool
	 */
	public function hasParams(): bool {
		return $this->params;
	}

	/**
	 * @param bool $params
	 */
	public function setParams( bool $params ) {
		$this->params = $params;
	}

}