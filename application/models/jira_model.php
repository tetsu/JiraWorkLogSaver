<?php
class Jira_model extends CI_Model
{
	var $user_id = "";
	var $userName = "";
	var $projectKey = "";
	
	function __construct()
    {
        parent::__construct();
    }
/********************************************************************
Table "user"
id         : int
email      : varchar
first_name : varchar
last_name  : varchar
*********************************************************************
Table "group"
id         : int
name       : varchar
*********************************************************************
Table "member"
id         : int
group_id   : int
user_id    : int
********************************************************************/
	function get_members_post()
	{
		$this->user_id = $this->input->post('group');
		if($this->user_id)
		{
			$this->db->select('*');
			$this->db->from('member');
			$this->db->where('group_name', $this->user_id);
			$this->db->join('group', 'group.id = member.group_id');
			$this->db->join('user', 'user.id = member.user_id');
			$query = $this->db->get();
		}
		else
		{
			throw new Exception("Group Name is required!");
		}
		return $query->result();
	}
/********************************************************************/
	function get_members($group_name)
	{
		if($group_name)
		{
			$this->db->select('*');
			$this->db->from('member');
			$this->db->where('group_name', $group_name);
			$this->db->join('group', 'group.id = member.group_id');
			$this->db->join('user', 'user.id = member.user_id');
			$query = $this->db->get();
		}
		else
		{
			throw new Exception("Group Name is required!");
		}
		return $query->result();
	}
/********************************************************************/
// Function: worklogs_to_database($input_array)
// Summary: This is a function to save project worklogs to database
//
// input array structure example:
//
// Array
// (
//	[@attributes] => Array
//		(
//			[date_from] => 2012-11-20 00:00:00
//			[date_to] => 2012-11-20 23:59:59
//			[number_of_worklogs] => 10
//			[format] => json
//			[duration_ms] => 29
//			[projectKey] => SMART
//			[total_hours] => 14.1
//			[users] => Array
//				(
//					[ai.shimogori] => 0.6
//					[mitsuyuki.shiiba] => 1
//					[cynthia.kustanto] => 2.5
//					[emma.suzuki] => 1
//					[bernadette.le] => 1
//					[frank.fernandes] => 8
//				)
//			[number_of_users] => 6
//			[days] => 1
//		)
//	[worklog] => Array
//		(
//			[0] => Array
//				(
//					[worklog_id] => 30418
//					[issue_id] => 35537
//					[issue_key] => SMART-2013
//					[hours] => 0.5
//					[work_date] => 2012-11-20
//					[username] => ai.shimogori
//					[staff_id] => ai.shimogori
//					[billing_key] => Array()
//					[billing_attributes] => Array()
//					[activity_id] => v14500
//					[activity_name] => Employee App It8
//					[work_description] => Working on issue SMART-2013
//					[parent_key] => SMART-610
//					[reporter] => mitsuyuki.shiiba
//					[external_id] => Array()
//					[external_tstamp] => Array()
//					[external_hours] => 0.0
//					[external_result] => Array()
//					[hash_value] => 105e71d3d32e2eefc44cb2e021348507f50bd9d3
//				)
//			[1] => Array
//				(......)
//	   
// Output: Worklog Data and save data to database
/********************************************************************/
	public function worklogs_to_database($input_array)
	{
		if( isset($input_array['worklog'][0])
		&& ($input_array['@attributes']['number_of_worklogs'] > 0) )
		{
			$i=0;
			foreach($input_array['worklog'] as $log)
			{
				$log = json_decode(json_encode($log), true);
				$project_key = explode("-", $log['issue_key']);
				$sql = "REPLACE INTO WORKLOGS "
					."(worklog_id, issue_id, project_key, issue_key, "
					."username, work_date, hours) VALUES (" 
					."'".$log['worklog_id']."', "
					."'".$log['issue_id']."', "
					."'".$project_key[0]."', "
					."'".$log['issue_key']."', "
					."'".$log['username']."', "
					."'".$log['work_date']."', "
					."'".$log['hours']."')";
				$i += $this->db->query($sql);
			}
			return $i;
		}
		return 0;
	}
/********************************************************************/
// Function: users_to_database($input_array)
// Summary: This is a function to save user data to database.
//		  It also updates data when a user already exists
//		  but has changed his/her information.
/********************************************************************/
	public function users_to_database($input_array)
	{
		$this->load->library('jira_library');
		$input_array = json_decode(json_encode($input_array), true);
		
		if(isset($input_array['@attributes']['users']))
		{
			$i = 0;
			foreach($input_array['@attributes']['users'] as $user_name => $hours)
			{
				$query['username'] = $user_name;
				$result_object = $this->jira_library->get_to('user', $query);
				$result_array = json_decode(json_encode($result_object), true);
				$sql = "REPLACE INTO USERS "
					."(username, user_display_name, user_email_address) VALUES (" 
					. "'" . $result_array['name'] . "', "
					. "'" . $result_array['displayName'] . "', "
					. "'" . $result_array['emailAddress'] . "')";
				$i += $this->db->query($sql);
			}
			return $i;
		}
		return 0;
	}
/********************************************************************/
// Function: projects_to_database($input_array)
// Summary: This is a function to save project data to database
/********************************************************************/
	public function projects_to_database($input_array)
	{
		$this->load->library('jira_library');
		$input_array = json_decode(json_encode($input_array), true);
		
		//create an array of existing project names in database
		$this->db->select('project_key');
		$query = $this->db->get('PROJECTS');
		$current_projects = json_decode(json_encode($query->result()),true);
		
		if(isset($input_array['worklog'][0]))
		{
			$i = 0;
			//Save Project info to database only if it doesn't exit in database
			foreach($input_array['worklog'] as $log)
			{
				$exist = 0;
				$ikey_array = explode('-', $log['issue_key']);
				$ikey = $ikey_array[0];
				foreach($current_projects as $pkey)
				{
					if($pkey['project_key'] == $ikey)
					{
						$exist++;
						break;
					}
				}
				if($exist == 0)
				{
					$result_object = $this->jira_library->no_post_api('project/'.$ikey);
					$result_array = json_decode(json_encode($result_object), true);
					$sql = "REPLACE INTO PROJECTS ".
						"(project_id, project_key, project_name) VALUES (" 
							. "'" . $result_array['id'] . "', "
							. "'" . $result_array['key'] . "', "
							. "'" . $result_array['name'] . "')";
					$i += $this->db->query($sql);
				}
			}
			return $i;
		}
		return 0;
	}
/********************************************************************/
// Function: tempo_userdata_to_database($input_array)
// Summary: This is a function to get user data from Tempo API,
//		  and save it to database.
//		  It updates database only when it finds a new user.
//
//		  Might need to move to MODEL.
/********************************************************************/
	public function tempo_userdata_to_database($input_array)
	{ 
		$input_array = json_decode(json_encode($input_array), true);
		
		if(isset($input_array['worklog'][0]))
		{
			$tempo = 0;//number of Tempo API calls
			$jira = 0;//number of Jira API calls
			$this->load->library('jira_library');
			
			//create an array of existing project names in database
			$this->db->select('username');
			$query = $this->db->get('USERS');
			$current_users = json_decode(json_encode($query->result()),true);
			
			//Save user info to database
			foreach($input_array['worklog'] as $log)
			{
				$exist = 0;
				foreach($current_users as $user)
				{
					if($user['username'] == $log['username'])
					{
						$exist++;
						break;
					}
				}
				if($exist == 0)
				{
					$q = array();
					$q['username'] = $log['username'];
					$result_object = $this->jira_library->get_to('user', $q);
					$jira++;
					$result_array = json_decode(json_encode($result_object), true);
					
					if( isset($result_array['name']) 
					&& isset($result_array['displayName'])
					&& isset($result_array['emailAddress']))
					{
						$sql = "REPLACE INTO USERS "
							."(username, user_display_name, user_email_address) VALUES (" 
							."'".$result_array['name']."', "
							."'".$result_array['displayName']."', "
							."'".$result_array['emailAddress']."')";
						$this->db->query($sql);
						$tempo++;
					}
				}
			}
			return $tempo;
		} else {
			return 0;
		}
		return 0;
	}
/********************************************************************/
// Function: log_cron($data)
// Summary: save cron job status to database
/********************************************************************/
	function log_cron($data)
	{
		if(isset($data))
		{
			if(!$this->db->insert('CRONLOGS', $data))
			{
				throw new Exception("Database Error.");
			}
		}
		else
		{
			throw new Exception("No data input.");
		}
	}
/********************************************************************/
// Function: get_users()
// Summary: Get all user information from the database
/********************************************************************/
	function get_users()
    {
        $query = $this->db->get('USERS');
        return $query->result();
    }
/********************************************************************/
// Function: get_worklogs($data, $limit, $offset)
// Summary: Get worklog information from database, not Tempo API
/********************************************************************/
	function get_worklogs($data)
    {	
		if($data['dateFrom'] && $data['dateTo'])
		{	
			if(isset($data['userName'])){
				$user_array = explode("~", $data['userName']);
				$un = " and username IN (";
				$i = 0;
				foreach($user_array as $user)
				{
					if($i == 0){
						$un = $un."'".$user."'";
					} else {
						$un = $un.", '".$user."'";
					}
					$i++;
				}
				$un = $un.")";
			} else {
				$un = "";
			}
			if( isset($data['projectkey']) )
			{
				$pk = " and project_key = '".$data['projectkey']."'";
			}
			else
			{
				$pk = "";
			}
			
			if( isset($data['limit']) ){
				$limit_query = " LIMIT ".$data['limit'];
				if( isset($data['offset']) ){
					if( $data['offset'] > 0)
						$limit_query = $limit_query." OFFSET ".$data['offset'];
				}
			} else {
				$limit_query = "";
			}
			
			$sql =
				"select * from WORKLOGS where work_date "
				.">= cast( '".$data['dateFrom']."' as datetime) and work_date "
				."<= cast( '".$data['dateTo']."' as datetime)"
				.$un.$pk.$limit_query;
			$query = $this->db->query($sql);
			return json_decode(json_encode($query->result(), true));
		}
		else
		{
			throw new Exception("Both start and end dates are required.");
		}
    }
/********************************************************************/
// Function: get_total_work_hours($data)
// Summary: Get work total hours from database, not Tempo API
/********************************************************************/
	function get_total_work_hours($data)
    {
		if($data['dateFrom'] && $data['dateTo'])
		{
			if(isset($data['userName'])){
				$user_array = explode("~", $data['userName']);
				$un = " and username IN (";
				$i = 0;
				foreach($user_array as $user)
				{
					if($i == 0){
						$un = $un."'".$user."'";
					} else {
						$un = $un.", '".$user."'";
					}
					$i++;
				}
				$un = $un.")";
			} else {
				$un = "";
			}
			if( isset($data['projectkey']) )
			{
				$pk = " and project_key = '".$data['projectkey']."'";
			}
			else
			{
				$pk = "";
			}
			$sql =
				"select SUM(hours) AS total_hours from WORKLOGS where work_date "
				.">= cast( '".$data['dateFrom']."' as datetime) and work_date "
				."<= cast( '".$data['dateTo']."' as datetime)".$un.$pk;
			$query = $this->db->query($sql);
			$return_array = json_decode(json_encode($query->result()),true);
			return round($return_array[0]['total_hours'], 2);
		}
		else
		{
			throw new Exception("Both start and end dates are required.");
		}
    }
/********************************************************************/
// Function: get_total_worklogs($data)
// Summary: Get work total number of worklogs from database, 
//          not Tempo API
/********************************************************************/
	function get_total_work_logs($data)
    {
		if($data['dateFrom'] && $data['dateTo'])
		{
			if(isset($data['userName'])){
				$user_array = explode("~", $data['userName']);
				$un = " and username IN (";
				$i = 0;
				foreach($user_array as $user)
				{
					if($i == 0){
						$un = $un."'".$user."'";
					} else {
						$un = $un.", '".$user."'";
					}
					$i++;
				}
				$un = $un.")";
			} else {
				$un = "";
			}
			if( isset($data['projectkey']) )
			{
				$pk = " and project_key = '".$data['projectkey']."'";
			}
			else
			{
				$pk = "";
			}
			$sql =
				"select count(*) from WORKLOGS where work_date "
				.">= cast( '".$data['dateFrom']."' as datetime) and work_date "
				."<= cast( '".$data['dateTo']."' as datetime)".$un.$pk;
			$query = $this->db->query($sql);
			$return_array = json_decode(json_encode($query->result()),true);
			return $return_array[0]['count(*)'];
		}
		else
		{
			throw new Exception("Both start and end dates are required.");
		}
    }
/********************************************************************/
}