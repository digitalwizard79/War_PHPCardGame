<?php

namespace Application\Model;

/**
 * Player object
 * Represents the player in a Game object
 *
 * @author Thomas Powers <digitalwizard79@gmail.com>
 */
class Player
{
	/**
	 * An array of Card objects
	 * 
	 * @var array
	 */
	private $hand = array();
	
	/**
	 * The player's score
	 * 
	 * @var int 
	 */
	private $score = 0;
	
	/**
	 * The player's name
	 * 
	 * @var string
	 */
	private $name = "";
	
	/**
	 * The number of games the player has won
	 * @var int
	 */
	private $gamesWon = 0;
	
	/**
	 * The number of rounds the player has won
	 * 
	 * @var int
	 */
	private $roundsWon = 0;
	
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
	 * Add an array of Card objects to the player's hand
	 * 
	 * @param array (of Card instances)
	 */
	public function addCardsToHand(array $cards)
	{		
		foreach($cards as $card) {
			$this->addCardToHand($card, true);
		}		
	}
	
	/**
	 * Returns the last (top) Card object in the $hand property
	 * 
	 * @return Card
	 */
	public function getTopCard()
	{
		$count = count($this->hand);
		
		if ( isset($this->hand[$count-1]) ) {
			return $this->hand[$count-1];
		}		
	}
	
	/**
	 * Returns the $hand property
	 * 
	 * @return array
	 */
	public function getHand()
	{
		return $this->hand;
	}
	
	/**
	 * Returns the $name property
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
	
	public function getGamesWon()
	{
		return $this->gamesWon;
	}
	
	/**
	 * Returns the $roundsWon instance variable
	 * 
	 * @return int
	 */
	public function getRoundsWon()
	{
		return $this->roundsWon;
	}
	
	/**
	 * Return the current score
	 * 
	 * @return int
	 */
	public function getScore()
	{
		return $this->score;
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
	 * Sets the value of the $name property
	 * 
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}
	
	/**
	 * Increments the number of turns won by 1
	 */
	public function increaseRoundsWon()
	{
		$this->roundsWon++;
	}
	
	/**
	 * Increments the number of games won by 1
	 */
	public function increaseGamesWon()
	{
		$this->gamesWon++;
	}
	/**
	 * Adds the value passed to the existing score
	 * 
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
	 * 
	 * @param int $score
	 */
	public function setScore($score)
	{
		$this->score = $score;
	}
	
	/**
	 * Converts the object to an easily readable array to make it easier
	 * to pass as an AJAX response formatted as JSON
	 * 
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
			'cardCount'	=>	count($this->hand),
		);
	}	
}