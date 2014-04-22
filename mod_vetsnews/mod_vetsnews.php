<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
// Include the helper.
require_once dirname(__FILE__) . '/helper.php';
//get params
$cache_time = $params->get('cache_time',120);
$num_items = $params->get('num_items',5);
$sheading = $params->get('sheading','');
$source = '';

$cache = JFactory::getCache('mod_vetsnews', '');
$cache->setCaching(1);
// Cache setLifeTime works in minutes!
$cache->setLifeTime($cache_time);

$date = urlencode('01' . date( "/m/Y",strtotime("-2 months")));
$url = 'https://www.gov.uk/government/announcements?keywords=veteran&from_date=' . $date;
$vets_news = '';
if (!($vets_news = $cache->get($url)))
{
    $options = array(
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => false,    // don't return headers
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_USERAGENT      => "bfgnet", // who am i
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT        => 120,      // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        CURLOPT_SSL_VERIFYPEER => false     // Disabled SSL Cert checks
    );
    
    $ch = curl_init($url);
    curl_setopt_array( $ch, $options );
    $vets_news = curl_exec($ch);
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    curl_close($ch);
 
    if (!empty($vets_news))
				{
          $regex = '#<div class="feeds">.*?<ol.*?\>(.*?)</ol>.*?<!-- report_a_problem -->#s';
          if (preg_match($regex,$vets_news,$match))
          {
            $vets_news = $match[1];
            $cache->store($vets_news, $url);
            $source = 'uncached';
          }
          else
          {
             $vets_news = '';
          }
				}
      else
      {
         $vets_news = '';
      }
}
else
{
  $source = 'cached';
}   
if (!empty($vets_news))
{
  require JModuleHelper::getLayoutPath('mod_vetsnews','default');
}
?>