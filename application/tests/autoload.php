<?php

define('BASEPATH', dirname(__FILE__) . '/../system/');
define('APPPATH', dirname(__FILE__) . '/../application/');

function autoload_core($class_name)
{
	$file_name = $class_name;
	if (substr($class_name, 0, 3) === 'CI_')
	{
		$file_name = substr($file_name, 3);
	}
	$file = BASEPATH . 'core/' . $file_name . '.php';
	if (file_exists($file))
	{
		require_once $file;
	}
}

function autoload_model($class_name)
{	
	$file_name = strtolower($class_name);
	$file = APPPATH . 'models/' . $file_name . '.php';
	if (file_exists($file))
	{
		require_once $file;
	}
}

spl_autoload_register('autoload_core');
spl_autoload_register('autoload_model');