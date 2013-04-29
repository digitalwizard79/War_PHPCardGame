/**
 * Resets all of the fields to default
 */
function clearGameField(clearScore) {
	$('#player1Hand').html('');
	$('#player1Play').html('');
	$('#player1Count').html('');
	$('#player2Hand').html('');
	$('#player2Play').html('');
	$('#player2Count').html('');
	$('#p1WinMessage').fadeOut();
	$('#p2WinMessage').fadeOut();
	$('#message').html('');
	
	// Clears the score if reset button was pressed
	if (clearScore) {
		$('#player1Score').html('0');
		$('#player2Score').html('0');
	}
}

/**
 * AJAX call for dealing cards
 */
function deal() {
	$.ajax({
		type: 'post',
		url: 'application/index/deal',
		dataType: 'json',
		success: function(json) {
			if (json.status === 1) {
				showDealAnim();
				$('#quitButton').fadeIn(500);
			} else {
				alert("There was an unexpected error. Please try resetting the game.");
				return false;
			}			
		},
		beforeSend: function() {
			isHalted = 0;
		}
	});
}

/**
 * Deal animation for player 1's hand
 */
function dealPlayer1() {
	showImage('p1HandImage', cardName, 'player1Hand');
}

/**
 * Deal animation for player 2's hand
 */
function dealPlayer2() {
	showImage('p2HandImage', cardName, 'player2Hand');
}

/**
 *	AJAX call for second section of the hand
 */
function finishHand() {
	$.ajax({		
		type: 'post',
		url: 'application/index/finish',
		datatype: 'json',
		success: function(json) {
			//hidePlayButtons();
			var status	= $.parseJSON(json).status;			
			
			if (status !== 1) {
				alert("An unknown error occurred.");
				isHalted = 1;
				return;
			}
		}
	});
}

/**
 * AJAX call for getting the winner details
 */
function getWinner() {
	$.ajax({
		type: 'post',
		url: 'application/index/getwinner',
		dataType: 'json',
		success: function(json) {
			if (json.status === 1) {
				if (json.gWinner !== 0) {
					hidePlayButtons();
					hidePlay();					
					switch(json.gWinner) {
						case 1:				
							$('#player1Score').html(json.p1.score);							
							$('#player2Count').html('Cards: ' + 52);
							break;
						case 2:							
							$('#player2Score').html(json.p2.score);
							$('#player1Count').html('Cards: ' + 52);							
							break;
					}
					
					alert(json.winMessage);
					rounds = 0;
				}
			} else {
				alert("An unexpected error ocurred");
				return false;
			}
		}
	});
}

/**
 * Hides the animation
 * NOTE: Used for shuffling animation
 */
function hideAnim() {
	$('#animDiv').html('');
	$('#animDiv').attr('class', 'invisible');
}

/**
 * Hides the instructions
 */
function hideInstructions() {
	$('#instructions').fadeOut(1000);
	setTimeout("$('#instructions').dialog('close')", 950);
}

/**
 * Hides the bottom row of buttons
 */
function hidePlayButtons() {
	$('#bottomButton').fadeOut();
}

/**
 * Hides the cards in play
 * @returns {undefined}
 */
function hidePlay() {
	$('#player1Play').html('');
	$('#player2Play').html('');
}

/**
 * Hides the player wins messages
 */
function hidePlayersWinMsgs() {	
	$('#p1WinMessage').hide();
	$('#p2WinMessage').hide();
}

/**
 * AJAX call for starting a new game
 * @returns {undefined}
 */
function newGame() {
	$.ajax({
		type: 'post',
		url: 'application/index/new',
		success: function() {
			showImage('deckImage', 'count_52', 'deckHolder');
			clearGameField(false);
			deckCounter = 52;			
		},
		beforeSend: function() {
			isReset = true;
			hidePlayersWinMsgs();
			promptForSpeed();
			isHalted  = 0
			hasWinner = 0;
			hidePlayButtons();
		}
	});
}

/**
 * A prompt to allow the user to choose speed of automatic game
 * @returns {undefined}
 */
function promptForSpeed() {
	$('#speedPrompt').fadeIn(1000);
	$('#speedPrompt').dialog({
		width: 'auto',
		height: 'auto',
		resizable: false,
		draggable: false,
		modal: true
	});
	$('.ui-dialog-titlebar').hide();
}

/**
 * AJAX call to get details when player quits game
 */
function quitGame() {
	$.ajax({
		async: 'false',
		type: 'post',
		url: 'application/index/quit',
		dataType: 'json',
		success: function(json) {
			message = $.parseJSON(json).message;
			alert(message);			
			window.location.reload();
		}
	});
}

/**
 * Animation for removing cards from deck
 */
function removeFromDeck() {
	var name = "count_" + deckCounter;
	showImage('deckImage', name, 'deckHolder');
}

/**
 * Resets the deck to 52 cards and empties the players' hands
 */
function resetDeck(noPrompt) {
	if (noPrompt === undefined) {
		noPrompt = 0;
	}
	
	$.ajax({
		type: 'post',
		url: 'application/index/reset',
		success: function() {
			showImage('deckImage', 'count_52', 'deckHolder');
			clearGameField(true);
			deckCounter = 52;
		},
		beforeSend: function() {
			isHalted = 0;
			hidePlayersWinMsgs();
			if (noPrompt == 0) {
				promptForSpeed();
			}
			hasWinner = 0;
			hidePlayButtons();
		}
	});
}

/**
 * Sets the speed of the automatic game
 * @param int num
 */
