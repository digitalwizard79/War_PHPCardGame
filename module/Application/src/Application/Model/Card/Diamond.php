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
	
	/**
	 * Returns the path for the image of the specific card
	 * Must be defined
	 * 
	 * @return string
	 */
	/*public function setImagePath()
	{
		parent::setImagePath();
	}*/
}