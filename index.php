// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once("vendor/autoload.php");
require_once("Qccheck.php");

    $conn = mysqli_connect("localhost","ubuntu","","gtvingestdb") or die("Database Cannot be Connected - ".mysqli_error($conn));

// Create connection
// $conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}





use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
//QC Check;
$qccheck = new Qccheck();

$qccheck1 = $qccheck->qcprocess();
$transcodecheck = $qccheck->transcode();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$fileList = array();

if(isset($_GET) && isset($_GET["downloadvdo"]) && $_GET["downloadvdo"] != '')
{
    $video = strip_tags(trim($_GET['downloadvdo']));

    // downloadVdo($video);
    dwld($video);
}

/*
$handle = opendir('/home/gtvingest/ingest/'); 
if ($handle) {
    while (($entry = readdir($handle)) !== FALSE) {
        $fileList[] = $entry;
    }
} 
closedir($handle);
*/


// Function to check if a remote file exists
function remoteFileExists($url)
{
    $curl = curl_init($url);

    //don't fetch the actual page, you only want to check the connection is ok
    curl_setopt($curl, CURLOPT_NOBODY, true);

    //do request
    $result = curl_exec($curl);

    $ret = false;

    //if request did not fail
    if ($result !== false)
    {
        //if request was ok, check response code
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);  

        if ($statusCode == 200)
        {
            $ret = true;   
        }
    }
    curl_close($curl);
    return $ret;
}
// new code
$folder = '../../ingest/';
$filetype = '*.*';    
$files = glob($folder.$filetype);    
$total = count($files);  
usort($files, function($x, $y) {
    return filectime($x) < filectime($y);
});
// foreach($files as $item){
//     echo basename($item) . " => Last Modified On " . @date('F d, Y, H:i:s', filemtime($item)) . "<br/>";
// }
// print_r($files);
// die();
// print($total);  
$per_page = 20; 
// $last_page1 = ceil($total / $per_page);
$last_page = ceil($total / $per_page);
// print($last_page1);

if(isset($_GET["page"])  && ($_GET["page"] <= $last_page) && ($_GET["page"] > 0) ){
    
    $page = $_GET["page"];
    // echo $per_page .'data'. $page;
    // echo $page .'dfgsssssssssssssssssssss' ;
    $offset = ($per_page)*($page - 1);  
    // print($offset). 'fsdfdfsdsdfdfs';
    // $offset = ($per_page + 1); 
    // print('entered');    
}else{
    // print('not');
    // echo "Page out of range showing results for page one";
    $page=1;
    $offset=0;      
}    
$max = $offset + $per_page;    
if($max>$total){
    $max = $total;
}
  


// end of new code
//$fileList = listdir_by_date('/home/gtvingest/ingest/');
// $fileList = glob('/home/gtvingest/ingest/*');
$fileList = glob('../../ingest/*');

function dwld($filename)
{
    $s3 = Aws\S3\S3Client::factory();

    $command = $s3->getCommand('GetObject', array(
       'Bucket' => 'ebws-hybrik-transcoded',
       'Key'    => $filename,
       'ResponseContentDisposition' => 'attachment; filename="'.$filename.'"'
    ));

    $signedUrl = $command->createPresignedUrl('+15 minutes');
    echo $signedUrl;
    header('Location: '.$signedUrl);
    exit;
}


function listdir_by_date($path)
{ 
    $dir = opendir($path);
    $list = array();
    // $files = scandir($path, SCANDIR_SORT_DESCENDING);
    // $newest_file = $files[0];
    while($file = readdir($dir))
    {
        if ($file != '.' and $file != '..')
        {
            // add the filename, to be sure not to
            // overwrite a array key
            $ctime = filectime($data_path . $file) . ',' . $file;
            $list[$ctime] = $file;
            // $files = scandir('data', SCANDIR_SORT_DESCENDING);
            // $newest_file = $files[0];
        }
    }
    closedir($dir);
    krsort($list);
    return $list;
}
function filesize_format($size, $sizes = array('Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'))
{
    if ($size == 0) return('n/a');
    return (round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $sizes[$i]);
}


foreach ($files as $path) {
   $docs[$path] = filectime($path);
} 
asort($docs);

