<?php

namespace InternetPixels\EventManager\Tests;

use InternetPixels\EventManager\Entities\EventEntity;
use InternetPixels\EventManager\Entities\EventListenerEntity;
use InternetPixels\EventManager\Manager\EventManager;
use PHPUnit\Framework\TestCase;

/**
 * Class EventManagerTest
 * @package InternetPixels\EventManager\Tests
 */
class EventManagerTest extends TestCase {

	/**
	 * @var bool
	 */
	private $callbackReceived = false;

	public function setUp() {
		$this->callbackReceived = false;
	}

	public function testBasicEventsWithListeners() {
		$this->callbackReceived = false;
		$eventManager           = new EventManager();

		$event = new EventEntity();
		$event->setKey( 'test.event.after.post' );
		$eventManager->registerEvent( $event );

		$listener = new EventListenerEntity();
		$listener->setEventKey( 'test.event.after.post' );
		$listener->setPriority( 50 );
		$listener->setCallback( [ $this, 'eventCallback' ] );
		$eventManager->registerListener( $listener );

		$listener = new EventListenerEntity();
		$listener->setEventKey( 'test.event.after.post' );
		$listener->setPriority( 10 );
		$listener->setCallback( [ $this, 'eventCallback' ] );
		$eventManager->registerListener( $listener );

		$this->assertFalse( $this->callbackReceived );
		$this->assertTrue( $eventManager->executeEvent( 'test.event.after.post' ) );
		$this->assertTrue( $this->callbackReceived );
	}

	public function testBasicEventsWithListeners_AND_params() {
		$this->callbackReceived = false;
		$eventManager           = new EventManager();

		$event = new EventEntity();
		$event->setKey( 'test.event.after.post' );
		$event->setParams( true );
		$eventManager->registerEvent( $event );

		$listener = new EventListenerEntity();
		$listener->setEventKey( 'test.event.after.post' );
		$listener->setPriority( 50 );
		$listener->setCallback( [ $this, 'eventCallback_WITH_parameter' ] );
		$eventManager->registerListener( $listener );

		$this->assertFalse( $this->callbackReceived );
		$executed = $eventManager->executeEvent( 'test.event.after.post', [
			'first parameter'
		] );
		$this->assertEquals( 'first parameter', current( $executed ) );
		$this->assertTrue( $this->callbackReceived );
	}

	public function testBasicEventsWithListeners_AND_params_AND_modifyDataInEachListener() {
		$this->callbackReceived = false;
		$eventManager           = new EventManager();

		$event = new EventEntity();
		$event->setKey( 'test.event.after.post' );
		$event->setParams( true );
		$eventManager->registerEvent( $event );

		$listener = new EventListenerEntity();
		$listener->setEventKey( 'test.event.after.post' );
		$listener->setPriority( 30 );
		$listener->setCallback( [ $this, 'eventCallback_AND_replaceWord' ] );
		$eventManager->registerListener( $listener );

		$this->assertFalse( $this->callbackReceived );
		$executed = $eventManager->executeEvent( 'test.event.after.post', [
			'seek me'
		] );
		$this->assertEquals( 'replaced by me', current( $executed ) );
		$this->assertTrue( $this->callbackReceived );
	}

	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage Event "test.event.after.post" is is not added
	 */
	public function testRegisterNewListenerOnNonExistingEvent() {
		$eventManager = new EventManager();

		$listener = new EventListenerEntity();
		$listener->setEventKey( 'test.event.after.post' );
		$listener->setPriority( 25 );
		$listener->setCallback( [ $this, 'eventCallback' ] );
		$eventManager->registerListener( $listener );
	}

	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage Missing callback method in listener for event "test.event.after.post"
	 */
	public function testRegisterNewListener_WITHOUT_callback() {
		$eventManager = new EventManager();

		$event = new EventEntity();
		$event->setKey( 'test.event.after.post' );
		$eventManager->registerEvent( $event );

		$listener = new EventListenerEntity();
		$listener->setEventKey( 'test.event.after.post' );
		$listener->setPriority( 25 );
		$eventManager->registerListener( $listener );

		$eventManager->executeEvent( 'test.event.after.post' );
	}

	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage Parameters are given in the execution for the event "test.event.after.post", while they are not enabled in the event
	 */
	public function testBasicEventsWithListeners_AND_params_WITHOUT_enablingParameters() {
		$this->callbackReceived = false;
		$eventManager           = new EventManager();

		$event = new EventEntity();
		$event->setKey( 'test.event.after.post' );
		$eventManager->registerEvent( $event );

		$listener = new EventListenerEntity();
		$listener->setEventKey( 'test.event.after.post' );
		$listener->setPriority( 35 );
		$listener->setCallback( [ $this, 'eventCallback_WITH_parameter' ] );
		$eventManager->registerListener( $listener );

		$this->assertFalse( $this->callbackReceived );
		$eventManager->executeEvent( 'test.event.after.post', [
			'first parameter without setting'
		] );
		$this->assertFalse( $this->callbackReceived );
	}


	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage Missing parameters for event "test.event.after.post"
	 */
	public function testBasicEventsWithListeners_AND_params_WITHOUT_executingParameters() {
		$this->callbackReceived = false;
		$eventManager           = new EventManager();

		$event = new EventEntity();
		$event->setKey( 'test.event.after.post' );
		$event->setParams( true );
		$eventManager->registerEvent( $event );

		$listener = new EventListenerEntity();
		$listener->setEventKey( 'test.event.after.post' );
		$listener->setPriority( 50 );
		$listener->setCallback( [ $this, 'testCallback' ] );
		$eventManager->registerListener( $listener );

		$this->assertFalse( $this->callbackReceived );
		$eventManager->executeEvent( 'test.event.after.post' );
		$this->assertFalse( $this->callbackReceived );
	}

	public function eventCallback() {
		$this->callbackReceived = true;

	}

	public function eventCallback_WITH_parameter( $params = null ) {
		$this->callbackReceived = true;

		$this->assertNotNull( $params );
		$this->assertEquals( 'first parameter', current( $params ), 'Wrong parameter in event callback given' );

		return $params;
	}

	public function eventCallback_AND_replaceWord( $params = null ) {
		$this->callbackReceived = true;

		$this->assertNotNull( $params );

		$words = current( $params );
		$words = str_replace( 'seek me', 'replaced by me', $words );

		return [ $words ];
	}

}