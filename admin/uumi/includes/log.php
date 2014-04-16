<?php

class Log
{
	private $entries;
	
	/**
	* 
	*/
	public function __construct(){
		$this->entries = array();
	}
	
	/**
	* 
	*/
	public function add($message)
	{
		$logEntry = new LogEntry($message);
		$this->entries[] = $logEntry;
		return $logEntry;
	}
	
	public function get_all_entries()
	{
		$return = '';
		if(count($this->entries) > 0)
		{
			$return .= '<ul>';
			foreach($this->entries as $entry)
			{
				$return .= $entry->print_all();
			}
			$return .= '</ul>';
		}
		
		return $return;
	}
}

class LogEntry
{
	private $timestamp;
	private $message;
	private $children;
	
	/**
	* 
	*/
	public function __construct($message) {
		$this->timestamp = time();
		$this->message = $message;
		$this->children = array();
	}
	
	/**
	* 
	*/
	public function add_child($message)
	{
		$entry = new LogEntry($message);
		$this->children[] = $entry;
		return $entry;
	}
	
	public function print_all()
	{
		$parent_message = '<li>' . $this->message;
		$child_messages = '';
		
		if(count($this->children) > 0)
		{
			$child_messages .=  '<ul>';
			foreach($this->children as $child)
			{
				$child_messages .= $child->print_all();
			}
			$child_messages .=  '</ul>';
		}
		return $parent_message . $child_messages . '</li>';
	}
}