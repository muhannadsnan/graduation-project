<?php
if(basename($_SERVER['PHP_SELF'])  == 'maillist.php') $maillist=true;
 
if(isset($_POST['email'])) { 

/****************** manipulation for maillist email sending *****************/ 
	
// collect emails subrcribed in our maillist

	if($maillist){  
		$tbl=table("select email from mail_list");
		while($row=mysql_fetch_array($tbl)){
			$email_to .= "{$row['email']}, ";
		}
	}
	else{    $email_to = "msn-23@live.com";   }
 
    $email_subject = "Contact Us Message:";
 
    function died($error) {// error code goes here 
        echo errors_found; 
        echo These_errors_appear_below; 
        echo $error."<br /><br />"; 
        echo go_back_fix_errors; 
        die(); 
    }
 
    // validation expected data exists 

    if(!isset($_POST['full_name']) || /*!isset($_POST['last_name']) ||*/ !isset($_POST['email']) || 
        !isset($_POST['telephone']) || !isset($_POST['comments'])) 
    { died(problem_with_form); }
 
// collect information that user entered
 
    $full_name = $_POST['full_name']; // required 
    //$last_name = $_POST['last_name']; // required 
    $email_from = $_POST['email']; // required 
    $telephone = $_POST['telephone']; // not required 
    $comments = $_POST['comments']; // required    
 
    $error_message = ""; 
    $email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
 
/**************** Validation ****************/

// validate email format

  if(!preg_match($email_exp,$email_from)) { 
    $error_message .= Invalid_Email; 
  }
 
    $string_exp = "/^[A-Za-z .'-]+$/";
 
  if(!preg_match($string_exp,$full_name)) { 
    $error_message .= Invalid_FullName; 
  }
 
  /*if(!preg_match($string_exp,$last_name)) { 
    $error_message .= Invalid_LastName; 
  }*/
 
  if(strlen($comments) < 2) { 
    $error_message .= Invalid_Comments; 
  }
 
  if(strlen($error_message) > 0) { 
    died($error_message); 
  }
 
    $email_message = "Form details below.\n\n";    
 
    function clean_string($string) { 
      $bad = array("content-type","bcc:","to:","cc:","href"); 
      return str_replace($bad,"",$string); 
    }
      
// removing unwanted text from message before sending

    $email_message .= "Full Name: ".clean_string($full_name)."\n"; 
    //$email_message .= "Last Name: ".clean_string($last_name)."\n"; 
    $email_message .= "Email: ".clean_string($email_from)."\n"; 
    $email_message .= "Telephone: ".clean_string($telephone)."\n"; 
    $email_message .= "Comments: ".clean_string($comments)."\n";    
 
// create email headers 
$headers = 'From: '.$email_from."\r\n"
. 'Reply-To: '.$email_from."\r\n" 
.'X-Mailer: PHP/' 
. phpversion();

//send the email with all this information
 
@mail($email_to, $email_subject, $email_message, $headers);  

// Show messages

if($maillist){  ?><div style="width:500px;margin:50px auto;color:green"><?=Successfully_Sent?></div><?  }
else{ ?><div style="width:500px;margin:50px auto;"><?=Thank_you_for_contacting_us?></div><? } 

}

// collect emails that we sent to, to display after sending

$email_to2 = "<br/> > " . $email_to;
$email_to2 = str_replace(",","<br/> > ",$email_to2);
$email_to2 .= ">>>>>>>>>>>>>>>>>end";
?>