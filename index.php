<html>
<head>

<style>
.pagination {
display: inline-block;
padding-left: 0;
margin: 20px 0;
border-radius: 4px;
}
.pagination li {
display: inline;
margin-left: 0;
border-top-left-radius: 4px;
border-bottom-left-radius: 4px;
}
.pagination > .active > a, .pagination > .active > a:hover{
z-index: 3;
color: #fff;
cursor: default;
background-color: #337ab7;
border-color: #337ab7;
}
.pagination > li > a, .pagination > li > span {
position: relative;
float: left;
padding: 5px;
text-decoration: none;
}
</style>

</head>
<body>

<?php
// database variables
$host = "localhost";  //database location
$user = "root";  //database username
$pass = "";  //database password
$db_name = "countries_db";  //database name
$link = mysql_connect($host, $user, $pass);
mysql_select_db($db_name);

function get_transactions_details($limit=10, $offset=0){
if (isset($_GET["page"])) {
$page = $_GET["page"];
} else {
$page=1;
};
$start_from = ($page-1) * $limit;

$trans_all = array();
$query = "SELECT * FROM countries WHERE 1 ORDER BY countries_name LIMIT $start_from, $limit";
$sql = mysql_query($query) or die ("MYsql Error" . mysql_error());
while($fetch=mysql_fetch_array($sql)){
$trans_all[] = $fetch;
}
return $trans_all;
}

function paging($table_name, $link ='#', $where = '1', $limit=4, $offset=0) {
if (isset($_GET["page"])) { $page = $_GET["page"]; } else { $page=1; };
$start_from = ($page-1) * $limit;
$sql = "SELECT COUNT(countries_id) FROM $table_name where $where ORDER BY countries_name";

// $sql = get_search_query($data, $checklist, $cat, $limit, $offset=0);

$rs_result = mysql_query($sql) or die ('MySQL Error'.mysql_error());
$row = mysql_fetch_row($rs_result);
$total_records = $row[0];   // mysql_num_rows($rs_result)
$total_pages = ceil($total_records / $limit);
$pagLink = "<ul class='pagination'>";
$active = '';
for ($i=1; $i<=$total_pages; $i++) {
if(@$_GET['page'] == $i)
$active = 'active';
else
$active = '';
if(($active == '') && ($i ==1) && (!@$_GET['page']))
$active = 'active';
$pagLink .= "<li class='$active'><a href='$link?page=".$i."'>" . $i. "</a></li>";
};
$pagLink .= "</ul>";
return $pagLink;
}

?>

<h3 class="text-danger text-left" style="margin-top:0;"> Countries List </h3>
<table width="100%">
<tbody>
<tr class="danger">
<td class="text-center"> S.No </td>
<td class="text-center"> Country Name </td>
<td class="text-center"> Country ISO Code </td>
<td class="text-center"> Country ISD Code </td>
</tr>

<?php
$limit=15;
$i = 0;
if(@$_GET['page'])
$i = ($limit * ($_GET['page']-1));

$trans_list = get_transactions_details($limit, $offset=0);

$paging = paging($table_name ='countries’, $link ='pagination.php', $where = "1", $limit, $offset=0);

// p($trans_list);

foreach($trans_list as $trans_details){
echo '<tr>
<td width="8%" class="text-center">' .++$i. '</td>
<td class="text-center">' .$trans_details['countries_name']. '</td>
<td class="text-center">' .$trans_details['countries_iso_code']. '</td>
<td class="text-center">' .$trans_details['countries_isd_code']. '</td>';
echo '</tr>';

}
?>
</tbody>
</table>

<div class="text-center">

<?php echo $paging; ?>

</div>

</body>

</html>

