<html>
<head>
<title>Background Colors change based on the day of the week</title>
</head>
<?php
//mistonline.in
$today = date("l");
print("$today");
if($today == "Sunday")
{
$bgcolor = "#FEF0C5";
}
elseif($today == "Monday")
{
$bgcolor = "#FFFFFF";
}
elseif($today == "Tuesday")
{
$bgcolor = "#FBFFC4";
}
elseif($today == "Wednesday")
{
$bgcolor = "#FFE0DD";
}
elseif($today == "Thursday")
{
$bgcolor = "#E6EDFF";
}
elseif($today == "Saturday")
{
$bgcolor = "#E9FFE6";
}
else
{
// Since it is not any of the days above it must be Saturday
$bgcolor = "#F0F4F1";
}
print("<body bgcolor=\"$bgcolor\">\n");


