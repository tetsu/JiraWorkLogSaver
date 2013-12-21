<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/********************************************************************/
// jiradb.php
//
// Author: Tetsuro Mori
// Description: Grab issue data from Jira using Issue XML output.
/********************************************************************/
class Jiradb extends CI_Controller {
	public function index
	(
			$max = 1000,//Number of Jira issues to abstract per Jira API call. Max 1000.
			$start = 7200, //Starting time in how many seconds ago from now. 7200 means 2 hours ago. When it's set 0, it goes back to when the first ticket was updated on 2011-08-03.
			$end = 0,   //Ending time in how many seconds ago. 0 means NOW.
			$interval = 7320, //Period of time in sec to grab data. 7320 means 2:02 hour amount of data
			$every = 7200, //Time between each API call's starting time. Unit in sec. 3600 means every hour
			$ejql = "", //Extra JQL query in UTF8 format. e.g. project%20%3d%20%22Global%20Ichiba%22%20
    		$field = "" //which field to get. Keep it empty if you don't know.
    )
	{
		ini_set("date.timezone", "Asia/Tokyo");
		$start_time = date("Y-m-d H:i:s");
		
		if($start == 0){
			//time from the first issue update till now
			$start = strtotime("now") - strtotime("2011-08-03 00:00:00");
		}
		
		$param = array();
		$param['max'] = $max;
		$param['start'] = $start;
		$param['end'] = $end;
		$param['interval'] = $interval;
		$param['every'] = $every;
		$param['ejql'] = $ejql;
		$param['format'] = "array";
		$param['field'] = $field;
		$log = array();
		
		if (($ch = curl_init()) == false)
			throw new Exception("curl_init error");
		echo "Curl initialized.\n";
		$this->load->library('jiradb_library');
		$this->jiradb_library->set_data_array($param);
		$this->jiradb_library->set_curl_session($ch);
		$ch = $this->jiradb_library->new_loop_controller();
		$jiradb_log = $this->jiradb_library->get_log();
		
		$end_time = date("Y-m-d H:i:s");
		$log['duration'] = strtotime($end_time) - strtotime($start_time);
		$log['date_from'] = date("Y-m-d H:i:s", strtotime("-".$start." sec"));
		$log['date_to'] = date("Y-m-d H:i:s", strtotime("-".$end." sec"));
		$log['message'] = "XML Search results were successfully stored to DB.".
							" It took ".$log['duration']." seconds, ".
							"and grabbed ".$jiradb_log['num_of_issues'].
							" out of ".$jiradb_log['total_issues']." issues. ".
							"Maxmum number of issues per API call was ".$jiradb_log['max'].".";
		echo $log['message'];
		
		$log['api_calls'] = $jiradb_log['api_calls'];
		$log['db_entries'] = $jiradb_log['db_entries'];
		$this->jiradb_library->log_cron($log);
		curl_close($ch);
		echo "\nCurl closed\n";
	}
}


/* End of file jiradb.php */
/* Location: ./application/controller/jiradb.php */