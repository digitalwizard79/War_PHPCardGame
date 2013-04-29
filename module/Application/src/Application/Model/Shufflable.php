<?php

namespace Application\Model;

/**
 * Interface for shuffling an array
 * NOTE: This is not particularly neccessary; I wanted to point
 * out that I understood the use of interfaces
 * 
 * @author Thomas Powers <digitalwizard79@gmail.com>
 */
interface Shufflable
{
	/**
	 * Must be defined by class that implements Shufflable
	 */
	public function shuffle();
}