?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="https://unpkg.com/purecss@2.0.6/build/pure-min.css" integrity="sha384-Uu6IeWbM+gzNVXJcM9XV3SohHtmWE+3VGi496jvgX1jyvDTXfdK+rfZc8C1Aehk5" crossorigin="anonymous">
<style>

    
.pagination {
  display: inline-block;
}

.pagination a {
  color: black;
  float: left;
  padding: 8px 16px;
  text-decoration: none;
  transition: background-color .3s;
  border: 1px solid #ddd;
  margin: 0 4px;
}

.pagination a.active {
  background-color: #4CAF50;
  color: white;
  border: 1px solid #4CAF50;
}

.pagination a:hover:not(.active) {background-color: #ddd;}
</style>
</head>
<body>
<div class="content-wrapper">
    <div class="content" style="margin: 20px;">
<h1>God TV Ingest</h1>
<div>
<input type="button" name="Bulk_QC_video" onclick="bulckqcforcheck()" id="Bulk_QC_video" value="Bulk_QC" />
</div>

<table class="pure-table pure-table-bordered">
    <thead>
        <tr>
	    <th><input type="checkbox"  onclick="disablebulckqc()" name="select-all" id="select-all" /></th>
            <th>File</th>
            <th>Size</th>
            <th>Date</th>
            <th>File isExist</th>
            <th>QC</th>
            <th>Transcode</th>
            <th>Download</th>
           <th>Archive</th>
        </tr>
    </thead>
    <tbody>
<?php



//foreach($fileList as $filename){echo "$filename " . filesize($filename) . "\n";}

// foreach ($docs as $path => $timestamp)
// {
    // echo 'dsasaddsadsqqqqqqqqq'. $offset. 'qqqqqqqqqqqqqqqzxdsdsf';
for($i = $offset; $i < $max; $i++){
    
        $file = $files[$i];
         
        $path_parts = pathinfo($file);
      
        $filename = $path_parts['filename'];  
        $Checkvideo = basename($file);
        // print($Checkvideo);
        // die();
        $sql = "SELECT is_archived FROM ingest_videos WHERE video_name =  '$Checkvideo'";
        $result = $conn->query($sql);
        // print($Checkvideo);
        $checkqc_done = "SELECT job_status FROM qc_jobs WHERE video =  '$Checkvideo' LIMIT 1";
        $qc_results = $conn->query($checkqc_done);
        $transcode_done = "SELECT status FROM transcode_jobs WHERE video =  '$Checkvideo' LIMIT 1";
        $transcode_result = $conn->query($transcode_done);
	$archive = "SELECT isarchive FROM archive_videos WHERE name =  '$Checkvideo' LIMIT 1";
	$archive_result = $conn->query($archive);
	$isarchive = $archive_result->fetch_assoc();
	
                 // // $job_done_status = '';
        // if(isset($qc_results))
        // {
            // print_r($qc_results->fetch_assoc());
            // die();
            // $job_done_status = $qc_results->fetch_assoc()['job_status'];
            // $transcodestatus = $transcode_result->fetch_assoc()['status'];
            // print_r($job_done_status);
            // die();
        // }
        // $job_done_status = $qc_results->fetch_assoc()['job_status'];
        // print($job_done_status);
        // die();
        // print_r($result->fetch_assoc()['is_archived']);
        // if (isset($result->num_rows) && $result->num_rows > 0) {
            // output data of each row
            // while($row = $result->fetch_assoc()) {
            //   echo "id: " . $row["is_archived"]. "<br>";
            // }
        //   }else{
        //     //   echo 'dszfsdfdfs';
        //   }
        $job_done_status = 'completed';
        print '<tr>';
        if($job_done_status ==  'completed')
        {
            ?>
            <td  onclick="disablebulckqc()">
            <i  nput type="checkbox" name="bulckqc" value='<?php basename($file) ?>'>
            </td>
        <?php
        }else{
            ?>
              <td  onclick="disablebulckqc()">
            
              </td>
            <?php
        }

        ?>
        
        </td>
   

	    
            <td><a href="/dljson.php?jsn=<?php print basename($file) ?>"> Download <?php print basename($file) ?> JSON</a> </td>
            <td><?php  filesize_format(filesize($file))?> </td>
            <td><?php
            print date("F d Y H:i:s.", 
                      filectime($file));
                      ?>
           </td>

<?php
            // your filename
           

            // this will use AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY from env vars
            // $s3 = Aws\S3\S3Client::factory();
            // $s3 = new S3();

            // $info = $s3->getObjectInfo($_ENV['S3_BUCKET'], $filename);

            // S3_BUCKET must also be defined in env vars
            // $bucket = $_ENV['S3_BUCKET'] ?: die('No "S3_BUCKET" config var in found in env!');

            // register stream wrapper method
            // $s3->registerStreamWrapper();

            // does file exist
            // $keyExists = file_exists("s3://".$bucket."/".$filename);

            $filename=str_replace([".mxf",".mov",".mpg"],[".mp4",".mp4",".mp4"],basename($filename));

        //     $keyExists=remoteFileExists('https://ingest.goddigital.com/ingested/W5-BWOF22-45.mp4');
        //    $keyExists=remoteFileExists('https://s3.us-east-1.amazonaws.com/ebws-hybrik-transcoded/'.$filename);
	        $keyExists = remoteFileExists('https://s3.wasabisys.com/Media/source/'.'Untitled_converted.mp4');
		
            if ($keyExists)
            {
                // echo 'file exists';
                $chk_file_exists = 'File Exist';
            }
            else
            {
                // echo 'file does not exist'; 
                $chk_file_exists = "File Doesn't Exist";  
            }
            ?>
            <td><?php echo $chk_file_exists;?></td>
            <?php
           
            // print($job_done_status);
            // die();
            if(isset($job_done_status) && $job_done_status == "completed")
            {
            ?>
            <td>
                <input type="button" name="transcode_btn" id="transcode_btn" disabled value="Completed" onclick="submit_qc_job('<?php echo basename($file); ?>')" />
            </td>
            <?php
            }elseif(isset($job_done_status) && $job_done_status == "failed"){
            ?>
            
            <td>
                <input type="button" name="transcode_btn" id="transcode_btn" disabled value="Failed" onclick="submit_qc_job('<?php echo basename($file); ?>')" />
            </td>

            <?php
            }elseif(isset($job_done_status) && $job_done_status == "queued" || $job_done_status == "running"){
            ?>
            <td>
                <input type="button" name="transcode_btn" id="transcode_btn" disabled value="InProgress" onclick="submit_qc_job('<?php echo basename($file); ?>')" />
            </td>
            <?php
            }else{
                ?>
                <td>
                <input type="button" name="transcode_btn" id="transcode_btn" value="QC" onclick="submit_qc_job('<?php echo basename($file); ?>')" />
            </td>
                <?php
            }
            ?>
            <?php
            if(isset($job_done_status) && $job_done_status == "completed"){
              if($transcodestatus == 'completed'){
            ?>
                <td>
                <input type="button" name="transcode_btn" id="transcode_btn" disabled value="sucessfully" onclick="submit_transcode_job('<?php echo basename($file); ?>')" />
            </td>
            <?php }else{ ?>
                <td>
                <input type="button" name="transcode_btn" id="transcode_btn" value="Transcode" onclick="submit_transcode_job('<?php echo basename($file); ?>')" />
            </td>
                <?php }?>
            
            <?php
            }else{
            ?>
             <td>
                <input type="button" name="transcode_btn" disabled id="transcode_btn" value="Transcode" onclick="submit_transcode_job('<?php echo basename($file); ?>')" />
            </td>
            <?php
            }
            ?>
            <td>
                <?php if ($keyExists) { ?>
                <a href="allfiles-download.php?downloadvdo=<?php echo $filename;?>">Download (<?php echo $filename;?>)</a>
                <?php }else{ ?>
                File Doesn't Exist
                <?php }?>
            </td>
<?php
if('sddddddddddddd'){
?>
<td>

<input type="button" name="archive_btn" id="archive_btn" value="Undo" onclick="submit_undoarchive_job('<?php echo basename($file); ?>')" />
</td>
<?php
}
else{?>
	<td>
	<input type="button" name="archive_btn" id="archive_btn" value="Archive" onclick="submit_archive_job('<?php echo basename($file); ?>')" />
	</td>
<?php
}
?>
            
        </tr>
<?php
    }
?>
    </tbody>
</table>
</div>
</div>

<?php
'<div class="pull-right">';
function show_pagination($current_page, $last_page){
    // echo 'current page :'.$current_page;
    
    '<div  style="display: inline-block;text-align:right float: right">';
    if($current_page > 1){
        echo '<a style="color: black;
        float: left;
        padding: 8px 16px;
        text-decoration: none;
        transition: background-color .3s;
        border: 1px solid #ddd;
        margin: 0 4px;"
        href="?page='.($current_page-1).'">&laquo;</a>';   
            }
    for($i = 1; $i <= $last_page; $i++)
    {  
     if($current_page == $i)
     {
        echo '<a style="color: blue;float: left;padding: 8px 16px;text-decoration: none;transition: background-color .3s;border: 1px solid #ddd;margin: 0 4px;"
        href="?page='.($i).'">'. $i. '</a> '; 
     }else{
        echo '<a style="color: black;float: left;padding: 8px 16px;text-decoration: none;transition: background-color .3s;border: 1px solid #ddd;margin: 0 4px;"
        href="?page='.($i).'">'. $i. '</a> ';  
     }
         
   
    }
    if($current_page < $last_page){
        echo '<a style="color: black;
            float: left;
            padding: 8px 16px;
            text-decoration: none;
            transition: background-color .3s;
            border: 1px solid #ddd;
            margin: 0 4px;"
            href="?page='.($current_page+1).'">&raquo;</a>';
            '<div>';
        }
    // echo '<div>';
    // if( $current_page > 1 ){
    //     echo ' <a href="?page='.($current_page-1).'"> &lt;&lt;Previous </a> ';
    // }
    // if( $current_page < $last_page ){
    //     echo ' <a href="?page='.($current_page+1).'"> Next&gt;&gt; </a> ';  
    // }
    // echo '</div>';
}
'</div>';
echo "<div>";
    show_pagination($page, $last_page);
'</div>';
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>

<script type="text/javascript">
 function bulckqcforcheck()
    { 
   var markedVideos = document.querySelectorAll('input[type="checkbox"]:checked');  
   const video = [];
   i = 4;
    for (var chk of markedVideos) {  
        video[i] = chk.value
        // document.body.append(chk.value + ' ');  
        i++
        }
        debugger;
             $.ajax({
               type:'POST',
               url:'/bulckqc.php?jsn='+video,
               success:function(data) {
               }
            });
    }
    function disablebulckqc()
    {
        document.getElementById("Bulk_QC_video").style.display = "block";        
    }
    window.onload = function() 
    {
        document.getElementById("Bulk_QC_video").style.display = "none";
    };
    function submit_qc_job($file)
    {
        if($file != null && $file != '')
        {
            window.location.href = "/qc.php?jsn="+$file;
        }
    }
    function submit_transcode_job(filepath)
    {
        if(filepath != "")
        {
            window.location.href = "/transcode1.php?jsn="+filepath;
            // window.location.href = "/transcode.php?jsn="+filepath;
        }
    }
    function submit_undo_job(file)
    {
        if(file != "")
        {
            window.location.href = "/allfiles-archive.php?unarchivevideo="+file;
            // window.location.href = "/transcode1.php?jsn="+filepath;
        }
    }
    function submit_archive_job(file)
    {
        if(file != "")
        {
            window.location.href = "/allfiles-archive.php?archivevideo="+file;
            // window.location.href = "/transcode.php?jsn="+filepath;
        }
    }
function submit_archive_job(file)
{
  	$.ajax({  
         type:"POST",  
         url:"archivevideo.php",  
         data:"fname="+file,  
         beforeSend: function(data){  
            //alert("this test");  
         },
success:function(data){  
             location.reload();         
},
      });
}
function submit_undoarchive_job(file)
{
//alert('undo');
	$.ajax({  
         type:"POST",  
         url:"undovideo.php",  
         data:"fname="+file,  
         beforeSend: function(data){  
            //alert("this test");  
         },
	success:function(data){  
            location.reload();
         },
      });

}
$('#select-all').click(function(event) {   
    if(this.checked) {
        // Iterate each checkbox
        $(':checkbox').each(function() {
            this.checked = true;                        
        });
    } else {
        $(':checkbox').each(function() {
            this.checked = false;                       
        });
    }
}); 

</script>


</body>
</html>
