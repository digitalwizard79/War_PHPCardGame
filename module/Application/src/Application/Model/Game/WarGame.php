<?php

namespace Application\Model;

/**
 * Description of War
 * 
 * NOTE: This can be modified to add more than two players, but
 * it currently not setup that way
 *
 * @author Thomas Powers <digitalwizard79@gmail.com>
 */
class Game_WarGame extends Game
{
	/**
	 * Counter for how many turns have been played
	 * in the current game
	 * 
	 * @var int
	 */
	private $turns = 0;
	
	/**
	 * Override the default constructor so we can
	 * pass in parameters
	 * 
	 * @param array $players
	 * @param \Application\Model\Deck_WarDeck $deck
	 * @param \Application\Model\Storage_FieldOfPlay $field
	 */
	public function __construct(array $players, Deck_WarDeck $deck, Storage_FieldOfPlay $field)
	{ 
		$this->_createPlayers($players);
		$this->deck			= $deck;
		$this->fieldOfPlay	= $field;
	}
	
	/**
	 * Check to see if a war is taking place
	 * NOTE: A war occurs when both cards are of equal value
	 * 
	 * @return boolean
	 */
	public function checkForWar()
	{
		if ( $this->players[0]->getTopCard()->getValue() == $this->players[1]->getTopCard()->getValue() ) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Loops through each player instance and
	 * checks to see if the card count is 0 for either
	 * 
	 * @return boolean $haveWinner
	 */
	public function checkForWinner()
	{
		$x = 1;
		foreach($this->players as $player) {
			//if ($player->getCount() <= 1) {
			if ($player->getCount() <= 0) {
				$this->winner = $x;
				return true;
			}
			$x++;
		}
		
		return false;
	}
	
	/**
	 * Deal the appropriate amount of cards to the right players
	 */
	public function deal()
	{
		$this->deck->deal($this->players);
	}
	
	/**
	 * Returns the $hands instance variable
	 * 
	 * @return int
	 */
	public function getTurns()
	{
		return $this->turns;
	}
	
	/**
	 * Returns the Player instance for the corresponding value passed
	 * 
	 * @param int $num
	 * @return \Application\Model\Player | -1 if not found
	 */
	public function getPlayer($num)
	{
		if ( isset($this->players[$num - 1]) )  {
			return $this->players[$num - 1];
		} else {
			$this->errors[count($this->errors)] = array(
				'function'	=>	"getPlayer",
				'message'	=>	"Index out of bounds: " . $num
			);
			
			return -1;
		}		
	}
	
	/**
	 * Returns the number of the Player instance
	 * with the highest card value
	 * 
	 * @return int | -1 if not found
	 */
	public function getRoundWinner()
	{
		if (!$this->checkForWar()) {
			if ($this->players[0]->getTopCard()->getValue() > $this->players[1]->getTopCard()->getValue()) {
				return 1;
			} else if ($this->players[0]->getTopCard()->getValue() < $this->players[1]->getTopCard()->getValue()) {
				return 2;
			}
		} else {
			return -1;
		}
	}
	
	/**
	 * Returns the number of the player who won the game
	 * NOTE: We have to check for a value of 1 or less due to the way
	 * the play Action handles cards
	 * 
	 * @return int | -1 if no winner
	 */
	public function getWinner()
	{
		if (null == $this->winner) {
			if ($this->players[0]->getCount() <= 0) {
				$this->winner = 2;
				return 2;
			} else if ($this->players[1]->getCount() <= 0) {
				$this->winner = 1;
				return 1;
			}
		} else {
			return $this->winner;
		}
	}
	
	/**
	 * Creates the Player instances for the game and
	 * stores them in the $players array
	 * 
	 * @param array $players
	 */
	private function _createPlayers(array $players)
	{
		if ( is_array($players) && !empty($players) ) {
			$x = 0;
			foreach($players as $player) {
				$this->players[$x] = $player;
				$x++;
			}
		} else {
			$this->errors[count($this->errors)] = array(
				'function'	=>	"_createPlayers",
				"message"	=>	"Error: Not an array or empty array"
			);			
		}
	}
	
	/**
	 * Sets the game to "new game" status, which means that:
	 *  1. The deck is re-initialized
	 *  2. The field of play is reset
	 *  3. The players are re-initialized except for the scores
	 */
	public function newGame()
	{
		$this->deck->reset();
		$this->fieldOfPlay->reset();
		for($x = 0; $x < count($this->players); $x++) {
			$this->players[$x]->newgame();
		}
	}
	
	public function increaseTurns()
	{
		$this->turns++;
	}
}