<?php
/*
	This model represents FixVersion data entry.
	Inheriting variables and methods from Data_model.php
*/
require_once( APPPATH . 'models/data_model' . EXT);

class FixVersion_data_model extends Data_model
{
	public function __construct()
	{
		parent::__construct();
		$this->table_name = "FIXVERSIONS";
	}
	
	public function fixversion_to_db($issue_key, $input_array)
	{	
		if(!empty($input_array))
		{
			$input_array = $this->format_array($input_array);
		
			foreach($input_array as $value)
			{
				$this->set_data("issue_key", $issue_key);
				$this->set_data("fix_version_name", $value);
				$this->save_to_db();
			}
		}
	}
	
	public function object_to_db($issue_key, $obj)
	{	
		if($obj)
		{
			for($i=0;$i<count($obj);$i++)
			{
				$this->table_name = "ISSUE_FIXVERSION";
				$this->set_data("issue_key", $issue_key);
				$this->set_data("fix_version_name", (string)$obj[$i]);
				$this->save_to_db();
				$this->table_name = "FIXVERSIONS";
				$this->format_data_array();
				//echo $issue_key.":".(string)$obj[$i]."\n";
			}
		}
	}
	
	public function manipulate($content)
	{
		$num_of_db_entries = 0;
		
		foreach($content as $fixversion)
		{
			foreach($fixversion as $field_name => $value)
			{
				switch($field_name)
				{
					case 'name':
						$this->set_data("fixversion_name", $value); break;
					case 'id':
						$this->set_data("fixversion_id", $value); break;
					case 'self':
						$this->set_data("fixversion_info_url", $value); break;
					case 'archived':
						$this->set_data("fixversion_archived", $value); break;
					case 'released':
						$this->set_data("fixversion_released", $value); break;
				}
			}
			
			$num_of_db_entries += $this->save_to_db();
		}
		
		return $num_of_db_entries;
	}
	
	public function generate_sql($content)
	{
		$sql = array();
		
		foreach($content as $fixversion)
		{
			foreach($fixversion as $field_name => $value)
			{
				switch($field_name)
				{
					case 'name':
						$this->set_data("fixversion_name", $value); break;
					case 'id':
						$this->set_data("fixversion_id", $value); break;
					case 'self':
						$this->set_data("fixversion_info_url", $value); break;
					case 'archived':
						$this->set_data("fixversion_archived", $value); break;
					case 'released':
						$this->set_data("fixversion_released", $value); break;
				}
			}
			
			$sql[] = $this->get_sql();
		}
		
		return $sql;
	}
	
	public function get_fixversion_id($fixversion_name)
	{
		$fixversion_id = 0;
		return $fixversion_id;
	}
	
	public function object_to_string($obj)
	{	
		$fixversion_names = "";
		if($obj)
		{
			for($i=0;$i<count($obj);$i++)
			{
				if($i==0)
					$fixversion_names = (string)$obj[$i];
				else
					$fixversion_names .= ", ".(string)$obj[$i];
			}
			
			echo $fxiversion_name."\n";
		}
		return $fixversion_name;
	}
}

/* End of file fixversion_data_model.php */
/* Location: ./application/models/fixversion_data_model.php */