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
			$this->count++;
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
			$this->count--;
		}		
		
		if ($doReverse) {
			$player->reverseOrder();
		}
		
		// Reinitialize as an empty array
		$this->reset();
	}
}