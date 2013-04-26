<?php

namespace Application\Model;

/**
 * Abstract Game class
 *
 * @author Thomas Powers <digitalwizard79@gmail.com>
 */
abstract class Game
{
	/**
	 * An array of Player objects
	 * Note: Represents the players in the game
	 * 
	 * @var array of Player
	 */
	protected $players		= array();
	
	/**
	 * Deck object
	 * Note: Represents the deck of cards
	 * 
	 * @var Deck 
	 */
	protected $deck			= null;
	
	/**
	 * Field of play
	 * NOTE: Storage for the cards that are in the field of play
	 * 
	 * @var FieldOfPlay
	 */
	protected $fieldOfPlay	= null;
	
	/**
	 * Keeps track of how many rounds (games) have been played
	 * @var int
	 */
	protected $round		= 0;
	
	/**
	 * Array of error messages
	 * @var array
	 */
	protected $errors		= array();
	
	/**
	 * Number of the player that won
	 * NOTE: -1 if nobody has won yet
	 * @var int
	 */
	protected $winner		= -1;
	
	/**
	 * Method that handles how the cards are dealt
	 * Note: This is abstract because each instance of game could
	 * have a different deal method
	 */
	abstract public function deal();	
	
	public function getWinner()
	{
		return $this->winner;
	}
	
	public function emptyErrors()
	{
		$this->errors = array();
	}
	
	public function getPlayers(array $players = null)
	{
		if ($players == null) {
			return $this->players;
		} else {
			foreach($this->players as $player) {
				array_push($players, $player);
			}
		}
	}
	
	/**
	 * Returns the $deck instance variable
	 * 
	 * @return \Application\Model\Deck
	 */
	public function getDeck()
	{
		return $this->deck;
	}
	
	/**
	 * Returns the $fieldOfPlay instance variable
	 * 
	 * @return \Application\Model\Storage_FieldOfPlay
	 */
	public function getFieldOfPlay()
	{
		return $this->fieldOfPlay;
	}
	
	/**
	 * Returns the $round instance variable
	 * 
	 * @return int
	 */
	public function getRound()
	{
		return $this->round;
	}
	
	/**
	 * Returns the $errors instance variable
	 * 
	 * @return array
	 */
	public function getErrors()
	{
		return $this->errors;
	}		
}