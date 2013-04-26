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
	
	/**
	 * Returns the path for the image of the specific card
	 * 
	 * @return string
	 */
	public function setImagePath()
	{
		$class = get_class();
		$pos = strrpos($class, '\\');
		$className = substr($class, $pos+1);
		
		$this->imgPath = str_replace('_', '/', $className . "_" . $this->value);
		return  $this->imgPath;
	}
}