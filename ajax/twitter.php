<?php
session_start();
require_once('../classes/twitteroauth/twitteroauth.php');
include("../include/global.php");
define('CONSUMER_KEY', 'y2A9dSiLE2kIZtukot0lCQ');
define('CONSUMER_SECRET', '0cmQ8atW0ERHkLhxtDCsnO6WTEuTBIcWFOdWou0Tuo');

function processLinks($text) {
    //echo '<div style="display:none" > <pre>  '. $text.' </pre></div>';
    $text = utf8_decode( $text );
    $text = preg_replace('@(https?://([-\w\.]+)+(d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', ''/*'<a href="$1" target="_blank">$1</a>'*/,  $text );
    return $text;
}

function getLink($text) {
    $text = utf8_decode( $text );
    if (preg_match('@(https?://([-\w\.]+)+(d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', $text, $matches))
        return $matches[1];
    else
        return '#';
}

function getConnectionWithAccessToken($oauth_token, $oauth_token_secret) {
  $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $oauth_token, $oauth_token_secret);
  return $connection;
}

$settingsTable = TP.'pr_settings';
$settingsQuery = "SELECT * FROM {$settingsTable} WHERE TRUE LIMIT 1";
$settingsRes = mysql_query($settingsQuery) or die(mysql_error());
$settings = mysql_fetch_assoc($settingsRes);
$count = isset($settings['number_of_twitts']) ? $settings['number_of_twitts'] : '20';
date_default_timezone_set("Europe/London");
$connection = getConnectionWithAccessToken("174988687-bGtorKsnvwfWn0X6Twe0q7Zyu54JMlLWWACnViwh", "9Fr6IIZaslcyhYph5B1SyXtEOkV56MG9Bbb2Ea8uAg");
$twits = $connection->get("statuses/friends_timeline", array("count"=>$count,'trim_user'=>false));
//var_dump($twits);
$ret = '<ul>';

if (sizeOf($twits)) {
    foreach ($twits as $twit) {
        $link = getLink($twit->text);
        $message = processLinks($twit->text);
        $date = date('D jS M y H:i',strtotime($twit->created_at));
        $ret .= "<li class=\"tweet\">
                    <span class=\"tweet-status\">
                        <a href='$link' target='_blank'> 
                            <img src=\"{$twit->user->profile_image_url}\" align=\"left\" style=\"margin-right:10px;\">{$message}
                       </a>
                   </span><br />
                   <span class=\"tweet-details\">$date</span>
                </li>
                ";
    }
} else {
    $ret .= '<li> Twitter is temporarily unavailable. <li>';
}
$ret .= '</ul>';
echo $ret;