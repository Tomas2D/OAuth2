<?php
namespace Drahak\OAuth2\Grant;

use Drahak\OAuth2\InvalidStateException;
use Drahak\OAuth2\Storage;
use Drahak\OAuth2\Storage\AccessToken;
use Drahak\OAuth2\Storage\RefreshTokenFacade;
use Drahak\OAuth2\Storage\ITokenFacade;

/**
 * AuthorizationCode
 * @package Drahak\OAuth2\Grant
 * @author Drahomír Hanák
 */
class AuthorizationCode extends GrantType
{

	/** @var array */
	private $scope = array();

	/** @var Storage\AuthorizationCodes\AuthorizationCode */
	private $entity;

	/**
	 * @return array
	 */
	protected function getScope()
	{
		return $this->scope;
	}

	/**
	 * Get authorization code identifier
	 * @return string
	 */
	public function getIdentifier()
	{
		return self::AUTHORIZATION_CODE;
	}

	/**
	 * Verify request
	 * @throws Storage\InvalidAuthorizationCodeException
	 */
	protected function verifyRequest()
	{
		$code = $this->input->getParameter('code');

		$this->entity = $this->token->getToken(ITokenFacade::AUTHORIZATION_CODE)->getEntity($code);
		$this->scope = $this->entity->getScope();

		$this->token->getToken(ITokenFacade::AUTHORIZATION_CODE)->getStorage()->remove($code);
	}

	/**
	 * Verify access token
	 * @throws Storage\InvalidAuthorizationCodeException
	 */
	public function verifyToken($access_token)
	{
		try {
			$entity = $this->token->getToken(ITokenFacade::ACCESS_TOKEN)->getEntity($access_token);

			if ($entity instanceof Storage\AccessTokens\AccessToken) {

			}

			return array(
				'active' => true,
				'access_token' => $entity->getAccessToken(),
				'token_type' => 'bearer',
				'expires_in' => $entity->getExpires(),
				'scope' => $entity->getScope(),
				'username' => $entity->getUsername()
			);

		}
		catch (Storage\InvalidAccessTokenException $e) {
		}
		catch (InvalidStateException $e) {
		}

		return array(
			'active' => FALSE,
			'access_token' => NULL,
			'token_type' => NULL,
			'expires_in' => NULL,
			'refresh_token' => NULL,
			'scope' => NULL,
			'username' => NULL
		);

	}

	/**
	 * Generate access token
	 * @return string
	 */
	protected function generateAccessToken()
	{
		$client = $this->getClient();
		$accessTokenStorage = $this->token->getToken(ITokenFacade::ACCESS_TOKEN);
		$refreshTokenStorage = $this->token->getToken(ITokenFacade::REFRESH_TOKEN);

		$accessToken = $accessTokenStorage->create($client, $this->user->getId() ?: $this->entity->getUserId(), $this->getScope());
		$refreshToken = $refreshTokenStorage->create($client, $this->user->getId() ?: $this->entity->getUserId(), $this->getScope());

		return array(
			'active' => true,
			'access_token' => $accessToken->getAccessToken(),
			'token_type' => 'bearer',
			'expires_in' => $accessTokenStorage->getLifetime(),
			'refresh_token' => $refreshToken->getRefreshToken(),
			'username' => $this->entity->getUsername()
		);

	}

}