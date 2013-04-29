<?php

namespace Application\Model;

/**
 * Abstract storage class
 * Cannot be instantiated
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
	 * Returns the storage array | -1 if empty
	 * @return array
	 */
	public function getStorage()
	{
		if ( !empty($this->storage) ) {
			return $this->storage;
		} else {
			return -1;
		}
	}
	
	/**
	 * (Re)Initializes the storage as an empty array
	 */
	public function reset()
	{
		$this->storage = array();
		
	}
}