<?php
namespace Drahak\OAuth2\Storage\AccessTokens;

use DateTime;
use Nette\SmartObject;

/**
 * Base AccessToken entity
 * @package Drahak\OAuth2\Storage\AccessTokens
 * @author Drahomír Hanák
 *
 * @property-read string $accessToken
 * @property-read DateTime $expires
 * @property-read string|int $clientId
 * @property-read array $scope
 */
class AccessToken implements IAccessToken
{

	use SmartObject;

	/** @var string */
	private $accessToken;

	/** @var DateTime */
	private $expires;

	/** @var string|int */
	private $clientId;

	/** @var string|int */
	private $userId;

	/** @var array */
	private $scope;

	/** @var boolean */
	private $active;

	/** @var string */
	private $username;


	public function __construct($accessToken, DateTime $expires, $clientId, $userId, array $scope, $username = NULL)
	{
		$this->accessToken = $accessToken;
		$this->expires = $expires;
		$this->clientId = $clientId;
		$this->userId = $userId;
		$this->scope = $scope;
		$this->active = ($expires) > (new DateTime());
		$this->username = $username;
	}

	/**
	 * @return string
	 */
	public function getAccessToken()
	{
		return $this->accessToken;
	}

	/**
	 * @return int|string
	 */
	public function getClientId()
	{
		return $this->clientId;
	}

	/**
	 * @return int|string
	 */
	public function getUserId()
	{
		return $this->userId;
	}

	/**
	 * @return \DateTime
	 */
	public function getExpires()
	{
		return $this->expires;
	}

	/**
	 * @return array
	 */
	public function getScope()
	{
		return $this->scope;
	}

	/**
	 * @return int
	 */
	public function getIsActive()
	{
		return $this->active;
	}

	/**
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}

}