<?php
/*
	This model represents SECURITY data entry.
	Inheriting variables and methods from Data_model.php
*/
require_once( APPPATH . 'models/data_model' . EXT);

class Security_data_model extends Data_model
{
	public function __construct()
	{
		parent::__construct();
		$this->table_name = "SECURITIES";
	}
	
	public function manipulate($content)
	{
		$num_of_db_entries = 0;
		
		foreach($content as $field_name => $value)
		{
			switch($field_name)
			{
				case 'self':
					$this->set_data("security_info_url", $value); break;
				case 'id':
					$this->set_data("security_id", $value);
					break;
				case 'description':
					$this->set_data("security_description", $value); break;
				case 'name':
					$this->set_data("security_name", $value); break;
			}
			$num_of_db_entries += $this->save_to_db();
		}
		return $num_of_db_entries;
	}
}

/* End of file security_data_model.php */
/* Location: ./application/models/security_data_model.php */