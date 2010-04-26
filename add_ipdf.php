<?php
header("Cache-Control: no-cache");
include("../../../wp-config.php");

        global $wpdb;
        $iPdf_table = $wpdb->prefix . "oqeypdfs";

if(isset($_GET['id']) && isset($_GET['update']) && isset($_GET['ipdf_id']) ){

$ipdf_update = sprintf("UPDATE $iPdf_table
                           SET iPdf_title = '%s',
						       iPdf_title_for = '%s',
							   iPdf_order = '%s',
							   iPdf_password = '%s'
						 WHERE iPdf_id = '%d'
					   ", mysql_real_escape_string($_GET['title']), 
						  mysql_real_escape_string($_GET['title_for']),
						  mysql_real_escape_string($_GET['order']),
						  mysql_real_escape_string($_GET['password']),
						  mysql_real_escape_string($_GET['ipdf_id'])
						 );
						
$update_pdf = mysql_query($ipdf_update) or die (mysql_error());
echo  "updated";
}

if(isset($_GET['delete']) && isset($_GET['ipdf_id']) && isset($_GET['pdf_link']) ){

    $results=$wpdb->query("DELETE FROM $iPdf_table
								 WHERE iPdf_id = '".mysql_real_escape_string($_GET['ipdf_id'])."'
						  ");	
    
	$link_pdf = ABSPATH."/wp-content/plugins/oqey-pdfs/pdfs/".urlencode($_GET['pdf_link']);		
    unlink($link_pdf);
	echo "deleted";
}
?>