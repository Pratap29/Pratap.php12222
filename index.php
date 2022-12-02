<?php
$connect=mysql_connect("servername","username","password") OR die ("connection failed");
$db=mysql_select_db("database_name", $connect);

if(isset($_POST['submit']))
{

//the following code will automatically generate a random password and will store into database.

$pass=mt_rand(1000000, 2555555);
$name=$_POST['name'];
$email=$_POST['email'];
$class=$_POST['class'];

$sql="INSERT INTO students(name,email,class,password) VALUES('$name','$email','$class','$pass')";
$query=mysql_query($sql);
if($query)
{
echo "New data is created." ;
}
else
{
echo "Error !!" ;
}
}
?>

<form action="" method="post">

<input type="text" name="name" placeholder="Full Name">
<input type="email" name="email" placeholder="Enter your email-id">
<input type="text" name="class" placeholder="Enter class name">
<input type="submit" name="submit" value="Click Here">

</form>

