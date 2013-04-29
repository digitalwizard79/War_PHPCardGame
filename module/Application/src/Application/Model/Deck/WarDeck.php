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
	/**
	 * Override default constructor
	 * @throws Exception
	 */
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
			$x = 1;		// Tracks the index for the $players array
			
			// Loop through the array
			foreach($this->cardList as $card) {
				$players[$x]->addCardToHand($card);
				array_pop($this->cardList);
				$this->count--;

				// As long as the count is not higher than how many objects in the array
				// keep incrementing
				if ($x >= count($players)) {
					$x = 1;
				} else {
					$x++;
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