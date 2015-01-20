<?php
class Local {
	private $path;
	private $folders;
	private $tree;
	
	public $excludes = array('sent', 'draft', 'trash');
	
	function __construct($path) {
		$this->path = $path;
		$this->build();
	}
	
	//
	// build local tree
	//
	function build() {
		echo "[+] local: building filelist...\n";
		
		// load files list
		$temp = $this->recursive($this->path.'/*');
		
		// attach message id
		$this->tree = $this->load($temp);
		
		// building local folders
		$this->folders = $this->directories($this->tree);
		
		print_r($this->tree);
		print_r($this->folders);
	}
	
	//
	// extract a message id line from a mail
	//
	private function messageid($filename) {
		$fd = fopen($filename, 'r');
		
		while($line = fgets($fd)) {
			$temp = strtoupper($line);
			
			if(substr($temp, 0, 12) == 'MESSAGE-ID: ') {
				fclose($fd);
				return trim($line);
			}
		}
		
		return $filename;
	}
	
	//
	// remove file id and pre-path
	//
	private function clear($filename) {
		$temp = str_replace($this->path.'/', '', $filename);
		$temp = substr($temp, 0, strrpos($temp, '/'));
		return $temp;
	}
	
	//
	// attach message-id to each file
	//
	private function load($list) {
		$result = array();
		
		foreach($list as $key => $filename) {
			if(is_dir($filename))
				continue;
			
			$id = $this->messageid($filename);
			$result[$id] = $this->clear($filename);
		}
		
		return $result;
	}
	
	//
	// load all files on mail directory
	//
	private function recursive($pattern, $flags = 0) {
		$files = glob($pattern, $flags);

		foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
			$files = array_merge($files, $this->recursive($dir.'/'.basename($pattern), $flags));

		return $files;
	}
	
	//
	// build an unique list of directories found
	//
	function directories($list) {
		$result = array();
		
		foreach($list as $key => $value) {
			if(isset($result[$value]))
				$result[$value]++;
			
			else $result[$value] = 1;
		}
		
		return $result;
	}
	
	//
	// return a list of folders
	//
	function folders() {
		$folders = array();
		$localex = array('inbox');
		
		foreach($this->folders as $folder => $items) {
			if(in_array($folder, $this->excludes))
				continue;
			
			if(in_array($folder, $localex))
				continue;
			
			$temp = explode('/', $folder);
			$folders = $this->iterate($folders, $temp);
		}
		
		return $folders;
	}
	
	//
	// build a iterative path for subdirectories
	//
	private function iterate($folders, $items) {
		for($i = 0; $i < count($items); $i++) {
			$temp = array();
			
			for($j = 0; $j <= $i; $j++)
				$temp[] = $items[$j];
			
			$path = implode('/', $temp);
			
			if(!in_array($path, $folders))
				$folders[] = $path;
		}	
		
		return $folders;
	}
	
	function tree() {
		return $this->tree;
	}
}
?>
