<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
  if(!empty($sheading))
  {
    echo '<h2 class="vets-news"><strong>' . $sheading . '</strong></h2>';
  }

$count = 0;
$regex = '#<li id="news_article_\d*?" class="document-row">.*?class="public_timestamp" title="(.*?)">.*?</ul>.*?</li>#s';
preg_match_all($regex,$vets_news,$matches);
$doc = array();
// go through the matches array making key=timestamp and value=list item 
foreach($matches[1] as $key => $value)    
  { 
    $doc[$value] = $matches[0][$key]; 
  }
// sorts list by date, most recent first
krsort($doc,SORT_REGULAR);
echo '<ol id="vetsnews">';
foreach($doc as $item)
{
   $count++;
   echo str_replace('<a href="','<a target="_blank" href="https://www.gov.uk',$item);
   if ($count == $num_items) break;
}
echo '</ol>';

// simple version with no processing to output all the list items
// NB items are not in date order

// echo '<ol id="vetsnews">' . $vets_news . '</ol>';
 

if($params->get('logging',0))
{
  modHelper::hits($source);
}





