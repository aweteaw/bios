<?php 
# Deskripsi:
# Menampilkan list produk yang akan ditambahkan..

include_once('./bios-config.php');
?>
<html><head><title>Belanja Produk - <?php $client_name=@mysql_result(mysql_query("SELECT client_name FROM client,laporan WHERE client_id=laporan_client AND laporan_id=$lapid"),0); echo $client_name?></title>
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
		die("hello script kiddies.. ");
  
  }
}
flush();

if($session_aktif=mysql_result(mysql_query("SELECT count(*) FROM session WHERE uid='$uid' AND sid='$sid'"),0))
{
	$data_operator=mysql_fetch_array(mysql_query("SELECT * FROM operator WHERE operator_name='$uid' LIMIT 1"));
			
?>
</p>
<h2><?php  echo $client_name?> (Nota No. <?php echo $lapid?>)</h2>
<?php 
if($submit && $produk && $jumlah && $lapid)
{
	
	#mencegah operator curang, mencegah mengedit tambahan produk setelah komputer direset..
	if(@mysql_result(mysql_query("SELECT client_status FROM client,laporan WHERE laporan_client=client_id AND laporan_id=$lapid"),0) != 2)
	{
		if(($data_operator[operator_id]<>1 && $data_operator[operator_edit_report]<>1))
		{
			@mysql_close($connection);
			die("Forbidden..1");
		}
		
		#batasi hak akses operator hanya pada laporan buatannya saja.
		if(($data_operator[operator_id]<>1 and $data_operator[operator_edit_report]==1))
		{
			if(mysql_result(mysql_query("SELECT laporan_operator FROM laporan WHERE laporan_id=$lapid"),0)<>$uid)
			{
				@mysql_close($connection);
				die("Forbidden..2");
			}
		}
	}

	if($jumlah > @mysql_result(mysql_query("SELECT produk_stok FROM produk WHERE produk_id=$produk"),0))
	{
		echo "<script language='Javascript'>alert ('Stok produk tidak mencukupi.. Pesanan Dibatalkan')</script>";
	} else
	{
		
		$harga=mysql_result(mysql_query("SELECT produk_harga FROM produk WHERE produk_id=$produk"),0);
		$result=mysql_query("INSERT INTO client_tambahan (ct_laporan_id,ct_produk_id,ct_jumlah,ct_harga) VALUES ($lapid,$produk,$jumlah,$harga)");
		
		$result=mysql_query("UPDATE produk SET produk_stok=produk_stok-$jumlah WHERE produk_id = $produk");
		echo mysql_error();
	
		if($result)
		{
			echo "<h4><img src=\"./img/icon_ok.gif\">SUKSES ditambahkan!</h4>";
		} else
		{
			echo "<h4><img src=\"./img/icon_error.gif\">Error! ada kegagalan tampaknya .. Silahkan ulangi!</h4>";
		}
	}

} elseif($del)
{

	#mencegah operator curang, mencegah mengedit tambahan produk setelah komputer direset..
	if(@mysql_result(mysql_query("SELECT client_status FROM client,laporan WHERE laporan_client=client_id AND laporan_id=$lapid"),0) != 2)
	{
		if(($data_operator[operator_id]<>1 && $data_operator[operator_edit_report]<>1))
		{
			@mysql_close($connection);
			die("Forbidden.. 3");
		}
		
		#batasi hak akses operator hanya pada laporan buatannya saja.
		if(($data_operator[operator_id]<>1 and $data_operator[operator_edit_report]==1))
		{
			if(mysql_result(mysql_query("SELECT laporan_operator FROM laporan WHERE laporan_id=$lapid"),0)<>$uid)
			{
				@mysql_close($connection);
				die("Forbidden.. 4");
			}
		}
	}
	
	
    	$retur_stok=mysql_result(mysql_query("SELECT ct_jumlah FROM client_tambahan WHERE ct_id=$del"),0);
	
	$result=mysql_query("DELETE FROM client_tambahan WHERE ct_id = $del LIMIT 1");
	
	$result=mysql_query("UPDATE produk SET produk_stok=produk_stok+$retur_stok");
    
    echo mysql_error();
    
    if($result)
    {
        echo "<h4><img src=\"./img/icon_ok.gif\">BERHASIL DIHAPUS!</h4>";
    } else
    {
        echo "<h4><img src=\"./img/icon_error.gif\">Error! ada kegagalan tampaknya .. Silahkan ulangi!</h4>";
    }


}
?>
<?php 
#mencegah operator curang, mencegah mengedit tambahan produk setelah komputer direset..
if(@mysql_result(mysql_query("SELECT client_status FROM client,laporan WHERE laporan_client=client_id AND laporan_id=$lapid"),0) == 2 or $data_operator[operator_id]==1 or $data_operator[operator_edit_report]==1)
{

?>
<form action="<?php  echo "$print_product_location?uid=$_REQUEST[uid]&sid=$_REQUEST[sid]"?>" method="post" name="" id="">
<table width="100%"  border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td>Tambah: 
      <select name="produk">
<?php 
    $data_produk=mysql_query("SELECT produk_id, produk_nama, produk_stok, produk_harga FROM produk WHERE produk_show=1 AND produk_stok >= 1 ORDER BY produk_nama ASC");
    while($isi_data_produk=mysql_fetch_array($data_produk))
    {

        print "<option value=\"$isi_data_produk[produk_id]\">$isi_data_produk[produk_nama] - Rp. $isi_data_produk[produk_harga] [ Stok: $isi_data_produk[produk_stok]x ]</option>";
    }
?>
      </select> 
      | Jumlah: 
      <input name="jumlah" type="text" id="jumlah" value="1" size="10" maxlength="7">      <input name="submit" type="submit" id="submit" value="Tambah!">
      <input name="lapid" type="hidden" value="<?php  echo $lapid?>"></td>
  </tr>
</table>
</form>
<?php 
}
?>
<hr>
        Total Tambahan:
        <?php $total=mysql_result(mysql_query("SELECT count(*) FROM client_tambahan where ct_laporan_id=$lapid"),0);echo $total?>
        <p>
          
<table width=100% cellspacing=1 cellpadding=3 bgcolor='#999999' border=0>
<tr bgcolor='#E9E9E9'>
<td width="100" class=small>:::</td>
<td class=small>Nama Produk</td>
<td class=small>Harga satuan</td>
<td class=small>Jumlah</td>
<td class=small>Total</td>
</tr>
<?php 
    $data_produk=mysql_query("SELECT * FROM produk, client_tambahan WHERE produk_id=ct_produk_id AND ct_laporan_id=$lapid ORDER BY ct_id ASC");
    while($isi_data_produk=mysql_fetch_array($data_produk))
    {
        print "<tr bgcolor='#FFFFFF'>
                   <td><a href=\"$print_product_location" . "?uid=$uid&sid=$sid&lapid=$lapid&del=$isi_data_produk[ct_id]\" onClick=\"return confirm('ANDA YAKIN HAPUS TAMBAHAN ORDER INI? " . ereg_replace("\"","",$isi_data_product[produk_nama]) . " ..')\"><img src='./img/icon_delete.png' border=0 title='Delete'></a></td>
                   <td>$isi_data_produk[produk_nama]</td>
		   <td align='right'>" . number_format($isi_data_produk[ct_harga],0, ",",".") . "</td>
                   <td align='right'>$isi_data_produk[ct_jumlah]</td>
		   <td align='right'>" . number_format($isi_data_produk[ct_harga] * $isi_data_produk[ct_jumlah],0, ",",".")  . "</td>
               </tr>\n";
			   
			 $sum_total+=$isi_data_produk[ct_harga] * $isi_data_produk[ct_jumlah];
    }
	
	echo "<tr bgcolor='#FFFF00'><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>TOTAL: Rp.</td><td align='right'><b>" . number_format($sum_total,0, ",",".") . "</b></td></tr>";
?>
</table>



		 
</table>


<?php 
}
@mysql_close($connection);
?>

</body></html>