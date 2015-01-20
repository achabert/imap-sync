# imap-sync
sync a local pop3 tree with a remote imap server (gmail)

# Background
I used POP3 for a long time with Gmail to keep everything at home
and archive mail on Gmail server. On my client (Sylpheed) I made a lot of folders
to arrange the whole shit. But now, I using IMAP (for better syncronization with Mobile).
That's it, there were no folder (labels on Gmail) on server side, everything was on "All Mail" folder.

# What's about
This code build a tree of the local mail arborescence and create it on a remote IMAP
server, then move the mail from one folder to its right folder
It compare Message-Id field to move mails on it's right folder

# How to use it
Edit mail-sync.php and put your local path on argument to Local class
Put your username (email) and password on IMAP class.
Use this code on console (php-cli), not on a web-page !

# Working ?
I used this code for my Sylpheed local tree to Gmail. Not tested with another environment.
Be careful !
