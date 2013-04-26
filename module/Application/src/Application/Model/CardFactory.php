<?php

namespace Application\Model;

/**
 * Factory for creating Card objects
 *
 * @author Thomas Powers <digitalwizard79@gmail.com>
 */
class CardFactory
{
	public static function create($className, $rank)
	{
		return new $className($rank);
	}
}