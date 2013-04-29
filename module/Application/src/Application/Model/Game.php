<?php

namespace Application\Model;

/**
 * Abstract Game class
 * NOTE: Cannot be instaniated
 *
 * @author Thomas Powers <digitalwizard79@gmail.com>
 */
abstract class Game
{
	/**
	 * Deck object
	 * Note: Represents the deck of cards
	 * 
	 * @var Deck 
	 */
	protected $deck	= null;
	
	/**
	 * Array of error messages
	 * @var array
	 */
	protected $errors = array();
	
	/**
	 * Field of play
	 * NOTE: Storage for the cards that are in the field of play
	 * 
	 * @var FieldOfPlay
	 */
	protected $fieldOfPlay	= null;
	
	/**
	 * Number of games played
	 * 
	 * @var int
	 */
	protected $games = 0;
	
	/**
	 * Number of the player that won
	 * NOTE: -1 if nobody has won yet
	 * 
	 * @var int
	 */
	protected $gameWinner = 0;
	
	/**
	 * An array of Player objects
	 * Note: Represents the players in the game
	 * 
	 * @var array of Player
	 */
	protected $players = array();
	
	/**
	 * Method that handles how the cards are dealt
	 * Note: This is abstract because each child of Game will
	 * likely have a different deal method
	 */
	abstract public function deal();
	
	/**
	 * Returns the winner of the game
	 * 
	 * @return int
	 */
	public function getGameWinner()
	{
		return $this->gameWinner;
	}
	
	/**
	 * Initializes the $errors array to empty
	 */
	public function clearErrors()
	{
		$this->errors = array();
	}
	
	/**
	 * Returns the Deck instance variable
	 * 
	 * @return \Application\Model\Deck
	 */
	public function getDeck()
	{
		return $this->deck;
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
	 * Returns the $games instance variable
	 * 
	 * @return int
	 */
	public function getGames()
	{
		return $this->games;
	}
	
	/**
	 * Returns the array of Player instances
	 * 
	 * @return array
	 */
	public function getPlayers()
	{
		if ($this->players == null) {
			return -1;
		} else {
			return $this->players;
		}
	}
}