<?php
/*
	This model represents COMPONENT data entry.
	Inheriting variables and methods from Data_model.php
*/
require_once( APPPATH . 'models/data_model' . EXT);

class Component_data_model extends Data_model
{
	public function __construct()
	{
		parent::__construct();
		$this->table_name = "COMPONENTS";
	}
	
	public function object_to_db($issue_key, $obj)
	{	
		if($obj)
		{
			for($i=0;$i<count($obj);$i++)
			{
				$this->table_name = "ISSUE_COMPONENT";
				$this->set_data("issue_key", $issue_key);
				$this->set_data("component_name", (string)$obj[$i]);
				$this->save_to_db();
				//echo $issue_key.":".(string)$obj[$i]."\n";
				$this->format_data_array();
				$this->table_name = "COMPONENTS";
			}
		}
	}
	
	public function manipulate($content)
	{
		$num_of_db_entries = 0;
		
		foreach($content as $component)
		{
			foreach($component as $field_name => $value)
			{
				switch($field_name)
				{
					case 'name':
						$this->set_data("component_name", $value); break;
					case 'id':
						$this->set_data("component_id", $value); break;
					case 'self':
						$this->set_data("component_info_url", $value); break;
					case 'description':
						$this->set_data("component_description", $value); break;
				}
			}
			
			$num_of_db_entries += $this->save_to_db();
		}
		
		return $num_of_db_entries;
	}
	
	public function generate_sql($content)
	{
		$sql = array();
		
		foreach($content as $component)
		{
			foreach($component as $field_name => $value)
			{
				switch($field_name)
				{
					case 'name':
						$this->set_data("component_name", $value); break;
					case 'id':
						$this->set_data("component_id", $value); break;
					case 'self':
						$this->set_data("component_info_url", $value); break;
					case 'description':
						$this->set_data("component_description", $value); break;
				}
			}
			
			$sql[] = $this->get_sql();
		}
		
		return $sql;
	}
}

/* End of file component_data_model.php */
/* Location: ./application/models/component_data_model.php */