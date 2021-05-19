<?php

namespace SteamAuth;

use DateTime;

/**
 * Steam user object.
 *
 * @author Jacob Fitzpatrick <contact@jacobfitzpatrick.co.uk>
 * @package SteamAuth
 */
class User
{
	/** @var mixed Claimed steam ID, used to get information about the user from API */
	protected $id;
	/** @var array Profile data array, as returned by steam API */
	protected $profile_data;

	/** @var string Steam user ID */
	public const ATTRIBUTE_STEAM_ID = 'steamid';
	/** @var string Profile community visibility  */
	public const ATTRIBUTE_COMMUNITY_VISIBLE = 'communityvisibilitystate';
	/** @var string Profile state */
	public const ATTRIBUTE_PROFILE_STATE = 'profilestate';
	/** @var string Profile username / display name */
	public const ATTRIBUTE_USERNAME = 'personaname';
	/** @var string Last time the user logged off on steam */
	public const ATTRIBUTE_LAST_LOGOFF = 'lastlogoff';
	/** @var string URL to steam profile page */
	public const ATTRIBUTE_URL = 'profileurl';
	/** @var string Default avatar */
	public const ATTRIBUTE_AVATAR = 'avatar';
	/** @var string Medium size avatar */
	public const ATTRIBUTE_AVATAR_MEDIUM = 'avatarmedium';
	/** @var string Full size avatar */
	public const ATTRIBUTE_AVATAR_FULL = 'avatarfull';
	/** @var string Profile state */
	public const ATTRIBUTE_STATE = 'personastate';
	/** @var string Users real name (not always available) */
	public const ATTRIBUTE_REAL_NAME = 'realname';
	/** @var string Primary clan ID */
	public const ATTRIBUTE_PRIMARY_CLAN = 'primaryclanid';
	/** @var string Time account was created */
	public const ATTRIBUTE_TIME_CREATED = 'timecreated';
	/** @var string Users country code */
	public const ATTRIBUTE_COUNTRY_CODE = 'loccountrycode';

	/** @var int Online status */
	public const STATE_OFFLINE = 0;
	/** @var int Offline status */
	public const STATE_ONLINE = 1;
	/** @var int Busy status */
	public const STATE_BUSY = 2;
	/** @var int Away status */
	public const STATE_AWAY = 3;
	/** @var int Snooze status */
	public const STATE_SNOOZE = 4;
	/** @var int Looking to trade status */
	public const STATE_LOOKING_TO_TRADE = 5;
	/** @var int Looking to play status */
	public const STATE_LOOKING_TO_PLAY = 6;

	/** @var int Profile is not visible to anyone */
	public const VISIBILITY_PRIVATE = 0;
	/** @var int Profile is only visible to certain people */
	public const VISIBILITY_FRIENDS_ONLY = 1;
	/** @var int Profile is visible to everyone */
	public const VISIBILITY_PUBLIC = 3;

	/** @var int Default avatar site 32 x 32 */
	public const AVATAR_SIZE_NORMAL = 1;
	/** @var int Medium avatar size 64 x 64 */
	public const AVATAR_SIZE_MEDIUM = 2;
	/** @var int Full avatar size 184 x 184 */
	public const AVATAR_SIZE_FULL = 3;

	/**
	 * User constructor.
	 * Pass data from the steam user session $_SESSION['steamAuth']['user']
	 *
	 * @param array $data
	 */
	public function __construct (array $data = [])
	{
		$this->id = $_SESSION['steamAuth']['id'];
		$this->profile_data = $data;
	}

	/**
	 * Get account attribute
	 *
	 * @param string $attribute
	 *
	 * @return mixed|null Attribute value
	 */
	public function getAttribute(string $attribute)
	{
		return $this->profile_data[$attribute] ?? null;
	}

	/**
	 * Get steam ID
	 *
	 * @return string Steam ID
	 */
	public function getSteamID(): string
	{
		return $this->getAttribute(self::ATTRIBUTE_STEAM_ID);
	}

	/**
	 * Get username
	 *
	 * @return string Username
	 */
	public function getUsername(): string
	{
		return $this->getAttribute(self::ATTRIBUTE_USERNAME);
	}

	/**
	 * Is the users real name available?
	 *
	 * @return bool
	 */
	public function hasRealName(): bool
	{
		return !empty($this->getAttribute(self::ATTRIBUTE_REAL_NAME));
	}

