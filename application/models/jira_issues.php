<?php
class Jira_issues extends CI_Model
{
	public function get_XML($jql)
	{
		$xml_issues;
		
		$CI =& get_instance();
		$CI->load->library('jira_library');
		
		try{
			$xml_issues = $CI->jira_library->curl_issues($jql);
			return $xml_issues;
		}
		catch (Exception $e)
		{
			$error_message = array();
			$error_message['Error'] = $e->getMessage();
			return json_encode($error_message);
		}
	}
	
	public function get_array($jql)
	{
		$issue_array = array();
		return $issue_array;
	}
	
	public function get_JSON($jql)
	{
		$json_issues = '';
		return $json_issues;
	}
	
	private function XML_to_array($xml)
	{
		$output_array = (array)simplexml_load_string($xml);
		return json_decode(json_encode($output_array), true);
	}
}

/* End of file jira_issues.php */
/* Location: ./application/models/jira_issues.php */