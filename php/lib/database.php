<?php

Class Database extends SQLite3
{
	
	protected $dbname;
		
	function __construct($dbname)
	{
		$this->dbname = $dbname;
		$this->open($dbname);
	}
	
	/*
	 *  Return Array
	 */ 
	function GetUserInfo($id)
	{
	   $dbfields = 'user_id,user_type,user_first_name,user_group_id,user_group_name';
	   $query = "SELECT {$dbfields} FROM bot_setting WHERE user_id = {$id}";
	   
	   $result = array();	
	   $ret = $this->query($query);
	 
	   while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
		  $result[] = array(
			'id' => $row['user_id'],
			'firstname' => $row['user_first_name'],
			'type' => $row['user_type'],
			'groupID' => $row['user_group_id'],
			'groupname' => $row['user_group_name']
		  );
		  
			
	   }
	   //$this->close();
	   return $result;
	}


	/*
	 *  Get User Information from the group
	 * 
	 *  Paramenters:
	 *  $user_id as Integer
	 *  $group_id ad Integer
	 * 
	 *  Return:
	 *  99 as a Super User
	 *  88 as the Administrator
	 *  77 as the Moderator
	 *  
	 *	55 a user who has been kicked by the administrator
	 */ 
	function GetUserInfoFromGroup($user_id,$group_id)
	{
		
		$dbfields = 'user_id,user_type,user_group_id';
		$query = "SELECT {$dbfields} FROM bot_setting WHERE user_id = {$user_id} AND user_group_id = {$group_id} LIMIT 1";
		
		$ret=$this->query($query);
		
		$result = ''; $value='';
		while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
			$value = $row['user_type'];	
		}
		
		switch ($value){
			case 99: $result='SU'; break;
			case 88: $result='ADMIN'; break;
			case 77: $result='MOD'; break;
			case 55: $result='BANNED'; break;
		}
	  
	   return $result;
	}

	/* Return String
	 */ 
	function GetRules($groupid)
	{	
	   $dbfields = 'rules_text,rules_group_id';
	   $query = "SELECT {$dbfields} FROM bot_setting WHERE rules_group_id = {$groupid} LIMIT 1";
	   $result = '';
	   $ret = $this->query($query);
	 
	   while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
		 $result = $row['rules_text'];	
	   }
	   
	  // $this->close();
	   return $result;
		
	}
	
	function SetRules($userid, $groupid,$rules,$edit=false)
	{
		
		if ($edit == false){
			$query  = 'INSERT INTO bot_setting (user_id, rules_group_id, rules_text) VALUES ';
			$query .= '(' . $userid. ', ' . $groupid . ", '" . $rules . "');";
		} else {
			$query  = "UPDATE bot_setting SET rules_text = '{$rules}' ";
			$query .= "WHERE rules_group_id = {$groupid}";
		}	
		
				
		$ret = $this->exec($query);
		
		if(!$ret)
		{
			return $this->lastErrorMsg();
		} 
		else 
		{
			return TRUE;
		}	
	}
}	
