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
	
	/**
	 * Initialize the session if it's not already
	 * Initialize the game instance components
	 */
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

	/**
	 * Functionality for dealing the cards
	 * @return json
	 */
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
	 * Functionality for determining who won
	 * @return json
	 */
	public function getwinnerAction()
	{
		$wargame	= $this->_load('wargame');
		$status		= 0;
		
		$wargame->checkForGameWinner();
		$wargame->setGameWinner();
		
		if ( count($wargame->getErrors()) > 0 ) {
			$errors = $wargame->getErrorsAsString();
			echo json_encode(array('status' => $status, 'message' => $errors));
		} else {
			$this->_save(array('wargame', $wargame));
			echo $wargame->getDetails();
		}
		return $this->response;
	}
	
	/**
	 * Handles the mechanics for the first portion of play
	 * NOTE: The play is two-parts. 
	 * One displays the hand. The other wraps up shifting cards, etc.
	 * 
	 * @return json
	 */
	public function playAction()
	{
		// Retrieve the game object from session
		$wargame		= $this->_load('wargame');
		$status			= 0;
		
		$wargame->playHand();
		if ( count($wargame->getErrors()) >= 1 ) {
			$errors = $wargame->getErrorsAsString();			
			echo json_encode(array('status' => $status, 'message' => $errors));
		} else {
			$this->_save(array('wargame', $wargame));
			echo $wargame->getDetails();
		}
		return $this->response;
	}
	
	/**
	 * Handles the mechanics for the second portion of play
	 * NOTE: The play is two-parts. 
	 * One displays the hand. The other wraps up shifting cards, etc.
	 * 
	 * @return json
	 */
	public function finishAction()
	{
		$wargame = $this->_load('wargame');
		$status  = 0;
		
		$wargame->finishHand();
		if ( count($wargame->getErrors()) >= 1 ) {
			$errors = $wargame->getErrorsAsString();			
			echo json_encode( array('status' => $status, 'message' => $errors) );
		} else {
		
			$this->_save(array('wargame', $wargame));
			echo $wargame->getDetails();
		}
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
	 * Handles displaying details when the player quits
	 * 
	 * @return json
	 */
	public function quitAction()
	{
		$wargame = $this->_load('wargame');
		$wargame->quit();
		
		echo json_encode( $wargame->getDetails() );
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