<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
/********************************************************************/
// Jiradb_library.php
//
// Author: Tetsuro Mori
/********************************************************************/
class Jiradb_library
{
	protected $curl_session;
	protected $data;
	protected $xml;
	protected $log;
	protected $object;
	protected $CI;
/********************************************************************/
	public function index()
	{
		echo "Hello World\n";
	}
/********************************************************************/
	public function __construct()
	{
		$this->log = array();
		$this->log['total_issues'] = 0;
		$this->log['num_of_issues'] = 0;
		$this->log['api_calls'] = 0;
		$this->log['db_entries'] = 0;
		$this->log['max'] = 0;
		
		$this->CI =& get_instance();
		$this->CI->load->model('Data_model', 'Basic_data');
		$this->CI->Basic_data->set_table_name("ISSUES");
		$this->CI->load->model('FixVersion_data_model', 'FixVersion_data');
		$this->CI->load->model('Component_data_model', 'Component_data');
		$this->CI->load->model('Comment_data_model', 'Comment_data');
		//$this->CI->load->model('Issue_link_data_model', 'Link_data');
		$this->CI->load->model('Subtask_data_model', 'Subtask_data');
		$this->CI->load->model('Customfield_model', 'Customfield_data');
		$this->CI->load->model('Label_data_model', 'Label_data');
	}
/********************************************************************/
	protected function format_CI()
	{
		echo "Before CI Format: ".memory_get_usage().". ";
		
		$this->CI =& get_instance();
		$this->CI->load->model('Data_model', 'Basic_data');
		$this->CI->Basic_data->set_table_name("ISSUES");
		$this->CI->load->model('FixVersion_data_model', 'FixVersion_data');
		$this->CI->load->model('Component_data_model', 'Component_data');
		$this->CI->load->model('Comment_data_model', 'Comment_data');
		//$this->CI->load->model('Issue_link_data_model', 'Link_data');
		$this->CI->load->model('Subtask_data_model', 'Subtask_data');
		$this->CI->load->model('Customfield_model', 'Customfield_data');
		$this->CI->load->model('Label_data_model', 'Label_data');
		echo "After CI Format: ".memory_get_usage().". \n";
	}
/********************************************************************/
	public function set_data_array($input_array)
	{
		$this->data = $input_array;
	}
/********************************************************************/
	public function log_cron($data)
	{
		$CI =& get_instance();
		$CI->load->database();
		if(isset($data))
		{
			if(!$CI->db->insert('CRONLOGS', $data))
			{
				throw new Exception("Database Error.");
			}
		}
		else
		{
			throw new Exception("No data input.");
		}
	}
/********************************************************************/
	public function set_data($name, $value)
	{
		$this->data[$name] = $value;
	}
/********************************************************************/
	public function get_log()
	{
		return $this->log;
	}
/********************************************************************/
	public function set_curl_session($ch)
	{
		$this->curl_session = $ch;
	}
/********************************************************************/
// project = "GI" AND updated > "2013-06-06 00:00" AND updated < "2013-06-06 20:00"
// $interval : The interval time to grab data. 3600 to get 1 hour of past data
// $every    : How often the system grab data. 3600 for every hour.
//
// date("Y-m-d H:i", strtotime( "-".($i-1)." day" ));
/********************************************************************/
	public function new_loop_controller()
	{
		echo "Loop Start: ".memory_get_usage().". \n";
		if($this->data['interval']){
			$interval = $this->data['interval'];
			
			if($this->data['every']){
				$every = $this->data['every'];
			} else {
				$every = 24*60*60;
			}
		} else {
			$interval = 24*60*60;
			$every = 24*60*60;
		}
		
		if($this->data['ejql'] != ""){
			$ejql = urldecode($this->data['ejql'])." AND ";
		} else {
			$ejql = "";
		}
		
		for($i = $this->data['start']; $i > $this->data['end']; $i -= $every){
			$this->xml = NULL;
			$this->object = NULL;
			$start_time = date("Y-m-d H:i", strtotime( "-".$i." sec"));
			$end_time = date("Y-m-d H:i", strtotime( "-".($i-$interval)." sec"));
				
			$this->data['jql'] = $ejql."updated > \"".$start_time."\""
    							." AND updated < \"".$end_time."\""
    							." ORDER BY updated DESC";
    		
    		$this->data['jql'] = rawurlencode($this->data['jql']);
    		//echo $this->data['jql']."\n";
    		//echo $start_time." ".$end_time."\n";
    		//echo $this->log['max']."\n";
    		echo "Before XML: ".memory_get_usage().". ";
			$this->get_xml();
			echo "After XML: ".memory_get_usage().". \n";
		}
		return $this->curl_session;
	}
/********************************************************************/
	protected function get_xml()
	{
		$field_str = "";
		$max_str = "";
		$cookiefile = tempnam("/tmp", "cookies");
		$agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
		
		if($this->data['field'])
			$field_str = "&field=".$this->data['field'];
		
		if($this->data['max'] == 0)
		{
			$this->data['max'] = 1000;
			$max_str = "&tempMax=1000";
		}
		elseif($this->data['max'] > 0)
			$max_str = "&tempMax=".$this->data['max'];
			
		echo XML_URL."?jqlQuery=".$this->data['jql'].$max_str.$field_str."\n";
		$url_options = XML_URL."?jqlQuery=".$this->data['jql'].$max_str.$field_str."\n";
		
		curl_setopt_array($this->curl_session, array(
			CURLOPT_URL => $url_options,
			CURLOPT_USERPWD => USERNAME.':'.PASSWORD,
			CURLOPT_HTTPHEADER => array('Content-type: application/json'),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_USERAGENT => $agent,
			CURLOPT_COOKIEFILE => $cookiefile,
			CURLOPT_COOKIEJAR => $cookiefile,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => 0
		));
		$this->xml = NULL;
		$this->xml = curl_exec($this->curl_session);
		$this->log['api_calls']++;
		if($this->xml === false)
		{
			throw new Exception("curl_exec error");
			echo "curl failed";
		}
		
		echo "Received Jira XML result.\n";
		//echo "Before Manager: ".memory_get_usage().". ";
		$this->xml_manager();
		$this->object = NULL;
		//echo "After Manager: ".memory_get_usage().". \n";
	}
/********************************************************************/
	protected function xml_manager()
	{
		//echo $this->xml."\n";
		$this->object = NULL;
		$this->object = simplexml_load_string($this->xml);
		$this->xml = NULL;
		if($this->object)
		{
			echo "XML was converted to an object.\n";

			$total = $this->object->channel->issue->attributes()->total;
			$num_of_items = count($this->object->channel->item);
			echo $num_of_items." of ".$total." items abstracted.\n";
		
			$this->log['total_issues'] += $total;
			$this->log['num_of_issues'] += $num_of_items;
		
			$this->log['max'] = intval($this->log['max']);
			$total = intval($total);
			if( ($this->log['max']) < $total)
			{
				$this->log['max'] = $total;
			}
		
			for($i=0; $i<$num_of_items; $i++)
			{
				$this->manipulate_item($i);
			}
			$this->object = NULL;
		} else {
			echo "XML was empty\n";
		}
	}
/********************************************************************/
	protected function manipulate_item($i)
	{
		//echo "Mani2 Before: ".memory_get_usage().". ";
		
		//$this->object->channel->item[$i];
		
		if($this->object->channel->item[$i]->key)
		{
			$this->CI->Basic_data->set_data("issue_key", (string)$this->object->channel->item[$i]->key);
			$this->CI->Basic_data->set_data("issue_id", (int)$this->object->channel->item[$i]->key->attributes()->id);
		}
		if($this->object->channel->item[$i]->title)
			$this->CI->Basic_data->set_data("issue_title", (string)$this->object->channel->item[$i]->title);
		if($this->object->channel->item[$i]->link)
			$this->CI->Basic_data->set_data("issue_link", (string)$this->object->channel->item[$i]->link);
		if($this->object->channel->item[$i]->project)
		{
			$this->CI->Basic_data->set_data("project_name",
				(string)$this->object->channel->item[$i]->project);
			$this->CI->Basic_data->set_data("project_key",
				(string)$this->object->channel->item[$i]->project->attributes()->key);
		}
		if($this->object->channel->item[$i]->description)
			$this->CI->Basic_data->set_data("issue_description", (string)$this->object->channel->item[$i]->description);
		if($this->object->channel->item[$i]->environment)
			$this->CI->Basic_data->set_data("issue_environment", (string)$this->object->channel->item[$i]->environment);
		if($this->object->channel->item[$i]->summary)
			$this->CI->Basic_data->set_data("issue_summary", (string)$this->object->channel->item[$i]->summary);
		if($this->object->channel->item[$i]->parent)
			$this->CI->Basic_data->set_data("parent_issue_key", (string)$this->object->channel->item[$i]->parent);
		if($this->object->channel->item[$i]->type)
			$this->CI->Basic_data->set_data("type", (string)$this->object->channel->item[$i]->type);
		if($this->object->channel->item[$i]->priority)
			$this->CI->Basic_data->set_data("priority", (string)$this->object->channel->item[$i]->priority);
		if($this->object->channel->item[$i]->status)
			$this->CI->Basic_data->set_data("status", (string)$this->object->channel->item[$i]->status);
		if($this->object->channel->item[$i]->resolution)
			$this->CI->Basic_data->set_data("resolution_name", (string)$this->object->channel->item[$i]->resolution);
		if($this->object->channel->item[$i]->assignee)
		{
			$this->CI->Basic_data->set_data("issue_assignee_displayname",
				(string)$this->object->channel->item[$i]->assignee);
			$this->CI->Basic_data->set_data("issue_assignee_username",
				(string)$this->object->channel->item[$i]->assignee->attributes()->username);
		}
		if($this->object->channel->item[$i]->reporter)
		{
			$this->CI->Basic_data->set_data("issue_assignee_displayname",
				(string)$this->object->channel->item[$i]->reporter);
			$this->CI->Basic_data->set_data("issue_assignee_username",
				(string)$this->object->channel->item[$i]->reporter->attributes()->username);
		}
		if($this->object->channel->item[$i]->labels)
		{
			$this->CI->Label_data->object_to_db((string)$this->object->channel->item[$i]->key, $this->object->channel->item[$i]->labels);
			$this->log['db_entries'] += $this->CI->Label_data->get_log('db_entries');
		}
		if($this->object->channel->item[$i]->created)
			$this->CI->Basic_data->set_data("issue_created", (string)$this->object->channel->item[$i]->created);
		if($this->object->channel->item[$i]->updated)
			$this->CI->Basic_data->set_data("issue_updated", (string)$this->object->channel->item[$i]->updated);
		if($this->object->channel->item[$i]->resolved)
			$this->CI->Basic_data->set_data("issue_resolved_date", (string)$this->object->channel->item[$i]->resolved);
		if($this->object->channel->item[$i]->component)
		{
			$this->CI->Component_data->object_to_db((string)$this->object->channel->item[$i]->key, $this->object->channel->item[$i]->component);
			$this->log['db_entries'] += $this->CI->Component_data->get_log('db_entries');
		}
		if($this->object->channel->item[$i]->fixVersion)
		{
			$this->CI->FixVersion_data->object_to_db((string)$this->object->channel->item[$i]->key, $this->object->channel->item[$i]->fixVersion);
			$this->log['db_entries'] += $this->CI->FixVersion_data->get_log('db_entries');
		}
		if($this->object->channel->item[$i]->due)
			$this->CI->Basic_data->set_data("issue_due", (string)$this->object->channel->item[$i]->due);
		if($this->object->channel->item[$i]->views)
			$this->CI->Basic_data->set_data("issue_views", (int)$this->object->channel->item[$i]->views);
		if($this->object->channel->item[$i]->votes)
			$this->CI->Basic_data->set_data("issue_votes", (int)$this->object->channel->item[$i]->votes);
		if($this->object->channel->item[$i]->watches)
			$this->CI->Basic_data->set_data("issue_watches", (int)$this->object->channel->item[$i]->watches);
		if($this->object->channel->item[$i]->timeoriginalestimate)
			$this->CI->Basic_data->set_data("time_original_estimate",
				(string)$this->object->channel->item[$i]->timeoriginalestimate->attributes()->seconds);
		if($this->object->channel->item[$i]->timeestimate)
			$this->CI->Basic_data->set_data("time_estimate",
				(string)$this->object->channel->item[$i]->timeestimate->attributes()->seconds);
		if($this->object->channel->item[$i]->timespent)
			$this->CI->Basic_data->set_data("time_spent",
				(string)$this->object->channel->item[$i]->timespent->attributes()->seconds);
		if($this->object->channel->item[$i]->aggregatetimeoriginalestimate)
			$this->CI->Basic_data->set_data("aggregate_time_original_estimate",
				(string)$this->object->channel->item[$i]->aggregatetimeoriginalestimate->attributes()->seconds);
		if($this->object->channel->item[$i]->aggregatetimeremainingestimate)
			$this->CI->Basic_data->set_data("aggregate_time_remaining_estimate",
				(string)$this->object->channel->item[$i]->aggregatetimeremainingestimate->attributes()->seconds);
		if($this->object->channel->item[$i]->aggregatetimespent)
			$this->CI->Basic_data->set_data("aggregate_time_spent",
				(string)$this->object->channel->item[$i]->aggregatetimespent->attributes()->seconds);
		if($this->object->channel->item[$i]->comments)
		{
			$this->CI->Comment_data->object_to_db((string)$this->object->channel->item[$i]->key, $this->object->channel->item[$i]->comments);
			$this->log['db_entries'] += $this->CI->Comment_data->get_log('db_entries');
		}
		if($this->object->channel->item[$i]->issuelinks)
		{
			//write code
			//$this->CI->Link_data->object_to_db((int)$this->object->channel->item[$i]->key->attributes()->id, (string)$item->key, $item->fixVersion);
		}
		if($this->object->channel->item[$i]->attachments)
		{
			//write code
		}
		if($this->object->channel->item[$i]->subtasks)
		{
			$this->CI->Subtask_data->subtask_to_db((string)$this->object->channel->item[$i]->key,
				json_decode(json_encode($this->object->channel->item[$i]->subtasks), true));
			$this->log['db_entries'] += $this->CI->Subtask_data->get_log('db_entries');
		}
		if($this->object->channel->item[$i]->customfields)
		{
			$this->CI->Customfield_data->customfield_to_db((string)$this->object->channel->item[$i]->key,
				json_decode(json_encode($this->object->channel->item[$i]->customfields), true));
			//$this->CI->Customfield_data->object_to_db($key,$content);
			$this->log['db_entries'] += $this->CI->Customfield_data->get_log('db_entries');
		}
		
		echo "Before DB: ".memory_get_usage().". ";
		$this->CI->Basic_data->save_to_db(); $this->log['db_entries']++;
		echo "After DB: ".memory_get_usage().". ";
		
		echo "Saved ".(string)$this->object->channel->item[$i]->key." to database. \n";
		//print_r($this->CI->Basic_data->get_data_array());echo "\n";
		
		$this->CI->Basic_data->format_data();
		$this->CI->FixVersion_data->format_data();
		$this->CI->Component_data->format_data();
		$this->CI->Comment_data->format_data();
		$this->CI->Subtask_data->format_data();
		//$this->CI->Link_data->format_data();
		$this->CI->Customfield_data->format_data();
		$this->CI->Label_data->format_data();
		
		$this->object->channel->item[$i] = NULL;
		
		//echo "Mani2 After: ".memory_get_usage().". \n";
	}
/********************************************************************/
}