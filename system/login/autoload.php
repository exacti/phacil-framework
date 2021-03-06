<?php

class login {
	
	private $MM_authorizedUsers = array();
	private $MM_donotCheckaccess = "false";
	private $request = '';
	private $session;
	
	public function __construct($authorizedUsers){
		$this->MM_authorizedUsers = $authorizedUsers;
		$this->request = new Request();
		$this->session = new Session();
		
	}

	// *** Restrict Access To Page: Grant or deny access to this page
	public function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
		
		// For security, start by assuming the visitor is NOT authorized. 
		$isValid = False; 
		
		// When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
		// Therefore, we know that a user is NOT logged in if that Session variable is blank. 
		if (!empty($UserName)) { 
			// Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
			// Parse the strings into arrays. 
			$arrUsers = Explode(",", $strUsers); 
			$arrGroups = Explode(",", $strGroups); 
			if (in_array($UserName, $arrUsers)) {
				$isValid = true; 
			} 
			// Or, you may restrict access to only certain users based on their username. 
			if (in_array($UserGroup, $arrGroups)) { 
				$isValid = true; 
			} 
			if (($strUsers == "") && false) { 
				$isValid = true; 
			} 
		} 
		return $isValid; 
	}

	public function check($restrictGoTo) {
				
		$MM_restrictGoTo = $restrictGoTo;
		
		if (!((isset($this->session->data['MM_Username'])) && ($this->isAuthorized("",$this->MM_authorizedUsers, $this->session->data['MM_Username'], $this->session->data['MM_UserGroup'])))) {
			$MM_qsChar = "?";
			$MM_referrer = $this->request->server['PHP_SELF'];
			if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
			if (isset($this->request->server['QUERY_STRING']) && strlen($this->request->server['QUERY_STRING']) > 0) 
				$MM_referrer .= "?" . $this->request->server['QUERY_STRING'];
			$MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
			header("Location: ". $MM_restrictGoTo); 
			exit;
		}
	}
	
	public function isLogged () {
		$lgged = $this->isAuthorized("",$this->MM_authorizedUsers, $this->session->data['MM_Username'], $this->session->data['MM_UserGroup']);
		
		return($lgged);
	}
	
	public function logout() {
		unset($this->session->data['user_id']);
	
		$this->user_id = '';
		$this->username = '';
		
		session_destroy();
  	}
	
	public function getUserName() {
    	return $this->session->data['MM_Username'];
  	}

	
	
}