<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 *
 * Copyright (c) 2008 Filip Procházka (filip@prochazka.su)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Kdyby\Events;

use Doctrine;
use Doctrine\Common\EventArgs;
use Doctrine\Common\EventSubscriber;
use Kdyby;
use Nette;



/**
 * @author Filip Procházka <filip@prochazka.su>
 */
class NamespacedEventManager extends Doctrine\Common\EventManager
{

	/**
	 * @var EventManager
	 */
	private $evm;

	/**
	 * @var string
	 */
	private $namespace;



	public function __construct($namespace, EventManager $eventManager)
	{
		if (!preg_match('~[^a-z0-9]\\z~i', $namespace = trim($namespace))) {
			$namespace .= '::';
		}

		$this->namespace = $namespace;
		$this->evm = $eventManager;
	}



	public function dispatchEvent($eventName, EventArgs $eventArgs = null)
	{
		list($ns, $event) = Event::parseName($eventName);

		if ($ns !== NULL) {
			throw new InvalidArgumentException("Unexpected event with namespace.");
		}

		if ($this->evm->hasListeners($event)) {
			$this->evm->dispatchEvent($event, $eventArgs);
		}

		if ($this->evm->hasListeners($this->namespace . $event)) {
			$this->evm->dispatchEvent($this->namespace . $event, $eventArgs);
		}
	}



	public function getListeners($event = null)
	{
		return $this->evm->getListeners($event);
	}



	public function hasListeners($event)
	{
		return $this->evm->hasListeners($event);
	}



	public function addEventListener($events, $listener)
	{
		$this->evm->addEventListener($events, $listener);
	}



	public function removeEventListener($events, $listener)
	{
		$this->evm->removeEventListener($events, $listener);
	}



	public function addEventSubscriber(EventSubscriber $subscriber)
	{
		$this->evm->addEventSubscriber($subscriber);
	}



	public function removeEventSubscriber(EventSubscriber $subscriber)
	{
		$this->evm->removeEventSubscriber($subscriber);
	}

}
