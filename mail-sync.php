<?php
include('local.class.php');
include('imap.class.php');

$local = new Local('/tmp/.sylpheed-2.0/Mails/xxxxxx@gmail.com');
$imap  = new IMAP('imap.gmail.com', 993, 'xxxxxx@gmail.com', '****');

//
// creates folders
//
foreach($local->folders() as $folder)
	$imap->create($folder);

//
// checking mails
//

// select All Mail
$imap->select('[Gmail]/All Mail');

// grabbing mails list
$mails = $imap->header('1', '*');

// building list of message id
$ids  = $imap->messageid($mails);
$tree = $local->tree();

//
// moving mails
//
$total = count($ids);
$index = 1;

foreach($ids as $id => $uid) {
	if(!isset($tree[$id])) {
		echo "[-] not found: $id\n";
		continue;
	}
	
	if(in_array($tree[$id], $local->excludes)) {
		echo "[-] skipping [".$tree[$id]."]: $id\n";
		continue;
	}
	
	echo "[+] moving $index / $total\n";
	$imap->move($uid, $tree[$id]);
	
	$index++;
}
?>
