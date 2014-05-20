<?php
/**
 * sh404SEF support for com_jevents component.
 * Author : Helen
 * contact :
 *    
 */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
// ------------------  standard plugin initialize function - don't change ---------------------------
global $sh_LANG;
$sefConfig = & shRouter::shGetConfig();  
$shLangName = '';
$shLangIso = '';
$title = array();
$shItemidString = '';
$dosef = shInitializePlugin( $lang, $shLangName, $shLangIso, $option);
if ($dosef == false) return;
// ------------------  standard plugin initialize function - don't change ---------------------------
JLoader::register('JEVConfig', JPATH_ADMINISTRATOR . "/components/com_jevents/libraries/config.php");
JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");
$params = JComponentHelper::getParams("com_jevents");
$cfg = & JEVConfig::getInstance();
if (!function_exists('jtranslatetask')) {
function jtranslatetask($ttask)
{
	//English only - too lazy to set up proper translations
	$tasks = array(
		"year.listevents" => "eventsbyyear",
		"month.calendar" => "monthcalendar",
		"week.listevents" => "eventsbyweek",
		"day.listevents" => "eventsbyday",
		"cat.listevents" => "eventsbycategory",
		"jevent.detail" => "detail",
		"icalevent.detail" => "event",
		"icalrepeat.detail" => "eventdetail",
		"search.form" => "search",
		"search.results" => "searchresults",
		"admin.listevents" => "admin_listevents",
		"jevent.edit" => "jevent_edit",
		"icalevent.edit" => "icalevent_edit",
		"icalevent.publish" => "icalevent_publish",
		"icalevent.unpublish" => "icalevent_unpublish",
		"icalevent.editcopy" => "icalevent_editcopy",
		"icalrepeat.edit" => "icalrepeat_edit",
		"jevent.delete" => "jevent_delete",
		"icalevent.delete" => "icalevent_delete",
		"icalrepeat.delete" => "icalrepeat_delete",
		"icalrepeat.deletefuture" => "icalrepeat_deletefuture",
		"modlatest.rss" => "modlatest_rss",
		"icalrepeat.vcal" => "icalrepeat_cal",
		"icalevent.vcal" => "icalevent_vcal");
	reset($tasks);	
	if (!array_key_exists($ttask, $tasks))
		return $ttask;
	return $tasks[$ttask];
}}

// this was the menu for Rhine - long deleted but still have requests
if($Itemid == '333')
{
	$dosef=false;
  return;
}
// sometimes the task is not set but view and layout are so tackle this!
	if (!isset($task) && isset($view) && isset($layout))
	{
    $task = $view . "." . $layout;
	}
	// We don't need the view - its only used to manipulate parameters
	if (isset($view))
	{
		shRemoveFromGETVarsList('view');
	}
	if (isset($layout))
	{
		shRemoveFromGETVarsList('layout');
	}
	$jtask = false;
//if $task is not set try and find it, if not found use month.calendar  
if (!isset($task))
  {
    if (isset($Itemid))
		{
			$menu = & JSite::getMenu();
			$menuitem = $menu->getItem($Itemid);
			if (!is_null($menuitem) && isset($menuitem->$task))
			{
				$jtask = $menuitem->$task;
			}
			else if (!is_null($menuitem) && isset($menuitem->$layout) && isset($menuitem->$view))
			{
				$jtask = $menuitem->$view . "." . $menuitem->$layout;
			}
		}
		if (!$jtask)
		{
      $jtask = 'month.calendar';
		}
  }
else
	{
		$jtask = $task;
		shRemoveFromGETVarsList('task');
	}
// Translatable URLs
	$transtask = jtranslatetask($jtask);
	if ($transtask == 'event' && isset($evid))
	{
	    //leave event in the translate function in case we want to use it later
      $dosef=false;
	}
//temporary array to create url
  $jsegments = array();
