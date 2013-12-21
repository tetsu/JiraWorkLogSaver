<?php
/*
	This model represents PRIORITY data entry.
	Inheriting variables and methods from Data_model.php
*/
require_once( APPPATH . 'models/data_model' . EXT);

class Priority_data_model extends Data_model
{
	public function __construct()
	{
		parent::__construct();
		$this->table_name = "PRIORITIES";
	}
	
	public function manipulate($content)
	{
		foreach($content as $field_name => $value)
		{
			switch($field_name)
			{
				case 'id':
					$this->set_data("priority_id", $value);
					break;
				case 'name':
					$this->set_data("priority_name", $value);
					break;
				case 'iconUrl':
					$this->set_data("priority_icon_url", $value);
					break;
				case 'self':
					$this->set_data("priority_info_url", $value);
					$this->curl_url = $value;
					$detail = $this->curl_request("array");
					foreach($detail as $name => $data)
					{
					  switch($name)
					  {
					    case 'description':
					      $this->set_data("priority_description", $data);
					      break;
					    case 'statusColor':
					      $this->set_data("priority_status_color", $data);
					      break;
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
				case 'id':
					$this->set_data("priority_id", $value);
					break;
				case 'name':
					$this->set_data("priority_name", $value);
					break;
				case 'iconUrl':
					$this->set_data("priority_icon_url", $value);
					break;
				case 'self':
					$this->set_data("priority_info_url", $value);
					$this->curl_url = $value;
					$detail = $this->curl_request("array");
					foreach($detail as $name => $data)
					{
					  switch($name)
					  {
					    case 'description':
					      $this->set_data("priority_description", $data);
					      break;
					    case 'statusColor':
					      $this->set_data("priority_status_color", $data);
					      break;
					  }
					}
					break;
			}
		}
		
		$sql[] = $this->get_sql();
		return $sql;
	}
}

/* End of file priority_data_model.php */
/* Location: ./application/models/priority_data_model.php */