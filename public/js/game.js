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
	
	if (clearScore) {
		$('#player1Score').html('0');
		$('#player2Score').html('0');
	}
}

function deal() {
	$.ajax({
		type: 'post',
		url: 'application/index/deal',
		dataType: 'json',
		success: function(json) {
			if (json.status == 1) {
				showDealAnim();
			} else {
				alert("There was an unexpected error. Please try resetting the game.");
				return false;
			}			
		},
		beforeSend: function() {
			isReset = false;
		}
	});
}

function dealPlayer1() {
	showImage('p1HandImage', cardName, 'player1Hand');
}

function dealPlayer2() {
	showImage('p2HandImage', cardName, 'player2Hand');
}

function hidePlayButtons() {
	$('#bottomButton').fadeOut();
}

function showPlayButtons() {
	$('#bottomButton').fadeIn();
}

function hideAnim() {
	$('#animDiv').html('');
	$('#animDiv').attr('class', 'invisible');
}

function hideMessage() {
	$('#message').html('');
}

function hidePlay() {
	$('#player1Play').html('');
	$('#player2Play').html('');
}

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
			endGame = 0;
			hidePlayButtons();
		}
	});
}

function playHand(autoPlay) {
	if (autoPlay == undefined) {
		autoPlay = 0;
	}
	
	if (!isReset) {
		$.ajax({
			type: 'post',
			url: 'application/index/play',
			dataType: 'json',
			data: { isWar:	(isWar ? "true" : "false") },
			success: function(json) {
				if (json.status == 1) {
					// Check to see if the game should end
					if (json.winner != 0) {
						hidePlayButtons();					

						// Determine who the winner is
						// display the appropriate information to the screen
						switch(json.winner) {
							case 1:							
								endGame = 1;
								$('#player1Score').html(json.p1.score);
								$('#player2Score').html(json.p2.score);
								clearGameField();
								showPlayer1WinsGame();
								return;
								break;
							case 2:							
								endGame = 1;
								showImage('p1ayer1Image', 'count_0', 'player1Hand');
								$('#player1Count').html('Cards: 0');
								$('#player1Score').html(json.p1.score);
								showImage('player2Image', 'count_52', 'player2Hand');
								$('#player2Count').html('Cards: 52');
								$('#player2Score').html(json.p2.score);
								clearGameField();
								showPlayer2WinsGame();
								return;	
								break;
						}
					} else {
						showImage('p1PlayImage', json.p1.topCard.imgPath, 'player1Play');
						showImage('p2PlayImage', json.p2.topCard.imgPath, 'player2Play');
						var p1Count = parseInt(json.p1.cardCount);
						var p2Count = parseInt(json.p2.cardCount);

						showImage('player1Image', 'count_' + p1Count, 'player1Hand');
						showImage('player2Image', 'count_' + p2Count, 'player2Hand');

						$('#player1Count').html('Cards: ' + json.p1.cardCount);
						$('#player1Score').html(json.p1.score);
						$('#player2Count').html('Cards: ' + json.p2.cardCount);
						$('#player2Score').html(json.p2.score);

						// Do we have a war?
						if (json.isWar == 1) {
							isWar = 1;
							showWarMessage();
						// If not, we need to show who won
						} else {
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

							setTimeout(hidePlay, animSpeed);
						}

						// Continue automatic play if not end of the game and automatic play is selected
						if (!endGame && autoPlay) {
							setTimeout("playHand(1)", animSpeed);
						}
					}
				} else {
					alert("An error occurred. Please reset and try again.");
					return false;
				}
			},
			beforeSend: function() {
				hidePlayersWinMsgs();
			}
		});
	}
}

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

function removeFromDeck() {
	var name = "count_" + deckCounter;
	showImage('deckImage', name, 'deckHolder');
}

function resetDeck() {
	$.ajax({
		type: 'post',
		url: 'application/index/reset',
		success: function() {
			showImage('deckImage', 'count_52', 'deckHolder');
			clearGameField(true);
			deckCounter = 52;
		},
		beforeSend: function() {
			isReset = true;
			hidePlayersWinMsgs();
			promptForSpeed();
			endGame = 0;
			hidePlayButtons();
		}
	});
}

function setSpeed(num) {
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
		
	$('#speedPrompt').fadeOut(1000);
	setTimeout("$('#speedPrompt').dialog('close')", 500);
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

function hidePlayersWinMsgs() {
	$('#message').html('');
	$('#p1WinMessage').hide();
	$('#p2WinMessage').hide();
}

function hideInstructions() {
	$('#instructions').fadeOut(1000);
	setTimeout("$('#instructions').dialog('close')", 950);
}

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

function showPlayer1WinsHand() {
	//$('#message').animate({fontSize: "20pt", left: "40%", top: "50%"}, msgSpeed);
	$('#p1WinMessage').show();
}

function showPlayer1WinsGame() {
	var score = $('#player1Score').html();
	alert("Player #1 has won the game!\nTotal Score: " + score);
	//$('#message').animate({fontSize: "80pt", left: "10%"}, 500 );
	//$('#message').animate({fontSize: "60pt", left: "25%", top: "30%"}, 500 );
}

function showPlayer2WinsHand() {
	//$('#message').animate({fontSize: "20pt", left: "40%", top: "50%"}, msgSpeed);
	$('#p2WinMessage').show();	
}

function showPlayer2WinsGame() {
	var score = $('#player2Score').html();
	alert("Player #2 has won the game!\nTotal Score: " + score);
	//$("#message").html('Player #2 wins the game!!!');
	//$('#message').animate({fontSize: "80pt", left: "10%"}, 500 );
//	$('#message').animate({fontSize: "60pt", left: "25%", top: "30%"}, 500 );
}

function showWarMessage() {
	$('#p1WinMessage').hide();
	$('#p2WinMessage').hide();
	
	$("#message").html('WAR!!!');
	$('#message').animate({fontSize: "100pt", left: "40%"}, animSpeed );
	$('#message').animate({fontSize: "80pt", left: "40%", top: "30%"}, animSpeed );
}

function shuffle() {
	$.ajax({
		type: 'post',
		url: 'application/index/shuffle',
		data: { action: 'shuffle' },
		success: function() {
			window.setTimeout("hideAnim()", 1200);
		},
		beforeSend: function() {	
			showAnim('shuffle');
		}		
	});
}