<?php

namespace InternetPixels\EventManager\Manager;

use InternetPixels\EventManager\Entities\EventEntity;
use InternetPixels\EventManager\Entities\EventListenerEntity;

/**
 * Class EventManager
 * @package InternetPixels\EventManager\Manager
 */
class EventManager {

	/**
	 * @var EventEntity[]
	 */
	private $events = [];

	/**
	 * @return EventEntity[]
	 */
	public function getEvents(): array {
		return $this->events;
	}

	/**
	 * Register an event. For example: user.add.form.post, user.action.login, user.action.logout etc.
	 *
	 * @param EventEntity $event
	 */
	public function registerEvent( EventEntity $event ) {
		$this->events[$event->getKey()] = $event;
	}

	/**
	 * Execute an event. Returns true when no parameters are added, returns the parameter bag if you've added them.
	 *
	 * @param string $eventKey
	 * @param array  $params
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function executeEvent( string $eventKey, array $params = [] ) {
		if ( !isset( $this->events[$eventKey] ) ) {
			throw new \Exception( sprintf( 'Event "%s" is is not added', $eventKey ) );
		}

		/** @var EventEntity $event */
		$event     = $this->validateEvent( $eventKey, $params );
		$listeners = $this->orderListeners( $event->getListeners() );

		/** @var EventListenerEntity $listener */
		foreach ( $listeners as $listener ) {
			$params = $this->doListenerCallback( $event, $listener, $params );
		}

		if ( is_null( $params ) ) {
			return true;
		}

		return $params;
	}

	/**
	 * Bind a new listener to an existing event.
	 *
	 * @param EventListenerEntity $listenerEntity
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function registerListener( EventListenerEntity $listenerEntity ) {
		if ( !isset( $this->events[$listenerEntity->getEventKey()] ) ) {
			throw new \Exception( sprintf( 'Event "%s" is is not added', $listenerEntity->getEventKey() ) );
		}

		/** @var EventEntity $event */
		$event = $this->events[$listenerEntity->getEventKey()];
		$event->addListener( $listenerEntity );

		$this->events[$listenerEntity->getEventKey()] = $event;

		return true;
	}

	/**
	 * Order the listeners by their priority.
	 *
	 * @param EventListenerEntity[] $listeners
	 *
	 * @return array
	 */
	private function orderListeners( $listeners = [] ) {
		usort( $listeners, function ( EventListenerEntity $listenerEntity ) {
			return $listenerEntity->getPriority();
		} );

		return $listeners;
	}

	/**
	 * Call a listener callback.
	 *
	 * @param EventEntity         $event
	 * @param EventListenerEntity $listener
	 * @param                     $params
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	private function doListenerCallback( EventEntity $event, EventListenerEntity $listener, $params ) {
		if ( empty( $listener->getCallback() ) ) {
			throw new \Exception( sprintf( 'Missing callback method in listener for event "%s"', $event->getKey() ) );
		}

		if ( $event->hasParams() === true ) {
			return call_user_func_array( $listener->getCallback(), [ $params ] );
		}

		return call_user_func( $listener->getCallback() );
	}

	/**
	 * Validate and get an event from local storage with given parameters.
	 *
	 * @param $eventKey
	 * @param $params
	 *
	 * @return EventEntity
	 * @throws \Exception
	 */
	private function validateEvent( $eventKey, array $params = [] ) {
		/** @var EventEntity $event */
		$event = $this->events[$eventKey];

		if ( $event->hasParams() === true && ( count( $params ) === 0 || $params === null ) ) {
			throw new \Exception( sprintf( 'Missing parameters for event "%s"', $eventKey ) );
		}

		if ( $event->hasParams() === false && ( count( $params ) >= 1 ) ) {
			throw new \Exception(
				sprintf(
					'Parameters are given in the execution for the event "%s", while they are not enabled in the event',
					$eventKey
				)
			);
		}

		return $event;
	}

}