<?php

namespace Application\Model;

use \Exception;

/**
 * Player object
 *
 * @author Thomas Powers <digitalwizard79@gmail.com>
 */
class Player
{
	/**
	 * An array of Card objects
	 * @var array
	 */
	private $hand		= array();
	
	/**
	 * The player's score
	 * @var int 
	 */
	private $score		= 0;
	
	/**
	 * The player's name
	 * @var string
	 */
	private $name		= "";
	
	/**
	 * The number of items in the $hand array
	 * 
	 * @var int
	 */
	private $count		= 0;
	
	/**
	 * Add a card object to the player's hand
	 * 
	 * @param \Application\Model\Card $card
	 * @param boolean $doReverse
	 */
	public function addCardToHand(Card $card, $doReverse = false)
	{
		if ($doReverse) {
			$this->reverseOrder();
		}
		
		array_push($this->hand, $card);		
				
		if ($doReverse) {
			$this->reverseOrder();
		}
	}
	
	/**
	 * Remove the last (top) card from the player's hand
	 */
	public function removeCardFromHand()
	{
		array_pop($this->hand);
	}
	
	/**
	 * Reverse the order of the Card objects in the $hand array
	 * Note: This gives us the capability of pushing a Card to the bottom
	 * of the player's hand after winning a hand
	 */
	public function reverseOrder()
	{
		$this->hand = array_reverse($this->hand);
	}
	
	/**
	 * Returns the last (top) Card object in the $hand property
	 * @return Card
	 */
	public function getTopCard()
	{
		if ( isset($this->hand[count($this->hand)-1]) ) {
			return $this->hand[count($this->hand)-1];
		}		
	}
	
	/**
	 * Returns the $hand property
	 * @return array
	 */
	public function getHand()
	{
		return $this->hand;
	}
	
	/**
	 * Returns the $count property
	 * @return type
	 */
	public function getCount()
	{
		return count($this->hand);
	}
	
	/**
	 * Returns the $name property
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * Return the current score
	 * @return int
	 */
	public function getScore()
	{
		return $this->score;
	}
	
	/**
	 * Sets the value of the $name property
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}
	
	/**
	 * Adds the value passed to the existing score
	 * @param int score
	 */
	public function addScore($score)
	{
		$this->score += $score;
	}
	
	/**
	 * Resets everything except for the score and name
	 * so the player can begin a new game
	 */
	public function newGame()
	{
		$this->count = 0;
		$this->hand	 = array();
	}
	
	/**
	 * Set the score to the value that is passed
	 * NOTE: This does not take into account the existing score at all
	 * @param int $score
	 */
	public function setScore($score)
	{
		$this->score = $score;
	}
	
	/**
	 * Converts the object to an easily readable array to make it easier
	 * to pass as an AJAX response formatted as JSON
	 * @return array
	 */
	public function toArray()
	{
		$cards = array();
		foreach($this->hand as $card) {
			$cards[count($cards)] = $card->toArray(); 
		}
		
		return array(
			'hand'		=>	$cards,
			'score'		=>	$this->score,
			'cardCount'	=>	$this->getCount(),
			'topCard'	=>	($this->getTopCard() !== null) ? $this->getTopCard()->toArray() : null
		);
	}	
}