<?php

if(isset($_POST['sub']))
{
$name=$_POST['name'];
$email=$_POST['email'];
$phone=$_POST['phone'];
$message=$_POST['message'];

$msg="Name: ". $name . "<br>" . "Email Id: ". $email . "<br>" . "Phone No: ". $phone . "<br>" .
 "Message: ". $message;

$to = "contact@ontimeinfotech.com";
$subject = "Inquiry Received";
$sender = "$email";
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
$headers .= "from: " . $sender."\n";
if(mail($to, $subject, $msg, $headers))
{
echo "Email has been sent.";
}
else
{
echo "Error !!";
}
}

?>

<h1> Enquiry Form </h1> <br/><br/>

<form action="" method="post">
<input type="text" name="name" placeholder="Name" required><br><br>
<input type="email" name="email" placeholder="Email" required><br><br>
<input type="phone" name="phone" placeholder="Mobile" maxlength="10" required><br><br>
<textarea name="message" ></textarea><br><br>
<input type="submit" name="sub" value="Submit">

</form>

