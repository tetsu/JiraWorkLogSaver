<?php
/*
	This model represents one data entry for one table
*/
class Data_model extends CI_Model
{
	protected $data_array = array();
	protected $table_name;
	protected $curl_url;
	protected $log;
	
	public function __construct()
	{
		parent::__construct();
		$this->log = array();
		$this->log['db_entries'] = 0;
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
	
	protected function format_string($string)
	{
		if(is_array($string))
		{
			return json_encode($string);
		} 
		else
		{
			return $string;
		}
	}
	
	public function format_data_array()
	{
		//unset($this->data_array);
		//$this->data_array = array();
		$this->data_array = NULL;
	}
	
	public function format_data()
	{
		$this->data_array = NULL;
		$this->log = NULL;
		$this->log['db_entries'] = 0;
	}
	
	public function format_all_data()
	{
		$this->data_array = NULL;
		$this->table_name = NULL;
		$this->curl_url = NULL;
		$this->log = NULL;
		$this->log['db_entries'] = 0;
	}
	
	public function set_data($field_name, $content)
	{
		if(!empty($content))
		{
			$field_name = $this->format_string($field_name); 
			$content = $this->format_string($content);
		}
		
		$this->data_array[$field_name] = $content;
	}
	public function set_table_name($tn)
	{
		$this->table_name = $tn;
	}
	
	public function set_curl_url($url)
	{
		$this->curl_url = $url;
	}
	
	public function get_data($field_name)
	{
		return $this->data_array[$field_name];
	}
	
	public function get_data_array()
	{
		return $this->data_array;
	}
	
	public function get_table_name()
	{
		return $this->table_name;
	}
	
	public function get_curl_url()
	{
		return $this->curl_url();
	}
	
	public function get_log($field_name)
	{
		return $this->log[$field_name];
	}
	
	public function get_log_array()
	{
		return $this->log;
	}
	
	public function set_log($field_name, $value)
	{
		return $this->log[$field_name] = $value;
	}
	
	public function format_log()
	{
		//$this->log = array();
		unset($this->log['db_entries']);
		$this->log['db_entries'] = 0;
	}
	
	
	//**************************************
	// save data in $data_array to database
	//**************************************
	public function save_to_db()
	{
		//echo "Save1: ".memory_get_usage().". \n";
		
		$fields = "";
		$content = "";
		$i = 0;
		
		//echo "Save2: ".memory_get_usage().". \n";
		
		foreach($this->data_array as $field_name => $value)
		{
			if(empty($value))
			{
				$value="";
			} elseif($i == 0)
			{
				$fields .= $field_name;
				$content .= "'".mysql_real_escape_string($value)."'";
			} else {
				$fields .= ", ".$field_name;
				$content .= ", "."'".mysql_real_escape_string($value)."'";
			}
			$i++;
		}
		
		$sql = "REPLACE INTO ".$this->table_name." "
		       ."(".$fields.") VALUES (".$content.")";
		$db_success = $this->db->query($sql);
		$this->log['db_entries'] += strval($db_success);
		
		//echo "Save3: ".memory_get_usage().". \n";
		return $db_success;
	}
	
	//**************************************
	// save an array to database
	//**************************************
	public function array_to_db($table, $array)
	{
		$fields = "";
		$content = "";
		$i = 0;
		
		foreach($array as $field_name => $value)
		{
			if(empty($value))
			{
				$value="";
			} elseif($i == 0)
			{
				$fields .= $field_name;
				$content .= "'".mysql_real_escape_string($value)."'";
			} else {
				$fields .= ", ".$field_name;
				$content .= ", "."'".mysql_real_escape_string($value)."'";
			}
			$i++;
		}
		
		$sql = "REPLACE INTO ".$table." "
		       ."(".$fields.") VALUES (".$content.")";
		return $this->db->query($sql);
	}
	
	//*************************************************
	// show $data_array content as SQL REPLACE command 
	//*************************************************
	public function get_sql()
	{
		$fields = "";
		$content = "";
		$i = 0;
		
		foreach($this->data_array as $field_name => $value)
		{
			if(empty($value))
			{
				$value="";
			}
			elseif($i == 0) {
				$fields .= $field_name;
				$content .= "'".mysql_real_escape_string($value)."'";
				$i++;
			}
			else {
				$fields .= ", ".$field_name;
				$content .= ", "."'".mysql_real_escape_string($value)."'";
				$i++;
			}
		}
		
		$sql = "REPLACE INTO ".$this->table_name." "
		       ."(".$fields.") VALUES (".$content.")";
		return $sql;
	}
	
	//******************************************
	// Summary: Combine two single-level arrays
	//******************************************
	public function combine_arrays($array1, $array2)
	{
		if(!empty($array1) && !empty($array2))
		{
			foreach($array2 as $value)
			{
				$array1[] = $value;
			}
		}
		
		return $array1;
	}
	
	//******************************************
	// Method: log_cron($data)
	// Summary: Save Cron log data to database
	//******************************************
	public function log_cron($data)
	{
		//echo "\n"; print_r($data);
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
	
	//************************************************************
	// Method: curl_request($format)
	// Summary: Use Jira REST API without GET or POST data input
	//************************************************************
	public function curl_request($format = "json")
	{
		if (($ch = curl_init()) == false)
		{
			throw new Exception("curl_init error");
		}
		curl_setopt_array($ch, array(
			CURLOPT_URL => $this->curl_url,
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
}

/* End of file base_model.php */
/* Location: ./application/models/base_model.php */