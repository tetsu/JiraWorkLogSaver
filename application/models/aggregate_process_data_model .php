<?php
/*
	This model represents Aggregate process data entry.
	Inheriting variables and methods from Data_model.php
*/
require_once( APPPATH . 'models/data_model' . EXT);

class Aggregate_process_data_model extends Data_model
{
	public function __construct()
	{
		parent::__construct();
		$this->table_name = "STATUS";
	}
	
	public function manipulate($content)
	{
		foreach($content as $field_name => $value)
		{
			switch($field_name)
			{
				case 'self':
					$this->set_data("status_info_url", $value); break;
				case 'description':
					$this->set_data("status_description", $value); break;
				case 'iconUrl':
					$this->set_data("status_icon_url", $value); break;
				case 'name':
					$this->set_data("status_name", $value); break;
				case 'id':
					$this->set_data("status_id", $value); break;
			}
		}
		if($this->save_to_db())
		{
			echo "Status \"".$this->get_data("status_name")
				."\" was successfully stored.\n";
		}
	}
}

/* End of file aggregate_process_data_model.php */
/* Location: ./application/models/aggregate_process_data_model.php */