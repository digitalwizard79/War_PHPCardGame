<?php

namespace Application\Model;

use \Exception;

/**
 * Deck instance for the game of War
 *
 * @author Thomas Powers <digitalwizard79@gmail.com>
 */
class Deck_WarDeck extends Deck
{		
	public function __construct()
	{
		try {
			$this->_initDeck();
		} catch(Exception $e) {
			throw $e;
		}
	}
	
	/**
	 * Deals the cards evenly among the array of Player objects
	 * 
	 * @param array
	 */
	public function deal(array $players)
	{
		// Make sure the array isn't empty
		if ( !empty($players) ) {
			$count = 1;		// Tracks the index for the $players array
			
			// Loop through the array
			foreach($this->cardList as $card) {
				$players[$count-1]->addCardToHand($card);

				// As long as the count is not higher than how many objects in the array
				// keep incrementing
				if ($count >= count($players)) {
					$count = 1;
				} else {
					$count++;
				}
			}			
		} else {
			throw new Exception("Array is empty.");
			die();
		}
	}
	
	/**
	 * Re-initializes the deck
	 */
	public function reset()
	{
		$this->_initDeck();
	}
}