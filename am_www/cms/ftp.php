<?php
$ftp_server="ftp.servage.net";
$ftp_user_name="docs";
$ftp_user_pass="docs2011@@";
$file = base64_decode($_REQUEST['s']);
$remote_file = base64_decode($_REQUEST['d']);

//if ($_REQUEST['co']!=(strlen($_REQUEST['s']) * 9 / 13)) exit();
// set up basic connection
//echo (strlen($_REQUEST['s']) * 9 / 13);

if (!file_exists($file)) exit();

$conn_id = ftp_connect($ftp_server);

// login with username and password
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

// upload a file
if (ftp_put($conn_id, $remote_file, $file, FTP_BINARY)) {
 echo "successfully uploaded $file\n";
 echo $remote_file;
 if ($_REQUEST['act']=='cut'){
 	if (substr($file,0,13) == "../temp_docs/"){
 		@unlink($file);
 	}
 }
} else {
 echo "There was a problem while uploading $file\n";
}

// close the connection
ftp_close($conn_id);