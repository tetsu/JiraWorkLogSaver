<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class worklogApp extends CI_Controller {
/********************************************************************/
    public function index()
    {
		$data['base_url'] = "http://".$_SERVER['HTTP_HOST']
			.preg_replace('@/+$@','',dirname($_SERVER['SCRIPT_NAME']))
			.'/index.php/';
		$this->load->view('worklog/header', $data);
		$this->load->view('worklog/users_and_pkey');
		$this->load->view('worklog/daily');
		$this->load->view('worklog/footer');
    }
/********************************************************************/
	public function workdata($action = "")
	{
		try
        {
			$this->load->library('jira_library');
			
			
			if( $this->input->post('period') == "daily" )
			{
            	if( $this->input->post('dateFrom') )
            	{
            		$input_array['date_from'] = $this->input->post('dateFrom');
				}
			
				if( $this->input->post('dateTo') )
				{
					$input_array['date_to'] = $this->input->post('dateTo');
				}
			}
			elseif( $this->input->post('period') == "monthly" )
			{
            	if( $this->input->post('year') && $this->input->post('month') )
            	{
            		$year = $this->input->post('year');
            		$month = $this->input->post('month');
            		$input_array['date_from'] = $year."-".$month."-01";
            		
            		switch( $month ){
            			case 1: $month_letter = 'january'; break;
            			case 2: $month_letter = 'february'; break;
            			case 3: $month_letter = 'march'; break;
            			case 4: $month_letter = 'april'; break;
            			case 5: $month_letter = 'may'; break;
            			case 6: $month_letter = 'june'; break;
            			case 7: $month_letter = 'jury'; break;
            			case 8: $month_letter = 'august'; break;
            			case 9: $month_letter = 'september'; break;
            			case 10: $month_letter = 'october'; break;
            			case 11: $month_letter = 'november'; break;
            			case 12: $month_letter = 'december'; break;
            		}
            		
					$input_array['date_to'] =
						date("Y-m-d" ,strtotime("last day of ".$month_letter." ".$year));
				}
			}
            elseif( $this->input->post('period') == "six-month" )
			{
            	if( $this->input->post('s_year') && $this->input->post('s_month') )
            	{
            		$s_year = $this->input->post('s_year');
            		$s_month = $this->input->post('s_month');
            		$input_array['date_from'] = $s_year."-".$s_month."-01";
            		
            		$l_month = ($s_month + 5) % 12;
            		$l_year = $s_year + floor( ($s_month + 5) / 12 );
            		
            		switch( $l_month ){
            			case 1: $month_letter = 'january'; break;
            			case 2: $month_letter = 'february'; break;
            			case 3: $month_letter = 'march'; break;
            			case 4: $month_letter = 'april'; break;
            			case 5: $month_letter = 'may'; break;
            			case 6: $month_letter = 'june'; break;
            			case 7: $month_letter = 'jury'; break;
            			case 8: $month_letter = 'august'; break;
            			case 9: $month_letter = 'september'; break;
            			case 10: $month_letter = 'october'; break;
            			case 11: $month_letter = 'november'; break;
            			case 0: $month_letter = 'december'; break;
            		}
            		
					$input_array['date_to'] =
						date("Y-m-d" ,strtotime("last day of ".$month_letter." ".$l_year));
				}
			}
			
			
			if( $this->input->post('projectKey') )
            {
            	$input_array['project_key'] = $this->input->post('projectKey');
            }
            
            
            if( $this->input->post('userName') )
            {
            	$input_array['users'] = explode('~', $this->input->post('userName'));
            	$json = $this->jira_library->get_multiple_user_worklog($input_array);
            }
            else
            {
				$json = $this->jira_library->get_project_worklog($input_array);
			}
            
			
			$json = json_decode($json, true);
			
			$data = $json['@attributes'];
			if(isset($json['worklog'])){
				$data['worklog'] = $json['worklog'];
			}
			if(isset($json['daily_data'])){
				$data['daily_data']  = $json['daily_data'];
			}
			if(isset($json['daily_hours'])){
				$data['daily_hours']  = $json['daily_hours'];
			}
			
			//send data to output.php
			$data['base_url'] = "http://".$_SERVER['HTTP_HOST']
				.preg_replace('@/+$@','',dirname($_SERVER['SCRIPT_NAME']))
				.'/'; 
			$this->load->view('worklog/output', $data);
		}
		catch (exception $ex)
		{
            $rval = array("error"=>$ex->getMessage());
        }
	}
/********************************************************************/
// Function: load_period($id)
// Summary: Get an assigned period entry form section in HTML format
/********************************************************************/
    public function load_period($id)
    {
		echo $this->load->view('worklog/'.$id, '', true);
    }
/********************************************************************/
// Function: update_worklogs_i($interval, $duration_in_days)
// Summary: Get all the worklogs in Rakuten Jira OnDemand,
//          and save them in the database.
//          Called from Cron Jobs so that all the worklog data is
//          updated automatically.
//
// Input:
//    duration_in_days -> Number of total days to get worklog data
//    interval -> Number of days for each Tempo API call
//
// http://localhost:8888/index.php/worklogApp/update_worklogs
/********************************************************************/
	public function update_worklogs($duration_in_days = 8, $interval = 4)
	{	
		$this->load->library('jira_library');
		$this->load->model('Jira_model');
		
		date_default_timezone_set('America/Los_Angeles');
		
		$data['date_from'] =
			date("Y-m-d 00:00:00", strtotime("-".($duration_in_days -1)." day") );
		$data['date_to'] = date("Y-m-d 23:59:59");
		
		$start_time = date("Y-m-d H:i:s");
		$return_data = 
			$this->jira_library->get_all_worklog($duration_in_days, $interval);
		$end_time = date("Y-m-d H:i:s");
		
		$data['duration'] = strtotime($end_time) - strtotime($start_time);
		
		$data['message'] = "Succeeded "
			.$return_data['api_calls']." Tempo API calls and "
			.$return_data['db_entries']
			." database entries at ".date("Y-m-d H:i:s").". "
			."It took ".$data['duration']." seconds.\n";
			
		$data['api_calls'] = $return_data['api_calls'];
		$data['db_entries'] = $return_data['db_entries'];
		
		$this->Jira_model->log_cron($data);
		$data['api'] = "update_worklogs";
		
		echo $data['message'];
		return json_encode($data);
	}
/********************************************************************/
// Function: users()
// Summary: Get all user info from database,
//          and show it on a web page.
/********************************************************************/
	public function users()
	{	
		$data['base_url'] = "http://".$_SERVER['HTTP_HOST']
			.preg_replace('@/+$@','',dirname($_SERVER['SCRIPT_NAME']))
			.'/index.php/';
		$this->load->model('Jira_model');
		$user_data = $this->Jira_model->get_users();
		$data['users'] = json_decode(json_encode($user_data), true);
		//print_r($data);
		
		$this->load->view('worklog/users', $data);
	}
/********************************************************************/
// Function: get_worklogs_from_dabase()
// Summary: Get worklogs from database.
//
// Input Method: POST
// dateFrom - Starting date in Y-m-d format
// dateTo - Ending date in Y-m-d format
// userName - Jira user name (e.g. ai.shimogori or mitsuyuki.shiiba)
// projectKey - Jira project key (e.g. SMART or BUY)
//
// Output: work logs in JSON format
// RDS password: 37aSTaBA
/********************************************************************/
	public function get_worklogs_from_database()
	{	
		try
		{
			if( $this->input->get('dateFrom') )
			{
				$data['dateFrom'] = $this->input->get('dateFrom')." 00:00:00";
			} else {
				throw new Exception("Both dateFrom and dateTo are required.");
			}
			if( $this->input->get('dateTo') )
			{
				$data['dateTo'] = $this->input->get('dateTo')." 23:59:59";
			} else {
				throw new Exception("Both dateFrom and dateTo are required.");
			}
			if( $this->input->get('userName') )
			{
				$data['userName'] = $this->input->get('userName');
			}
			if( $this->input->get('projectKey') )
			{
				$data['projectKey'] = $this->input->get('projectKey');
			}
			if( $this->input->get('limit') && $this->input->get('offset') )
			{
				$data['limit'] = $this->input->get('limit');
				$data['offset'] = $this->input->get('offset');
			}
		
			$this->load->model('Jira_model');
			$output = $this->Jira_model->get_worklogs($data);
			echo json_encode($output);
		}
		catch(exception $ex)
		{
            echo $ex->getMessage();
        }
	}
/********************************************************************/
    public function start()
    {
		$data['base_url'] = "http://".$_SERVER['HTTP_HOST']
			.preg_replace('@/+$@','',dirname($_SERVER['SCRIPT_NAME']))
			.'/index.php/';
		$this->load->view('db/header', $data);
		$this->load->view('db/users_and_pkey');
		$this->load->view('db/daily');
		$this->load->view('db/footer');
    }
/********************************************************************/
	public function dbout($action = "")
	{		
		try
        {
			$this->load->library('jira_library');
			
			// Modify Input Data, and put in array
			if( $this->input->post('period') == "daily" )
			{
            	if( $this->input->post('dateFrom') )
            	{
            		$input_array['dateFrom'] = 
            			$this->input->post('dateFrom')." 00:00:00";
				}
			
				if( $this->input->post('dateTo') )
				{
					$input_array['dateTo'] = 
						$this->input->post('dateTo')." 23:59:59";
				}
			}
			elseif( $this->input->post('period') == "monthly" )
			{
            	if( $this->input->post('year') && $this->input->post('month') )
            	{
            		$year = $this->input->post('year');
            		$month = $this->input->post('month');
            		$input_array['dateFrom'] = 
            			$year."-".$month."-01 00:00:00";
            		
            		switch( $month ){
            			case 1: $month_letter = 'january'; break;
            			case 2: $month_letter = 'february'; break;
            			case 3: $month_letter = 'march'; break;
            			case 4: $month_letter = 'april'; break;
            			case 5: $month_letter = 'may'; break;
            			case 6: $month_letter = 'june'; break;
            			case 7: $month_letter = 'jury'; break;
            			case 8: $month_letter = 'august'; break;
            			case 9: $month_letter = 'september'; break;
            			case 10: $month_letter = 'october'; break;
            			case 11: $month_letter = 'november'; break;
            			case 12: $month_letter = 'december'; break;
            		}
            		
					$input_array['dateTo'] =
						date("Y-m-d 23:59:59", 
							strtotime("last day of ".$month_letter." ".$year));
				}
			}
            elseif( $this->input->post('period') == "six-month" )
			{
            	if( $this->input->post('s_year') && $this->input->post('s_month') )
            	{
            		$s_year = $this->input->post('s_year');
            		$s_month = $this->input->post('s_month');
            		$input_array['dateFrom']
            			= $s_year."-".$s_month."-01 00:00:00";
            		
            		$l_month = ($s_month + 5) % 12;
            		$l_year = $s_year + floor( ($s_month + 5) / 12 );
            		
            		switch( $l_month ){
            			case 1: $month_letter = 'january'; break;
            			case 2: $month_letter = 'february'; break;
            			case 3: $month_letter = 'march'; break;
            			case 4: $month_letter = 'april'; break;
            			case 5: $month_letter = 'may'; break;
            			case 6: $month_letter = 'june'; break;
            			case 7: $month_letter = 'jury'; break;
            			case 8: $month_letter = 'august'; break;
            			case 9: $month_letter = 'september'; break;
            			case 10: $month_letter = 'october'; break;
            			case 11: $month_letter = 'november'; break;
            			case 0: $month_letter = 'december'; break;
            		}
            		
					$input_array['dateTo'] =
						date("Y-m-d 23:59:59", 
							strtotime("last day of ".$month_letter." ".$l_year));
				}
			}
			
			// Put projectKey and userName in array
			if( $this->input->post('projectKey') )
			{
            	$input_array['projectkey'] = $this->input->post('projectKey');
			}
            
            
            if( $this->input->post('userName') )
            {
				$input_array['userName'] = $this->input->post('userName');
			}
			
			/***********************************************************/
			
			if( $this->input->post('page') )
			{
				$data['page'] = $this->input->post('page');
			} else {
				$data['page'] = 1;
			}
			$data['limit'] = MAX_WORKLOG_DISPLAY;
			$data['offset'] = MAX_WORKLOG_DISPLAY * ($data['page']-1);
			
			$input_array['page'] = $data['page'];
			$input_array['limit'] = $data['limit'];
			$input_array['offset'] = $data['offset'];
			
			$data['base_url'] = "http://".$_SERVER['HTTP_HOST']
				.preg_replace('@/+$@','',dirname($_SERVER['SCRIPT_NAME'])).'/';
			
			$this->load->model('Jira_model');
			
			$data['worklog']
				= $this->Jira_model->get_worklogs($input_array);
			$data['total_hours']
				= $this->Jira_model->get_total_work_hours($input_array);
			$data['number_of_worklogs']
				= $this->Jira_model->get_total_work_logs($input_array);
			$data['userName'] = $this->input->post('userName');
			$data['projectKey'] = $this->input->post('projectKey');
			$data['dateFrom'] = $input_array['dateFrom'];
			$data['dateTo'] = $input_array['dateTo'];
			$data['number_of_pages']
				= ceil($data['number_of_worklogs'] / MAX_WORKLOG_DISPLAY);
			
			/*** Calculate number of workdays ***/
			$present = strtotime(date("Y-m-d"));
			$dateFrom = strtotime($input_array['dateFrom']);
			$dateTo = strtotime($input_array['dateTo']);
			
			if($present < $dateFrom)
			{
				$data['number_of_workdays'] = 0;
			}
			elseif( $present < $dateTo)
			{
				$data['number_of_workdays'] =
				$this->jira_library->get_number_of_weekdays($input_array['dateFrom'], date("Y-m-d"),array("2012-12-25"));
			}
			else
			{
				$data['number_of_workdays'] =
				$this->jira_library->get_number_of_weekdays($input_array['dateFrom'], $input_array['dateTo'],array("2012-12-25"));
			}
			
			
			/*** Load view files ***/
			$data = json_decode(json_encode($data), true);
			$header_data['base_url'] = $data['base_url'];
			
			echo $this->load->view('db/result_header', $header_data, true);
			echo $this->load->view('db/result_summary', $data, true);
			echo $this->load->view('db/result_excel', $data, true);
			echo $this->load->view('db/result_worklog_table', $data, true);
			echo $this->load->view('db/result_footer', "", true);
		}
		catch (exception $ex)
		{
            $rval = array("error"=>$ex->getMessage());
        }
	}
/********************************************************************/
// Function: load_period($id)
// Summary: Get an assigned period entry form section in HTML format
/********************************************************************/
    public function period($id)
    {
		echo $this->load->view('db/'.$id, '', true);
    }
/********************************************************************/
// Function: get_worklog_table()
// Summary: Get worklogs from database, and show as a table.
//          This function is called from jQuery to update table
//
// Input: POST method
//   'dateFrom'   - Starting date
//   'dateTo'     - Ending date
//   'userName'   - Names of users. Split names with '~'
//   'projectKey' - Project Key
//   'limit'      - Number of worklogs to get
//   'offset'     - the first position of worklogs to get
/********************************************************************/
	public function get_worklog_table()
	{
		try
		{
			if( $this->input->post('dateFrom') )
			{
				$data['dateFrom'] = $this->input->post('dateFrom')." 00:00:00";
			} else {
				throw new Exception("Both dateFrom and dateTo are required.");
			}
			if( $this->input->post('dateTo') )
			{
				$data['dateTo'] = $this->input->post('dateTo')." 23:59:59";
			} else {
				throw new Exception("Both dateFrom and dateTo are required.");
			}
			if( $this->input->post('userName') )
			{
				$data['userName'] = $this->input->post('userName');
			}
			if( $this->input->post('projectKey') )
			{
				$data['projectKey'] = $this->input->post('projectKey');
			}
			if( $this->input->post('page') )
			{
				$data['page'] = $this->input->post('page');
				$data['limit'] = MAX_WORKLOG_DISPLAY;
				$data['offset']
					= MAX_WORKLOG_DISPLAY * ($this->input->post('page') - 1);
			}
			
			$this->load->library('jira_library');
			$this->load->model('Jira_model');
			
			$data['number_of_worklogs']
				= $this->Jira_model->get_total_work_logs($data);
			$data['number_of_pages']
				= ceil($data['number_of_worklogs'] / MAX_WORKLOG_DISPLAY);
			$data['worklog']
				= $this->jira_library->get_worklogs_from_database($data);
		
			echo $this->load->view('db/result_worklog_table', $data, true);
		}
		catch (exception $ex)
		{
            echo $ex->getMessage();
        }
	}
/********************************************************************/
// Function: get_issues($jql)
// Summary: Get an assigned period entry form section in HTML format
// http://localhost:8888/index.php/worklogApp/get_issues/project+%3D+%22Project+Management+Department%22+AND+assignee+%3D+chiba/10
/********************************************************************/
    public function get_issues($jql, $max = 1000, $format = "array")
    {
		//echo $jql;
		$this->load->library('jira_library');
		$result_array = $this->jira_library->curl_issues($jql, $max, $format);
		
		//print_r($result_array);
		
		foreach($result_array as $issue)
		{
			print_r($issue);
		}
    }
/********************************************************************/
    public function get_object($jql, $max = 100, $format = "object")
    {
		//echo $jql;
		$this->load->library('jira_library');
		$result_array = $this->jira_library->curl_issues($jql, $max, $format);
		
		//print_r($result_array);
		
		foreach($result_array as $issue)
		{
			print_r($issue);
		}
    }
/********************************************************************/
	public function tempo_test()
	{
		$url = "https://rakuten.atlassian.net/plugins/servlet/tempo-getWorklog/";
		$input_array["tempoApiToken"] = TEMPO_API_TOKEN;
		$input_array["format"] = "xml";
		$input_array["dateFrom"] = "2013-01-30";
		$input_array["dateTo"] = "2013-01-31";
		$input_array["projectKey"] = "WI";
		
		$i=0;
		foreach($input_array as $field_name => $value)
		{
			if($i==0){$url .= "?".$field_name."=".$value;}
			else{$url .= "&".$field_name."=".$value;}
			$i++;
		}
		
		echo $url."\n";
		
		$xml = file_get_contents($url);
		echo $xml."\n";
	}
}