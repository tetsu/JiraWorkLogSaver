<?php
/*
	This model represents USER data entry.
	Inheriting variables and methods from Data_model.php
*/
require_once( APPPATH . 'models/data_model' . EXT);

class Customfield_data_model extends Data_model
{
	public function __construct()
	{
		parent::__construct();
		$this->table_name = "CUSTOMFIELDVALUES";
	}
	
	public function manipulate($field_name, $content, $issue_key)
	{
		$this->set_data("issue_key", $issue_key);
		$this->set_data("customfield_id", $field_name);
		if(isset($content))
		{
			$content = json_encode($content);
			$this->set_data("customfield_value", $content);
			return $this->get_sql();
		} else {
			return 0;
		}
	}
	
	public function generate_sql($content)
	{
		$this->set_data("issue_key", $issue_key);
		$this->set_data("customfield_id", $field_name);
		if(isset($content))
		{
			$content = json_encode($content);
			$this->set_data("customfield_value", $content);
			return $this->get_sql();
		} else {
			return "";
		}
	}
}

/* End of file customfield_data_model.php */
/* Location: ./application/models/customfield_data_model.php */