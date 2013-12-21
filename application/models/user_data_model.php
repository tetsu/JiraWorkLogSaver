<?php
/*
	This model represents USER data entry.
	Inheriting variables and methods from Data_model.php
*/
require_once( APPPATH . 'models/data_model' . EXT);

class User_data_model extends Data_model
{
	public function __construct()
	{
		parent::__construct();
		$this->table_name = "USERS";
	}
	
	public function manipulate($content)
	{
		if(isset($content['self']))
		{
			$this->curl_url = $content['self'];
		} else {
			throw new Exception("Self URL is not assigned.");
			return false;
		}
		
		$detail = $this->curl_request("array");
		foreach($detail as $name => $data)
		{
			switch($name)
			{
				case 'self':
					$this->set_data("user_info_url", $data); break;
				case 'name':
					$this->set_data("username", $data); break;
				case 'emailAddress':
					$this->set_data("user_email_address", $data); break;
				case 'avatarUrls':
					foreach($data as $size => $a_url)
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
				case 'displayName':
					$this->set_data("user_display_name", $data); break;
				case 'active':
					$this->set_data("user_active", $data); break;
				case 'timeZone':
					$this->set_data("user_time_zone", $data); break;
			}
		}
		return $this->save_to_db();
	}
	
	public function manipulate_no_switch($content)
	{
		if(isset($content['self']))
		{
			$this->curl_url = $content['self'];
		} else {
			throw new Exception("Self URL is not assigned.");
			return false;
		}
		
		$detail = $this->curl_request("array");
		foreach($detail as $name => $data)
		{
			if($name == "self")
				$this->set_data("user_info_url", $data);
			elseif($name == "name")
				$this->set_data("username", $data);
			elseif($name == "emailAddress")
				$this->set_data("user_email_address", $data);
			elseif($name == "avatarUrls")
			{
				foreach($data as $size => $a_url)
				{
					if($size == '16x16')
						$this->set_data("16x16", $a_url);
					elseif($size == '48x48')
						$this->set_data("48x48", $a_url);
				}
			}
			elseif($name == "displayName")
				$this->set_data("user_display_name", $data);
			elseif($name == "active")
				$this->set_data("user_active", $data);
			elseif($name == "timeZone")
				$this->set_data("user_time_zone", $data);
		}
		return $this->save_to_db();
	}
	
	public function generate_sql($content)
	{
		$sql = array();
		
		if(isset($content['self']))
		{
			$this->curl_url = $content['self'];
		} else {
			throw new Exception("Self URL is not assigned.");
			return false;
		}
		
		$detail = $this->curl_request("array");
		foreach($detail as $name => $data)
		{
			switch($name)
			{
				case 'self':
					$this->set_data("user_info_url", $data); break;
				case 'name':
					$this->set_data("username", $data); break;
				case 'emailAddress':
					$this->set_data("user_email_address", $data); break;
				case 'avatarUrls':
					foreach($data as $size => $a_url)
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
				case 'displayName':
					$this->set_data("user_display_name", $data); break;
				case 'active':
					$this->set_data("user_active", $data); break;
				case 'timeZone':
					$this->set_data("user_time_zone", $data); break;
			}
		}
		$sql[] = $this->get_sql();
		return $sql;
	}
}

/* End of file priority_data_model.php */
/* Location: ./application/models/priority_data_model.php */