<?php

require 'bootstrap.php';
use Abraham\TwitterOAuth\TwitterOAuth;

/* If access tokens are not available redirect to connect page. */
if (empty($_SESSION['access_token']) ||
    empty($_SESSION['access_token']['oauth_token']) ||
    empty($_SESSION['access_token']['oauth_token_secret'])
) {
    header('Location: ./clearsessions.php');
    exit;
}

/* Get user access tokens out of the session. */
$access_token = $_SESSION['access_token'];

/* Create a TwitterOauth object with consumer/user tokens. */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

/* If method is set change API call made. Test is called by default. */
$user = $connection->get('account/verify_credentials', ['tweet_mode' => 'extended', 'include_entities' => 'true']);

if (property_exists($user, 'status')) {
    // Embedded status doesn't always have everything needed for <twitter-status>
    $tweet = $connection->get('statuses/show', [
      'id' => $user->status->id_str,
      'tweet_mode' => 'extended',
      'include_entities' => 'true'
    ]);
} else {
    $tweet = [];
}

$blockees = $connection->get('lists/members', [
    'count' => '106',
    'list_id' => '1364742925784133633',
    'include_entities' => 'false',
    'skip_status' => 'true',
    ]);

$data = [
    'access_token' => $access_token,
    'json_status' => json_encode($tweet),
    'json_user' => json_encode($user),
    'user' => $user,
    'blockees' => json_decode(json_encode($blockees), true),
];

/* echo $twig->render('block_users.html', $data); */

<style type="text/css">
  table    { width: 100%; background-color: #1DA1F2; color: #FFF; margin-left: auto; margin-right: auto; }
  table td { border:inset 2px #FFF; text-align:center;}
  h1 h4    {text-align: center; }
  .center {text-align: center; border: 2px solid #1DA1F2;}
</style>

<div class="center">
  <h1>Congratulations!</h1>
  <h4>You have blocked the following 'Vox' accounts:</h4>


{% if user.status %}
<br />


<table>
    {% for blockee in blockees.users %}
        <tr>
          <td > @{{ blockee.screen_name }} </td>
          <td> <img src="{{blockee.profile_image_url_https}}" width="30" height="30 vertical-align="middle" border-radius="50%"></td>
          <td> Bio: {{ blockee.description }} </td>
          <td> Followers: {{ blockee.followers_count }} </td>
        </tr>
    {% endfor %}
</table>
</div>


