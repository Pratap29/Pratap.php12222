<?php
$string= "Welcome! This page doesn't exist!";
echo $string."<br>";

//Change the fontsize using PHP, HTML and css through echo
echo '<span style="font-size: 50px;"> ' . $string.  ', <a href="something.php">New Page</a></span>';

//Change the font color using PHP, HTML and css through echo
echo "<br>";
echo '<span style="color: blue; font-size: 50px;"> ' . $string.  ', <a href="something.php">New Page</a></span>';
?>


