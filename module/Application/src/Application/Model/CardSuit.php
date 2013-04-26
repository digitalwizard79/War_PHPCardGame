<?php

namespace Application\Model;

/**
 * Card Suit class
 * 
 * @author Thomas Powers <digitalwizard79@gmail.com>
 */
class CardSuit
{
	const SPADES	= 1;
	const HEARTS	= 2;
	const CLUBS		= 3;
	const DIAMONDS	= 4;
	
	/**
	 * Returns the name for the card suit
	 * 
	 * @param int $suit
	 * @return string
	 * @throws Exception
	 */
	public static function getName($suit)
	{
		switch($suit) {
			case 1:
				return "Spade";
				break;
			case 2:
				return "Heart";
				break;
			case 3:
				return "Club";
				break;
			case 4:
				return "Diamond";
				break;
			default:
				throw new Exception("Invalid suit passed: " . $suit);
				die();
		}
	}
}