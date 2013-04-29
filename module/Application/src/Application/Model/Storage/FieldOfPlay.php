<?php

namespace Application\Model;

/**
 * Storage component for cards that are in the field of play
 * Note: Singleton (Only one field of play should exist per game)
 *
 * @author Thomas Powers <digitalwizard79@gmail.com>
 */
class Storage_FieldOfPlay extends Storage
{
	
	/**
	 * Adds the instances in the passed array to the existing array
	 * 
	 * @param array $cards
	 */
	public function addToStorage(array $cards)
	{
		foreach($cards as $card) {
			$this->storage[count($this->storage)] = $card;
		}
	}
	
	/**
	 * Empty the storage and add cards to player's deck
	 * NOTE: The storage array is reversed so that the cards
	 * are added to the front of the array instead of the back (end
	 * 
	 * @param Player $player
	 */
	public function addStorageToPlayer(Player $player, $doReverse = false)
	{
		if ($doReverse) {
			$player->reverseOrder();
		}
		
		// Loop through the storage array and add each card to the player
		foreach($this->storage as $card) {
			$player->addCardToHand($card);			
		}		
		
		if ($doReverse) {
			$player->reverseOrder();
		}
		
		// Reinitialize as an empty array
		$this->reset();
	}
	
	/**
	 * Returns the top two cards of the deck
	 * NOTE: Used to display the top two cards on the play field
	 * @return array
	 * @throws Exception (if index out of bounds)
	 */
	public function getTopCards()
	{
		$count = count($this->storage);
		
		try {
			return array(
				$this->storage[$count-2], 
				$this->storage[$count-1]
			);
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}
}