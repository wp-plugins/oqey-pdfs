<?php
define("IPDF_PLUGIN_DIR", dirname(__FILE__));
$message = "";
include(ABSPATH."/wp-config.php");


if(isset($_REQUEST['pdf_verify'])){
        $file_type = $_FILES['pdffile']['name'];
        $file_type_length = strlen($file_type) - 3;
        $file_type = substr($file_type, $file_type_length);
	    $file_type = strtolower($file_type);
		
if($file_type=="pdf"){
	if($_FILES) {
	   // $filesize = $_FILES['pdffile']['size'];
	    $file_name = urlencode($_FILES['pdffile']['name']);
	    $link_value_check = ABSPATH."/wp-content/plugins/oqey-pdfs/pdfs/".$file_name;
		$maxlimit = ini_get('upload_max_filesize');
		if($_FILES['pdffile']['size'] > $maxlimit ){
	    if(!is_file($link_value_check))//verifica daca este deja un fisier cu acelasi nume pe server
		{		
		move_uploaded_file($_FILES['pdffile']['tmp_name'], IPDF_PLUGIN_DIR."/pdfs/${file_name}");
		
		global $wpdb;
        $iPdf_table = $wpdb->prefix . "oqeypdfs";
        //insert data in db
		$file_name = urlencode($file_name);
        $wpdb->query("INSERT INTO $iPdf_table (iPdf_link) 
                           VALUES ('".$file_name."')");		
		
		$message =  '<div style="color:#006633;">file was uploaded</div>';
		
		}else{ $message =  '<div style="color:#FF6600;">file exist</div>'; }
		
		}else{
		       $message = "Your file is too big, max upload file = ".ini_get('upload_max_filesize');
		}
	}
	
	}else{ 
	    $message =  '<div style="color:#FF6600;">all files must be in pdf format</div>';
	}

}
?>
<div class="wrap">
        	<h2>Manage pdf`s</h2>
          </div>
<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" name="form1" id="form1">
<table width="500" border="0" align="left" cellpadding="0" cellspacing="0">
<tr>
<td>
<table width="500" border="0" cellpadding="3" cellspacing="1" bgcolor="#FFFFFF">
<tr>
<td width="164"><strong>Single PDF File Upload </strong></td>
<td width="321"><?php echo $message; ?></td>
</tr>
<tr>
<td colspan="2">Select pdf file
<input name="pdf_verify" type="hidden" value="hidden" />
<input name="pdffile" type="file" id="pdffile" size="50" style="border:#000033 2px solid;" />
</td>
</tr>
<tr>
<td colspan="2" align="center">

  <div align="center">
    <input type="submit" name="Submit" value="Upload" style="width:100px;" />
  </div></td>
</tr>
<tr>
<td colspan="2" align="center">

  <div align="left" style="color:#CC6600;">
     <?php echo "Note : Your php.ini upload_max_filesize is ".ini_get('upload_max_filesize'); ?>
  </div>
  </td>
</tr>
</table>
</td>

</tr>
</table>

<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</form>


<p>&nbsp;</p>
<?php

        global $wpdb;
		$siteurl = get_option('siteurl');
        $plugin_full_url = get_option('siteurl')."/wp-content/plugins/oqey-pdfs/";
        $iPdf_table = $wpdb->prefix . "oqeypdfs";

$get_pdf = $wpdb->get_results("SELECT * 
                                 FROM $iPdf_table
							 ORDER BY iPdf_order ASC
                              ");
echo '<table width="900" border="0" cellspacing="0" cellpadding="1">
      <tr>
       <td width="25">&nbsp;</td>
       <td>Link</td>
       <td>Title</td>
       <td>Description</td>
       <td>Order</td>
       <td>Password</td>
       <td>&nbsp;</td>
	   <td>&nbsp;</td>
     </tr>';
$j = 1;
foreach ($get_pdf as $pdf_i) {
if($j%2){ $colorb = '#CCCCCC'; }else{ $colorb = '#999999'; }
echo '
  <tr id="tr_'.$j.'" style="background-color:'.$colorb.';">
    <td width="25">'.$j.'</td>
    <td><input type="text" name="title" id="link'.$j.'" value="'.urldecode($pdf_i->iPdf_link).'" readonly="readonly"></td>
    <td><input type="text" name="title" id="title'.$j.'" value="'.$pdf_i->iPdf_title.'"></td>
    <td><input type="text" name="title" id="title_for'.$j.'" value="'.$pdf_i->iPdf_title_for.'"></td>
    <td><input type="text" name="title" id="order'.$j.'" value="'.$pdf_i->iPdf_order.'" size="3" maxlength="3" ></td>
    <td><input type="text" name="title" id="password'.$j.'" value="'.$pdf_i->iPdf_password.'"></td>
    <td>
	<input type="hidden" name="ipdf_id_'.$j.'" id="ipdf_id_'.$j.'" value="'.$pdf_i->iPdf_id.'">
	<input type="submit" name="delete" id="delete" value="delete" onclick="deleteIpdf(\''.$j.'\'); return false;">
	<input type="submit" name="update" id="update" value="update" onclick="updateIpdf(\''.$j.'\'); return false;">
	</td>
	<td width="100"><div id="ajax_ipdf_'.$j.'"></div></td>
  </tr>';
  $j ++;
}

echo '</table>';

?>

<p>&nbsp;</p>
<script type="text/javascript">
function getxmlhttp (){
		var xmlhttp = false;		
		try {
			xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (E) {
				xmlhttp = false;
			}
		}
		if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
			xmlhttp = new XMLHttpRequest();
		}
		return xmlhttp;
	}

function updateIpdf(id){ 
ajaxRequest = getxmlhttp ();
	ajaxRequest.onreadystatechange = function(){
		  if(ajaxRequest.readyState == 1){
	    var ajaxDisplay = document.getElementById('ajax_ipdf_'+id);
        ajaxDisplay.innerHTML='working...';
      }
		if(ajaxRequest.readyState == 4){
			var ajaxDisplay = document.getElementById('ajax_ipdf_'+id);
			ajaxDisplay.innerHTML = ajaxRequest.responseText;			
	  }
	}
	var ipdf_id = document.getElementById('ipdf_id_'+id).value;
	var title = document.getElementById('title'+id).value;
	var title_for = document.getElementById('title_for'+id).value;
	var order = document.getElementById('order'+id).value;
	var password = document.getElementById('password'+id).value;	

	var queryString = "?id=" + id + "&update=yes" + "&ipdf_id=" + ipdf_id + "&title=" + title + "&title_for=" + title_for + "&order=" + order + "&password=" + password + "&key=" + Math.random()*99999999;
	ajaxRequest.open("GET", "<?php echo $plugin_full_url; ?>add_ipdf.php" + queryString, true);
	ajaxRequest.send(null); 
}

function deleteRow(id){
var row = document.getElementById("tr_"+id);
row.style.display = 'none';
}

function deleteIpdf(id){ 
ajaxRequest = getxmlhttp ();
	ajaxRequest.onreadystatechange = function(){
		  if(ajaxRequest.readyState == 1){
	    var ajaxDisplay = document.getElementById('ajax_ipdf_'+id);
       ajaxDisplay.innerHTML='working...';
      }
		if(ajaxRequest.readyState == 4){
			var ajaxDisplay = document.getElementById('ajax_ipdf_'+id);
			ajaxDisplay.innerHTML = ajaxRequest.responseText;
			setTimeout("deleteRow("+ id +")",500);
			
	  }
	}
	var ipdf_id = document.getElementById('ipdf_id_'+id).value;
	var pdf_link = document.getElementById('link'+id).value;
	var queryString = "?id=" + id + "&ipdf_id=" + ipdf_id + "&delete=yes" + "&pdf_link=" + pdf_link + "&key=" + Math.random()*99999999;
	ajaxRequest.open("GET", "<?php echo $plugin_full_url; ?>add_ipdf.php" + queryString, true);
	ajaxRequest.send(null); 
}
</script>