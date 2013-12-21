<?php
/*
	This model handles Jira Search REST API
*/
require_once( APPPATH . 'models/jira_api_model' . EXT);

class Search_API_model extends Jira_API_model
{
	public function __construct()
	{
		parent::__construct();
		$this->api_type = "search";
		$this->data['jql'] = "";
		$this->data['startAt'] = 0;
		$this->data['maxResults'] = 100;
	}
	
	public function set_search_data($jql, $start_at, $max_results)
	{
		$this->data['jql'] = $jql;
		$this->data['startAt'] = $start_at;
		$this->data['maxResults'] = $max_results;
	}
	
	private function check_search_data()
	{
		if(!isset($this->data['startAt'])) return false;
		elseif(!isset($this->data['maxResults'])) return false;
		
		return true;
	}
	
	/************************************************************/
	// Method: get_total()
	// Summary: Get the total number of issues
	//          that Rakuten Jira onDemand account has.
	/************************************************************/
	public function get_total()
	{
		if($this->check_search_data())
		{
			$temp_jql = $this->data['jql'];
			$temp_start_at = $this->data['startAt'];
			$temp_max_results = $this->data['maxResults'];
		}
		
		$this->data['startAt'] = 0;
		$this->data['maxResults'] = 0;
		
		$result_data = $this->curl_get("array");
		
		if(isset($temp_start_at))
			$this->data['startAt'] = $temp_start_at;
		if(isset($temp_max_results))
			$this->data['maxResults'] = $temp_max_results;
		
		return $result_data['total'];
	}
	
	/************************************************************/
	// Method: get_issues()
	// Summary: Get detail data of Jira issues.
	/************************************************************/
	public function get_issues()
	{
		if(!$this->check_search_data())
		{
			throw new Exception("Need to set JQL, startAt, and maxResults");
			return array();
		}
		
		$result_array = $this->curl_get("array");
		
		return $result_array['issues'];
	}
}

/* End of file search_api_model.php */
/* Location: ./application/models/search_api_model.php */