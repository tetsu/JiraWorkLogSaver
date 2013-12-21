<?php
/*
	This model represents SUBTASK data entry.
	Inheriting variables and methods from Data_model.php
*/
require_once( APPPATH . 'models/data_model' . EXT);

class Subtask_data_model extends Data_model
{
	public function __construct()
	{
		parent::__construct();
		$this->table_name = "SUBTASKS";
	}
	
	public function subtask_to_db($parent_key, $input_array)
	{	
		if(isset($input_array['subtask']))
		{
			$input_array = $this->format_array($input_array['subtask']);
		
			foreach($input_array as $value)
			{
				$this->set_data("subtask_issue_key", $value);
				$this->set_data("parent_issue_key", $parent_key);
				$this->save_to_db();
			}
		}
	}
	
	public function manipulate($content, $parent_issue_key)
	{
		$num_of_db_entries = 0;
		
		foreach($content as $subtask)
		{
			foreach($subtask as $field_name => $value)
			{
				switch($field_name)
				{
					case 'key':
						$this->set_data("subtask_issue_key", $value); break;
					case 'id':
						$this->set_data("subtask_id", $value); break;
					case 'self':
						$this->set_data("subtask_info_url", $value); break;
				}
			}
			$this->set_data("parent_issue_key", $parent_issue_key);
			$num_of_db_entries += $this->save_to_db();
		}
		return $num_of_db_entries;
	}
	
	public function generate_sql($content, $parent_issue_key)
	{
		$sql = array();
		
		foreach($content as $subtask)
		{
			foreach($subtask as $field_name => $value)
			{
				switch($field_name)
				{
					case 'key':
						$this->set_data("subtask_issue_key", $value); break;
					case 'id':
						$this->set_data("subtask_id", $value); break;
					case 'self':
						$this->set_data("subtask_info_url", $value); break;
				}
			}
			$this->set_data("parent_issue_key", $parent_issue_key);
			$sql[] = $this->get_sql();
		}
		return $sql;
	}
}

/* End of file subtask_data_model.php */
/* Location: ./application/models/subtask_data_model.php */