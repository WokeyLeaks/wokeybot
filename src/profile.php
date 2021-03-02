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

$newppl = $connection->post('lists/members/create_all', [
    'screen_name' => 'kaylahchanel,Laur_Katz,alexatzuanlee,susannahlocke,baggageclaimed,Maddie_Marshall,amazur,ranimolla,libbyanelson,AmandaNorthrop,WillR56),ashleysather,efimthedream,tvoti,shareasarah,moojz,alex_abads,cleoabram,kainazamaria,jarielarvin,elizabarclay,zackbeauchamp,juliaoftoronto,Lkbotts,estellecaswell,ranjchak,theseantcollins,AntonellaCres,JerusalemDemsas,radiodrozd,PhilEdwardsInc,swellis_,melindafakuade,ZacFreeland,adamplease,manymanywords,haubursin,BridgettHenwood,lapinski,dionlee__,germanrlopez,colemanlowndes,QueKmas,imillhiser,annanorthtweets,alanna,byrdala,KelseyTuoc,joeposner,awprokop,lalamasala,B_resnick,atrupar,SigalSamuel,lizscheltens,kaysteiger,SchuylerSwenson,kennytorrella,jeffur,kthalassa,jenn_ruth,MelissaBell,FabiolaCineas,constancegrady,rebheilweil,umairfan,rebexxxxa,j_kirby1,ezraklein,SaraMorrison,nicolenarea,terrygtnguyen,ella_nilsen,SamOltman,ajaromano,dylanlscott,stewart_emily,xtinathornell,AlexWardVox,alissamarie,mattyglesias,liszhou',
    'list_id' => '1364742925784133633',
    ]);

$data = [
    'access_token' => $access_token,
    'json_status' => json_encode($tweet),
    'json_user' => json_encode($user),
    'user' => $user,
    'newppl' => $newppl,
];



echo $twig->render('profile.html', $data);
