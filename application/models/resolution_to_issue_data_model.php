<?php
/*
	This model represents RESOLUTION-ISSUE data entry.
	Inheriting variables and methods from Data_model.php
*/
require_once( APPPATH . 'models/data_model' . EXT);

class Resolution_to_Issue_data_model extends Data_model
{
	public function __construct()
	{
		parent::__construct();
		$this->table_name = "RESOLUTION_TO_ISSUE";
	}
	
	public function manipulate($content, $issue_id)
	{
		$this->set_data("issue_id", $issue_id);
		
		foreach($content as $field_name => $value)
		{
			switch($field_name)
			{
				case 'id':
					$this->set_data("resolution_id", $value);
					break;
			}
		}
		return $this->save_to_db();
	}
}

/* End of file resolution_to_issue_data_model.php */
/* Location: ./application/models/resolution_to_issue_data_model.php */