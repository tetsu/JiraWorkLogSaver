<?php
/*
	This model represents USER data entry.
	Inheriting variables and methods from Data_model.php
*/
require_once( APPPATH . 'models/data_model' . EXT);

class ETC_data_model extends Data_model
{
	public function __construct()
	{
		parent::__construct();
		$this->table_name = "ETC";
	}
	
	public function manipulate($field_name, $content, $issue_key)
	{
		$this->set_data("issue_key", $issue_key);
		$this->set_data("etc_field_name", $field_name);
		if(is_array($content)) $content = json_encode($content);
		$this->set_data("etc_value", $content);
		
		return $this->save_to_db();
	}
	
	public function generate_sql($field_name, $content, $issue_key, $issue_id)
	{
		$this->set_data("issue_key", $issue_key);
		$this->set_data("issue_id", $issue_id);
		$this->set_data("etc_field_name", $field_name);
		if(is_array($content)) $content = json_encode($content);
		$this->set_data("etc_value", $content);
		
		return $this->get_sql();
	}
}

/* End of file extra_data_model.php */
/* Location: ./application/models/extra_data_model.php */