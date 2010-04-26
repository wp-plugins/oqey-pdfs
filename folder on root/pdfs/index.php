<?php
header("Cache-Control: no-cache");
include("../wp-config.php");
global $wpdb;
$siteurl = get_option('siteurl');
$plugin_full_url = get_option('siteurl')."/wp-content/plugins/oqey-pdfs/";
$iPdf_table = $wpdb->prefix . "oqeypdfs";

if(isset($_POST['parolapdf'])){
$parola = mysql_real_escape_string($_POST['parolapdf']);
     
	 $ipdf_get = $wpdb->get_row("SELECT *
                                   FROM $iPdf_table
							      WHERE iPdf_password = '$parola'");
			

if($ipdf_get->iPdf_password!=""){

header('Content-type: application/pdf');
// It will be called downloaded.pdf
header('Content-Disposition: attachment; filename="download.pdf"');

// The PDF source is in original.pdf
$pdf_link = $plugin_full_url."pdfs/".$ipdf_get->iPdf_link;
readfile($pdf_link);

}else{ 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>invalid password, please try again</title>
<style type="text/css">
body{
	background-image: url(<?php bloginfo('template_directory'); ?>/images/l-doc-r.png);
	background-repeat: repeat;
	font-family:'Century Gothic',Geneva, Arial, Helvetica, sans-serif;
}
a { color:#ee2d31; text-decoration:none; }
a:hover { text-decoration:underline; }
</style>
</head>
<body>

<form name="downloadForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
 <table width="330" border="0" align="center" cellpadding="10" cellspacing="0" style="height:100px; margin-top:300px; background-color:#FFFFFF; border:#CC6600 2px solid;" >
  <tr>
     <td colspan="3">&nbsp;</td>
  </tr>      
  <tr>
     <td height="27" colspan="3"><div align="center" style="font-family:'Century Gothic'; font-size:24px; color:#333366;">invalid password, please try again</div></td>
  </tr>
  <tr>
        <td width="92">PASSWORD:</td>
        <td width="117"><input type="password" name="parolapdf" id="parolapdf" size="15" maxlength="20" /></td>
        <td width="111"><a href="#" onclick="document.downloadForm.submit();">DOWNLOAD</a></td>
  </tr>
</table>
</form>

</body>
</html>

<?php
 }
}
?>