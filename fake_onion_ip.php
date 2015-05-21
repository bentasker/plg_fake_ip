<?php
/**
 * @package     fake_onion_ip.plugin
 *
 * @copyright   Copyright (C) 2015 B Tasker. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * See http://projects.bentasker.co.uk/jira_projects/browse/MISC-5.html to see what this plugin was built for
 */

defined('_JEXEC') or die;


/**
 * Plugin to send a custom header if specific conditions are met
 *
 */
class plgSystemfake_onion_ip extends JPlugin
{

	protected $plugin;
	protected $plgParams;
	var $debug = array();


	function __construct(&$subject, $config){
	    parent::__construct ( $subject, $config );
	    $this->plugin = JPluginHelper::getPlugin ( 'system', 'fake_onion_ip' );
	    $this->plgParams = new JRegistry ( $this->plugin->params );
	}


	/**
	 * Plugin to change the IP address that protections such as admin tools will see
	 *
	 * @return void;
	 */
	public function onAfterInitialise()
	{
	      $app = JFactory::getApplication();
	      $runon = $this->plgParams->get('runonAdmin',2);
	      $isAdmin = $app->isAdmin();

	      if ($isAdmin && $runon == '0'){
		    return true;
	      }

	      $trigger = $this->plgParams->get('triggerheaderName','X-Example-Null');
	      $reqvalue = false;

	      if (stristr($trigger,"=")){
		    $split = explode("=",$trigger);
		    $reqvalue = $split[1];
		    $trigger = $split[0];
	      }

	      $header = str_replace("-","_",strtoupper($trigger));

	      if (isset($_SERVER['HTTP_'.$header]) && (!$reqvalue || $_SERVER['HTTP_'.$header] == $reqvalue)){
		  $info_header=str_replace("-","_",strtoupper($this->plgParams->get('infoheaderName','X-Null-val')));
		  $fakeip = $this->calculateFakeIP($_SERVER['HTTP_'.$info_header],$this->plgParams->get('fakeaddressspace','169.254'));
		  $_SERVER['HTTP_X_FORWARDED_FOR'] = $fakeip;
		  // For admin tools to pick up on it, we also need to set in REMOTE_ADDR
		  $_SERVER['REMOTE_ADDR'] = $fakeip;
	      }else{
		  // Nothing to do
		  return true;
	      }
	}


	/** Create a 'real' IP in the DHCP reserved address space (so there are no local clashes)
	* Designed to be based on the client's source port, but any int within that range will probably do
	*
	* @arg sport - INT
	* @arg pref - String - the first two octets of the IPv4 address to generate
	*
	* @return string
	*/
	protected function calculateFakeIP($sport,$pref){

	      // Calculate the time to the nearest 10 minute block
	      $second = time();
	      $now = ceil($second/600)*600;
	      $min = date('i',$now);


	      // Calculate the fourth octet
	      $portcalc = round(($sport/258),1);
	      $oct4 = round($portcalc,0);

	      // Use the decimal remainder of the calculation to work out what to add to our minute calculation
	      $dec = ($portcalc - $oct4)*10;

	      // Prevent negatives
	      if ($oct4 > $portcalc){
		      $dec = (($portcalc+1) - $oct4)*10;
	      }else{
		      $dec = ($portcalc - $oct4)*10;
	      }

	      // Round it and we have our third octet
	      $oct3 = round($min + $dec);

	      // Return our new IP
	      return "$pref.$oct3.$oct4";
	}

}

