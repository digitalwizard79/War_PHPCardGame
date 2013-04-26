<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use \Exception;

use Application\Model\Card;
use Application\Model\Deck_WarDeck;
use Application\Model\Player;
use Application\Model\Game_WarGame;
use Application\Model\Storage_FieldOfPlay;

class IndexController extends AbstractActionController
{
	protected $session = null;	
	
	public function __construct()
	{	$this->session = null;
		if ( null === $this->session ) {
			$this->_initSession();
		}

		if ( null == $this->_load('wargame') ) {
			$this->_initWarGame();
		}		
	}
	
	public function indexAction()
    {	
		return new ViewModel();
    }

	public function dealAction()
	{
		$wargame	= $this->_load('wargame');
		$deck		= $wargame->getDeck();
		$status		= 1;
		
		// Deal the cards to each player
		$wargame->deal();
		
		if ( count($wargame->getErrors()) > 0 ) {
			$status = 0;
			$p1 = null;
			$p2 = null;
		}

		// Save our data to the session
		$this->_save(array('wargame' => $wargame));
		
		echo json_encode( array('status' => $status) );
		return $this->response;
	}
	
	/**
	 * Handles the mechanics for the field of play
	 */
	public function playAction()
	{
		// Retrieve the game object from session
		$wargame		= $this->_load('wargame');				
		$status			= 1;
		$message		= "";
		$roundWinner	= null;
		$winner			= 0;

		// Check to see if there is a winner
		if ( $wargame->checkForWinner() ) {
			$winner = $wargame->getWinner();
			$wargame->getPlayer($winner)->addScore(100);
			
			$this->_save(array('wargame' => $wargame));
			echo json_encode(array(
				'status' => $status, 'winner' => $winner, 'p1' => $wargame->getPlayer(1)->toArray(), 'p2' => $wargame->getPlayer(2)->toArray()
			));
			return $this->response;
		// No winner, so we continue
		} else {
			// Store the cards into a temporary array
			$cards = array(
				$wargame->getPlayer(1)->getTopCard(),
				$wargame->getPlayer(2)->getTopCard()
			);

			// Remove the cards from each player's hand  
			$wargame->getPlayer(1)->removeCardFromHand();
			$wargame->getPlayer(2)->removeCardFromHand();
			
			if ( $wargame->checkForWar() ) { 
				$message = "WAR!!!!";

				$wargame->getFieldOfPlay()->addToStorage($cards);				
			} else {								
				$roundWinner = $wargame->getRoundWinner();

				if ( is_numeric($roundWinner) ) {

					// If we are at war from last round, we need to
					// put the cards in storage into the player's hand
					if ( $this->params()->fromPost('isWar') == 'true' ) {
						$wargame->getFieldOfPlay()->addStorageToPlayer($wargame->getPlayer($roundWinner), true);
					}

					// Loop through the cards in play and add them
					// to the player's hand
					foreach($cards as $card) {
						$wargame->getPlayer($roundWinner)->addCardToHand($card, true);
					}				
				}				
			}
		}
	
		$errors = $wargame->getErrors();
		if ( !empty($errors) ) {
			$status = 0;
		}		
		
		// Clear the errors for next round
		$wargame->emptyErrors();
		
		$this->_save(array('wargame' => $wargame));
		
		echo json_encode(array(
			'status' => $status, 'p1' => $wargame->getPlayer(1)->toArray(), 'p2' => $wargame->getPlayer(2)->toArray(),
			'isWar' => $wargame->checkForWar(), 'message' => $message, 'rWinner' => $roundWinner, 'winner' => $winner
		));
		return $this->response;
	}
	
	/**
	 * Start a new game
	 * NOTE: Only the player's data will remain (To maintain the score). All else is re-initialized.
	 */
	public function newAction()
	{
		// We're about to wipe out the session
		// so let's store the existing score somewhere
		$wargame = $this->_load('wargame');
		
		// Destroy the current session
		unset($this->session);
		
		// Start a new session and initialize the game object again
		$this->_initSession();
		$wargame->newGame();

		// Save the game to session
		$this->_save(array('wargame' => $wargame));
		return $this->response;
	}
	
	/**
	 * Reset the game
	 * NOTE: This will destory the session, including players, deck, storage, and wargame. Everything is re-initialized
	 */
	public function resetAction()
	{
		// Destroy the session
		unset($this->session);
		
		// Restart the game and session
		$this->_initSession();
		$this->_initWarGame();
		
		// Disable the layout/view renderer for JSON/AJAX response
		return $this->response;
	}
	
	/**
	 * Shuffle the deck
	 */
	public function shuffleAction()
	{
		// Get the game object from the session
		$wargame = $this->_load('wargame');
		
		// Shuffle the deck
		$wargame->getDeck()->shuffle();
		
		// Save the game after shuffle
		$this->_save(array('wargame' => $wargame));
		
		// Disable layout & view renderer for JSON/AJAX response
		return $this->response;						
	}
	
	/**
	 * Initialize the Game instance and all of its dependencies
	 */
	private function _initWarGame()
	{
		// Initialize arguments
		$deck			= new Deck_WarDeck();		
		$player1		= new Player('Player #1');		
		$player2		= new Player('Player #2');		
		$fieldOfPlay	= new Storage_FieldOfPlay();

		// Create the WarGame object that will handle the game mechanics
		$wargame = new Game_WarGame(array($player1, $player2), $deck, $fieldOfPlay);
		
		// Save all of the objects in the session
		$this->_save(array('wargame' => $wargame));
	}
	
	/**
	 * Initialize the session if one doesn't exist already
	 */
	private function _initSession()
	{
		$this->session = new \Zend\Session\Container('game');
	}
	
	/**
	 * Loads a key from the current session
	 * @param string $key
	 * @return Object
	 */
	private function _load($key)
	{
		if ( $this->session == null) {
			$this->_initSession();
		}
		
		return $this->session->{$key};
	}
	
	/**
	 * Saves objects to the current session
	 * @param array of Objects
	 */
	private function _save(array $obj) 
	{
		// Start the session if it doesn't exist
		if ( $this->session == null) {
			$this->_initSession();			
		}
		
		foreach($obj as $key => $val) {
			$this->session->{$key} = $val;
		}
	}
}