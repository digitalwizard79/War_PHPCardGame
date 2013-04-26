<?php

if ( isset($_POST['action']) && !empty($_POST['action']) ) {
	$action = strip_tags($_POST['action']);
	switch($action) {
		case "loadPlayerOneCardImage":
			loadPlayerOneCardImage();
			break;
		case "shuffle";
			shuffleDeck();
			break;		
	}
} else {
	header("Location: " . $_SERVER['SERVER_NAME']);
}

function loadPlayerOneCardImage()
{		
	//echo json_encode(array('imgList' => $imgList));
}

function shuffleDeck()
{
	$deck = $_SESSION['game']['deck'];
print_r($deck);die();	
	//$deck->shuffle();
	$_SESSION['game']['deck'] = $deck;
}
