<?php

namespace SteamAuth;

use ErrorException;
use LightOpenID;

/**
 * Request authentication
 *
 * @author Jacob Fitzpatrick <contact@jacobfitzpatrick.co.uk>
 * @package SteamAuth
 */
class Request
{
	/** @var string Steam API key */
	protected $api_key;
	/** @var string Page to return to on successful redirect  */
	protected $return_to;

	/**
	 * Create new steam login request
	 *
	 * @param string      $api_key   Steam API key
	 * @param string|null $return_to Page to return to after login
	 */
	public function __construct(string $api_key, ?string $return_to = null)
	{
		$this->api_key = $api_key;
		$this->return_to = $return_to;
	}

	/**
	 * Get user
	 *
	 * @param string $id
	 *
	 * @return array
	 */
	public function getUser(string $id): array
	{
		// todo: Replace this with my upcoming steam API package
		$content = json_decode(file_get_contents(
			'https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=' . $this->api_key . '&steamids=' . $id
			),
		true
		);

		return $content['response']['players'][0];
	}

	/**
	 * Redirect the user to the steam login page
	 */
	public function redirectToLogin(): void
	{
		/* Redirect with "see other" response */
		http_response_code(303);
		header('Location: ' . $this->getLoginURL());

		exit;
	}

	/**
	 * Set return URL for login
	 *
	 * @param string $return_url
	 */
	public function setReturnURL(string $return_url): void
	{
		$this->return_to = $return_url;
	}

	/**
	 * Get URL used for steam login, send the user to this URL to login.
	 *
	 * @return string Steam login URL
	 * @throws ErrorException
	 */
	public function getLoginURL(): string
	{
		/* Open ID */
		$open_id = new LightOpenID($this->getSiteURL());
		$open_id->identity = 'https://steamcommunity.com/openid';
		$open_id->returnUrl = $this->formatReturnURL();

		/* Return login URL */
		return $open_id->authUrl();
	}

	/**
	 * Format the return to URL
	 *
	 * @return string
	 */
	private function formatReturnURL(): string
	{
		return $this->getSiteURL() . '/' . ltrim($this->return_to, '/');
	}

	/**
	 * Get full URL for the current site
	 *
	 * @return string
	 */
	private function getSiteURL(): string
	{
		return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
	}
}