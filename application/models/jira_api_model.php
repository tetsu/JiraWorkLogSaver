<?php
/*
	This model handles Jira REST APIs
*/
class Jira_API_model extends CI_Model
{
	protected $api_url = JIRA_API_BASE_URL;
	protected $data = array();
	protected $api_type = "";
	
	public function set_api_url($url)
	{
		$this->api_url = $url;
	}
	
	public function get_api_url()
	{
		return $this->api_url;
	}
	
	public function set_api_type($api_name)
	{
		$this->api_type = $api_name;
	}
	
	public function get_api_type()
	{
		return $this->api_type;
	}
	
	public function set_data($field_name, $value)
	{
		$this->data[$field_name] = $value;
	}
	
	public function get_data($field_name)
	{
		return $this->data[$field_name];
	}
	
	public function get_data_array()
	{
		return $this->data;
	}
	
	public function __construct()
	{
		parent::__construct();
	}
	
	protected function format_array($input_array)
	{
		if(count($input_array) == 1)
		{
			$output_array = array();
			$output_array[0] = $input_array;
			return $output_array;
		}
		elseif(count($input_array) > 1) {
			return $input_array;
		}
		elseif(count($input_array) == 0) {
			return "";
		}
	}
	
	/*********************************************************/
	// Method: curl_post($format)
	// Summary: Use Jira REST API with POST data input method
	/*********************************************************/
	protected function curl_post($format = "json")
	{
		if (($ch = curl_init()) == false)
		{
			throw new Exception("curl_init error");
		}
		
		curl_setopt_array($ch, array(
			CURLOPT_POST => true,
			CURLOPT_URL => $this->api_url.$this->api_type,
			CURLOPT_USERPWD => USERNAME.':'.PASSWORD,
			CURLOPT_POSTFIELDS => $this->data,
			CURLOPT_HTTPHEADER => array('Content-type: application/json'),
			CURLOPT_RETURNTRANSFER => true
		));
		$result = curl_exec($ch);
		curl_close($ch);
		
		if ($result === false)
		{
			throw new Exception("curl_exec error");
			return false;
		}
		
		
		if($format == "json") return $result;
		elseif($format == "array") return json_decode($result, true);
	}
	
	/********************************************************/
	// Method: curl_get($format)
	// Summary: Use Jira REST API with GET data input method
	/********************************************************/
	protected function curl_get($format = "json")
	{
		$request_string = "";
		$i = 0;
		$result;
		
		foreach($this->data as $field_name => $value)
		{
			if($i>0) $request_string .= "&";
			$request_string .= $field_name."=".$value;
			$i++;
		}
		
		if (($ch = curl_init()) == false)
		{
			throw new Exception("curl_init error");
		}
		
		curl_setopt_array($ch, array(
			CURLOPT_URL => $this->api_url.$this->api_type."?".$request_string,
			CURLOPT_USERPWD => USERNAME.':'.PASSWORD,
			CURLOPT_HTTPHEADER => array('Content-type: application/json'),
			CURLOPT_RETURNTRANSFER => true
		));
		$result = curl_exec($ch);
		curl_close($ch);
		
		if ($result === false)
		{
			throw new Exception("curl_exec error");
			return false;
		}
		
		
		if($format == "json") return $result;
		elseif($format == "array") return json_decode($result, true);
	}
	
	/************************************************************/
	// Method: curl_request($format)
	// Summary: Use Jira REST API without GET or POST data input
	/************************************************************/
	public function curl_request($format = "json")
	{
		if (($ch = curl_init()) == false)
		{
			throw new Exception("curl_init error");
		}
		curl_setopt_array($ch, array(
			CURLOPT_URL => $this->api_url.$api_type,
			CURLOPT_USERPWD => USERNAME . ':' . PASSWORD,
			CURLOPT_HTTPHEADER => array('Content-type: application/json'),
			CURLOPT_RETURNTRANSFER => true
		));
		$result = curl_exec($ch);
		curl_close($ch);
		
		if ($result === false) {
			throw new Exception("curl_exec error");
			return false;
		}
		
		
		if($format == "json") return $result;
		elseif($format == "array") return json_decode($result, true);
	}
	/********************************************************/
	// Method: get_sql()
	// Summary: Return SQL query
	/********************************************************/
	public function get_request_url()
	{
		$request_string = "";
		$i = 0;
		
		foreach($this->data as $field_name => $value)
		{
			if($i == 0)
			{
				$request_string .= "?";
			}
			elseif($i>0)
			{
				$request_string .= "&";
			}
			$request_string .= $field_name."=".$value;
			$i++;
		}
		
		return $this->api_url.$api_type.$request_string;
	}
	/********************************************************/
	// Method: get_rows($table_name, $field_name, $value)
	// Summary: checks if the data exists. 
	//          Return number of results if exists.
	//          Return 0 if it doesn't exit.
	/********************************************************/
	public function get_rows($table_name, $field_name, $value)
	{
		$sql = "SELECT * FROM ".$table_name
		       ." WHERE ".$field_name." = '".$value."'";
		$query = $this->db->query($sql);
		
		return $query->num_rows();
	}
	/********************************************************/
	// Method: get_rows($table_name, $field_name, $value)
	// Summary: checks if the data exists. 
	//          Return number of results if exists.
	//          Return 0 if it doesn't exit.
	/********************************************************/
	public function get_rows_pkey($table_name, $field_name, $value, $primal_key)
	{
		$sql = "SELECT ".$primal_key.",".$field_name." FROM ".$table_name
		       ." WHERE ".$field_name." = '".$value."'";
		$query = $this->db->query($sql);
		
		return $query->num_rows();
	}
	/*********************************************************/
	// Method: log_cron($data)
	// Summary: Save Cron log data to dtabase
	/*********************************************************/
	public function log_cron($data)
	{
		if(isset($data))
		{
			if(!$this->db->insert('CRONLOGS', $data))
			{
				throw new Exception("Database Error.");
			}
		}
		else
		{
			throw new Exception("No data input.");
		}
	}
	/**********************************************************/
}

/* End of file jira_api_model.php */
/* Location: ./application/models/jira_api_model.php */