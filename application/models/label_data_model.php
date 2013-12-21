<?php
/*
	This model represents LABEL data entry.
	Inheriting variables and methods from Data_model.php
*/
require_once( APPPATH . 'models/data_model' . EXT);

class Label_data_model extends Data_model
{
	public function __construct()
	{
		parent::__construct();
		$this->table_name = "LABELS";
	}
	
	public function label_to_db($issue_key, $input_array)
	{
		if(isset($input_array['label']))
		{
			$input_array = $this->format_array($input_array['label']);
		
			foreach($input_array as $value)
			{
				$this->set_data("issue_key", $issue_key);
				$this->set_data("label_value", $value);
				$this->save_to_db();
			}
		}
	}
	
	public function object_to_db($issue_key, $input_object)
	{
		$input_array = json_decode(json_encode($input_object), true);
		
		if(isset($input_array['label']))
		{
			$input_array = $this->format_array($input_array['label']);
		
			foreach($input_array as $value)
			{
				$this->set_data("issue_key", $issue_key);
				$this->set_data("label_value", $value);
				$this->save_to_db();
			}
		}
	}
	
	public function manipulate($content, $issue_key)
	{
		$num_of_db_entries = 0;
		
		foreach($content as $label)
		{
			$this->set_data("issue_key", $issue_key);
			$this->set_data("label_value", $label);
			$num_of_db_entries += $this->save_to_db();
		}
		
		return $num_of_db_entries;
		
	}
	
	public function generate_sql($content, $issue_key)
	{
		$num_of_db_entries = 0;
		$sql = array();
		
		//echo $content." ".$issue_key."\n";
		
		foreach($content as $label)
		{
			$this->set_data("issue_key", $issue_key);
			$this->set_data("label_value", $label);
			$sql[] = $this->get_sql();
		}
		
		return $sql;
		
	}
}

/* End of file label_data_model.php */
/* Location: ./application/models/label_data_model.php */