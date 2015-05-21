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
		  $_SERVER['HTTP_X_FORWARDED_FOR'] = $this->calculateFakeIP($_SERVER['HTTP_'.$info_header]);
	      }else{
		  // Nothing to do
		  return true;
	      }
	}


	protected function calculateFakeIP($info){
	      // Temp test
	      return '10.19.1.25';
	}

}

