<?php
include ('dbconnect.php');
if(isset($_POST['']))
{
user_login($_POST);
}
?>

<html>
<head> </head>
<body>
<section class="bg-grey">
<div class="main">
<div class="container">
<div class="col-md-12">
<div class="form-content-box">
<div class="login-header">
<h3 class="text-center"> Login </h3>
</div>
<div class="details">
<form action="" method="post">
<div class="form-group">
<input type="email" name="email" placeholder="Enter email" autocomplete="off" required 
class="form-control" pattern="[a-z0-9._%+-]+@[a-z.-]+\.[a-z]{2,3}$">
</div>
<div class="form-group">
<input type="password" name="password" class="form-control" placeholder="Password" required>
</div>

<div class="form-group">
<button type="submit" name="login" class="btn btn-submit">login</button>
</div>
</form>
</div>

<div class="login-footer">
</div>
</div>
</div>
</div>
</div>
<section>
<div class="clearfix"></div>
</body>
</html>

<?php

function user_login($data){
$email = $data['email'];
$password = md5($data['password']);
$login_qry = "select * from users where email='$email' AND password='$password'";
$login=mysqli_query($GLOBALS['db'],$login_qry) or die ("mysql error2".mysqli_error($GLOBALS['db']));
$result=mysqli_fetch_array($login);
if(mysqli_num_rows($login)>0)
{
$_SESSION['user']['id']=$result['id'];

echo '<script type="text/javascript">window.location.href="home.php";</script>';

}
else
{
$_SESSION['user']['id']="";
echo '<script type="text/javascript">alert("Please Enter Correct Username and Password");</script>';
}
}

?>

