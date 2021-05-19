<?php

namespace SteamAuth;

/**
 * Handle response from steam after login, this should be used on the return to
 * page specified when making the request.
 *
 * @author Jacob Fitzpatrick <contact@jacobfitzpatrick.co.uk>
 * @package SteamAuth
 */
class Response
{
	/** @var string Steam API key */
	protected $api_key;
	/** @var array Response errors */
	protected $errors = [];
	/** @var bool Is response valid */
	protected $valid = false;

	/**
	 * @param string $api_key Steam API key - must match the API key used to make
	 * the initial request.
	 */
	public function __construct (string $api_key)
	{
		$this->api_key = $api_key;

		/* Get the request (supports GET and POST) */
		$response = $_REQUEST;

		/* Get the steam ID */
		$id = $response['openid_claimed_id'] ?? '';

		/* Check if we have a claimed ID */
		// todo: Add some more validation here, just checking the claimed id is insecure
		if (!empty($id)) {

			/* Get ID from open id response */
			preg_match("/^https?:\/\/steamcommunity\.com\/openid\/id\/(7[0-9]{15,25}+)$/", $id, $matches);

			/* Fetch user details from steam API */
			$request = new Request($api_key, '');
			$info = $request->getUser($matches[1]);

			/**
			 * Check if details are set.
			 * If we can't get any details we probably arent authorised to access
			 * this users account, meaning login has failed or this is a spoofed
			 * request.
			 */
			if (!empty($info)) {

				/* Set user in session */
				$_SESSION['steamAuth'] = [
					'user' => $info,
					'valid' => true,
					'api_key' => $api_key,
					'id' => $matches[1]
				];

				/* Response is valid */
				$this->valid = true;
			} else {

				/* We were unable to fetch information for the given steam ID */
				$this->errors[] = 'Could not fetch user details';
			}
		} else {

			/* The response contains no valid steam ID */
			$this->errors[] = 'Response contains no claimed id';
		}

		/**
		 * If the response wasn't valid but the user is logged in then we should
		 * set it as valid anyway.
		 */
		if (!$this->valid && User::isLoggedIn()) {
			$this->valid = true;
		}
	}

	/**
	 * Get user if login is successful
	 *
	 * @return User|null
	 */
	public function getUser(): ?User
	{
		/**
		 * If the user is now logged in return an instance of the user class.
		 * User details should be stored in the session at this point it the
		 * login was successful.
		 */
		if (User::isLoggedIn()) {
			return User::getCurrent();
		}

		/* Return null if we don't have a user */
		return null;
	}

	/**
	 * Get errors from failed login attempt, will return empty array if login
	 * was successful.
	 *
	 * @return array Errors returned by steam
	 */
	public function getErrors(): array
	{
		return $this->errors;
	}

	/**
	 * Validate the response from steam and make sure login has been successful.
	 * If login is not successful see $this->getErrors()
	 *
	 * @return bool Is response valid
	 * @see getErrors()
	 */
	public function isValid(): bool
	{
		return $this->valid;
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