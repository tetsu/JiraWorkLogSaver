<?php
/*
	This model represents VERSION data entry.
	Inheriting variables and methods from Data_model.php
*/
require_once( APPPATH . 'models/data_model' . EXT);

class Version_data_model extends Data_model
{
	public function __construct()
	{
		parent::__construct();
		$this->table_name = "VERSIONS";
	}
	
	public function manipulate($content, $issue_id)
	{
		$num_of_db_entries = 0;
		
		foreach($content as $version)
		{
			foreach($version as $field_name => $value)
			{
				switch($field_name)
				{
					case 'self':
						$this->set_data("version_info_url", $value); break;
					case 'id':
						$this->set_data("version_id", $value);
						
						$v2i = new Data_model();
						$v2i->set_table_name("VERSION_TO_ISSUE");
						$v2i->set_data("issue_id", $issue_id);
						$v2i->set_data("version_id", $value);
						$num_of_db_entries += $v2i->save_to_db();
						break;
					case 'description':
						$this->set_data("version_description", $value); break;
					case 'name':
						$this->set_data("version_name", $value); break;
					case 'archived':
						$this->set_data("version_archived", $value); break;
					case 'released':
						$this->set_data("version_released", $value); break;
					case 'releaseDate':
						$this->set_data("version_release_date", $value); break;
				}
			}
			$num_of_db_entries += $this->save_to_db();
		}
		return $num_of_db_entries;
	}
	
	public function generate_sql($content, $parent_issue_key)
	{
	}
}

/* End of file version_data_model.php */
/* Location: ./application/models/version_data_model.php */