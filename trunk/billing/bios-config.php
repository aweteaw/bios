<?php
######################### SILAHKAN EDIT MULAI DARI SINI #################

# Setting database MySQL
$mysql_user='root';
$mysql_pass='tes123';
$mysql_dbname='bios';

# Setting Lokasi dan nama file
$url_location='http://localhost/~budi/billing/';
$client_file_name='client.php';
$operator_file_name='operator.php';
$screenshot_file_name='screenshot.php';
$print_bill_file_name='bill.php';
$print_product_file_name='product.php';

# set menjadi 1, untuk mode debug billing operator dan client 
# pada mode debug ini, detail biaya per zona waktu akan ditampilkan lengkap
# default 0
$billing_debug_mode=0;

# set nilai dibawah menjadi 1 bila Anda ingin menampilkan durasi detik pada halaman
# billing operator dan client
# default 1
$show_second=1;


######################### STOP, HANYA EDIT SAMPAI SINI SAJA.. #################


if(eregi("bios-config.php",$_SERVER['REQUEST_URI'])) die('hello script kiddies..');

@$connection=mysql_connect("localhost",$mysql_user,$mysql_pass) or die ("401: Database billing tidak dapat diakses"); @mysql_select_db("$mysql_dbname"); $setting_billing=@mysql_fetch_array(mysql_query("SELECT * FROM setting LIMIT 1")); extract($setting_billing,EXTR_OVERWRITE);
foreach($_REQUEST as $var_request => $value)
{
   if($var_request <> 'tarif' && $var_request <> 'durasi'  &&  $var_request <> 'minimal' )
   {
   		$$var_request = escapeshellcmd(mysql_escape_string(strip_tags(trim($value))));
   }
}
$client_location=$url_location . $client_file_name;
$operator_location=$url_location . $operator_file_name;
$screenshot_location=$url_location . $screenshot_file_name;
$print_bill_location=$url_location . $print_bill_file_name;
$print_product_location=$url_location . $print_product_file_name;

switch($setting_error_reporting)
{
	case 1:
	error_reporting(0);
	break;
	case 2:
	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	break;
	case 3:
	error_reporting(E_ALL);
	break;
}
?>