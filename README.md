# Event manager for PHP
Handle events and triggers in your application with this PHP library. No hassle with the correct hooking time of listeners, simple set the priority.

This is a open-source library. Please consider a link to this repository when you're actively using it.

[![License](https://camo.githubusercontent.com/cf76db379873b010c163f9cf1b5de4f5730b5a67/68747470733a2f2f6261646765732e66726170736f66742e636f6d2f6f732f6d69742f6d69742e7376673f763d313032)](https://github.com/internetpixels/event-manager)
[![Build Status](https://travis-ci.org/internetpixels/event-manager.svg)](https://travis-ci.org/internetpixels/event-manager)
[![Maintainability](https://api.codeclimate.com/v1/badges/2acca3dc3de3ffdef388/maintainability)](https://codeclimate.com/github/internetpixels/event-manager/maintainability)

## Installation
Install this PHP event manager by using composer:

	composer require internetpixels/event-manager

## Basic examples

### Register an event
Before you're able to use a new event, you'll need to register it. Find a logical place in your application, preferably in the start of the runtime. 

The event needs to be registered before you can add any listeners to it, otherwise this library will throw an exception!

	$eventManager = new EventManager();

	$event = new EventEntity();
	$event->setKey( 'test.event.after.post' );
	
	$eventManager->registerEvent( $event );

### Register a listener
After the event is added, you can "hook" new listeners to the event. This means, when the event is triggered, it will call all triggers with their given priority. 

Priority 1 is the most important and the default priority is 50. The event manager sorts the listeners automatically, so it doesn't matter in what order you add the listeners to the event manager.

A listener can only have 1, required, callback method. This method will be called once the event is executed.

	$listener = new EventListenerEntity();
	$listener->setEventKey( 'test.event.after.post' );
	$listener->setPriority( 20 );
	$listener->setCallback( [ $this, 'eventCallback' ] );
	
	$eventManager->registerListener( $listener );

### Callback example
Each listener has a callback method, a basic callback method may look like this in your application. You should receive the ``$params`` array, and return them as an array for further usage.

	public function eventCallback( $params = null ) {
		// TODO: Do something with the parameters
		var_dump($params);

		return $params;
	}

### Execute the event
When your setup is completed with the event and at least one listener, you can execute the event in your application. This will trigger all listener(s) with their given priority.

	$eventManager->executeEvent( 'test.event.after.post' )
	
## Using the events as filters
You can use the events as filters. The registration process is nearly the same as a normal event, you only have to enable parameters on the event registration and add them in the ``executeEvent`` method.

The ``executeEvent`` method will return the parameters.

	$event = new EventEntity();
	$event->setParams( true );
	$event->setKey( 'test.event.after.post' );
    	
	$eventManager->registerEvent( $event );

	$executed = $eventManager->executeEvent( 'test.event.after.post', [
		'first parameter value',
	] );
	
	var_dump( $executed );
	
The parameters are now available in the event callback ``$params`` array.

### Multiple params in execution
It is very easy to add more parameters in your execution. Just add new array values in the execute method.

	$executed = $eventManager->executeEvent( 'test.event.after.post', [
		'first parameter value',
		'second parameter value',
		'third parameter value',
	] );