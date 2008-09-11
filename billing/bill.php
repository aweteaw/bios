<?php 
# Deskripsi:
# Menampilkan tagihan siap cetak

include_once('./bios-config.php');
?>
<html><head><title>Nota - <?php $client_name=@mysql_result(mysql_query("SELECT client_name FROM client,laporan WHERE client_id=laporan_client AND laporan_id=$lapid"),0); echo $client_name?></title>
<style type="text/css">
<!--
body {
        margin-left: 5px;
        margin-top: 5px;
        margin-right: 5px;
        margin-bottom: 5px;
}
-->
</style>
<link href="skin.css" rel="stylesheet" type="text/css">
</head><body>
<p>
<?php
# 5 October 2005
$operator_ip=substr(escapeshellcmd(mysql_escape_string(strip_tags(trim($_SERVER['REMOTE_ADDR'])))),0,15);

$list_domain_operator=explode(',',$setting_domain_operator);

$domain_valid=0;

foreach($list_domain_operator as $list_ip)
{
	if($list_ip == $operator_ip) $domain_valid=1;
}


if($setting_domain_operator && !$domain_valid)
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
	$data_bill=mysql_fetch_array(mysql_query("SELECT laporan_id, DATE_FORMAT(laporan_start,'%H:%i:%s - %d %b %Y') as time_start, DATE_FORMAT(laporan_end,'%H:%i:%s - %d %b %Y') as time_end, laporan_durasi, laporan_biaya, laporan_operator FROM laporan WHERE laporan_id=$lapid"));
	
    $hour=floor($data_bill[laporan_durasi]/3600);
    $minute=floor(($data_bill[laporan_durasi] - $hour * 3600)/60);
    $second=$data_bill[laporan_durasi] - $hour*3600 - $minute*60;

	if($hour < 10) $hour="0$hour";
	if($minute < 10) $minute="0$minute";
	if($second < 10) $second="0$second";
	
	$time_duration="$hour hr : $minute min : $second sec";
		
?>
</p>
<table width="250" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><div align="center"><img src="img/LOGO-WARNET.png" width="128" height="128"></div></td>
  </tr>
  <tr>
    <td align="center"><b><?php echo strtoupper($setting_cafe_name)?></b><br><?php echo $setting_cafe_address?></td>
  </tr>
  <tr>
    <td><hr>
    <h2><?php echo $client_name?></h2>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="60">Mulai</td>
        <td>: <?php echo $data_bill[time_start]?></td>
      </tr>
      <tr>
        <td width="60">Selesai</td>
        <td>: <?php echo $data_bill[time_end]?></td>
      </tr>
      <tr>
        <td width="60">Durasi</td>
        <td>: <?php echo $time_duration?></td>
      </tr>
      <tr>
        <td width="60">Biaya</td>
        <td>: Rp. <?php echo number_format($data_bill[laporan_biaya],0, ",",".")?>,-</td>
      </tr>
      <tr>
        <td>Tambahan</td>
        <td>:</td>
      </tr>
    </table> 
<?php
    $data_produk=mysql_query("SELECT * FROM produk, client_tambahan WHERE produk_id=ct_produk_id AND ct_laporan_id=$data_bill[laporan_id] ORDER BY ct_id ASC");
    while($isi_data_produk=mysql_fetch_array($data_produk))
    {
        print "+ $isi_data_produk[produk_nama] " . number_format($isi_data_produk[ct_harga],0, ",",".") . " x $isi_data_produk[ct_jumlah]= " . number_format($isi_data_produk[ct_harga] * $isi_data_produk[ct_jumlah],0, ",",".")  . "<br>\n";
			   
		$sum_total+=$isi_data_produk[ct_harga] * $isi_data_produk[ct_jumlah];
    }
	
	if(!$sum_total) echo "- ";
?>
 
<p>
<b>GRAND TOTAL: Rp.  <?php echo number_format($data_bill[laporan_biaya] + $sum_total,0, ",",".")?></b>
    <p align="center">Terima kasih..<br>(<?php echo mysql_result(mysql_query("SELECT operator_name_full FROM operator WHERE operator_name='$uid'"),0)?>)<br>Nota No. <?php echo $data_bill[laporan_id]?></p></td>
  </tr>
</table>
<?php
}
@mysql_close($connection);
?>
<script>
<!--
window.print()
//-->
</script>
</body></html>