<?php

namespace Application\Model;

/**
 * Wargame class
 * NOTE: This can be modified easily to add more than two players, but
 * it's currently not setup that way
 * This class cannot be extended
 *
 * @author Thomas Powers <digitalwizard79@gmail.com>
 */
final class Game_WarGame extends Game
{
	/**
	 * Counter for how many turns have been played
	 * in the current game
	 * 
	 * @var int
	 */
	private $rounds = 0;
	
	/**
	 * Holds the winner of the current/last round
	 * @var int
	 */
	private $roundWinner = 0;
	
	/**
	 * Boolean value that determines
	 * if we are at war
	 */
	private $atWar = 0;
	
	/**
	 * Holds the details that are sent
	 * back to each AJAX call
	 * 
	 * @var array
	 */
	private $details = array();
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
		$this->atWar		= -1;
	}
	
	/**
	 * Loops through each player instance and
	 * checks to see if the card count is 0 for either
	 * 
	 * @return boolean $haveWinner
	 */
	public function checkForGameWinner()
	{
		$x = 1;
		foreach($this->players as $player) {
			if (count($player->getHand()) <= 0) {
				$this->gameWinner = $x;		
			}
			$x++;
		}		
	}
	
	/**
	 * Deal the cards
	 */
	public function deal()
	{
		$this->deck->deal($this->players);
	}
	
	/**
	 * Complete the second part of the hand
	 * NOTE: There are two parts to each hand that is played
	 */
	public function finishHand()
	{
		$this->_findRoundWinner();		
		$this->players[$this->roundWinner]->increaseRoundsWon();
		$this->_setDetails('finish');
		$this->_transferFOPToPlayer($this->roundWinner); 
	}
	
	/**
	 * Container for the details
	 * 
	 * @return json
	 */
	public function getDetails()
	{
		return json_encode($this->details);
	}
	
	/**
	 * Converts the $error array to a string
	 * @return string
	 */
	public function getErrorsAsString()
	{
		if ( count($this->errors) >= 1 ) {
			$errorMessage = "Errors ocurred during processing. Details below:\n\n";
			foreach($this->errors as $error) {
				foreach($error as $details) {
					$errorMessage .= 'Function: ' . $details['function'] . "\n";
					if ( count($details['params']) >= 1 ) {
						$errorMessage .= "Paramaters: \n";
						foreach($details['params'] as $key => $value) {
							$errorMessage .= "	$key: $value\n";
						}
					}
					$errorMessage .= 'Message: ' . $details['message'] . "\n";
					$errorMessage .= "----------------------------------------\n";
				}
			}
		} else {
			$errorMessage = "No errors occurred during processing.";
		}
		
		return $errorMessage;
	}
	
	/**
	 * Returns the number of rounds
	 * 
	 * @return int
	 */
	public function getRounds()
	{
		return $this->rounds;
	}
	
	/**
	 * Returns the Player instance for the corresponding value passed
	 * 
	 * @param int $num
	 * @return \Application\Model\Player | -1 if not found
	 */
	public function getPlayer($num)
	{
		if ( isset($this->players[$num]) )  {
			return $this->players[$num];
		} else {
			$error	=	array(
				'function'	=>	'getPlayer',
				'params'	=>	array(
					'index'	=>	$index
				),
				'message'	=>	'Array index out of bounds'
			);
			
			$this->_addError($error);			
		}		
	}
	
	/**
	 * Sets the game to "new game" status, which means that:
	 *  1. The deck is re-initialized
	 *  2. The field of play is reset
	 *  3. The players' cards are reset
	 */
	public function newGame()
	{
		$error = array(
			'function'	=>	'newGame',
			'params'	=>	array()
		);
			
		$this->deck->reset();
		$this->fieldOfPlay->reset();
		$this->gameWinner = 0;
		$this->turnWinner = -1;
		for($x = 1; $x <= count($this->players); $x++) {
			if ( isset($this->players[$x]) ) {
				$this->players[$x]->newgame();
			} else {
				$error['message'] = 'Array index out of bounds';
				$this->_addError($error);
			}
		}		
	}
	
	/**
	 * The first portion of the hand
	 * NOTE: Each hand consists of two parts: the start and the finish
	 * NOTE: 'FOP' = FieldOfPlay
	 */
	public function playHand()
	{
		$this->clearErrors();
		$this->_transferTopCardToFOP();
		$this->_findRoundWinner();
		$this->rounds++;
		$this->_setDetails('play');
	}
	
	/**
	 * Handles the details of quitting the game
	 */
	public function quit()
	{
		$this->_setDetails('quit');
	}
	
	/**
	 * Handles the logic for a player winning
	 * @param int $index
	 */
	public function setGameWinner()
	{
		$index = $this->gameWinner;
		if ( isset($this->players[$index]) ) {
			$this->players[$index]->addScore(50);
			$this->players[$index]->increaseRoundsWon();
			$this->players[$index]->increaseGamesWon();
			$this->games++;			
			$this->_setDetails('gamewin');
		} else {
			$error = array(
				'function'	=>	'_setGameWinner',
				'params'	=>	array(
					'index'	=>	$index
				),
				'message'	=>	'Array index out of bounds'
			);
			
			$this->_addError($error);
		}
		
	}
	
	/**
	 * Adds an array with error information 
	 * @param array $error
	 */
	private function _addError(array $error)
	{
		array_push($error, $this->errors);
	}
	
	/**
	 * Check to see if a war is taking place
	 * NOTE: A war occurs when both cards are of equal value
	 * 
	 * @return boolean
	 */
	private function _checkForWar()
	{
		$fop = $this->fieldOfPlay->getStorage();
		if ( is_array($fop) && count($fop) >= 2 ) {
			if (count($this->players[1]->getHand()) <= 0 || count($this->players[2]->getHand()) <= 0) {
				$this->atWar = -1;
				$this->roundWinner = -1;
			} else {
			
				if ( $fop[count($fop)-2]->getValue() == $fop[count($fop)-1]->getValue() ) {
					$this->atWar = 1;
				} else {
					$this->roundWinner = -1;
					$this->atWar = -1;
				}
			}
		} else {
			$error = array(
				'function'	=>	'_checkForWar',
				'params'	=>	array(),
				'message'	=>	"Storage array less than two"
			);
			
			$this->_addError($error);
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
			$x = 1;
			foreach($players as $player) {
				$this->players[$x] = $player;
				$x++;
			}
		} else {
			$error = array(
				'function'	=>	'_createPlayers',
				'params'	=>	$players,
				'message'	=>	'Error: Not an array or empty array'
			);
			
			$this->_addError($error);
		}
	}
	
	/**
	 * Determines if there is a round winner or a war
	 */
	private function _findRoundWinner()
	{
		$this->_checkForWar();
		
		// Not a new war
		if ($this->atWar != 1) {			
			$fop = $this->fieldOfPlay->getStorage();			
			if (is_array($fop)) {
				if ($fop[count($fop)-2]->getValue() > $fop[count($fop)-1]->getValue()) {
					$this->roundWinner = 1;
				} else if ($fop[count($fop)-2]->getValue() < $fop[count($fop)-1]->getValue()) {
					$this->roundWinner = 2;
				} else {
					$error = array(
						'function'	=>	'_findRoundWinner',
						'params'	=>	array(),
						'message'	=>	'Unexpected error occurred'
					);

					$this->_addError($error);
				}
			}
		}
	}
	
	/**
	 * Sets the details according to the type passed
	 * Options:
	 *	1. gamewin
	 *  2. finish
	 *  3. play
	 *  4. quit
	 * 
	 * @param type $type
	 */
	private function _setDetails($type)
	{
		// Create the error template 
		$error = array(
			'function'	=>	'_setDetails',
			'params'	=>	array(
				'type'	=>	$type,
			),
		);		
		
		switch($type) {
			case "gamewin":
				if ( isset($this->players[$this->gameWinner]) ) {
					$winMessage  = "Player #" . $this->gameWinner . " has won the game!\n\n";
					$winMessage .= "	Total Score: " . $this->players[$this->gameWinner]->getScore() . "\n";
					$winMessage .= "	Games Played: " . $this->games . "\n";
					$winMessage .= "	Rounds Played: " . $this->rounds;
				} else {
					$error['message'] = 'Array index out of bounds';
					$this->_addError($error);
				}
			
				$this->details = array(
					'status'		=>	1,
					'p1'			=>	$this->players[1]->toArray(),
					'p1CardCount'	=>	count($this->players[1]->getHand()),
					'p2'			=>	$this->players[2]->toArray(),
					'p2CardCount'	=>	count($this->players[2]->getHand()),
					'gWinner'		=>	$this->gameWinner,
					'rWinner'		=>  $this->roundWinner,
					'winMessage'	=>	( isset($winMessage) ) ? $winMessage : ""
				);
				break;
			case "play":
				if ( count($this->fieldOfPlay->getStorage()) >= 2 ) {
					$cards = $this->fieldOfPlay->getTopCards();
				} else {
					$cards = null;
				}
				
				$this->details = array(
					'status'		=>	1,
					'p1'			=>	$this->players[1]->toArray(),
					//'p1CardCount'	=>	count($this->players[1]->getHand()),
					'p2'			=>	$this->players[2]->toArray(),
					//'p2CardCount'	=>	count($this->players[2]->getHand()),
					'p1PlayCard'	=>	( $cards[0] instanceof Card ) ? $cards[0]->toArray() : '',
					'p2PlayCard'	=>	( $cards[1] instanceof Card ) ? $cards[1]->toArray() : '',
					'isWar'			=>	$this->atWar,		
					'rWinner'		=>	$this->roundWinner,
					'rounds'		=>	$this->rounds
				);
				break;
			case "finish":
				$this->details = array(
					'status'	=>	1,					
					'gWinner'	=>	$this->gameWinner,					
				);
				break;
			case "quit":
				$message  = "Game Details\n";
				$message .= "------------------\n";
				for($x = 1; $x <= count($this->players); $x++) {
					$message .= "	Player #$x won " . $this->players[$x]->getRoundsWon() . " round(s)!\n";
					$message .= "	Player #$x Total Score: " . $this->players[$x]->getScore() . "\n";
					$message .= "----------------------------------------------------------------------\n";
				}

				$message .= "Total Games Played: " . $this->games . "\n";
				$message .= "Total Rounds Played: " . $this->rounds;
				
				$this->details = array(
					'message'	=> $message
				);				
				break;
			default:
				$error['message'] = "Unknown type passed";				
				$this->_addError($error);
		}
	}
	
	/**
	 * Moves the top card from each player's deck to the field of play
	 * NOTE: If there is a war, we take two cards from each deck
	 * (Symbolizing one face down from each and one face up)
	 */
	private function _transferTopCardToFOP()
	{		
		// The limit is set based on war or normal hand
		$limit = ($this->atWar == 1) ? 2: 1;
		$cards = array();
		
		// Loop through players
		for($x = 1; $x <= $limit; $x++) {
			if ( isset($this->players[1]) && count($this->players[1]->getHand()) >= 1 ) {
				array_push($cards, $this->players[1]->getTopCard());
				$this->players[1]->removeCardFromHand();
			} else {
				$error = array(
					'function'	=>	'_transferTopCardsToFOP',
					'params'	=>	array(),
					'message'	=>	'Array index out of bounds: 1'
				);

				$this->_addError($error);
			}
			
			if ( isset($this->players[2]) && count($this->players[2]->getHand()) >= 1 ) {
				array_push($cards, $this->players[2]->getTopCard());
				$this->players[2]->removeCardFromHand();
			} else {
				$error = array(
					'function'	=>	'_transferTopCardsToFOP',
					'params'	=>	array(),
					'message'	=>	'Array index out of bounds: 2'
				);

				$this->_addError($error);
			}
		}		

		// Move the cards to storage (fieldOfPlay)
		$this->fieldOfPlay->addToStorage($cards);
	}
	
	/**
	 * Moves the cards in storage (fieldOfPlay) to the winner's hand
	 * 
	 * @param int $index
	 */
	private function _transferFOPToPlayer($index)
	{
		$cards = $this->fieldOfPlay->getStorage();
		if ( is_array($cards) ) {
			if ( isset($this->players[$index]) ) {							
				$this->fieldOfPlay->addStorageToPlayer($this->players[$index], $cards);
			} else {
				$error = array(
					'function'	=>	'_transferFOPToPlayer',
					'params'	=>	array(
						'index'	=>	$index
					),
					'message'	=>	'Array index out of bounds'
				);
				$this->_addError($error);
			}
		} 
	}
}