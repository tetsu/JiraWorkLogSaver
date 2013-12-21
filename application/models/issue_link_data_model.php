<?php
/*
	This model represents Issue Link data entry.
	Inheriting variables and methods from Data_model.php
*/
require_once( APPPATH . 'models/data_model' . EXT);

class Issue_link_data_model extends Data_model
{
	protected $link_array = array();
	
	public function __construct()
	{
		parent::__construct();
		$this->table_name = "USERS";
	}
	
	public function object_to_db($issue_id, $issue_key, $obj)
	{
		if($obj)
		{
			$this->link_array['issue_1_id'] = $issue_id;
			$this->link_array['issue_1_key'] = $issue_key;
			
			$this->table_name = "ISSUE_LINK_TYPES";
			for($i=0;$i<count($obj->issuelinktype);$i++)
			{
				$this->set_data("issue_link_type_id",
					(int)$obj->issuelinktype[$i]->attributes()->id);
				$this->link_array['"issue_link_type_id']
					= (int)$obj->issuelinktype[$i]->attributes()->id;
				$this->set_data("issue_link_type_name", 
					(string)$obj->issuelinktype[$i]->name);
				$this->set_data("inward_link_description",
					(string)$obj->issuelinktype[$i]->inwardlinks->attributes()->description);
				$this->link_array['inward_link_description']
					= (string)$obj->issuelinktype[$i]->inwardlinks->attributes()->description;
				$this->save_to_db();
				$this->manage_inward_links($obj->issuelinktype[$i]->inwardlinks);
			}
			$this->table_name = "USERS";
		}
		$this->format_data_array();
	}
	
	public function manage_inward_links($link_obj)
	{
		for($i=0;$i<count($link_obj->issuelink);$i++)
		{
			$this->link_array['issue_2_id']
				= (int)$link_obj->issuelink[$i]->issuekey->attributes()->id;
			$this->link_array['issue_2_key']
				= (string)$link_obj->issuelink[$i]->issuekey;
			$this->array_to_db("ISSUE_LINKS", $this->link_array);
		}
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
				case 'id':
					$this->set_data("issue_link_id", $data); break;
				case 'self':
					$this->set_data("issue_link_info_url", $data); break;
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
		if($this->save_to_db())
		{
			echo "User \"".$this->get_data("user_display_name")
				."\" was successfully stored or updated.\n";
		}
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
				case 'id':
					$this->set_data("issue_link_id", $data); break;
				case 'self':
					$this->set_data("issue_link_info_url", $data); break;
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
		$sql[] = $this->get_sql()
		return $sql;
	}
}

/* End of file issue_link_data_model.php */
/* Location: ./application/models/issue_link_data_model.php */