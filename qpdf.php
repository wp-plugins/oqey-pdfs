<?php
// oQey-Pdfs
// Copyright (c) 2010 qusites.com
// This is an add-on for WordPress
// http://wordpress.org/
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// *****************************************************************

/*
Plugin Name: oQey-Pdfs
Version: 0.1
Description: oQey-Pdfs plugin.
Author: qusites.com | Dorin D.
Author URI: http://qusites.com/
*/
// Create tables to store pdfs details
global $iPdf_db_version;
$iPdf_db_version = "0.1";

function iPdf_db_install() {
   global $wpdb;
   global $iPdf_db_version;
   $iPdf_table_name = $wpdb->prefix . "oqeypdfs";
   
   $pdfs_dir_up = ABSPATH."/wp-content/plugins/oqey-pdfs/pdfs/";
   wp_mkdir_p ($pdfs_dir_up);
   
   if($wpdb->get_var("show tables like '$iPdf_table_name'") != $iPdf_table_name ) {      
$sql = "CREATE TABLE " . $iPdf_table_name . " (
		iPdf_id int NOT NULL AUTO_INCREMENT,
		iPdf_link varchar(255) NOT NULL DEFAULT '',
		iPdf_title varchar(255) NOT NULL DEFAULT '',
		iPdf_title_for varchar(255) NOT NULL DEFAULT '',
		iPdf_order int(11) NOT NULL DEFAULT '0',
		iPdf_password varchar(255) NOT NULL DEFAULT '',
		PRIMARY KEY  (iPdf_id)
	);";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);	
	add_option("iPdf_db_version", $iPdf_db_version);
   }
   
   // Upgrade table code
   $installed_iPdf_ver = get_option( "iPdf_db_version" );
   if( $installed_iPdf_ver != $iPdf_db_version ) {
$sql = "CREATE TABLE " . $iPdf_table_name . " (
		iPdf_id int NOT NULL AUTO_INCREMENT,
		iPdf_link varchar(255) NOT NULL DEFAULT '',
		iPdf_title varchar(255) NOT NULL DEFAULT '',
		iPdf_title_for varchar(255) NOT NULL DEFAULT '',
		iPdf_order int(11) NOT NULL DEFAULT '0',
		iPdf_password varchar(255) NOT NULL DEFAULT '',
		PRIMARY KEY  (iPdf_id)
	);";
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);
      update_option( "iPdf_db_version", $iPdf_db_version );
     }
}

if (function_exists('register_activation_hook')) {
	register_activation_hook( __FILE__, 'iPdf_db_install' );
}
//..............................//
function addiPdfToSubmenu() { // add in settings menu
    add_submenu_page('options-general.php', 'oQey-Video plugin page', 'oQey-Video plugin', 10, __FILE__, 'initoQeyiPdfPlugin');
}
function quiPdfPluginUrl() {
	$quiPdf_url = get_option('siteurl') . '/wp-content/plugins/oqey-pdfs';   
	return $quiPdf_url;
}
// Hook for adding admin menus
add_action('admin_menu', 'qu_ipdf_add_pages');
// action function for above hook
function qu_ipdf_add_pages() {
    // Add a new top-level menu:
    add_menu_page('oQey-Pdfs plugin', 'oQey-Pdfs', 8, __FILE__, 'qu_ipdf_toplevel_page');
    // Add a submenu to the custom top-level menu:
    add_submenu_page(__FILE__, 'Manage', 'Manage', 8, 'manageipdf.php', 'qu_ipdf_Manage_page');
}

// qu_toplevel_page() displays the page content for the custom Test Toplevel menu
function qu_ipdf_toplevel_page() {
	echo '<div class="wrap">
        	<h2>oQey-Pdfs plugin</h2>
          </div>
		  <div class="wrap">oQey-Pdfs is a Wordpress Plugin that allows easy to manage Pdf`s files.<br/>
		  Insert [iPdf: pdfdocs] in your post or page to get pdfs.
 		  </div>
		  ';
}
// qu_ipdf_sublevel_page() displays the page content for the first submenu
// of the custom Test Toplevel menu
function qu_ipdf_Manage_page() {
	include("manageipdf.php");
}
/////...........front......................
add_filter('the_content', 'iPdf_embed');
add_action('wp_head', 'iPdfhead');

// Import external javascript in Wordpress header
function iPdfhead()
{
	$pathcssiPdf = get_option('siteurl') . '/wp-content/plugins/oqey-pdfs/pdfd.css';
	echo '<link href="' . $pathcssiPdf . '" type="text/css" rel="stylesheet" media="all" />';
	echo "\n";
}

function iPdf_embed($content){//insert pdf in content
$content = preg_replace_callback( "/(<p>)?\[iPdf:([^]]+)](<\/p>)?/i", "add_iPdf", $content );
return $content;
}
function add_iPdf(){// insert pdf in content
global $wpdb;
$site_url = get_option('siteurl');
$iPdf_table = $wpdb->prefix . "oqeypdfs";
//.....................................................

//get all pdfs from db
$iPdf_pdf = $wpdb->get_results(" SELECT *
                                   FROM $iPdf_table
							   ORDER BY iPdf_order
								");
$dd = 1;	

$output = '<div class="output">';
$output .='<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0" class="pdfd">';
					  
foreach ($iPdf_pdf as $iPdf_pdf){
//$output .= "<li>".$site_url."/".$iPdf_pdf->iPdf_link."</li>";

if($dd%2)
{
$output .= '<tr><td>';
$output .= '
<form name="downloadForm'.$dd.'" action="'.$site_url.'/pdfs/index.php" method="post" target="_blank">
 <table width="330" border="0" align="center" cellpadding="0" cellspacing="0" class="pdfd">
  <tr>
        <td colspan="3" align="left">
        <span align="left" style="text-transform:uppercase;">'.$iPdf_pdf->iPdf_title.'</span><br/>        
		<span align="left" style="text-transform:lowercase; font-size:10px;">'.$iPdf_pdf->iPdf_title_for.'</span>        
		</td>
        </tr>      
      <tr>
        <td width="92">PASSWORD:</td>
        <td width="117">
        <input type="password" name="parolapdf" id="parolapdf'.$dd.'" size="15" maxlength="20" />
        </td>
        <td width="111"><a href="#" onclick="document.downloadForm'.$dd.'.submit();">DOWNLOAD</a></td>
      </tr>
    </table>


	</form>';		
			
$output .= '</td>';

}else{

$output .= '<td>';
$output .= '
<form name="downloadForm'.$dd.'" action="'.$site_url.'/pdfs/index.php" method="post" target="_blank">
 <table width="330" border="0" align="center" cellpadding="0" cellspacing="0" class="pdfd" id="pdfd" >
  <tr>
        <td colspan="3" align="left">
        <span align="left" style="text-transform:uppercase;">'.$iPdf_pdf->iPdf_title.'</span><br/>        
		<span align="left" style="text-transform:lowercase; font-size:10px;">'.$iPdf_pdf->iPdf_title_for.'</span>        
		</td>
        </tr>      
      <tr>
        <td width="92">PASSWORD:</td>
        <td width="117">
        <input type="password" name="parolapdf" id="parolapdf'.$dd.'" size="15" maxlength="20" />
        </td>
        <td width="111"><a href="#" onclick="document.downloadForm'.$dd.'.submit();">DOWNLOAD</a></td>
      </tr>
    </table>

	</form>';		
			
$output .= '</td></tr>';

}

$dd++;
}
$output .='</table></div>';

return $output;
}
?>