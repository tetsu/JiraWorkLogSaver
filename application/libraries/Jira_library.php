<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
/********************************************************************/
// Jira_library.php
//
// Author: Tetsuro Mori
/********************************************************************/
// I like to see totals per user for below settings:
// Filter 1: Project = e.g. Project Mercury
// Filter 2: Team = e.g. cybage-mercury
//
// Summarized per Day, Week and Month
//
// Matt
/********************************************************************/
class Jira_library
{
	public function index()
	{
		echo "Hello World\n";
	}
/********************************************************************/
// Function: post_to($resource, $data)
// Summary: Use "POST" method for Jira REST API.
/********************************************************************/
	public function post_to($resource, $data)
	{
		$jdata = json_encode($data);
		
		if (($ch = curl_init()) == false)
		{
			throw new Exception("curl_init error");
		}
		
		curl_setopt_array($ch, array(
			CURLOPT_POST => true,
			CURLOPT_URL => JIRA_URL.'/rest/api/latest/'.$resource,
			CURLOPT_USERPWD => USERNAME.':'.PASSWORD,
			CURLOPT_POSTFIELDS => $jdata,
			CURLOPT_HTTPHEADER => array('Content-type: application/json'),
			CURLOPT_RETURNTRANSFER => true
		));
		$result = curl_exec($ch);
		if ($result === false)
		{
			throw new Exception("curl_exec error");
		}
		curl_close($ch);
		return json_decode($result);
	}
/********************************************************************/
// Function: get_to($resource, $data)
// Summary: Use "GET" method for Jira REST API.
/********************************************************************/
	public function get_to($resource, $data)
	{
		$request = "";
		$i = 0;
		foreach($data as $fieldname => $value){
			if($i == 0){
				$request = "?".$fieldname."=".$value;
			} else {
				$request = $request."&".$fieldname."=".$value;
			}
			$i++;
		}
		
		if (($ch = curl_init()) == false)
		{
			throw new Exception("curl_init error");
		}
		curl_setopt_array($ch, array(
			CURLOPT_URL => JIRA_URL.'/rest/api/latest/'.$resource.$request,
			CURLOPT_USERPWD => USERNAME.':'.PASSWORD,
			CURLOPT_HTTPHEADER => array('Content-type: application/json'),
			CURLOPT_RETURNTRANSFER => true
		));
		$result = curl_exec($ch);
		if ($result === false) {
			throw new Exception("curl_exec error");
		}
		curl_close($ch);
		return json_decode($result, true);
	}
/********************************************************************/
// Function: no_post_api($resource)
// Summary: Use Jira REST API without GET or POST method
/********************************************************************/
	public function no_post_api($resource)
	{
		if (($ch = curl_init()) == false)
		{
			throw new Exception("curl_init error");
		}
		curl_setopt_array($ch, array(
			CURLOPT_URL => JIRA_URL . '/rest/api/latest/' . $resource,
			CURLOPT_USERPWD => USERNAME . ':' . PASSWORD,
			CURLOPT_HTTPHEADER => array('Content-type: application/json'),
			CURLOPT_RETURNTRANSFER => true
		));
		$result = curl_exec($ch);
		if ($result === false) {
			throw new Exception("curl_exec error");
		}
		curl_close($ch);
		return json_decode($result);
	}
/********************************************************************/
// Function: curl_issues($resource, $data)
// Summary: Use "GET" method for Jira REST API.
/********************************************************************/
	public function curl_issues($jql, $max = 100, $format = "array", $field = "")
	{	
		$field_str = "";
		$max_str = "";
		
		if (($ch = curl_init()) == false)
			throw new Exception("curl_init error");
		
		if($field)
			$field_str = "&field=".$field;
		
		if($max == 0)
		{
			$max = 1000;
			$max_str = "&tempMax=1000";
		}
		elseif($max > 0)
			$max_str = "&tempMax=".$max;
			
		echo XML_URL."?jqlQuery=".$jql.$max_str.$field_str."\n";
		
		curl_setopt_array($ch, array(
			CURLOPT_URL => XML_URL."?jqlQuery=".$jql.$max_str.$field_str,
			CURLOPT_USERPWD => USERNAME.':'.PASSWORD,
			CURLOPT_HTTPHEADER => array('Content-type: application/json'),
			CURLOPT_RETURNTRANSFER => true
		));
		$output_xml = curl_exec($ch);
		if ($output_xml === false) {
			throw new Exception("curl_exec error");
			echo "curl failed";
		}
		curl_close($ch);
		echo "got xml result\n";
		if($format == "array")
		{
			//$pre_output = (array)simplexml_load_string($output_xml);
			$pre_output = simplexml_load_string($output_xml);
			echo "converted to object\n";
			$pre_output = json_decode(json_encode($pre_output), true);
			echo "Converted to array by json_encode and json_decode\n";
			//$pre_output = $output_array;
			//echo "test\n";
			if(($max==1) && (array_key_exists('item', $pre_output['channel'])))
			{
				//"array\n";
				$output[0] = $pre_output['channel']['item'];
				return $output;
			} 
			elseif(($max>1) && (array_key_exists('item', $pre_output['channel'])))
			{
				//"array\n";
				$output = $pre_output['channel']['item'];
				//print_r($output);
				return $output;
			}
		}
		elseif($format == "JSON")
		{
			$output_array = (array)simplexml_load_string($output_xml);
			$output = json_decode(json_encode($output_array), true);
			return json_encode($output['channel']['item']);
		}
		elseif($format == "XML")
		{
			return $output_xml;
		}
		elseif($format == "object")
		{
			return simplexml_load_string($output_xml);
		}
	}
/********************************************************************/
// Function: curl_session($resource, $data)
// Summary: Use "GET" method for Jira REST API.
/********************************************************************/
	public function curl_session($jql, $max = 100, $format = "array", $field = "")
	{	
		$field_str = "";
		$max_str = "";
		$cookiefile = tempnam("/tmp", "cookies");
		$agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
		
		if (($ch = curl_init()) == false)
			throw new Exception("curl_init error");
		
		if($field)
			$field_str = "&field=".$field;
		
		if($max == 0)
		{
			$max = 1000;
			$max_str = "&tempMax=1000";
		}
		elseif($max > 0)
			$max_str = "&tempMax=".$max;
			
		echo XML_URL."?jqlQuery=".$jql.$max_str.$field_str."\n";
		
		curl_setopt_array($ch, array(
			CURLOPT_URL => XML_URL."?jqlQuery=".$jql.$max_str.$field_str,
			CURLOPT_USERPWD => USERNAME.':'.PASSWORD,
			CURLOPT_HTTPHEADER => array('Content-type: application/json'),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_USERAGENT => $agent,
			CURLOPT_COOKIEFILE => $cookiefile,
			CURLOPT_COOKIEJAR => $cookiefile,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => 0
		));
		$output_xml = curl_exec($ch);
		if($output_xml === false) {
			throw new Exception("curl_exec error");
			echo "curl failed";
		}
		//curl_close($ch);
		echo "got xml result\n";
		if($format == "array")
		{
			$pre_output = simplexml_load_string($output_xml);
			echo "converted to object\n";
			$pre_output = json_decode(json_encode($pre_output), true);
			echo "Converted to array by json_encode and json_decode\n";
			if(($max==1) && (array_key_exists('item', $pre_output['channel'])))
			{
				$output[0] = $pre_output['channel']['item'];
				return $output;
			} 
			elseif(($max>1) && (array_key_exists('item', $pre_output['channel'])))
			{
				$output = $pre_output['channel']['item'];
				return $output;
			}
		}
		elseif($format == "JSON")
		{
			$output_array = (array)simplexml_load_string($output_xml);
			$output = json_decode(json_encode($output_array), true);
			return json_encode($output['channel']['item']);
		}
		elseif($format == "XML")
		{
			return $output_xml;
		}
		elseif($format == "object")
		{
			return simplexml_load_string($output_xml);
		}
	}
/********************************************************************/
// Function: search_issue($query) *
// Summary: search worklog issues, and return as JSON format
/********************************************************************/
	public function search_issue($query)
	{
		$CI =& get_instance();
		$CI->load->library('jira_library');
		
		try{
			$issues = $CI->jira_library->post_to('search', $query);
			return $issues;
		}
		catch (Exception $e)
		{
			$error_message = array();
			$error_message['Error'] = $e->getMessage();
			return json_encode($error_message);
		}
	}
/********************************************************************/
// Function: search_issue($query)
// Summary: search issues using Jira API, and return as JSON format
/********************************************************************/
	public function get_number_of_search_result($query)
	{
		$CI =& get_instance();
		$CI->load->library('jira_library');
		
		try
		{
			$object = $CI->jira_library->post_to('search', $query);
		}
		catch(Exception $e)
		{
			$error_message = array();
			$error_message['Error'] = $e->getMessage();
			echo json_encode($error_message);
		}
		return $object->total;
	}
/********************************************************************/
// Function: search_all_issues($query)
// Summary: search issues using Jira API. Unlimited results, but slow.
/********************************************************************/
	public function search_all_issues($query)
	{
		$CI =& get_instance();
		$CI->load->library('jira_library');
		$query['maxResults'] = 0;
		$total = $CI->jira_library->get_number_of_search_result($query);
		$query['maxResults'] = 50;
		$big_object_array['expand'] = "";
		$big_object_array['startAt'] = 0;
		$big_object_array['maxResults'] = $total;
		$big_object_array['total'] = $total;
		$i=0;
		do{
			$query['startAt'] = $i;
			$small_object = $CI->jira_library->post_to('search', $query);
			$small_object_array = json_decode(json_encode($small_object),true);
			
			//copy data from small_object to big_object
			foreach($small_object_array['issues'] as $key => $value)
			{
				$big_object_array['issues'][$i+$key] = $value;
			}
			$i=$i+$key+1;
		}while( ($total - $i) > 0);
		$big_object_array['expand'] = $small_object_array['expand'];
		
		return json_decode(json_encode($big_object_array));
	}
/********************************************************************/
// Function: get_issue_numbers($query)
// Summary: Get issue numbers from Jira API.
/********************************************************************/
	public function get_issue_numbers($query)
	{
		$CI =& get_instance();
		$CI->load->library('jira_library');
		$query['maxResults'] = 0;
		$total = $CI->jira_library->get_number_of_search_result($query);
		$query['maxResults'] = 50;
		$big_object_array['expand'] = "";
		$big_object_array['startAt'] = 0;
		$big_object_array['maxResults'] = $total;
		$big_object_array['total'] = $total;
		$i=0;
		do{
			$query['startAt'] = $i;
			$small_object = $CI->jira_library->post_to('search', $query);
			$small_object_array = json_decode(json_encode($small_object),true);
			
			//copy data from small_object to big_object
			foreach($small_object_array['issues'] as $key => $value)
			{
				$big_object_array['issues'][$i+$key]['key'] = $value['key'];
			}
			$i=$i+$key+1;
		}while( ($total - $i) > 0);
		
		$big_object_array['expand'] = $small_object_array['expand'];
		
		return json_decode(json_encode($big_object_array));
	}
/********************************************************************/
	public function get_ticket_worklog($ticket_number) 
	{
		$CI =& get_instance();
		$CI->load->library('jira_library');
		$object = $CI->jira_library->no_post_api('issue/'.$ticket_number.'/worklog');
		return $object;
	}
/********************************************************************/
	public function jira_to_datetime($jira_date) 
	{
		$date = substr($jira_date, 0, 10);
		$hour = substr($jira_date, 11, 8);
		return $date." ".$hour;
	}
/********************************************************************/
	public function is_earlier_than($datetime1, $datetime2) 
	{
		$d1 = new DateTime($datetime1);
		$d2 = new DateTime($datetime2);
		return $d1 < $d2;
	}
/********************************************************************/
	public function second_to_time($seconds) 
	{
		$hours = floor($seconds / 3600);
		$minutes = floor(($seconds - $hours*3600)/60);
		$seconds = $seconds - $hours*3600 - $minutes*60;
		return $hours.":".$minutes.":".$seconds;
	}
/********************************************************************/
//
//	Workflow
//	1. Get whole tickets of a project
//	2. Check worklog of each ticket
//	3. Check if the person has logged in the ticket
//	4. If so, add worklog to the worklog array. Else, ignore.
//	5. Print the array in JSON format after search through all the tickets
//
/********************************************************************/
	public function get_user_worklog($user_email, $query, $start, $end)
	{
		$CI =& get_instance();
		$CI->load->library('jira_library');
		$total = $CI->jira_library->get_number_of_search_result($query);
		$query['maxResults'] = 50;
		$big_object_array['expand'] = "";
		$big_object_array['email'] = $user_email;
		$big_object_array['startAt'] = 0;
		$big_object_array['maxResults'] = $total;
		$big_object_array['total'] = $total;
		$big_object_array['totalSecond'] = 0;
		$big_object_array['totalTime'] = 0;
		$big_object_array['startTime'] = $start;
		$big_object_array['endTime'] = $end;
		$total_second = 0;
		$i=0;
		$j=0;
		do{
			$query['startAt'] = $i;
			$small_object = $CI->jira_library->post_to('search', $query);
			$small_object_array = json_decode(json_encode($small_object),true);
			
			//copy data from small_object to big_object
			foreach($small_object_array['issues'] as $key => $value)
			{
				$worklog_obj = $CI->jira_library->get_ticket_worklog($value['key']);
				$worklog_array = json_decode(json_encode($worklog_obj), true);

				foreach($worklog_array['worklogs'] as $work_key => $work_value)
				{
					$create_time = 
						$CI->jira_library->jira_to_datetime($work_value['created']);
					$after_started =
						$CI->jira_library->is_earlier_than($start, $create_time);
					$before_ending =
						$CI->jira_library->is_earlier_than($create_time, $end);
					
					if
					(
						$work_value['author']['emailAddress'] == $user_email
						&& $after_started
						&& $before_ending
					)
					{
						$big_object_array['issues'][$j]['key']
							= $value['key'];
						$big_object_array['issues'][$j]['worklog']['created']
							= $create_time;
						$big_object_array['issues'][$j]['worklog']['timeSpentSeconds']
							= $work_value['timeSpentSeconds'];
						$total_second
							= $total_second + $work_value['timeSpentSeconds'];
						$j++;
					}
				}
			}
			$i=$i+$key+1;
		}while( ($total - $i) > 0);
		
		$big_object_array['expand'] = $small_object_array['expand'];
		$big_object_array['maxResults'] = $j;
		$big_object_array['total'] = $j;
		$big_object_array['totalSecond'] = $total_second;
		$big_object_array['totalTime']
			= $CI->jira_library->second_to_time($total_second);
		
		return json_decode(json_encode($big_object_array));
	}
/********************************************************************/
	public function get_user_worklog_no_term($user_email, $query)
	{
		$CI =& get_instance();
		$CI->load->library('jira_library');
		$total = $CI->jira_library->get_number_of_search_result($query);
		$query['maxResults'] = 50;
		$big_object_array['expand'] = "";
		$big_object_array['email'] = $user_email;
		$big_object_array['startAt'] = 0;
		$big_object_array['maxResults'] = $total;
		$big_object_array['total'] = $total;
		$big_object_array['totalSecond'] = 0;
		$big_object_array['totalTime'] = 0;
		//$big_object_array['startTime'] = $start;
		//$big_object_array['endTime'] = $end;
		$total_second = 0;
		$i=0;
		$j=0;
		do{
			$query['startAt'] = $i;
			$small_object = $CI->jira_library->post_to('search', $query);
			$small_object_array = json_decode(json_encode($small_object),true);
			
			//copy data from small_object to big_object
			foreach($small_object_array['issues'] as $key => $value)
			{
				$worklog_obj = $CI->jira_library->get_ticket_worklog($value['key']);
				$worklog_array = json_decode(json_encode($worklog_obj), true);

				foreach($worklog_array['worklogs'] as $work_key => $work_value)
				{					
					if
					($work_value['author']['emailAddress'] == $user_email)
					{
						$big_object_array['issues'][$j]['key']
							= $value['key'];
						$big_object_array['issues'][$j]['worklog']['created']
							= $CI->jira_library->jira_to_datetime($work_value['created']);
						$big_object_array['issues'][$j]['worklog']['timeSpentSeconds']
							= $work_value['timeSpentSeconds'];
						$total_second
							= $total_second + $work_value['timeSpentSeconds'];
						$j++;
					}
				}
			}
			$i=$i+$key+1;
		}while( ($total - $i) > 0);
		
		$big_object_array['expand'] = $small_object_array['expand'];
		$big_object_array['maxResults'] = $j;
		$big_object_array['total'] = $j;
		$big_object_array['totalSecond'] = $total_second;
		$big_object_array['totalTime']
			= $CI->jira_library->second_to_time($total_second);
		
		return json_decode(json_encode($big_object_array));
	}
/********************************************************************/
	/*
	Workflow
	1. Get whole tickets of a project
	2. Check worklog of each ticket
	3. Check if the person has logged in the ticket
	4. If so, add worklog to the worklog array. Else, ignore.
	5. Print the array in JSON format after search through all the tickets
	*/
/********************************************************************/
	public function get_user_worklog_stream($user_email, $query, $start, $end)
	{
		$CI =& get_instance();
		$CI->load->library('jira_library');
		$total = $CI->jira_library->get_number_of_search_result($query);
		$query['maxResults'] = 50;
		$big_object_array['expand'] = "";
		$big_object_array['email'] = $user_email;
		$big_object_array['startAt'] = 0;
		$big_object_array['maxResults'] = $total;
		$big_object_array['total'] = $total;
		$big_object_array['totalSecond'] = 0;
		$big_object_array['totalTime'] = 0;
		$big_object_array['startTime'] = $start;
		$big_object_array['endTime'] = $end;
		
		echo "{&quot;email&quot;:&quot;".$user_email."&quot;";
		
		$total_second = 0;
		$i=0;
		$j=0;
		do{
			$query['startAt'] = $i;
			$small_object = $CI->jira_library->post_to('search', $query);
			$small_object_array = json_decode(json_encode($small_object),true);
			
			//copy data from small_object to big_object
			foreach($small_object_array['issues'] as $key => $value)
			{
				$worklog_obj = $CI->jira_library->get_ticket_worklog($value['key']);
				$worklog_array = json_decode(json_encode($worklog_obj), true);

				foreach($worklog_array['worklogs'] as $work_key => $work_value)
				{
					$create_time = 
						$CI->jira_library->jira_to_datetime($work_value['created']);
					$after_started =
						$CI->jira_library->is_earlier_than($start, $create_time);
					$before_ending =
						$CI->jira_library->is_earlier_than($create_time, $end);
					
					if
					(
						$work_value['author']['emailAddress'] == $user_email
						&& $after_started
						&& $before_ending
					)
					{
						if($i==0 && $j==0){echo ",&quot;issues&quot;:[";}
						else {echo ",";}
						$log = array();
						$log['key']= $value['key'];
						$log['worklog']['created']= $create_time;
						$log['worklog']['timeSpentSeconds']
							= $work_value['timeSpentSeconds'];
						echo json_encode($log);
						$total_second
							= $total_second + $work_value['timeSpentSeconds'];
						$j++;
					}
				}
			}
			$i=$i+$key+1;
		}while( ($total - $i) > 0);
		
		if($j>0){echo "]";}
		
		echo ",&quot;totalSecond&quot;:".$total_second;
		echo ",&quot;totalTime&quot;:&quot;"
			.$CI->jira_library->second_to_time($total_second)
			."&quot";
		echo "}";//end of json output
		return json_decode(json_encode($big_object_array));
	}
/********************************************************************
From here, all the functions are using/related to Tempo APIs.
Not using any JIRA API.
********************************************************************/
	public function get_tempo_worklog($input_array)
	{
		$CI =& get_instance();
		$CI->load->library('jira_library');
		
		$url = "https://rakuten.atlassian.net/plugins/servlet/tempo-getWorklog/";
		
		//add query string attribute names to an array
		if(isset($input_array["user_name"])){
			$input_array["user_name"] = "userName=".$input_array["user_name"];
		}
		if(isset($input_array["project_key"])){
			$input_array["project_key"] = "projectKey=".$input_array["project_key"];
		}
		if(isset($input_array["date_from"])){
			$input_array["date_from"] = "dateFrom=".$input_array["date_from"];
		}
		else{
			//$input_array["date_from"] = "dateFrom=".date("Y-m-d", strtotime("-1 day"));
			$input_array["date_from"] = "dateFrom=".date("Y-m-d");
		}
		if(isset($input_array["date_to"])){
			$input_array["date_to"] = "dateTo=".$input_array["date_to"];
		}
		else{
			$input_array["date_to"] = "dateTo=".date("Y-m-d");
		}
		$input_array["output_format"] = "format=xml";
		$input_array["tempo_api_token"] = "tempoApiToken=".TEMPO_API_TOKEN;
		
		//create url string with GET attributes
		$i = 0;
		foreach($input_array as $d)
		{
			if($i==0){$url = $url."?";}
			else{$url = $url."&";}
			$url = $url.$d;
			$i++;
		}
		
		//out result as JSON
		$output_xml = file_get_contents($url);
		//$output_array = new SimpleXMLElement($output_xml);
		$output_array = (array)simplexml_load_string($output_xml);
		$total_hours = $CI->jira_library->get_total_work_hours($output_array);
		$output_array['@attributes']['total_hours'] = ''.$total_hours.'';
		$output = json_encode($output_array);
		echo $output;
	}
/********************************************************************/
	public function get_total_work_hours($input_array)
	{	
		$total_hours = sprintf('%.1f', 0.0);
		
		if(isset($input_array['worklog']))
		{		
			if($input_array['@attributes']['number_of_worklogs']==1)
			{
				//$total_hours += $input_array['worklog']->hours;
				//print_r($input_array);
				$worklog_hours = sprintf('%.1f', $input_array['worklog'][0]->hours);
				$total_hours = sprintf('%.1f', $total_hours + $worklog_hours);
			}
			else
			{
				foreach($input_array['worklog'] as $d)
				{
					//$total_hours += $d->hours;
					$worklog_hours = sprintf('%.1f', $d->hours);
					$total_hours = sprintf('%.1f', $total_hours + $worklog_hours);
				}
			}
		}
		//return $total_hours;
		return sprintf('%.1f', $total_hours);
	}
/********************************************************************/
public function add_hours($base, $addtion)
	{	
		$total_hours = sprintf('%.1f', 0.0 + $base + $addition);
		return sprintf('%.1f', $total_hours);
	}
/********************************************************************
Function: get_multiple_user_worklog($input_array)
Summary: Get worklogs of multiple users using Tempo API

input array structure:

array -+- users -+- [user name 1]
	   |		 +- [user name 2]
	   |		 +- [more user names]
	   |		 +- .....
	   |
	   +- project_key - [project key]
	   +- date_from   - [start date to get data]
	   +- date_to	 - [end date to get data]
	   
Output: Worklog Data and save data to database in JSON format
********************************************************************/
	public function get_multiple_user_worklog($input_array)
	{
		$CI =& get_instance();
		$CI->load->library('jira_library');
		
		$base_url = "https://rakuten.atlassian.net/plugins/servlet/tempo-getWorklog/";
		$username_array = array();
		$url_array = array();
		$data_array = array();
		$total_hours = 0;
		$total_worklog = 0;
		$total_duration = 0;
		$i = 0;
		
		//add query attributes to an array
		if(isset($input_array["users"]))
		{
			foreach($input_array["users"] as $user)
			{
				$username_array[] = "userName=".$user;
				$usernames[] = $user;
				$i++;
			}
			$input_array["user_name"] = $username_array[$i-1];
		}
		
		if(isset($input_array["project_key"])){
			$data_array["project_key"] = "projectKey=".$input_array["project_key"];
		}
		if(isset($input_array["date_from"])){
			$first_day = $input_array["date_from"];
			$data_array["date_from"] = "dateFrom=".$first_day;
		}
		else{
			//$first_day = date("Y-m-d", strtotime("-1 day"))
			$first_day = date("Y-m-d");
			$data_array["date_from"] = "dateFrom=".$first_day;
		}
		if(isset($input_array["date_to"])){
			$last_day = $input_array["date_to"];
			$data_array["date_to"] = "dateTo=".$last_day;
		}
		else{
			$last_day = date("Y-m-d");
			$data_array["date_to"] = "dateTo=".$last_day;
		}
		$data_array["output_format"] = "format=xml";
		$data_array["tempo_api_token"] = "tempoApiToken=".TEMPO_API_TOKEN;
		
		//Get worklog data of each user from Tempo API, and combine.
		$first_non_zero = 0;
		for($k=0;$k<$i;$k++)
		{
			//create url string with GET attributes
			$j = 0;
			$url_array[$k] = "";
			foreach($data_array as $d)
			{
				if($j==0){$url_array[$k] = $url_array[$k]."?";}
				else{$url_array[$k] = $url_array[$k]."&";}
				$url_array[$k] = $url_array[$k].$d;
				$j++;
			}
			$url_array[$k] = $base_url.$url_array[$k]."&".$username_array[$k];
			
			//Get data from Tempo API
			$output_xml = file_get_contents($url_array[$k]);
			$worklog_array = (array)simplexml_load_string($output_xml);
			
			//Create & initialize 'total_hours' data field
			$worklog_array['@attributes']['total_hours'] = 0;
			
			//Remove worklogs which is not related to the specified Project Key
			if(isset($input_array["project_key"]))
			{
				$worklog_array
					= $CI->jira_library->filter_worklog($worklog_array, $input_array["project_key"]);
			}
			
			//Calculate total hours of one user
			//print_r($worklog_array);
			$worklog_array['@attributes']['total_hours']
						= $CI->jira_library->get_total_work_hours($worklog_array);
			
			//Calculate total hours of all the users
			$total_hours += $worklog_array['@attributes']['total_hours'];
			
			//Calculate total number of worklogs of all the users
			$total_worklog += $worklog_array['@attributes']['number_of_worklogs'];
			
			//Calculate the search duration of all the users
			$total_duration += $worklog_array['@attributes']['duration_ms'];
			
			//Create an array to return
			if(!isset($output_array))
			{
				$output_array['@attributes'] = $worklog_array['@attributes'];
				$output_array['daily_data'] = "";
				
				foreach($usernames as $u)
				{
					$output_array['daily_data'][$u] = "";
				}
			}
			
			//Record total work hours of one user
			$output_array['@attributes']['users'][$usernames[$k]]
					= ''.$worklog_array['@attributes']['total_hours'].'';
			
			//Combine Arrays
			if($first_non_zero == 0)
			{
				if($worklog_array['@attributes']['number_of_worklogs'] == 0)
				{
					$output_array['@attributes']['users'][$usernames[$k]]
						= ''.$CI->jira_library->get_total_work_hours($worklog_array).'';
				}
				elseif($worklog_array['@attributes']['number_of_worklogs'] == 1)
				{
					$output_array["worklog"][] = $worklog_array["worklog"];
					
					$wd = ''.$worklog_array["worklog"]["work_date"].'';
					if(!isset($output_array["daily_data"][$usernames[$k]][$wd]))
					{
						$output_array["daily_data"][$usernames[$k]][$wd] = 0;
					}
					
					$output_array["daily_data"][$usernames[$k]][$wd]
						+= $worklog_array["worklog"]["hours"];
					
					$output_array['@attributes']['users'][$usernames[$k]]
						= ''.$CI->jira_library->get_total_work_hours($worklog_array).'';
					$first_non_zero = 1;
				}
				elseif($worklog_array['@attributes']['number_of_worklogs'] > 1)
				{
					foreach($worklog_array["worklog"] as $f)
					{
						$output_array["worklog"][] = $f;
						
						$wd = ''.$f->work_date.'';
						if(!isset($output_array["daily_data"][$usernames[$k]][$wd]))
						{
							$output_array["daily_data"][$usernames[$k]][$wd]
								= sprintf('%.1f', 0.0);
						}
						$output_array["daily_data"][$usernames[$k]][$wd]
							+= sprintf('%.1f', $f->hours);
					}
					$output_array['@attributes']['users'][$usernames[$k]]
						= ''.$CI->jira_library->get_total_work_hours($worklog_array).'';
					ksort($output_array['daily_data'][$usernames[$k]]);
					$first_non_zero = 1;
				}
			}
			else
			{	
				//Tempo API bug.
				//XML structure is different when there is only one worklog.
				//To fix the problem, I added this case.
				if($worklog_array['@attributes']['number_of_worklogs'] == 1)
				{
					$output_array["worklog"][] = $worklog_array["worklog"][0];
					
					$wd = ''.$worklog_array["worklog"][0]->work_date.'';
					$wh = sprintf('%.1f', $worklog_array["worklog"][0]->hours);
					if(!isset($output_array["daily_data"][$usernames[$k]][$wd]))
					{
						$output_array["daily_data"][$usernames[$k]][$wd]
								= sprintf('%.1f', 0.0);
					}
					$output_array["daily_data"][$usernames[$k]][$wd]
							+= sprintf('%.1f', $wh);
				}
				elseif($worklog_array['@attributes']['number_of_worklogs'] > 1)
				{
					foreach($worklog_array["worklog"] as $f)
					{
						$output_array["worklog"][] = $f;
						
						$wd = ''.$f->work_date.'';
						if(!isset($output_array["daily_data"][$usernames[$k]][$wd]))
						{
							$output_array["daily_data"][$usernames[$k]][$wd]
								= sprintf('%.1f', 0.0);
						}
						$output_array["daily_data"][$usernames[$k]][$wd]
							+= sprintf('%.1f', $f->hours);
					}
					ksort($output_array['daily_data'][$usernames[$k]]);
				}
			}
		}
		
		$output_array['@attributes']['total_hours'] = ''.$total_hours.'';
		$output_array['@attributes']['number_of_worklogs'] = ''.$total_worklog.'';
		$output_array['@attributes']['duration_ms'] = ''.$total_duration.'';
		
		//Get daily total work hours
		$output_array = $CI->jira_library->get_daily_hours($output_array);
		
		//clean up the worklog array
		$output_array = $CI->jira_library->clean_up_output_array($output_array);
		
		//save worklog data to database
		$CI->jira_library->worklogs_to_database($output_array);
		
		//save user data to database
		$CI->jira_library->users_to_database($output_array);
		
		return json_encode($output_array);
	}
/********************************************************************
function filter_worklog($data_array, $project_key)
Summary: This function removes worklogs that are not related
		 to the specified Project Key.
		 Since Tempo API ignores Project key when there is userName info,
		 this function was added.
********************************************************************/
	public function filter_worklog($data_array, $project_key)
	{
		if($project_key =='')
		{
			return $data_array;
		}
		if($data_array['@attributes']['number_of_worklogs'] == 1)
		{
			$key_array = explode('-', $data_array['worklog']->issue_key);
			if($key_array[0] != $project_key)
			{
				unset($data_array['worklog']);
				$data_array['@attributes']['number_of_worklogs'] = 0;
			}
		}
		elseif($data_array['@attributes']['number_of_worklogs'] > 1)
		{
			$i = 0;
			$set_numbers = array();
			$sorted_array = array();
			$unset_count = 0;
			foreach($data_array['worklog'] as $d)
			{
				$key_array = explode('-', $d->issue_key);
				if($key_array[0] != $project_key)
				{
					$data_array['@attributes']['number_of_worklogs'] -= 1;
					$data_array['@attributes']['total_hours'] -= $d->hours;
					unset($data_array['worklog'][$i]);
					$unset_count++;
				} else {
					$set_numbers[] = $i;
				}
				$i++;
			}
			
			//Sort worklogs when some of worklogs are deleted.
			if($unset_count > 0)
			{
				for($j=0;$j<$data_array['@attributes']['number_of_worklogs'];$j++)
				{
					$sorted_array[$j] = $data_array['worklog'][$set_numbers[$j]];
				}
				$data_array['worklog'] = $sorted_array;
				//$data_array['@attributes']['number_of_worklogs'] -= $unset_count;
			}
		}
		return $data_array;
	}
/********************************************************************/
// Function: restructure_worklog($data_array)
// Summary: Restructure the data array when there is 0 or 1 worklog
//　　　　　　because the structure is different.
/********************************************************************/
	public function restructure_worklog($data_array)
	{
		// change the partial-array to full-array just in case.
		$data_array = json_decode(json_encode($data_array), true);
		
		if(
		  ($data_array['@attributes']['number_of_worklogs'] > 1)
		  && isset($data_array['worklog'][0])
		)
		{}
		elseif($data_array['@attributes']['number_of_worklogs'] == 0){
			$data_array['worklog'] = "";
		}
		elseif( !isset($data_array['worklog']) ){
			$data_array['worklog'] = "";
		}
		elseif(
		  ($data_array['@attributes']['number_of_worklogs'] == 1)
		  && !isset($data_array['worklog']['username']))
		{
			$data_array['worklog'] = "";
			print_r($data_array);
		}
		elseif(
		  ($data_array['@attributes']['number_of_worklogs'] == 1)
		  && isset($data_array['worklog']['username']))
		{
			foreach($data_array['worklog'] as $f => $d)
			{
				$output_array['worklog'][0][$f] = $d;
			}
			$data_array['worklog'] = $output_array['worklog'];
			//print_r($data_array);
		}
		return $data_array;
	}
/********************************************************************/
	public function number_of_days($sql_date1, $sql_date2)
	{
		$result = strtotime($sql_date2) - strtotime($sql_date1);
		$result = intval( $result / ( 24 * 60 * 60))+1;
		return $result;
	}
/********************************************************************/
// Function: get_project_worklog($input_array)
// Summary: Get worklog data of a project, when no user is specified.
//
// input array structure:
//
// array -+- users -+- [user name 1]
//	   |		 +- [user name 2]
//	   |		 +- [more user names]
//	   |		 +- .....
//	   |
//	   +- project_key - [project key]
//	   +- date_from   - [start date to get data]
//	   +- date_to	 - [end date to get data]
//	   
// Output: Worklog Data and save data to database
/********************************************************************/
	public function get_project_worklog($input_array)
	{
		$CI =& get_instance();
		$CI->load->library('jira_library');
		
		$base_url = "https://rakuten.atlassian.net/plugins/servlet/tempo-getWorklog/";
		$data_array = array();
		
		//add query attributes to an array
		if(isset($input_array["users"])){
			//do nothing. 
			//There shouldn't be any user names passed to this function
		}
		
		if(isset($input_array["project_key"])){
			$data_array["project_key"] = "projectKey=".$input_array["project_key"];
		}
		if(isset($input_array["date_from"])){
			$first_day = $input_array["date_from"];
			$data_array["date_from"] = "dateFrom=".$first_day;
		}
		else{
			//$first_day = date("Y-m-d", strtotime("-1 day"))
			$first_day = date("Y-m-d");
			$data_array["date_from"] = "dateFrom=".$first_day;
		}
		if(isset($input_array["date_to"])){
			$last_day = $input_array["date_to"];
			$data_array["date_to"] = "dateTo=".$last_day;
		}
		else{
			$last_day = date("Y-m-d");
			$data_array["date_to"] = "dateTo=".$last_day;
		}
		$data_array["output_format"] = "format=xml";
		$data_array["tempo_api_token"] = "tempoApiToken=".TEMPO_API_TOKEN;
		
		//create url string with POST attributes
		$url_string
			= $CI->jira_library->post_data_to_url($data_array, $base_url);
		
		//Get data from Tempo API
		$output_xml = file_get_contents($url_string);
		$worklog_array = (array)simplexml_load_string($output_xml);
		
		//when there is 0 or 1 worklog, the XML structure is different,
		//so restructure the array before putting it to database.
		//$worklog_array = $CI->jira_library->restructure_worklog($worklog_array);
		
		//Calculate Total Work Hours
		$worklog_array['@attributes']['total_hours']
			= $CI->jira_library->get_total_work_hours($worklog_array);
			
		//Get all the user names
		$worklog_array = $CI->jira_library->get_user_names($worklog_array);
		
		//Get all the user work hours
		$worklog_array = $CI->jira_library->get_user_hours($worklog_array);
		
		//Sort users & work hours
		$worklog_array = $CI->jira_library->sort_user_hours($worklog_array);
		
		//Get total work hours of each users
		$worklog_array = $CI->jira_library->get_total_hours($worklog_array);
		
		//Get daily total work hours
		$worklog_array = $CI->jira_library->get_daily_hours($worklog_array);
		
		//clean up the worklog array
		$worklog_array = $CI->jira_library->clean_up_output_array($worklog_array);
		
		//save worklog data to database
		$CI->jira_library->worklogs_to_database($worklog_array);
		
		//save user data to database
		$CI->jira_library->users_to_database($worklog_array);
		
		//save project data to database
		$CI->jira_library->projects_to_database($worklog_array);
		
		return json_encode($worklog_array);
	}
/********************************************************************/
	public function get_user_names($input_array)
	{
		if(!isset($input_array["@attributes"]["number_of_worklogs"]))
		{
			$input_array["@attributes"]["number_of_worklogs"] = -1;
			return $input_array;
		}
		elseif($input_array["@attributes"]["number_of_worklogs"] == 1)
		{
			$input_array['daily_data'] = "";
			$input_array['daily_data'] = $input_array['worklog']->username;
		}
		elseif($input_array['@attributes']['number_of_worklogs'] > 1)
		{
			$input_array['daily_data'] = '';
			foreach( $input_array['worklog'] as $wl )
			{
				if(!isset($input_array['daily_data'][''.$wl->username.'']))
				{
					$input_array['daily_data'][''.$wl->username.''] = "";
					
				}
			}
		}

		return $input_array;
	}
/********************************************************************/
	public function get_user_hours($input_array)
	{
		if($input_array["@attributes"]["number_of_worklogs"] == 1)
		{
			$input_array['daily_data'][''.$input_array['worklog']->username.''][''.$input_array['worklog']->work_date.'']
				= sprintf('%.1f', $input_array['worklog']->hours);
		}
		elseif($input_array['@attributes']['number_of_worklogs'] > 1)
		{
			foreach( $input_array['worklog'] as $wl )
			{
				if(!isset($input_array['daily_data'][''.$wl->username.''][''.$wl->work_date.'']))
				{
					$input_array['daily_data'][''.$wl->username.''][''.$wl->work_date.'']
						= "";
				}
				
				$input_array['daily_data'][''.$wl->username.''][''.$wl->work_date.'']
						+= sprintf('%.1f', $wl->hours);
			}
		}

		return $input_array;
	}
/********************************************************************/
	public function sort_user_hours($input_array)
	{
		if($input_array["@attributes"]["number_of_worklogs"] == 1)
		{
			ksort($input_array['daily_data'][''.$input_array['worklog']->username.'']);
		}
		elseif($input_array['@attributes']['number_of_worklogs'] > 1)
		{
			ksort($input_array['daily_data']);
			
			foreach( $input_array['daily_data'] as $user_name => $wl_date )
			{
				ksort($wl_date);
				$input_array['daily_data'][$user_name] = $wl_date;
			}
		}

		return $input_array;
	}
/********************************************************************/
	public function post_data_to_url($post_data_array, $base_url)
	{
		$url_string = "";
		$j=0;
		foreach($post_data_array as $d)
		{
			if($j==0)
			{
				$url_string = $url_string."?";
			}
			else
			{
				$url_string = $url_string."&";
			}
			$url_string = $url_string.$d;
			$j++;
		}
		
		return $base_url.$url_string;
	}
/********************************************************************/
	public function clean_up_output_array($output_array)
	{
		$CI =& get_instance();
		$CI->load->library('jira_library');
		
		if( !isset($output_array['@attributes']['date_from'])){
			$output_array['@attributes']['date_from'] = date("Y-m-d");
		}
		if( !isset($output_array['@attributes']['date_to'])){
			$output_array['@attributes']['date_to'] = date("Y-m-d");
		}
		$first_day = $output_array['@attributes']['date_from'];
		$last_day = $output_array['@attributes']['date_to'];
		
		$output_array['@attributes']['days']
			= ''.$CI->jira_library->number_of_days($first_day, $last_day).'';
		$output_array['@attributes']['format'] = 'json';
		unset($output_array['@attributes']['userName']);
		unset($output_array['@attributes']['diffOnly']);
		unset($output_array['@attributes']['errorsOnly']);
		unset($output_array['@attributes']['validOnly']);
		unset($output_array['@attributes']['addBillingInfo']);
		unset($output_array['@attributes']['addIssueSummary']);
		unset($output_array['@attributes']['headerOnly']);
		unset($output_array['@attributes']['addIssueDetails']);
		unset($output_array['@attributes']['addUserDetails']);
		unset($output_array['@attributes']['addWorklogDetails']);
		unset($output_array['@attributes']['billingKey']);
		
		return $output_array;
	}
/********************************************************************/
	public function get_total_hours($input_array)
	{
		if($input_array["@attributes"]["number_of_worklogs"] == 1)
		{
			$input_array['@attributes']['users'] = "";
			$input_array['@attributes']['users'][''.$input_array['worklog']->username.'']
				= sprintf('%.1f', $input_array['worklog']->hours);
			$input_array['@attributes']['number_of_users'] = 1;
		}
		elseif($input_array['@attributes']['number_of_worklogs'] > 1)
		{
			$input_array['@attributes']['users'] = "";
			$i = 0;
			foreach( $input_array['worklog'] as $wl )
			{
				if(!isset($input_array['@attributes']['users'][''.$wl->username.'']))
				{
					$input_array['@attributes']['users'][''.$wl->username.''] = '0.0';
					$i++;
				}
				
				$input_array['@attributes']['users'][''.$wl->username.'']
						+= sprintf('%.1f', $wl->hours);
			}
			$input_array['@attributes']['number_of_users'] = $i;
		}
		else
		{
			$input_array['@attributes']['number_of_users'] = 0;
		}
		
		return $input_array;
	}
/********************************************************************/
	public function get_daily_hours($input_array)
	{
		if($input_array["@attributes"]["number_of_worklogs"] == 1)
		{
			$json = json_encode($input_array['worklog']);
			$input_array['worklog'] = json_decode($json, true);
			
			$input_array['daily_hours'][''.$input_array['worklog'][0]['work_date'].'']
				= sprintf('%.1f', $input_array['worklog'][0]['hours']);
		}
		elseif($input_array['@attributes']['number_of_worklogs'] > 1)
		{
			foreach( $input_array['worklog'] as $wl )
			{
				if(!isset($input_array['daily_hours'][''.$wl->work_date.'']))
				{
					$input_array['daily_hours'][''.$wl->work_date.''] = "";
				}
				
				$input_array['daily_hours'][''.$wl->work_date.'']
						+= sprintf('%.1f', $wl->hours);
			}
		}
		
		if(isset($input_array['daily_hours']))
		{
			ksort($input_array['daily_hours']);
		}
		
		return $input_array;
	}
/********************************************************************/
	public function get_five_weeks()
	{
		$weeks = array();
		
		$weeks[] = date("Y-m-d" ,strtotime("-5 Monday"));
		$weeks[] = date("Y-m-d" ,strtotime("-4 Sunday"));
		$weeks[] = date("Y-m-d" ,strtotime("-4 Monday"));
		$weeks[] = date("Y-m-d" ,strtotime("-3 Sunday"));
		$weeks[] = date("Y-m-d" ,strtotime("-3 Monday"));
		$weeks[] = date("Y-m-d" ,strtotime("-2 Sunday"));
		$weeks[] = date("Y-m-d" ,strtotime("-2 Monday"));
		$weeks[] = date("Y-m-d" ,strtotime("-1 Sunday"));
		$weeks[] = date("Y-m-d" ,strtotime("-1 Monday"));
		$weeks[] = date("Y-m-d" ,strtotime("+1 Sunday"));
		
		return $weeks;
	}
/********************************************************************/
	public function get_weeks($num_of_weeks)
	{
		$weeks = array();
		
		for($i=$num_of_weeks; $i>0; $i--)
		{
			$weeks[] = date("Y-m-d" ,strtotime("-".$i." Monday"));
			$weeks[] = date("Y-m-d" ,strtotime("-".($i-1)." Sunday"));
		}
		
		return $weeks;
	}
/********************************************************************/
	public function get_months($num_of_months)
	{
		$months = array();
		
		for($i=$num_of_months; $i>=0; $i--)
		{
			$months[] = date("Y-m-d" ,strtotime("first day of -".$i." month"));
			$months[] = date("Y-m-d" ,strtotime("last day of -".$i." month"));
		}
		return $months;
	}
/********************************************************************
function manipulate_monthly_json($monthly_json)

Output data array structure

array-+-monthly_data-+-[month 1]-+-monthly_total-[monthly total work hours]
	  |			  |		   +-date_from	-[starting date of the month]
	  |			  |		   +-date_to	  -[ending date of the month]
	  |			  |		   +-users-+-[user name 1]-[user's monthly Total hours]
	  |			  |				   +-[user name 2]-[user's monthly Total hours]
	  |			  |				   +-........
	  |			  |
	  |			  +-[month 2]-+-....
	  |			  |		   +-....
	  |			  +-.....	 
	  |
	  +-number_of_month-[number of month]
********************************************************************/
	public function manipulate_monthly_json($monthly_json)
	{
		$output_data = array();
		$number_of_months = count($monthly_json);
		$output_data['number_of_months'] = count($monthly_json);
		$output_data['monthly_data'] = "";
		
		/*
		for($i=0; $i<$number_of_months; $i++)
		{
			$monthly_data = json_decode($monthly_json[$i]);
			$month = data("Y-m", $monthly_data["@attributes"]["date_from"]);
			
			$output_data['monthly_data'][$month] = "";
		}
		*/
		
		print_r($output_data);
		//return $output_data;
	}
/********************************************************************/
	public function split_usernames($name_string)
	{
		return explode("~", $name_string);
	}
/********************************************************************/
// Function: worklogs_to_database($input_array)
// Summary: This is a function to save project worklogs to database
//
// input array structure example:
//
// Array
// (
//	[@attributes] => Array
//		(
//			[date_from] => 2012-11-20 00:00:00
//			[date_to] => 2012-11-20 23:59:59
//			[number_of_worklogs] => 10
//			[format] => json
//			[duration_ms] => 29
//			[projectKey] => SMART
//			[total_hours] => 14.1
//			[users] => Array
//				(
//					[ai.shimogori] => 0.6
//					[mitsuyuki.shiiba] => 1
//					[cynthia.kustanto] => 2.5
//					[emma.suzuki] => 1
//					[bernadette.le] => 1
//					[frank.fernandes] => 8
//				)
//			[number_of_users] => 6
//			[days] => 1
//		)
//	[worklog] => Array
//		(
//			[0] => Array
//				(
//					[worklog_id] => 30418
//					[issue_id] => 35537
//					[issue_key] => SMART-2013
//					[hours] => 0.5
//					[work_date] => 2012-11-20
//					[username] => ai.shimogori
//					[staff_id] => ai.shimogori
//					[billing_key] => Array()
//					[billing_attributes] => Array()
//					[activity_id] => v14500
//					[activity_name] => Employee App It8
//					[work_description] => Working on issue SMART-2013
//					[parent_key] => SMART-610
//					[reporter] => mitsuyuki.shiiba
//					[external_id] => Array()
//					[external_tstamp] => Array()
//					[external_hours] => 0.0
//					[external_result] => Array()
//					[hash_value] => 105e71d3d32e2eefc44cb2e021348507f50bd9d3
//				)
//			[1] => Array
//				(......)
//	   
// Output: Worklog Data and save data to database
/********************************************************************/
	public function worklogs_to_database($input_array)
	{		
		if( isset($input_array['worklog']) )
		{
			$CI =& get_instance();
			$CI->load->database();
			
			foreach($input_array['worklog'] as $log)
			{
				$log = json_decode(json_encode($log), true);
				$sql = "REPLACE INTO WORKLOGS "
					."(worklog_id, issue_id, project_key, issue_key, "
					."username, work_date, hours) VALUES (" 
					."'".$log['worklog_id']."', "
					."'".$log['issue_id']."', "
					."'".$input_array['@attributes']['projectKey']."', "
					."'".$log['issue_key']."', "
					."'".$log['username']."', "
					."'".$log['work_date']."', "
					."'".$log['hours']."')";
				$CI->db->query($sql);
				
			}
		
			$CI->db->close();
			return 1;
		}
		return 0;
	}
/********************************************************************/
// Function: users_to_database($input_array)
// Summary: This is a function to save user data to database.
//		  It also updates data when a user already exists
//		  but has changed his/her information.
//
//		  Might need to move to MODEL.
/********************************************************************/
	public function users_to_database($input_array)
	{
		
		$CI =& get_instance();
		$CI->load->database();
		$CI->load->library('jira_library');
		$input_array = json_decode(json_encode($input_array), true);
		
		if(isset($input_array['@attributes']['users']))
		{
			foreach($input_array['@attributes']['users'] as $user_name => $hours)
			{
				$query['username'] = $user_name;
				$result_object = $CI->jira_library->get_to('user', $query);
				$result_array = json_decode(json_encode($result_object), true);
				$sql = "REPLACE INTO USERS "
					."(username, display_name, email) VALUES (" 
					. "'" . $result_array['name'] . "', "
					. "'" . $result_array['displayName'] . "', "
					. "'" . $result_array['emailAddress'] . "')";
				$CI->db->query($sql);
			}
			$CI->db->close();
			return 1;
		}
		$CI->db->close();
		return 0;
	}
/********************************************************************/
// Function: projects_to_database($input_array)
// Summary: This is a function to save project data to database
//
// Might need to move to MODEL.
/********************************************************************/
	public function projects_to_database($input_array)
	{
		$CI =& get_instance();
		$CI->load->database();
		$CI->load->library('jira_library');
		$input_array = json_decode(json_encode($input_array), true);
		
		//create an array of existing project names in database
		$CI->db->select('project_key');
		$query = $CI->db->get('PROJECTS');
		$current_projects = json_decode(json_encode($query->result()),true);
		
		if(isset($input_array['worklog']))
		{
			//Save Project info to database only if it doesn't exit in database
			foreach($input_array['worklog'] as $log)
			{
				$exist = 0;
				$ikey_array = explode('-', $log['issue_key']);
				$ikey = $ikey_array[0];
				foreach($current_projects as $pkey)
				{
					if($pkey['project_key'] == $ikey)
					{
						$exist++;
						break;
					}
				}
				if($exist == 0)
				{
					$result_object = $CI->jira_library->no_post_api('project/'.$ikey);
					$result_array = json_decode(json_encode($result_object), true);
					$sql = "REPLACE INTO PROJECTS ".
						"(project_id, project_key, project_name) VALUES (" 
							. "'" . $result_array['id'] . "', "
							. "'" . $result_array['key'] . "', "
							. "'" . $result_array['name'] . "')";
					$CI->db->query($sql);
				}
			}
			$CI->db->close();
			return 1;
		}
		$CI->db->close();
		return 0;
	}
/********************************************************************/
// Function: tempo_userdata_to_database($input_array)
// Summary: This is a function to get user data from Tempo API,
//		  and save it to database.
//		  It updates database only when it finds a new user.
//
//		  Might need to move to MODEL.
/********************************************************************/
	public function tempo_userdata_to_database($input_array)
	{ 
		$input_array = json_decode(json_encode($input_array), true);
		
		if(isset($input_array['worklog']))
		{
			$CI =& get_instance();
			$CI->load->database();
			$CI->load->library('jira_library');
			
			//create an array of existing project names in database
			$CI->db->select('username');
			$query = $CI->db->get('USERS');
			$current_users = json_decode(json_encode($query->result()),true);
			//print_r($current_users);
			
			//Save user info to database
			foreach($input_array['worklog'] as $log)
			{
				$exist = 0;
				foreach($current_users as $user)
				{
					if($user['username'] == $log['username'])
					{
						$exist++;
						break;
					}
				}
				if($exist == 0)
				{
					$q = array();
					//echo $log['username'].' ';
					$q['username'] = $log['username'];
					$result_object = $CI->jira_library->get_to('user', $q);
					$result_array = json_decode(json_encode($result_object), true);
					$sql = "REPLACE INTO USERS "
						."(username, display_name, email) VALUES (" 
						."'".$result_array['name']."', "
						."'".$result_array['displayName']."', "
						."'".$result_array['emailAddress']."')";
					$CI->db->query($sql);
				}
			}
			$CI->db->close();
			return 1;
		} else {
			return 0;
		}
		return 0;
	}
/********************************************************************/
// function: get_all_worklog()
// Summary: This is a function to get & save all worklog data to database
/********************************************************************/
	public function get_all_worklog($duration_in_days = 8, $interval = 4)
	{
		$CI =& get_instance();
		$CI->load->library('jira_library');
		$CI->load->model('Jira_model');
		$return_data['db_entries']=0;
		$return_data['api_calls']=0;
		
		if($interval<1) return 0;
		
		date_default_timezone_set(DEFAULT_TIME_ZONE);
		//Call Tempo API for 1-week amount of worklog
		//Or it gets too heavy and causes errors.
		for($i=0; ($duration_in_days - $i*$interval) >= 1; $i++)
		{
			$dateFrom = 
				date("Y-m-d", strtotime("-".($duration_in_days - $i*$interval - 1)." day"));
			
			$last_day = $duration_in_days - $i*$interval - $interval;
			if($last_day < 0){ $last_day = 0;}
			$dateTo =
				date("Y-m-d", strtotime("-".$last_day." day"));
		
			$url_string = 
			"https://rakuten.atlassian.net/plugins/servlet/tempo-getWorklog/"
			."?format=xml&tempoApiToken=".TEMPO_API_TOKEN
			."&dateFrom=".$dateFrom."&dateTo=".$dateTo;
		
			//Get data as XML from Tempo API, and convert to an array
			$output_xml = file_get_contents($url_string);
			$worklog_array = simplexml_load_string($output_xml);
			$worklog_array = json_decode(json_encode($worklog_array), true);
			
			//when there is 0 or 1 worklog, the XML structure is different,
			//so restructure the array before putting it to database.
			$worklog_array = 
				$CI->jira_library->restructure_worklog($worklog_array);
			
			//save worklog data to database
			$return_data['db_entries'] += 
				$CI->Jira_model->worklogs_to_database($worklog_array);
			
			//save user data to database
			$return_data['db_entries'] += 
				$CI->Jira_model->tempo_userdata_to_database($worklog_array);
			
			//save project data to database
			$return_data['db_entries'] += 
				$CI->Jira_model->projects_to_database($worklog_array);
		}
		$return_data['api_calls'] = $i;
		
		return $return_data;
	}
/********************************************************************/
// Function: get_worklogs_from_database()
// Summary: Get worklogs from database, and return as an array
//
// Input:
//   'dateFrom'   - Starting date
//   'dateTo'     - Ending date
//   'userName'   - Names of users. Split names with '~'
//   'projectKey' - Project Key
//   'limit'      - Number of worklogs to get
//   'offset'     - the first position of worklogs to get
/********************************************************************/
	public function get_worklogs_from_database($input_data)
	{	
		if( isset($input_data['dateFrom']) )
		{
			$data['dateFrom'] = $input_data['dateFrom']." 00:00:00";
		} else {
			$data['dateFrom'] = date("Y-m-d 00:00:00");
		}
		if( isset($input_data['dateTo']) )
		{
			$data['dateTo'] = $input_data['dateTo']." 23:59:59";
		} else {
			$data['dateTo'] = date("Y-m-d 23:59:59");
		}
		if( isset($input_data['userName']) )
		{
			$data['userName'] = $input_data['userName'];
		}
		if( isset($input_data['projectKey']) )
		{
			$data['projectKey'] = $input_data['projectKey'];
		}
		if( isset($input_data['limit']) )
		{
			$data['limit'] = $input_data['limit'];
		}
		if( isset($input_data['offset']) )
		{
			$data['offset'] = $input_data['offset'];
		}
		
		$this->load->model('Jira_model');
		$output = $this->Jira_model->get_worklogs($data);
		return json_decode(json_encode($output), true);
	}
/********************************************************************/
// function: get_number_of_weekdays($dateFrom, $dateTo, $holidays)
// summary: return a number of business days between two dates.
//
// input:
//		$dateFrom - Starting date in Y-m-d or Y-m-d H:i:s format
//		$dateTo - Ending date in Y-m-d or Y-m-d H:i:s format
//		$holidays - holidays in array. e.g. array("2012-12-25", "2012-12-31")
// output:
//		A number of business days.
/********************************************************************/
	public function get_number_of_weekdays($dateFrom, $dateTo, $holidays)
	{		
		$dateFrom = strtotime(date("Y-m-d", strtotime($dateFrom)));
		$dateTo = strtotime(date("Y-m-d", strtotime($dateTo)));
		
		//The total number of days between the two dates. 
		$days = ($dateTo - $dateFrom) / 86400 + 1;

		$no_full_weeks = floor($days / 7);
		$no_remaining_days = fmod($days, 7);//0~6 remaining days
	
		//It will return 1 if it's Monday,.. ,7 for Sunday
		//$the_first_day_of_week = date("N", strtotime($dateFrom));
		$the_first_day_of_week = 
			date("N", strtotime(strtotime("Y-m-d",$dateTo)." -".($no_remaining_days-2)." days"));
		$the_last_day_of_week = date("N", strtotime($dateTo));
		
		//echo $no_remaining_days." ".$the_first_day_of_week." ".$the_last_day_of_week;
		
		
		//Subtract number of weekends
		if($no_remaining_days > 0)
		{
			$remaining_days = array();
			for($i=0;$i<$no_remaining_days;$i++)
			{
				$remaining_days[] = 
					date("N", strtotime(strtotime("Y-m-d",$dateTo)." -".($i)." days"));
			}
			
			foreach($remaining_days as $day)
			{
				if( $day == 6 || $day == 7)
					$no_remaining_days--;
			}
		}
		
		//Calculate number of weekdays
		$workingDays = $no_full_weeks * 5;
		if($no_remaining_days > 0 )
		{
			$workingDays += $no_remaining_days;
		}
	
		//Subtract holidays
		foreach($holidays as $holiday)
		{
			$time_stamp = strtotime($holiday);
			//If the holiday doesn't fall in weekend
			if(
			($dateFrom <= $time_stamp)
				&& ($time_stamp <= $dateTo)
				&& (date("N", strtotime($time_stamp)) != 6)
				&& (date("N", strtotime($time_stamp)) != 7)
			) $workingDays--;
		}
		
		return floor($workingDays);
	}
/********************************************************************/
}