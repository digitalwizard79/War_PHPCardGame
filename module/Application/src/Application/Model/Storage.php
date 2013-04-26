<?php

namespace Application\Model;

/**
 * Abstract storage class
 *
 * @author Thomas Powers <digitalwizard79@gmail.com>
 */
abstract class Storage
{
	/**
	 * Array that holds the Card instances in play
	 * 
	 * @var array
	 */
	protected $storage	= array();
	
	/**
	 * Integer to keep track of the number of cards in storage
	 * 
	 * @var int
	 */
	protected $count = 0;
	
	/**
	 * (Re)Initializes the storage as an empty array
	 */
	public function reset()
	{
		$this->storage = array();
	}
}