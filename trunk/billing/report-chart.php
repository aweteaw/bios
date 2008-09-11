<?php include_once('./bios-config.php');?>
<?php 
$operator_ip=substr(escapeshellcmd(mysql_escape_string(strip_tags(trim($_SERVER['REMOTE_ADDR'])))),0,15);

if($setting_domain_operator && @!eregi($setting_domain_operator,$operator_ip))
{
  if($uid && $sid)
  {

           if(@$session_aktif=mysql_result(mysql_query("SELECT count(*) FROM session WHERE uid='$uid' AND sid='$sid'"),0))
           {
                          //mysql_query("DELETE FROM session WHERE uid='$uid'");
                          mysql_close($connection);
						  die("hello script kiddies.. ");
           }
  } else
  {	
  
  		mysql_close($connection);
		die("Forbidden");
  
  }
}
flush();

if($session_aktif=mysql_result(mysql_query("SELECT count(*) FROM session WHERE uid='$uid' AND sid='$sid'"),0))
{

$data_operator[operator_id] = mysql_result(mysql_query("SELECT operator_id FROM operator WHERE operator_name='$uid'"),0);

	if($data_operator[operator_id]==1)
	{
		$data_laporan=mysql_query("SELECT sum(laporan_biaya) as total, DATE_FORMAT(laporan_start,'%Y-%m') as bulan FROM laporan,client WHERE DATE_FORMAT(laporan_start,'%Y-%m') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m')<= '$date_end' AND client_id=laporan_client GROUP BY bulan ASC");
		
		$total_grafik=mysql_result(mysql_query("SELECT sum(laporan_biaya) FROM laporan,client WHERE DATE_FORMAT(laporan_start,'%Y-%m') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m')<= '$date_end' AND client_id=laporan_client"),0);
	} else
	{
		$data_laporan=mysql_query("SELECT sum(laporan_biaya) as total, DATE_FORMAT(laporan_start,'%Y-%m') as bulan FROM laporan,client WHERE laporan_operator='$uid' AND DATE_FORMAT(laporan_start,'%Y-%m') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m')<= '$date_end' AND client_id=laporan_client GROUP BY bulan ASC");
		
		$total_grafik=mysql_result(mysql_query("SELECT sum(laporan_biaya) FROM laporan,client WHERE laporan_operator='$uid' AND DATE_FORMAT(laporan_start,'%Y-%m') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m')<= '$date_end' AND client_id=laporan_client"),0);
	}

$array_tipe=NULL;
$array_isi=NULL;
$array_tipe[]='';
$array_isi[]='';

	while($isi_data_laporan=mysql_fetch_array($data_laporan))
	{

		$array_tipe[]=$isi_data_laporan[bulan];
		$array_isi[]=round($isi_data_laporan[total] / $total_grafik * 100);
	}

//include charts.php to access the SendChartData function
include_once "charts.php";

//switch the series colors
$chart ['series_switch'] = true;

//hide the legend
$chart['legend_rect'] = array ( 'x'=>-1000 ,'y'=>-1000 );

$chart['series_color'] = array ( "FFFFFF", "000000", "FFF000" ); 
 
$chart['axis_value'] = array ('alpha' =>  0); 
$chart['chart_value'] = array (
  'prefix'         =>  "", 
  'suffix'         =>  "%", 
  'decimals'       =>  2,
  'decimal_char'   =>  ".",  
  'separator'      =>  "",
  'position'       =>  "outside",
  'hide_zero'      =>  false, 
  'as_percentage'  =>  true, 
  'font'           =>  "Arial", 
  'bold'           =>  false, 
  'size'           =>  9, 
  'color'          =>  "000000", 
  'alpha'          =>  90
   );  
 
$chart['series_gap'] = array ( 
  'set_gap'  =>  30
                              ); 



$chart['chart_data'] = array ($array_tipe, $array_isi);

$chart['chart_type'] = "3d column"; 

#uncomment dibawah untuk menampilkan grafik dalam bentuk garis
#$chart['chart_type'] = "array ( "line","column", "column" );

SendChartData($chart);

}
@mysql_close($connection);

?>
</body></html>