<?php

namespace Application\Model;

/**
 * Diamonds suit class derived from Card (abstract)
 *
 * @author Thomas Powers <digitalwizard79@gmail.com>
 */
class Card_Diamond extends Card
{
	/**
	 * Overrides constructor so we can pass a value
	 * @param int $value
	 */
	public function __construct($value)
	{
		$this->suit		= CardSuit::DIAMONDS;
		$this->value	= $value; 
	}
}