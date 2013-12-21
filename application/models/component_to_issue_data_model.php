<?php
/*
	This model represents COMPONENT-ISSUE data entry.
	Inheriting variables and methods from Data_model.php
*/
require_once( APPPATH . 'models/data_model' . EXT);

class Component_to_Issue_data_model extends Data_model
{
	public function __construct()
	{
		parent::__construct();
		$this->table_name = "COMPONENT_TO_ISSUE";
	}
	
	public function manipulate($content, $issue_id)
	{
		$this->set_data("issue_id", $issue_id);
		$num_of_db_entries = 0;
		
		foreach($content as $component)
		{
			foreach($component as $field_name => $value)
			{
				switch($field_name)
				{
					case 'id':
						$this->set_data("component_id", $value);
						break;
				}
			}
			
			$num_of_db_entries += $this->save_to_db();
		}
		return $num_of_db_entries;
	}
	
	public function generate_sql($content, $issue_id)
	{
		$this->set_data("issue_id", $issue_id);
		$sql = "";
		
		foreach($content as $component)
		{
			foreach($component as $field_name => $value)
			{
				switch($field_name)
				{
					case 'id':
						$this->set_data("component_id", $value);
						break;
				}
			}
			
			$sql .= $this->get_sql();
		}
		return $sql;
	}
}

/* End of file component_to_issue_data_model.php */
/* Location: ./application/models/component_to_issue_data_model.php */