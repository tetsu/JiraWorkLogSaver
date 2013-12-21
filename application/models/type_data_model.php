<?php
/*
	This model represents TYPE data entry.
	Inheriting variables and methods from Data_model.php
*/
require_once( APPPATH . 'models/data_model' . EXT);

class Type_data_model extends Data_model
{
	public function __construct()
	{
		parent::__construct();
		$this->table_name = "ISSUE_TYPES";
	}
	
	public function manipulate($content)
	{
		foreach($content as $field_name => $value)
		{
			switch($field_name)
			{
				case 'id':
					$this->set_data("issue_type_id", $value); break;
				case 'name':
					$this->set_data("issue_type_name", $value); break;
				case 'description':
					$this->set_data("issue_type_description", $value); break;
				case 'iconUrl':
					$this->set_data("issue_type_icon_url", $value); break;
				case 'self':
					$this->set_data("issue_type_info_url", $value); break;
				case 'subtask':
					//$this->set_data("issue_type_subtask", $value);
					break;
			}
		}
		return $this->save_to_db();
	}
	
	public function generate_sql($content)
	{
		$sql =  array();
		foreach($content as $field_name => $value)
		{
			switch($field_name)
			{
				case 'id':
					$this->set_data("issue_type_id", $value); break;
				case 'name':
					$this->set_data("issue_type_name", $value); break;
				case 'description':
					$this->set_data("issue_type_description", $value); break;
				case 'iconUrl':
					$this->set_data("issue_type_icon_url", $value); break;
				case 'self':
					$this->set_data("issue_type_info_url", $value); break;
				case 'subtask':
					//$this->set_data("issue_type_subtask", $value);
					break;
			}
		}
		$sql[] = $this->get_sql();
		return $sql;
	}
}

/* End of file type_data_model.php */
/* Location: ./application/models/type_data_model.php */