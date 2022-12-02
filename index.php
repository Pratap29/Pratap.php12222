<div class=”background”>neato!</div> 
<script> 
if(<?php echo $pageType ?> === ‘admin’ ) { 
document.querySelector(“.background”).addClass(“admin”); 
}else{ 
document.querySelector(“.background”).addClass(“user”); 
} 
</script> 
<style> 
.admin{ 
background:green 
} 
.user{ 
background:blue 
} 
</style> 

