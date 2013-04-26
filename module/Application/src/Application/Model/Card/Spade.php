<?php

namespace Application\Model;

/**
 * Description of Spade
 *
 * @author Thomas Powers <digitalwizard79@gmail.com>
 */
class Card_Spade extends Card
{
	public function __construct($value)
	{
		$this->suit		= CardSuit::SPADES;
		$this->value	= $value;
	}
	
	public function setImagePath()
	{
		$class = get_class();
		$pos = strrpos($class, '\\');
		$className = substr($class, $pos+1);
		
		$this->imgPath = str_replace('_', '/', $className . "_" . $this->value);
		return  $this->imgPath;
	}
}