<?php

namespace Application\Model;

/**
 * Spade suit class derived from Card (abstract)
 *
 * @author Thomas Powers <digitalwizard79@gmail.com>
 */
class Card_Spade extends Card
{
	/**
	 * Overrides default constructor so we can pass a value
	 * @param int $value
	 */
	public function __construct($value)
	{
		$this->suit		= CardSuit::SPADES;
		$this->value	= $value;
	}
}