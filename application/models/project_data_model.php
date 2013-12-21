<?php
/*
	This model represents Project data entry.
	Inheriting variables and methods from Data_model.php
*/
require_once( APPPATH . 'models/data_model' . EXT);

class Project_data_model extends Data_model
{
	public function __construct()
	{
		parent::__construct();
		$this->table_name = "PROJECTS";
	}
	
	public function manipulate($content)
	{
		foreach($content as $field_name => $value)
		{
			switch($field_name)
			{
				case 'self':
					$this->set_data("project_info_url", $value); break;
				case 'id':
					$this->set_data("project_id", $value); break;
				case 'key':
					$this->set_data("project_key", $value); break;
				case 'name':
					$this->set_data("project_name", $value); break;
				case 'avatarUrls':
					foreach($value as $size => $a_url)
					{
						switch($size)
						{
							case '16x16':
								$this->set_data("16x16", $a_url); break;
							case '48x48':
								$this->set_data("48x48", $a_url); break;
						}
					}
					break;
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
				case 'self':
					$this->set_data("project_info_url", $value); break;
				case 'id':
					$this->set_data("project_id", $value); break;
				case 'key':
					$this->set_data("project_key", $value); break;
				case 'name':
					$this->set_data("project_name", $value); break;
				case 'avatarUrls':
					foreach($value as $size => $a_url)
					{
						switch($size)
						{
							case '16x16':
								$this->set_data("16x16", $a_url); break;
							case '48x48':
								$this->set_data("48x48", $a_url); break;
						}
					}
					break;
			}
		}
		$sql[] = $this->get_sql();
		return $sql;
	}
}

/* End of file project_data_model.php */
/* Location: ./application/models/priority_data_model.php */