function setSpeed(num) {
	if (speed === 0) {
		speed = num;
	}
	
	if (autoMode) {
		switch(num) {
			case 1:
				animSpeed = 3000;
				msgSpeed  = 1000;
				break;
			case 2:
				animSpeed = 1000;
				msgSpeed  = 250;
				break;
			case 3:
				animSpeed = 100;
				msgSpeed  = 5;
				break;
		}
	} else {
		animSpeed = 3000;		
	}
		
	$('#speedPrompt').fadeOut(1000);
	setTimeout("$('#speedPrompt').dialog('close')", 500);
}

/**
 * Display animation
 * NOTE: Used for shuffling
 * @param string anim 
 */
function showAnim(anim) {
	if (anim != undefined) {
		var label = '';
		var img = $('<img id="animImage" />')
		img.attr('src', "img/ajax-loader-big.gif");
		img.attr('alt', 'Animation');
		
		switch(anim) {
			case "shuffle":
				label = $('<label>' + 'Shuffling cards...' + '</label>');
				break;
		}
		
		label.appendTo( $('#animDiv') );
		img.appendTo( label );
		$('#animDiv').attr('class', 'visible');
	} else {
		alert("An unexpected error ocurred. Please contact Tech Support.");
		return false;
	}
}

/**
 * Plays the deal animation
 */
function showDealAnim() {
	y = deckCounter;
	if (counter <= 26) {
		cardName = "count_" + counter;
		dealPlayer1();
		setTimeout(dealPlayer2, 50);
		window.setTimeout(showDealAnim, 50);
		y -= 2;
		$('#player1Count').html('Cards: ' + counter);
		$('#player2Count').html('Cards: ' + counter);
		removeFromDeck(y);
		deckCounter = y;
		counter++;
	} else {
		$('#deckHolder').html('');
		deckCounter = 0;
		counter = 1;
		showPlayButtons();
	}	
}

/**
 * Displays the instructions for the game
 */
function showInstructions() {
	$('#instructions').fadeIn(1000);
	$('#instructions').dialog({
		width: 'auto',
		height: 'auto',
		resizable: false,
		draggable: false,
		modal: true
	});
	$('.ui-dialog-titlebar').hide();
}

/**
 * Loads and displays an image
 * 
 * imgId: id of the image to create
 * imgPath: name of the image (less the .png extension)
 * divId: id of the div to append the image to
 */
function showImage(imgId, imgPath, divId) {
	$('#' + divId).html('');
	var img = $("<img id='" + imgId + "'>");
	img.attr('src', '/img/cards/' + imgPath + '.png');
	//img.attr('alt', 'card image');
	img.appendTo($('#' + divId));
}

/**
 * Displays the buttons on the bottom of the screen
 */
function showPlayButtons() {
	$('#bottomButton').fadeIn();
}

/**
 * AJAX call for first portion of the hand
 * NOTE: This function handles displaying the cards in play,
 * removing the cards from the players' hands and updating the card count
 */
function startHand() {
	if (isHalted == 0 || hasWinner == 0) {
		$.ajax({
			type: 'post',
			url: 'application/index/play',
			dataType: 'json',
			success: function(json) {
				if (json.status == 1) {
					rounds = json.rounds;

					if (json.p1PlayCard !== undefined && json.p2PlayCard !== undefined) {
						showImage('p1PlayImage', json.p1PlayCard.imgPath, 'player1Play');
						showImage('p2PlayImage', json.p2PlayCard.imgPath, 'player2Play');
					}

					var p1Count = parseInt(json.p1.cardCount);
					var p2Count = parseInt(json.p2.cardCount);					
					showImage('player1Image', 'count_' + p1Count, 'player1Hand');
					showImage('player2Image', 'count_' + p2Count, 'player2Hand');

					$('#player1Count').html('Cards: ' + p1Count);
					$('#player1Score').html(json.p1.score);
					$('#player2Count').html('Cards: ' + p2Count);
					$('#player2Score').html(json.p2.score);

					if (p1Count <= 0 || p2Count <= 0) {
						getWinner();
						return false;
					}

					// Do we have a war?
					if (json.isWar == 1) {
						isWar = 1;
						showWarMessage();
					// If not, we need to show who won
					} else {
						hidePlayersWinMsgs();
						switch(json.rWinner) {
							case 1:	
								isWar = 0;
								showPlayer1WinsHand();									
								break;
							case 2:								
								isWar = 0;
								showPlayer2WinsHand();
								break;
						}							
					}						

					// Continue automatic play if not end of the game and automatic play is selected
					if (autoMode) {
						setTimeout("startHand()", animSpeed);
					}		
				} else {
					alert("An error occurred. Please reset and try again.");
					return false;
				}
			},
			beforeSend: function() {
				hidePlayersWinMsgs();
				if (rounds >= 1) {
					finishHand();
				}
			}
		});
	}
}

/**
 * Displays the Player 1 win graphic
 */
function showPlayer1WinsHand() {
	//$('#message').animate({fontSize: "20pt", left: "40%", top: "50%"}, msgSpeed);
	$('#p1WinMessage').show();
}

/**
 * Displays the Player 2 win graphic
 */
function showPlayer2WinsHand() {
	$('#p2WinMessage').show();	
}

/**
 * Displays the "War" graphic
 * @returns {undefined}
 */
function showWarMessage() {
	alert("War!!!");	
}

/**
 * AJAX call to shuffle the deck
 */
function shuffle() {
	$.ajax({
		type: 'post',
		url: 'application/index/shuffle',
		data: { action: 'shuffle' },
		success: function() {
			window.setTimeout("hideAnim()", 1800);
		},
		beforeSend: function() {	
			showAnim('shuffle');
		}		
	});
}