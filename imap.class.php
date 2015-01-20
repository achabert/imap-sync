<?php
class IMAP {
	private $socket;
	
	function __construct($host, $port, $login, $password) {
		if(!($this->socket = fsockopen("ssl://$host", $port, $errno, $errstr)))
			die("[-] $errstr\n");
		
		$x = $this->read(false);
		
		$this->send("LOGIN $login $password");
		$x = $this->read();
	}
	
	function send($data) {
		echo "[+] imap: send: $data\n";
		fwrite($this->socket, "? $data\r\n");
	}
	
	function read($success = true) {
		$reply = array();
		
		while($data = fgets($this->socket)) {
			$line = trim($data);
			echo "[+] imap: read: $line\n";
			
			if($success && substr($line, -9) == '(Success)')
				return $reply;
			
			if($success && $line == '? OK Success')
				return $reply;
			
			if(!$success && substr($line, 0, 5) == '* OK ')
				return $reply;
			
			if(substr($line, 1, 4) == ' NO ') {
				echo "[-] imap: $data";
				return null;
			}
			
			$reply[] = $line;
		}
	}
	
	//
	// create a directory
	//
	function create($name) {
		$this->send('CREATE "'.$name.'"');
		$x = $this->read();
	}
	
	//
	// select a folder
	//
	function select($name) {
		$this->send('SELECT "'.$name.'"');
		$x = $this->read();
	}
	
	//
	// grab headers with UID
	//
	function header($from, $to) {
		$this->send("UID FETCH $from:$to (BODY[HEADER])");
		$data = $this->read();
		
		return $data;
	}
	
	//
	// build a list of Message-Id => UID
	//
	function messageid($list) {
		$uid = 0;
		$mid = array();
		
		foreach($list as $line) {
			if(preg_match('#^\* ([0-9]+) FETCH#', $line)) {
				$temp = explode(' ', $line);
				$uid  = $temp[4];
			}
			
			$temp = strtoupper($line);
			
			if(substr($temp, 0, 12) == 'MESSAGE-ID: ')
				$mid[$line] = $uid;
		}
		
		return $mid;
	}
	
	//
	// move a mail
	//
	function move($uid, $destination) {
		$this->send("UID MOVE $uid:$uid $destination");
		$x = $this->read();
	}
}
?>
