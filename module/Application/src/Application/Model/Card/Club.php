<?php

namespace Application\Model;

/**
 * Club suit class derived from Card (Abstract)
 *
 * @author Thomas Powers <digitalwizard79@gmail.com>
 */
class Card_Club extends Card
{
	/**
	 * Override the constructor so we can pass a value
	 * 
	 * @param int $value
	 */
	public function __construct($value)
	{
		$this->suit		= CardSuit::CLUBS;
		$this->value	= $value;		
	}	
}