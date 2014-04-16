<?php
/** 
* Name: template_engine          Revision: 1             Last Revision: 15/07/2007
* The Major Template Engine
*/
class template_engine {
	// Template Counter - The Number of Templates Collected
	var $counter = 0;
	// Root Directory - The location of the templates
	var $rootdir = "templates/";
	// Current Template
	var $tpldir = "default/";
	// Template File Extension - The File Extension of the Templates
	var $tplext = ".html";
	// Hold Output
	var $output;
	// Template Names
	var $loaded = array();
	// Template Timer
	var $timer = 0;
	/**
	* Loads the specified template after applying the file extension & directory
	*/
	function loadTemplate($templatename) 
	{
		$timer = $this->exectime();
		$template = "".$this->rootdir.$this->tpldir.$templatename.$this->tplext."";
		if (!file_exists($template)) 
		{
			// Load Error Handling
			//$errorhandle = new error_handle();
			die("<strong>load_template Failed:</strong> ".$templatename." does not exist.");
		}
		else 
		{
			$template = file_get_contents($template);
		}
		$this->counter++;
		$this->loaded[] .= $templatename;
		$this->timer += $this->calctime($timer,$this->exectime());
		return $template;	
	}

	/**
	* Replaces the given tag surronded by {} with the given string
	*/
	function replaceTags($template,$tags_array) 
	{
		$timer = $this->exectime();
		foreach ($tags_array as $key => $val) 
		{
			$template = str_replace('{'.$key.'}', $val, $template);
		}
		$this->timer += $this->calctime($timer,$this->exectime());
		return $template;
	}
	/**
	* Parses the output and places it in the buffer
	*/
	function showPage($body,$replace) 
	{
		$body = $this->loadTemplate($body);
		$body = $this->replaceTags($body,$replace);
		if(!in_array('BODYCONTENT', $replace))
		{
			$body = $this->replaceTags($body,array(
				'BODYCONTENT' => '',
				'THEME_PREFIX' => $this->rootdir . $this->tpldir,
				));
		}
		$this->output = $body;
		include_once 'includes/close.php';
	}
	
	/**
	* Returns a time for execution
	*/
	function execTime() 
	{
		$time = explode(' ', microtime());  
		$time = $time[0] +  $time[1]; 
		return $time;
	}
	
	/**
	* Compares the values given by two calls of exectime() to calculate time taken
	*/
	function calcTime($starttime,$endtime) 
	{
		return $endtime - $starttime;
	}
}
?>