	/**
	 * Get the users real name
	 *
	 * @return string Real name
	 */
	public function getRealName(): string
	{
		return $this->getAttribute(self::ATTRIBUTE_REAL_NAME);
	}

	/**
	 * Get avatar
	 *
	 * @param int $size Size of avatar
	 *
	 * @return string Image URL
	 */
	public function getAvatar(int $size = self::AVATAR_SIZE_NORMAL): string
	{
		switch ($size) {
			case self::AVATAR_SIZE_MEDIUM:
				return $this->getAttribute(self::ATTRIBUTE_AVATAR_MEDIUM);
			case self::AVATAR_SIZE_FULL:
				return $this->getAttribute(self::ATTRIBUTE_AVATAR_FULL);
			default:
				return $this->getAttribute(self::ATTRIBUTE_AVATAR);
		}
	}

	/**
	 * Get URL to steam profile page
	 *
	 * @return string Profile URL
	 */
	public function getUrl(): string
	{
		return $this->getAttribute(self::ATTRIBUTE_URL);
	}

	/**
	 * Get profile status
	 * See \SteamAuth\User::STATE_* constants
	 *
	 * @return int Profile state
	 */
	public function getStatus(): int
	{
		return $this->getAttribute(self::ATTRIBUTE_STATE);
	}

	/**
	 * Check the users status
	 * See \SteamAuth\User::STATE_* constants
	 *
	 * @param int $status
	 *
	 * @return bool
	 */
	public function checkStatus(int $status): bool
	{
		return $this->getStatus() === $status;
	}

	/**
	 * Is the user online
	 *
	 * @return bool
	 */
	public function isOnline(): bool
	{
		return $this->checkStatus(self::STATE_ONLINE);
	}

	/**
	 * Is this profile a community profile
	 *
	 * @return bool
	 */
	public function isCommunityProfile(): bool
	{
		return (int) $this->getAttribute(self::ATTRIBUTE_PROFILE_STATE) === 1;
	}

	/**
	 * Get time the account was created
	 *
	 * @return DateTime Account creation
	 */
	public function getTimeCreated(): DateTime
	{
		return (new DateTime())->setTimestamp(
			$this->getAttribute(self::ATTRIBUTE_TIME_CREATED)
		);
	}

	/**
	 * Get visibility status
	 * See \SteamAuth\User::VISIBILITY_*
	 *
	 * @return int Visibility
	 */
	public function getVisibility(): int
	{
		return $this->getAttribute(self::ATTRIBUTE_COMMUNITY_VISIBLE);
	}

	/**
	 * Check users visibility
	 * See \SteamAuth\User::VISIBILITY_*
	 *
	 * @param int $visibility
	 *
	 * @return bool
	 */
	public function checkVisibility(int $visibility): bool
	{
		return $this->getVisibility() === $visibility;
	}

	/**
	 * Get last log off time
	 *
	 * @return DateTime
	 */
	public function getLastLogOffTime(): DateTime
	{
		return (new DateTime())->setTimestamp(
			$this->getAttribute(self::ATTRIBUTE_LAST_LOGOFF)
		);
	}

	/**
	 * Get country code
	 *
	 * @return string Country code
	 */
	public function getCountryCode(): string
	{
		return $this->getAttribute(self::ATTRIBUTE_COUNTRY_CODE);
	}

	/**
	 * Get current logged in user
	 *
	 * @return User|null
	 */
	public static function getCurrent(): ?User
	{
		if (self::isLoggedIn()) {
			return new self($_SESSION['steamAuth']['user']);
		}

		return null;
	}

	/**
	 * Reload user data.
	 * For example if the users avatar has changed on steam the user details
	 * will need reloading to reflect those changes.
	 */
	public function reload(): void
	{
		// todo: Having the API key stored in the session like this is dirty
		$request = new Request($_SESSION['steamAuth']['api_key']);

		$data = $request->getUser($_SESSION['steamAuth']['id']);

		$_SESSION['steamAuth']['user'] = $data;
		$this->profile_data = $data;
	}

	/**
	 * Is the user logged in?
	 * User details are stored in the session upon successful capture of a
	 * response.
	 *
	 * @return bool
	 */
	public static function isLoggedIn(): bool
	{
		return $_SESSION['steamAuth']['valid'] ?? false;
	}

	/**
	 * Log the user out of their account, this destroys the steamAuth session.
	 */
	public static function logout(): void
	{
		unset($_SESSION['steamAuth']);
	}
}