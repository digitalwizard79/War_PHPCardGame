<?php

	function __autoload($class)
	{
		if (stristr($class, "_")) {
			$class = str_replace("_", "/", $class);
		}
		require "Classes/" . $class . ".php";
	}
	
	session_start();
	init();
	
	function init()
	{
		$deck = new Deck();
		$player1 = new Player();
		$player2 = new Player();
		$_SESSION['game']['deck'] = $deck;
	}
	
?>

<!DOCTYPE html>
<html>
	<head>
		<title>War: The Card Game</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		
		<!-- External Javascript Files -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
		<script src="js/game.js"></script>
		
		<!-- External CSS Files -->
		<link rel="stylesheet" type="text/css" href="css/style.css" />
		
		<script type="text/Javascript">
			$(document).ready(function() {
				// Show the end user the deck of cards
				showImage('deckImage', 'deckStack', 'deckHolder');
				
				$('#deckImage').click(function() {
					shuffle();
				})
			});			
				
		</script>
	</head>
	<body>
		<div id="wrapper">
			<h1>War: The Card Game</h1>
			<h2>Brought to you by Object-Oriented PHP!</h2>		
		</div>
		<div class="buttonRow" style="padding-top: 3%">
			<input type='button' class="buttonNormal" id="instrButton" name="instrButton" value="View Instructions" />
			<input type="button" class="buttonNormal" id="dealButton" name="dealButton" value="Deal Cards" />
			<input type="button" class="buttonRight" id="resetButton" name="resetButton" value="Reset Deck" />
		</div>
		<div>
			
			<span id="player1Hand" class="hidden"></span>
			<span id="player1Play" class="hidden"></span>
			
			<div id="deckHolder"></div>
			
			<span id="player2Play" class="hidden"></span>
			<span id="player2Hand" class="hidden"></span>
		</div>
		
		<div id="animDiv" class="hidden"></div>
	</body>
</html>