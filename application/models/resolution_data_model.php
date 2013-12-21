<?php
/*
	This model represents RESOLUTION data entry.
	Inheriting variables and methods from Data_model.php
*/
require_once( APPPATH . 'models/data_model' . EXT);

class Resolution_data_model extends Data_model
{
	public function __construct()
	{
		parent::__construct();
		$this->table_name = "RESOLUTIONS";
	}
	
	public function manipulate($content)
	{
		foreach($content as $field_name => $value)
		{
			switch($field_name)
			{
				case 'name':
					$this->set_data("resolution_name", $value); break;
				case 'id':
					$this->set_data("resolution_id", $value); break;
				case 'self':
					$this->set_data("resolution_info_url", $value); break;
				case 'description':
					$this->set_data("resolution_description", $value); break;
			}
		}
			
		return $this->save_to_db();
	}
	
	public function generate_sql($content)
	{
		$sql = array();
		foreach($content as $field_name => $value)
		{
			switch($field_name)
			{
				case 'name':
					$this->set_data("resolution_name", $value); break;
				case 'id':
					$this->set_data("resolution_id", $value); break;
				case 'self':
					$this->set_data("resolution_info_url", $value); break;
				case 'description':
					$this->set_data("resolution_description", $value); break;
			}
		}
			
		$sql[] = $this->get_sql();
		return $sql;
	}
}

/* End of file component_data_model.php */
/* Location: ./application/models/component_data_model.php */