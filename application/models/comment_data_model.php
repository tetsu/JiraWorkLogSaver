<?php
/*
	This model represents Comment data entry.
	Inheriting variables and methods from Data_model.php
*/
require_once( APPPATH . 'models/data_model' . EXT);

class Comment_data_model extends Data_model
{
	public function __construct()
	{
		parent::__construct();
		$this->table_name = "COMMENTS";
	}
	
	public function object_to_db($issue_key, $obj)
	{
		if($obj)
		{
			for($i=0;$i<count($obj->comment);$i++)
			{
				$this->set_data("comment_id", (int)$obj->comment[$i]->attributes()->id);
				$this->set_data("issue_key", $issue_key);
				$this->set_data("comment_author_username",
					(string)$obj->comment[$i]->attributes()->author);
				$this->set_data("comment_created",
					(string)$obj->comment[$i]->attributes()->created);
				$this->set_data("comment_content", (string)$obj->comment[$i]);
				$this->save_to_db();
				$this->format_data_array();
			}
		}
	}

}

/* End of file comment_data_model.php */
/* Location: ./application/models/comment_data_model.php */