# SteamAuth

SteamAuth allows you to implement steam sign-in within your PHP project. 
\
It is simple, fast, and secure...

## Request
To log a user in you must first make a request.

```php 
$request = new \SteamAuth\Request('API_KEY', 'RETURN_URL');

/* Option 1: Redirect the user */
$request->redirectToLogin();

/* Option 2: Present the user with a link or login button */
echo '<a href="' . $request->getLoginURL() . '">Login with Steam</a>';
```

**API_KEY** - This is your steam developer API key, you can find it by going to https://steamcommunity.com/dev/apikey
\
**RETURN_URL** - The URI on your website where the user will be returned to on successful login. For example if your website is example.com and you want to proccess the response on example.com/proc_login, you will only need to pass `proc_login` to the request constructor - Do not use full URL's!

The user will be redirected to the steam login page, once they sign in with their steam account they will be returned to the return URL specified with a response from steam.

**Note**: If you choose to use a steam login button you must consult and adhere to the steam brand guidelines.

## Response

When the user is returned to your site after logging in with steam you will need to handle the response, an example of how this is done can be found bellow.

```php
$response = new \SteamAuth\Response('API_KEY');

/* Check if the response is valid */
if ($response->isValid()) {

    /**
     * At this point the user is now logged in.
     * Their data will automatically be saved to the session.      
     */

    /* Get the steam user */
    $user = $response->getUser();
} else {

    /* Get errors */
    $errors = $response->getErrors();
}
```

## User

Once a request has been made, and the response has been handled you will be able to access information about the user.
SteamAuth comes with a handy class to make working with the steam user easy, but if you prefer to use your own implementation you can get all the information you need from the `$_SESSION['steamAuth']` variable.

### Check if the user is logged in

To check if a steam user is currently logged in you can do:

```php 
if (\SteamAuth\User::isLoggedIn()) {
    doSomeStuff();
}
```

### Get the user

To get a currently logged-in user you can do:

```php
$user = \SteamAuth\User::getCurrent();
```

### Reload the user

User details are not automatically updated, meaning it may be necessary to fetch the latest information from steam.
This can be done by simply doing:

```php 
$user->reload();
```

### Get user information

To get information about a user you can use a variety of methods. 
Bellow is an example of how each can be used:

```php
/* Directly access an attribute, see ATTRIBUTE_* constants for options */
$user->getAttribute($user::ATTRIBUTE_USERNAME);

// --------------- Steam ID ---------------
$user->getSteamID();

// --------------- Username ---------------
$user->getUsername();

// --------------- Avatar ---------------
$user->getAvatar();

/* Specify size */
$user->getAvatar($user::AVATAR_SIZE_MEDIUM);

// --------------- Status ---------------
$user->getStatus();
/* Check status */
if ($user->checkStatus($user::STATE_ONLINE)) {
    doSomeStuff();
}

// --------------- Real Name ---------------
/* This isn't always visible, check is is first */
if ($user->hasRealName()) {
    /* Get real name */
    $user->getRealName();
}

// --------------- Profile URL ---------------
$user->getURL();

// --------------- Community Profile ---------------
$user->isCommunityProfile();

// --------------- Time ---------------
/* Get account creation time */
$user->getTimeCreated();
/* Get last logoff time */
$user->getLastLogOffTime();

// --------------- Visibility ---------------
$user->getVisibility();
/* Check visibility */
if ($user->checkVisibility($user::VISIBILITY_PUBLIC)) {
    doSomeStuff();
}

// --------------- Logout ---------------
\SteamAuth\User::logout();
```


