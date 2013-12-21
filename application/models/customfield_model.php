<?php
/*
	This model represents CUSTOMFIELD data entry.
	Inheriting variables and methods from Data_model.php
*/
require_once( APPPATH . 'models/data_model' . EXT);

class Customfield_model extends Data_model
{
/********************************************************************/
	public function __construct()
	{
		parent::__construct();
		$this->table_name = "CUSTOMFIELDS";
	}
/********************************************************************/
	public function customfield_to_db($issue_key, $input_array)
	{	
		$db_entries = 0;
		$cfv = array();
		
		if(isset($input_array['customfield']))
		{
			$input_array = $this->format_array($input_array['customfield']);
		
			foreach($input_array as $content)
			{
				//$this->set_data("issue_key", $issue_key);
				
				foreach($content as $field => $data)
				{
					switch($field)
					{
						case "@attributes":
							$id = $data['id'];
							$this->set_data("customfield_id", $data['id']);
							$this->set_data("customfield_key", $data['key']);
							break;
						case "customfieldname":
							$this->set_data("customfield_name", $data);
							break;
						case "customfieldvalues":
							foreach($data as $customfield => $value)
							{
								switch($customfield)
								{
									case "0":
										//echo "No data\n";
										break;
									case "customfieldvalue":
										$cfv['customfield_id'] = $id;
										$cfv['customfield_value'] = $value;
										$cfv['issue_key'] = $issue_key;
										$db_entries += $this->send_to_db("CUSTOMFIELDVALUES", $cfv);
										break;
									default:
										//print_r($value);
										break;
								}
							}
							break;
					}
				}
			}
			$db_entries += $this->save_to_db();
			//echo $this->get_sql()."\n";
			//echo "Customfieldvalue saved.\n";
			//echo "End of customfield. ".$db_entries."\n";
			return $db_entries; 
		}
	}
/********************************************************************/
	public function object_to_db($issue_key, $input_object)
	{	
		$db_entries = 0;
		$cfv = array();
		
		if(isset($input_array['customfield']))
		{
			$input_array = $this->format_array($input_array['customfield']);
		
			foreach($input_array as $content)
			{
				//$this->set_data("issue_key", $issue_key);
				
				foreach($content as $field => $data)
				{
					switch($field)
					{
						case "@attributes":
							$id = $data['id'];
							$this->set_data("customfield_id", $data['id']);
							$this->set_data("customfield_key", $data['key']);
							break;
						case "customfieldname":
							$this->set_data("customfield_name", $data);
							break;
						case "customfieldvalues":
							foreach($data as $customfield => $value)
							{
								switch($customfield)
								{
									case "0":
										//echo "No data\n";
										break;
									case "customfieldvalue":
										$cfv['customfield_id'] = $id;
										$cfv['customfield_value'] = $value;
										$cfv['issue_key'] = $issue_key;
										$db_entries += $this->send_to_db("CUSTOMFIELDVALUES", $cfv);
										break;
									default:
										//print_r($value);
										break;
								}
							}
							break;
					}
				}
			}
			$db_entries += $this->save_to_db();
			//echo $this->get_sql()."\n";
			//echo "Customfieldvalue saved.\n";
			//echo "End of customfield. ".$db_entries."\n";
			return $db_entries; 
		}
	}
/********************************************************************/
	public function test()
	{
		return "test";
	}
/********************************************************************/
	public function custom_to_db($issue_key, $input_array)
	{
		$db_entries = 0;
		$cfv = array();
		
		$input_array = $this->format_array($input_array['customfield']);
		
		
		foreach($input_array as $content)
		{
			//$this->set_data("issue_key", $issue_key);
			
			foreach($content as $field => $data)
			{
				if($field == "@attributes")
				{
					$id = $data['id'];
					$this->set_data("customfield_id", $data['id']);
					$this->set_data("customfield_key", $data['key']);
				}
				elseif($field == "customfieldname")
				{
					$this->set_data("customfield_name", $data);
				}
				elseif($field == "customfieldvalues")
				{
					foreach($data as $customfield => $value)
					{
						if($customfield == "0")
						{
							//echo "No data\n\n";
						}
						elseif($customfield == "customfieldvalue")
						{
							$cfv['customfield_id'] = $id;
							$cfv['customfield_value'] = $value;
							$cfv['issue_key'] = $issue_key;
							$db_entries += $this->send_to_db("CUSTOMFIELDVALUES", $cfv);
						}
						else
						{
							//print_r($value);
						}
					}
					$db_entries += $this->save_to_db();
				}
			}
		}
		return $db_entries;
	}
/********************************************************************/
	public function send_to_db($table, $input_array)
	{
		$fields = "";
		$content = "";
		$i = 0;
		
		foreach($input_array as $field_name => $value)
		{
			if(is_array($value))
				$value = json_encode($value);
			
			if(empty($value))
			{
				$value = "";
			}
			elseif($i == 0)
			{
				$fields .= $field_name;
				$content .= "'".mysql_real_escape_string($value)."'";
			} 
			else
			{
				$fields .= ", ".$field_name;
				$content .= ", "."'".mysql_real_escape_string($value)."'";
			}
			$i++;
		}
		
		$sql = "REPLACE INTO ".$table." "
		       ."(".$fields.") VALUES (".$content.")";
		return $this->db->query($sql);
	}
/********************************************************************/
/********************************************************************/
}