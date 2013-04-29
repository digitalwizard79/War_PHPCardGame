<?php

namespace Application\Model;
 
/**
 * Abstract Card class
 * Cannot be instantiated
 *
 * @author Thomas Powers <digitalwizard79@gmail.com>
 */
abstract class Card
{
	/**
	 * Integer representation of the card suit
	 * 
	 * @var int
	 */
	protected $suit;
	
	/**
	 * Integer representation of the card value
	 * 1 through 10 = same
	 * 11 = Jack
	 * 12 = Queen
	 * 13 = King
	 * 14 = Ace
	 * 
	 * @var int
	 */
	protected $value;
	
	/**
	 * String representation of the card image location
	 * 
	 * @var string
	 */
	protected $imgPath;
	
	/**
	 * Static method that serves as a factory for creating Card instances
	 * 
	 * @param int $suit
	 * @param int $value
	 * @return \Application\Model\Card
	 * @throws Exception
	 */
	public static function factory($suit, $value)
	{
		try {
			$className = "Application\Model\Card_".CardSuit::getName($suit);
			return new $className($value);
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}		
	}
	
	/**
	 * Returns the $suit instance variable
	 * 
	 * @return int
	 */
	public function getSuit()
	{
		return $this->suit;
	}
	
	/**
	 * Returns the $value instance variable
	 * 
	 * @return int
	 */
	public function getValue()
	{
		return $this->value;
	}
	
	/**
	 * Return the path to the image of the specific card
	 * @return string
	 */
	public function setImagePath()
	{
		$class = get_called_class();
		$pos = strrpos($class, '\\');
		$className = substr($class, $pos+1);
		
		$this->imgPath = str_replace('_', '/', $className . "_" . $this->value);
		return  $this->imgPath;
	}
	
	/**
	 * Returns the current instance as an array
	 * Note: Used for AJAX transactions
	 * 
	 * @return array
	 */
	public function toArray()
	{
		return array(
			'suit'		=> $this->suit,
			'value'		=> $this->value,
			'imgPath'	=> $this->imgPath
		);		
	}
}