//get base url address from menu->path
//NB this might not match uid category if so uid category should take precedence!
	if (!empty($Itemid)) 
  {
    try {
      $menupath = ShlDbHelper::selectObject( '#__menu', 'path', array('id'=>$Itemid));
    } catch (Exception $e) {
      ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
      $menupath = null;
    }
  	$jsegments[]=$menupath->path;
  }
	switch ($jtask) {
		case "year.listevents":
		case "month.calendar":
		case "week.listevents":
		case "day.listevents":
		case "cat.listevents":
		case "jevent.detail":
		case "icalevent.detail":
		case "icalrepeat.detail":
		case "search.form":
		case "search.results":
		case "admin.listevents": {
				$jsegments[] = $transtask;
				$config = & JFactory::getConfig();
				$t_datenow = JEVHelper::getNow();
				// if no date in the query then use TODAY not the calendar date
				$nowyear = JevDate::strftime('%Y', $t_datenow->toUnix(true));
				$nowmonth = JevDate::strftime('%m', $t_datenow->toUnix(true));
				$nowday = JevDate::strftime('%d', $t_datenow->toUnix(true));
				if (isset($year))
				{
					$jyear = ($year=="YYYYyyyy")?"YYYYyyyy":intval($year);
					shRemoveFromGETVarsList('year');
				}
				else
				{
					$jyear = $nowyear;
				}
				if (isset($month))
				{
          $jmonth = ($month=="MMMMmmmm")?"MMMMmmmm":intval($month);
          $jmonth = str_pad($jmonth,2,'0');
					//shRemoveFromGETVarsList('month');
				}
				else
				{
					$jmonth = $nowmonth;
				}                                 
				if (isset($day))
				{
					$jday = intval($day);
					//shRemoveFromGETVarsList('day');
				}
				else
				{
					// if no date in the query then use TODAY not the calendar date
					$jday = $nowday;
				}
				// for week data always go to the start of the week
				if ($jtask == "week.listevents" && is_int($jmonth))
				{
					$startday = $cfg->get('com_starday');
					if (!$startday  )
					{
						$startday = 0;
					}
					$date = mktime(5, 5, 5, $jmonth, $jday, $jyear);
					$currentday = strftime("%w", $date);
					if ($currentday > $startday)
					{
						$date -= ($currentday - $startday) * 86400;
						list($jyear, $jmonth, $jday) = explode("-", strftime("%Y-%m-%d", $date));
					}
					else if ($currentday < $startday)
					{
						$date -= (7 + $currentday - $startday) * 86400;
						list($jyear, $jmonth, $jday) = explode("-", strftime("%Y-%m-%d", $date));
					}
				}
				// only include the year in the date and list views
				if (in_array($jtask, array("year.listevents", "month.calendar", "week.listevents", "day.listevents")))
				{
					$jsegments[] = $jyear;
				}
				// only include the month in the date and list views (excluding year)
				if (in_array($jtask, array("month.calendar", "week.listevents", "day.listevents")))
				{
					//$jsegments[] = $jmonth;
				}
				// only include the day in the week and day views (excluding year and month)
				if (in_array($jtask, array("week.listevents", "day.listevents")))
				{
					//$jsegments[] = $jday;
				}
				switch ($jtask) {
					case "jevent.detail":
					case "icalevent.detail":
					case "icalrepeat.detail":
          //need to find correct path ========================== hopefully all fixed now   ===============================================
          if(isset($uid))
            {  try {
                    $jcatid = ShlDbHelper::selectObject( '#__jevents_vevent', 'catid', array('uid'=>$uid));
                  } catch (Exception $e) {
                    ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
                      $jcatid = null;
                   }
                if(isset($jcatid))
                {
                     try {
                    $pathalias = ShlDbHelper::selectObject( '#__categories', 'alias', array('id'=>$jcatid->catid));
                  } catch (Exception $e) {
                    ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
                      $pathalias = null;
                   }
                } 

              if(isset($pathalias))
              {
                if(isset($catids))
                {
                    //shRemoveFromGETVarsList('catids');
                }
                $pathalias = $pathalias->alias;
                if(stripos($jsegments[0],$pathalias)=== false)
                {
                  //$pathalias = '/'.$pathalias. '/';
                  // replace incorrect path in $jsegments caused by wrong Itemid wth correct one from using uid to find category
                  $temppath = explode('/',$jsegments[0]);
                  if (count($temppath) == 3)
                  {
                     $temppath[1] = $pathalias;
                     $jsegments[0] = implode('/',$temppath);
                  }
                  //$jsegments[0] = substr_replace($jsegments[0],$pathalias,stripos($jsegments[0],'/'),1);
                  }
              } 

            }
						// ====================================================================================================================
            
            if (isset($jevtype))
						{
							shRemoveFromGETVarsList('jevtype');
						}
						if (isset($evid))
						{
							$jsegments[] = $evid;
							shRemoveFromGETVarsList('evid');
							shRemoveFromGETVarsList('day');
							shRemoveFromGETVarsList('month');
						}
						break;
					default:
						break;
				}
				/*if (isset($catids) && strlen($catids) > 0)
				{
					//$jsegments[] = $catids;
					//shRemoveFromGETVarsList('catids');
				}
				else
				{
					//$jsegments[] = "-";
				}*/
				switch ($jtask) {
					case "icalrepeat.detail":
						if (isset($uid))
						{
							shRemoveFromGETVarsList('uid');
						}
						if (isset($sh404SEF_title))
						{
							$jsegments[] = substr($sh404SEF_title,0,79);
							//$jsegments[] = substr(JFilterOutput::stringURLSafe($sh404SEF®_title), 0, 150);
							shRemoveFromGETVarsList('title');
						}
						else
						{
							  //must get title from db
							  if(isset($uid))
							  {
								  //TODO
								  //$titlequery = 'SELECT `summary`' 
								  //. ' FROM `c8m7z_jevents_vevdetail`' 
								  //. ' WHERE `evdet_id` =' 
								  //. ' (SELECT `ev_id`'
								  //. ' FROM `c8m7z_jevents_vevent`'
								  //. ' WHERE `uid` = '. $uid . ')';
								  try {
										$jtitle = ShlDbHelper::selectObject( '#__jevents_vevent', 'ev_id', array('uid'=>$uid));
										$jtitle = $jtitle->ev_id;
										} catch (Exception $e) {
										ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
										$jtitle = null;
								   	}
								}
								else
								{
									if(isset($evid))
									{
										try {
										$jtitle = ShlDbHelper::selectObject( '#__jevents_repetition', 'eventid', array('rp_id'=>$evid));
										$jtitle = $jtitle->eventid;
										} catch (Exception $e) {
										ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
										$jtitle = null;
								   	}
									}
								}
								if(isset($jtitle))
									{
										try {
										$jtitle = ShlDbHelper::selectObject( '#__jevents_vevdetail', 'summary', array('evdet_id'=>$jtitle));
										} catch (Exception $e) {
										ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
										$jtitle = null;
									   }
									   $jsegments[] = substr($jtitle->summary,0,79);
									}
						 }
						break;
					default:
						break;
				}
			}
			break;
		case "jevent.edit":
		case "icalevent.edit":
		case "icalevent.publish":
		case "icalevent.unpublish":
		case "icalevent.editcopy":
		case "icalrepeat.edit":
		case "jevent.delete":
		case "icalevent.delete":
		case "icalrepeat.delete":
		case "icalrepeat.deletefuture":
    $dosef=false;
			$jsegments[] = $transtask;
			if (isset($jevtype))
			{
				shRemoveFromGETVarsList('jevtype');
			}
			if (isset($evid))
			{
				$jsegments[] = $evid;
				shRemoveFromGETVarsList('evid');
			}
			else
			{
				$jsegments[] = "0";
			}
			break;
		case "modlatest.rss":
			$jsegments[] = $transtask;
			// assume implicit feed document
			//unset($query['format']);
			// feed type
			if (isset($type))
			{
				$jsegments[] = $type;
				shRemoveFromGETVarsList('type');
			}
			else
			{
				$jsegments[] = 'rss';
			}
			// modid
			if (isset($modid))
			{
				$jsegments[] = $modid;
				shRemoveFromGETVarsList('modid');
			}
			else
			{
				$jsegments[] = "0";
			}
			break;
		case "icalrepeat.vcal":
		case "icalevent.vcal":
			$jsegments[] = $transtask;
			if (isset($evid))
			{
				$jsegments[] = $evid;
				shRemoveFromGETVarsList('evid');
			}
			else
			{
				$jsegments[] = "0";
			}
			if (isset($catids))
			{
				//$jsegments[] = $catids;
				//shRemoveFromGETVarsList('catids');
			}
			else
			{
				$jsegments[] = "0";
			}
			break;
		default:
			$jsegments[] = $transtask;
			$jsegments[] = "/";
			break;
	}
	shRemoveFromGETVarsList('lang');
	shRemoveFromGETVarsList('option');
	shRemoveFromGETVarsList('Itemid');
	if (isset($catids))
			{
				//shRemoveFromGETVarsList('catids');
			}
	$title = $jsegments;
  
//dump($string);
//dump($title);
//$dosef=false;
// ------------------  standard plugin finalize function - don't change ---------------------------  
if ($dosef){
   $string = shFinalizePlugin( $string, $title, $shAppendString, $shItemidString, 
      (isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null), 
      (isset($shLangName) ? @$shLangName : null));
}      
// ------------------  standard plugin finalize function - don't change ---------------------------