<?php

namespace Application\Model;

/**
 * Abstract Deck object
 *
 * @author Thomas Powers <digitalwizard79@gmail.com>
 */
abstract class Deck implements Shufflable
{
	// Constants
	const DECK_TOTAL		= 52;
	const SUIT_COUNT		= 4;	
	const CARDS_PER_SUIT	= 13;
	
	// Properties
	protected $cardList		= array();
	protected $count		= 0;
	
	abstract public function deal(array $players);
	
	/**
	 * Initializes the deck
	 * @throws Exception
	 */
	protected function _initDeck()
	{
		$this->count = 0;
		$this->cardList = array();
		
		// Loop through each card suit (4 increments)
		for ($suit = 1; $suit <= self::SUIT_COUNT; $suit++) {
			
			// Loop through the cards for the specific card suit
			for($y = 2; $y <= self::CARDS_PER_SUIT+1; ++$y) {
					
				// The try catch block is required here because the CardSuit::getName() method
				// that is called by the Card::factory() method throws an exception if the suit is invalid
				try {					
					// Calls a static factory method to create the corresponding Card instance
					$card = Card::factory($suit, $y);
					$card->setImagePath();
				
					// Add the current Card instance to the $cardList array
					// and increase the count
					$this->cardList[count($this->cardList)] = $card;
					$this->count += 1;
				} catch (Exception $e) {
					throw new Exception($e->getMessage());
				}
			}
		}
	}
	
	/**
	 * Returns the deck of cards as an array of Card objects
	 * 
	 * @return array
	 */
	public function getCardList()
	{
		return $this->cardList;
	}
	
	/**
	 * Shuffles the deck of cards
	 * 
	 * Note: Must be defined because we implement the Shufflable interface
	 */
	public function shuffle()
	{
		shuffle($this->cardList);
	}
}