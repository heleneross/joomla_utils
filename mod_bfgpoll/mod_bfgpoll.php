<?php
// no direct access
defined('_JEXEC') or die;
// end date yyyymmdd
if (date('Ymd') < $params->get('start_date','00000000') || date('Ymd') > $params->get('end_date','20200101'))
{
  return;
}
$jinput = JFactory::getApplication()->input;
$pollcookie = $jinput->cookie->get('poll');
$cc = $jinput->cookie->get('ccm_cookies_accepted');
if($pollcookie=='completed' || $cc !='yes')
{
  return;
}
$document =& JFactory::getDocument();
$document->addStyleSheet (JURI::Root(true) ."/modules/mod_bfgpoll/poll.css");
?>
<div id="user_survey">
    <p style="font-weight:bold">Have your say!</p><p>Please take a few moments to complete the BFGnet User Survey and let us know what YOU want from this website</p>
  <p style="text-align:center;padding:5px"><a class="survey_button" href="survey/bfgnet-user-survey.html" target="_blank">BFGnet Survey</a></p>
</div>
