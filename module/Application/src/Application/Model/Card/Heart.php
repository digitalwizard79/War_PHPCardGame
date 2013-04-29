<?php

namespace Application\Model;

/**
 * Description of Heart
 *
 * @author Thomas Powers <digitalwizard79@gmail.com>
 */
class Card_Heart extends Card
{
	/**
	 * Overrides default constructor so we can pass a value
	 * @param int $value
	 */
	public function __construct($value)
	{
		$this->suit		= CardSuit::HEARTS;
		$this->value	= $value;
	}	
}