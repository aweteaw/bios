<?php 
# Deksripsi: 
# Menampilkan halaman report pendapatan warnet

if(eregi("report.php",$_SERVER['REQUEST_URI'])) die('hello script kiddies..');
?>
<script language="JavaScript" src="collapse_expand_single_item.js"></script>
<script src="script.js.php?uid=<?php  echo $uid?>"></script>
<h1><img src="./img/icon_report.png" align="left">Laporan Pendapatan (<?php if($roperator){ echo $roperator;} else{ echo $uid;}?>)</h1>
<p>Gunakan halaman ini untuk melihat laporan pendapatan </p>
<p>
<hr>

<img src="./img/u.gif" name="imgfirst" width="9" height="9" border="0" >
<a href="javascript:void(0);" onClick="shoh('first');" >Pilihan ..</a>

<div style="display: none;" id="first" >



<form action="<?php  echo "$operator_file_name" . "?uid=$uid&p=report&sid=$sid"?>#report" method="post">
<?php 
function getmonth($m=0) {
return (($m==0 ) ? date("F",mktime(gmdate("H")+$setting_timezone,gmdate("i"),gmdate("s"),gmdate("m"),gmdate("d"),gmdate("Y"))) : date("F", mktime(0,0,0,$m)));
} 

$dates_start=date("j",mktime(gmdate("H")+$setting_timezone,gmdate("i"),gmdate("s"),gmdate("m"),gmdate("d"),gmdate("Y")));

$month_start=date("n",mktime(gmdate("H")+$setting_timezone,gmdate("i"),gmdate("s"),gmdate("m"),gmdate("d"),gmdate("Y")));

$year_start=date("Y",mktime(gmdate("H")+$setting_timezone,gmdate("i"),gmdate("s"),gmdate("m"),gmdate("d"),gmdate("Y")));

$dump_start_date='';
$dump_start_month='';
$dump_start_year='';
$dump_end_date='';
$dump_end_month='';
$dump_end_year='';


for($n=1; $n<=31; $n++)
{
	if($n == $laporan_start_day)
	{
		$tambahan=' selected';
	} elseif($n == $dates_start && !$laporan_start_day)
	{
		$tambahan=' selected';
	} else
	{
		$tambahan='';
	}

	if($n < 10)
	{
		$m = "0$n";
	} else
	{
		$m = $n;
	}
	$dump_start_date.= "<option value='$m'$tambahan>$m</option>\n";
} 

for($n=1; $n<=12; $n++)
{
	if($n == $laporan_start_month)
	{
		$tambahan=' selected';
	} elseif($n == $month_start && !$laporan_start_month)
	{
		$tambahan=' selected';
	} else
	{
		$tambahan='';
	}

	if($n < 10)
	{
		$m = "0$n";
	} else
	{
		$m = $n;
	}

	$dump_start_month.= "<option value='$m'$tambahan>" . getmonth($n) . "</option>\n";
} 

for($n=2008; $n<=2010; $n++)
{
	if($n == $laporan_start_year)
	{
		$tambahan=' selected';
	} elseif($n == $year_start && !$laporan_start_year)
	{
		$tambahan=' selected';
	} else
	{
		$tambahan='';
	}
	$dump_start_year.= "<option value='$n'$tambahan>$n</option>\n";
} 

for($n=1; $n<=31; $n++)
{
	if($n == $laporan_end_day)
	{
		$tambahan=' selected';
	} elseif($n == $dates_start && !$laporan_end_day)
	{
		$tambahan=' selected';
	} else
	{
		$tambahan='';
	}

	if($n < 10)
	{
		$m = "0$n";
	} else
	{
		$m = $n;
	}
	$dump_end_date.= "<option value='$m'$tambahan>$m</option>\n";
} 

for($n=1; $n<=12; $n++)
{
	if($n == $laporan_end_month)
	{
		$tambahan=' selected';
	} elseif($n == $month_start && !$laporan_end_month)
	{
		$tambahan=' selected';
	} else
	{
		$tambahan='';
	}

	if($n < 10)
	{
		$m = "0$n";
	} else
	{
		$m = $n;
	}

	$dump_end_month.= "<option value='$m'$tambahan>" . getmonth($n) . "</option>\n";
} 

for($n=2008; $n<=2010; $n++)
{
	if($n == $laporan_end_year)
	{
		$tambahan=' selected';
	} elseif($n == $year_start && !$laporan_end_year)
	{
		$tambahan=' selected';
	} else
	{
		$tambahan='';
	}
	$dump_end_year.= "<option value='$n'$tambahan>$n</option>\n";
} 


?>
<?php 
if($data_operator[operator_id]==1)
{
?>
Operator: 
<select name="roperator" id="roperator">
  <option value='' <?php if(!$roperator) echo 'selected'?>>Semua</option>
<?php 
    $data_operators=mysql_query("SELECT operator_id, operator_name, operator_name_full FROM operator ORDER BY operator_name ASC");
    while($isi_data_operator=mysql_fetch_array($data_operators))
    {
	    if($roperator == "$isi_data_operator[operator_name]")
	    {
	   		$rstatus='selected';
		} else
		{
			$rstatus='';
		}
		echo "<option value=\"$isi_data_operator[operator_name]\" $rstatus>$isi_data_operator[operator_name] - $isi_data_operator[operator_name_full]</option>\n";
	}
?>
</select>
<?php 
}
?>
<p>Dari tanggal 
<select name="laporan_start_day">
<?php  echo $dump_start_date?>
</select>
    
<select name="laporan_start_month">
<?php  echo $dump_start_month?>
</select>

<select name="laporan_start_year">
<?php  echo $dump_start_year?>
</select> 
    - sampai dengan - 
<select name="laporan_end_day">
<?php  echo $dump_end_date?>
</select>
<select name="laporan_end_month">
<?php  echo $dump_end_month?>
</select>
<select name="laporan_end_year">
<?php  echo $dump_end_year?>
</select>
</p>
  <table width="50%"  border="0" cellspacing="0" cellpadding="5">
    <tr valign="top">
      <td><p>
        <input name="laporan_tipe" type="radio" value="detail" <?php if($laporan_tipe=='detail' || !$laporan_tipe) echo 'checked'?>>
Lihat Transaksi Detail SELURUHNYA</p>

<p>
        <input name="laporan_tipe" type="radio" value="detail_lain" <?php if($laporan_tipe=='detail_lain') echo 'checked'?>>
Lihat Transaksi Detail NON INTERNET SAJA</p>


        <?php if(!$pages_view) $pages_view = 25?>
Cetak dengan jumlah maksimal :<br>
<input name="pages_view" type="text" size="5" value="<?php  echo $pages_view?>">
transaksi / halaman
<p></p>
atau
<p>
  <input name="print_one_page" type="checkbox" value="1" <?php if($print_one_page) echo 'checked'?>>
  Cetak keseluruhan dalam satu halaman 
<p>Urutan Nota: 
  <select name="urutan" id="urutan">
    <option value="ASC" <?php if($urutan =='ASC') echo 'selected'?>>Ascending</option>
    <option value="DESC" <?php if($urutan =='DESC' or !$urutan) echo 'selected'?>>Descending</option>
  </select>
</td>
      <td><input name="laporan_tipe" type="radio" value="bulanan" <?php if($laporan_tipe=='bulanan') echo 'checked'?>>
Laporan Bulanan </td>
    </tr>
  </table>
  <p>
    <input name="submit_laporan" type="submit" id="submit_laporan" value="Tampilkan ..">
  </p>
</form>


</div>


<a name="report"></a>
<hr>
<p>  
<?php 
if($hapus && ($data_operator[operator_id]==1 or $data_operator[operator_edit_report]==1))
{
    if($data_operator[operator_id]==1)
    {
		$result=mysql_query("DELETE FROM laporan WHERE laporan_id=$hapus LIMIT 1");
	} else
	{
		$result=mysql_query("DELETE FROM laporan WHERE laporan_id=$hapus AND laporan_operator='$uid' LIMIT 1");
	}
    
    echo mysql_error();
    
    if($result)
    {
        echo "<h4><img src=\"./img/icon_ok.gif\" align=left>DATA BERHASIL DIHAPUS!</h4>";
    } else
    {
        echo "<h4><img src=\"./img/icon_error.gif\" align=left>Error! ada kegagalan tampaknya .. Silahkan ulangi!</h4>";
    }

    echo "<p><a href=\"$operator_file_name" . "?uid=$uid&p=report&sid=$sid\">Kembali ..</a>";

} elseif($edit && ($data_operator[operator_id]==1 or $data_operator[operator_edit_report]==1))
{
    echo "<p><a href=\"$operator_file_name" . "?uid=$uid&p=report&sid=$sid\">Kembali ..</a>";
    
    if(!$confirm)
    {
		if($data_operator[operator_id]==1)
		{
			$data_laporan=mysql_query("SELECT * FROM laporan,client WHERE laporan_client=client_id AND laporan_id=$edit");
		} else
		{
		    $data_laporan=mysql_query("SELECT * FROM laporan,client WHERE laporan_client=client_id AND laporan_id=$edit AND laporan_operator='$uid'");
		}
        $isi_data_laporan=mysql_fetch_array($data_laporan);
    ?>
</p>
<form action="<?php  echo "$operator_file_name" . "?uid=$uid&p=report&sid=$sid"?>" method=post>
		  <p>
		<input type="hidden" name="confirm" value="baliwae">
		<input type="hidden" name="edit" value="<?php  echo $edit?>">
        Client :<br> 
        <input type=text name="laporan_client_name" maxlength=255 size=30 value="<?php  echo $isi_data_laporan[client_name]?>" disabled>
		</p>
		  <p>Start  :<br> 
		    <input name="laporan_start" type=text maxlength=255 size=30 value="<?php  echo $isi_data_laporan[laporan_start]?>">
</p>
		  <p>End :<br>
            <input name="laporan_end" type=text maxlength=255 size=30 value="<?php  echo $isi_data_laporan[laporan_end]?>">
</p>
		  <p>Durasi :<br>
            <input name="laporan_durasi" type=text maxlength=255 size=25 value="<?php  echo $isi_data_laporan[laporan_durasi]?>"> 
            detik
</p>
		  <p>Biaya (Rp.)  :<br>
            <input name="laporan_biaya" type=text maxlength=255 size=30 value="<?php  echo $isi_data_laporan[laporan_biaya]?>">
</p>
		  <p>Catatan :<br>
<textarea name="laporan_catatan" cols="50" rows="4">
<?php  echo $isi_data_laporan[laporan_catatan]?>
</textarea>
</p>
		  <p>		    
		    <input name="submit_update" type=submit value="Update!">
	      </p>
</form>

    <?php 
    } else
    {
		if($data_operator[operator_id]==1)
		{
			$result=mysql_query("UPDATE laporan SET laporan_start='$laporan_start', laporan_end='$laporan_end', laporan_durasi=$laporan_durasi, laporan_biaya=$laporan_biaya, laporan_catatan='$laporan_catatan' WHERE laporan_id=$edit");
		} else
		{
			$result=mysql_query("UPDATE laporan SET laporan_start='$laporan_start', laporan_end='$laporan_end', laporan_durasi=$laporan_durasi, laporan_biaya=$laporan_biaya, laporan_catatan='$laporan_catatan' WHERE laporan_id=$edit AND laporan_operator='$uid'");
		}
		
        echo mysql_error();
        
        if($result)
        {
            echo "<h4><img src=\"./img/icon_ok.gif\" align=left>Laporan SUKSES diupdate!</h4>";
        } else
        {
            echo "<h4><img src=\"./img/icon_error.gif\" align=left>Error! ada kegagalan tampaknya .. Silahkan ulangi!</h4>";
        }
    }
} else{
?>

    <p>
<?php 
if($submit_laporan)
{
		if($laporan_tipe=='detail' ||$laporan_tipe=='detail_lain')
		{
		$date_start="$laporan_start_year-$laporan_start_month-$laporan_start_day";
		$date_end="$laporan_end_year-$laporan_end_month-$laporan_end_day";
		} elseif($laporan_tipe=='bulanan')
		{
		$date_start="$laporan_start_year-$laporan_start_month";
		$date_end="$laporan_end_year-$laporan_end_month";
		}
	
} elseif(!$date_start)
{
		$date_start=date("Y-m-d",mktime(gmdate("H")+$setting_timezone,gmdate("i"),gmdate("s"),gmdate("m"),gmdate("d"),gmdate("Y")));
		$date_end=date("Y-m-d",mktime(gmdate("H")+$setting_timezone,gmdate("i"),gmdate("s"),gmdate("m"),gmdate("d"),gmdate("Y")));
		

}

#		$tambahan=@mysql_result(mysql_query("SELECT sum(ct_jumlah*ct_harga) FROM produk, client_tambahan WHERE produk_id=ct_produk_id AND ct_laporan_id=$isi_data_laporan[laporan_id]"),0);

if($laporan_tipe=='bulanan')
{

	if($data_operator[operator_id]==1)
	{
		if($roperator)
		{
			$data_laporan=mysql_query("SELECT sum(laporan_biaya) as total, DATE_FORMAT(laporan_start,'%Y-%m') as bulan FROM laporan,client WHERE laporan_operator='$roperator' AND DATE_FORMAT(laporan_end,'%Y-%m') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m')<= '$date_end' AND client_id=laporan_client GROUP BY bulan ASC");

			$data_laporan_lain=mysql_query("SELECT sum(ct_jumlah*ct_harga) as total_lain,  DATE_FORMAT(laporan_start,'%Y-%m') as bulan FROM laporan,client_tambahan WHERE laporan_operator='$roperator' AND DATE_FORMAT(laporan_end,'%Y-%m') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m')<= '$date_end' AND ct_laporan_id=laporan_id GROUP BY bulan ASC");

			$total_grafik=mysql_result(mysql_query("SELECT sum(laporan_biaya) FROM laporan,client WHERE laporan_operator='$roperator' AND  DATE_FORMAT(laporan_end,'%Y-%m') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m')<= '$date_end' AND client_id=laporan_client"),0);
			$total_grafik_lain= mysql_result(mysql_query("SELECT sum(ct_jumlah*ct_harga) FROM laporan,client_tambahan WHERE laporan_operator='$roperator' AND  DATE_FORMAT(laporan_end,'%Y-%m') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m')<= '$date_end' AND ct_laporan_id=laporan_id"),0);
		} else
		{
			$data_laporan=mysql_query("SELECT sum(laporan_biaya) as total, DATE_FORMAT(laporan_start,'%Y-%m') as bulan FROM laporan,client WHERE DATE_FORMAT(laporan_end,'%Y-%m') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m')<= '$date_end' AND client_id=laporan_client GROUP BY bulan ASC");

			$data_laporan_lain=mysql_query("SELECT sum(ct_jumlah*ct_harga) as total_lain,  DATE_FORMAT(laporan_start,'%Y-%m') as bulan FROM laporan,client_tambahan WHERE DATE_FORMAT(laporan_end,'%Y-%m') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m')<= '$date_end' AND ct_laporan_id=laporan_id GROUP BY bulan ASC");

			$total_grafik=mysql_result(mysql_query("SELECT sum(laporan_biaya) FROM laporan,client WHERE DATE_FORMAT(laporan_end,'%Y-%m') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m')<= '$date_end' AND client_id=laporan_client"),0);
			$total_grafik_lain= mysql_result(mysql_query("SELECT sum(ct_jumlah*ct_harga) FROM laporan,client_tambahan WHERE DATE_FORMAT(laporan_end,'%Y-%m') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m')<= '$date_end' AND ct_laporan_id=laporan_id"),0);
		}
	} else
	{
			$data_laporan=mysql_query("SELECT sum(laporan_biaya) as total, DATE_FORMAT(laporan_start,'%Y-%m') as bulan FROM laporan,client WHERE laporan_operator='$uid' AND DATE_FORMAT(laporan_end,'%Y-%m') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m')<= '$date_end' AND client_id=laporan_client GROUP BY bulan ASC");

			$data_laporan_lain=mysql_query("SELECT sum(ct_jumlah*ct_harga) as total_lain,  DATE_FORMAT(laporan_start,'%Y-%m') as bulan FROM laporan,client_tambahan WHERE laporan_operator='$uid' AND DATE_FORMAT(laporan_end,'%Y-%m') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m')<= '$date_end' AND ct_laporan_id=laporan_id GROUP BY bulan ASC");

			$total_grafik=mysql_result(mysql_query("SELECT sum(laporan_biaya) FROM laporan,client WHERE laporan_operator='$uid' AND  DATE_FORMAT(laporan_end,'%Y-%m') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m')<= '$date_end' AND client_id=laporan_client"),0);
			$total_grafik_lain= mysql_result(mysql_query("SELECT sum(ct_jumlah*ct_harga) FROM laporan,client_tambahan WHERE laporan_operator='$uid' AND  DATE_FORMAT(laporan_end,'%Y-%m') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m')<= '$date_end' AND ct_laporan_id=laporan_id"),0);
	}

	$total_bulanan=0;
	$dump_laporan='';
	$grand_total=0;

	while($isi_data_laporan=mysql_fetch_array($data_laporan))
	{
		$dump_laporan .= "- Bulan $isi_data_laporan[bulan] = Rp." . number_format($isi_data_laporan[total],2,',','.') . "<br>\n";
		$array_tipe[]=$isi_data_laporan[bulan];
		$array_isi[]=round($isi_data_laporan[total]/ $total_grafik * 100);
		
		$total_bulanan+=$isi_data_laporan[total];
	}
	echo "<h3>Pendapatan Internet Periode: $date_start - $date_end</h3>$dump_laporan<p>Total = Rp." . number_format($total_bulanan,2,',','.') . "<br>\n";

	$grand_total+=$total_bulanan;

	//include charts.php to access the SendChartData function
	include_once "charts.php";
	echo InsertChart ( "charts.swf", "charts_library", "report-chart.php?date_start=$date_start&date_end=$date_end&uid=$uid&sid=$sid", 600, 300, "FFFFFF", false);

	echo "<hr>";

	$total_bulanan=0;
	$dump_laporan='';

	while($isi_data_laporan=mysql_fetch_array($data_laporan_lain))
	{
		$dump_laporan .= "- Bulan $isi_data_laporan[bulan] = Rp." . number_format($isi_data_laporan[total_lain],2,',','.') . "<br>\n";		
		$total_bulanan+=$isi_data_laporan[total_lain];
	}
	echo "<h3>Pendapatan NON Internet Periode: $date_start - $date_end</h3>$dump_laporan<p>Total = Rp." . number_format($total_bulanan,2,',','.') . "<br>\n";
	$grand_total+=$total_bulanan;

	echo "<hr><h2>GRAND TOTAL= Rp." . number_format($grand_total,2,',','.') . "</h2>";

} elseif($laporan_tipe=='detail_lain')
{


    if(!isset($fr)) $fr=0;
	
    if(!$urutan) $urutan = 'DESC';	

    if($print_one_page) $pages_view = $total;


	if($data_operator[operator_id]==1)
	{
		if($roperator)
		{
			$total=mysql_result(mysql_query("SELECT count(*) FROM laporan,client_tambahan,produk WHERE laporan_operator='$roperator' AND DATE_FORMAT(laporan_end,'%Y-%m-%d') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m-%d')<= '$date_end' AND ct_laporan_id=laporan_id AND ct_produk_id=produk_id"),0);

			if($print_one_page) $pages_view = $total;

			$data_laporan_lain=mysql_query("SELECT ct_id,laporan_id,laporan_operator, produk_nama, laporan_client, ct_jumlah, ct_harga, (ct_jumlah*ct_harga) as total_lain,  DATE_FORMAT(laporan_end,'%Y-%m-%d') as tanggal FROM laporan,client_tambahan,produk WHERE laporan_operator='$roperator' AND DATE_FORMAT(laporan_end,'%Y-%m-%d') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m-%d')<= '$date_end' AND ct_laporan_id=laporan_id AND ct_produk_id=produk_id ORDER BY laporan_id $urutan LIMIT $fr,$pages_view");


		} else
		{
			$total=mysql_result(mysql_query("SELECT count(*) FROM laporan,client_tambahan,produk WHERE DATE_FORMAT(laporan_end,'%Y-%m-%d') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m-%d')<= '$date_end' AND ct_laporan_id=laporan_id AND ct_produk_id=produk_id"),0);

			if($print_one_page) $pages_view = $total;

			$data_laporan_lain=mysql_query("SELECT ct_id,laporan_id,laporan_operator, produk_nama, laporan_client, ct_jumlah, ct_harga, (ct_jumlah*ct_harga) as total_lain,  DATE_FORMAT(laporan_end,'%Y-%m-%d') as tanggal FROM laporan,client_tambahan,produk WHERE DATE_FORMAT(laporan_end,'%Y-%m-%d') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m-%d')<= '$date_end' AND ct_laporan_id=laporan_id AND ct_produk_id=produk_id ORDER BY laporan_id $urutan LIMIT $fr,$pages_view");

		}
	} else
	{
			$total=mysql_result(mysql_query("SELECT count(*) FROM laporan,client_tambahan,produk WHERE laporan_operator='$uid' AND DATE_FORMAT(laporan_end,'%Y-%m-%d') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m-%d')<= '$date_end' AND ct_laporan_id=laporan_id AND ct_produk_id=produk_id"),0);

			if($print_one_page) $pages_view = $total;

			$data_laporan_lain=mysql_query("SELECT ct_id,laporan_id,laporan_operator, produk_nama, laporan_client, ct_jumlah, ct_harga, (ct_jumlah*ct_harga) as total_lain,  DATE_FORMAT(laporan_end,'%Y-%m-%d') as tanggal FROM laporan,client_tambahan,produk WHERE laporan_operator='$uid' AND DATE_FORMAT(laporan_end,'%Y-%m-%d') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m-%d')<= '$date_end' AND ct_laporan_id=laporan_id AND ct_produk_id=produk_id ORDER BY laporan_id $urutan LIMIT $fr,$pages_view");

	}
?>

    <h3>Transaksi  | 
    <?php  echo $date_start . " -->" . $date_end?></h3>
<b>Total Transaksi : <?php  echo $total?></b><br> 
<?php 
    ################ halaman #####################################################
    $pages=ceil($total/$pages_view);
     echo "Halaman: ";

    for($n=1;$n<=$pages;$n++)
    {
        $from=($n-1)*$pages_view;

        if(($fr/$pages_view)==($n-1))
        {
            echo "<b>$n|</b> ";
        } else
        {                                   
            echo "<a href=\"$operator_file_name" . "?uid=$uid&p=report&laporan_tipe=detail_lain&sid=$sid&fr=$from&pages_view=$pages_view&date_start=$date_start&date_end=$date_end&urutan=$urutan&roperator=$roperator\">$n</a>| ";
        }
    }
    ################ halaman end ############
?>
<p>

<table width=100% cellspacing=1 cellpadding=3 bgcolor='#999999' border=0>
<tr bgcolor='#E9E9E9'>
<td width="100" class=small>:::</td>
<td width="20" class=small>Nota.</td>
<td class=small>Client</td>
<td class=small>Waktu</td>
<td class=small>Produk</td>
<td class=small>Jumlah</td>
<td class=small>Harga (Rp.)</td>
<td class=small>Total (Rp.)</td>
<td class=small>Operator</td>
</tr>


<?php
$dump_biaya = 0;
if($total)
{
	while($isi_data_laporan=mysql_fetch_array($data_laporan_lain))
	{
		@$client_name=mysql_result(mysql_query("SELECT client_name FROM client WHERE client_id=$isi_data_laporan[laporan_client]"),0);
		if(!$client_name) $client_name = "$isi_data_laporan[laporan_client] - DELETED";
		
		if($data_operator[operator_id]==1 or $data_operator[operator_edit_report]==1)
		{
			$cmd="<a href='javascript:void(0);' onclick=\"popup_win('$print_product_location?uid=$uid&lapid=$isi_data_laporan[laporan_id]&sid=$sid','product_$isi_data_laporan[laporan_id]','scrollbars=yes,status=yes,width=" . "600" . ",height=" . "400" . "')\">" . "<img src='./img/icon_edit.png' border=0 title='Edit'></a> <a href='$print_product_location?uid=$uid&sid=$sid&lapid=$isi_data_laporan[laporan_id]&del=$isi_data_laporan[ct_id]' target=_blank onClick=\"return confirm('ANDA YAKIN HAPUS ORDER INI? " . ereg_replace("\"","",$isi_data_laporan[produk_nama]) . ", $client_name - Nota No. $isi_data_laporan[laporan_id] ..')\"> <img src='./img/icon_delete.png' border=0 title='Delete'></a>";
		} else
		{
			$cmd='';
		}
		
		$cmd .= " <a href='javascript:void(0);' onclick=\"popup_win('$print_bill_location?uid=$uid&lapid=$isi_data_laporan[laporan_id]&sid=$sid','bill_$isi_data_laporan[laporan_id]','scrollbars=yes,status=yes,width=" . "300" . ",height=" . "400" . "')\"><img src='./img/icon_print.png' border=0 title='Print Nota'></a>";

				
			print "<tr bgcolor='#FFFFFF'>
					   <td>$cmd</td>
					   <td>$isi_data_laporan[laporan_id]</td>
					   <td>$client_name</td>
					   <td>$isi_data_laporan[tanggal]</td>
					   <td>$isi_data_laporan[produk_nama]</td>
					   <td align=right>$isi_data_laporan[ct_jumlah]</td>
					   <td align=right>" . number_format($isi_data_laporan[ct_harga],0, ",",".") . "</td>
					   <td align=right>" . number_format($isi_data_laporan[total_lain],0, ",",".") . "</td>
					   <td>$isi_data_laporan[laporan_operator]</td>
				   </tr>\n";
		$dump_biaya += $isi_data_laporan[total_lain];
	}
}
	
?>

<tr bgcolor='#FFFFFF'>
					   <td>&nbsp;</td>
					   <td>&nbsp;</td>
					   <td>&nbsp;</td>
					   <td>&nbsp;</td>
					   <td>&nbsp;</td>
					   <td>&nbsp;</td>
					   <td>&nbsp;</td>
					   <td align=right bgcolor="#FFCCFF"><b><?php echo number_format($dump_biaya,0, ",",".")?>,-</b></td>
					   <td>&nbsp;</td>
</tr>
<tr bgcolor='#FFFFFF'>
					   <td>&nbsp;</td>
					   <td>&nbsp;</td>
					   <td>&nbsp;</td>
					   <td>&nbsp;</td>
					   <td>&nbsp;</td>
					   <td>&nbsp;</td>
					   <td>&nbsp;</td>
					   <td align=right bgcolor="#FFCCFF"><b>TOTAL</b></td>
					   <td>&nbsp;</td>
</tr>
</table>


<?php

} elseif($laporan_tipe=='detail' || !$laporan_tipe)
{

    
    #klo fromnya ga ada diisi default dari 0 (awal)
    if(!isset($fr)) $fr=0;
	
	if(!$urutan) $urutan = 'DESC';	

if($data_operator[operator_id]==1)
{
	if($roperator)
	{
		$total=mysql_result(mysql_query("SELECT count(*) FROM laporan,client WHERE client_id=laporan_client AND laporan_operator='$roperator' AND DATE_FORMAT(laporan_end,'%Y-%m-%d') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m-%d')<= '$date_end'"),0);
	} else
	{
		$total=mysql_result(mysql_query("SELECT count(*) FROM laporan,client WHERE client_id=laporan_client AND DATE_FORMAT(laporan_end,'%Y-%m-%d') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m-%d')<= '$date_end'"),0);
	}
} else
{
	$total=mysql_result(mysql_query("SELECT count(*) FROM laporan,client WHERE client_id=laporan_client AND laporan_operator='$uid' AND DATE_FORMAT(laporan_end,'%Y-%m-%d') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m-%d')<= '$date_end'"),0);
}

if($print_one_page) $pages_view = $total;

if($data_operator[operator_id]==1)
{
	if($roperator)
	{
		$data_laporan=mysql_query("SELECT laporan_id, client_id, laporan_client, client_name, DATE_FORMAT(laporan_start,'%Y/%m/%d - %H:%i:%s') as laporan_start, DATE_FORMAT(laporan_end,'%Y/%m/%d - %H:%i:%s') as laporan_end, laporan_durasi, laporan_biaya, laporan_catatan, laporan_operator FROM laporan,client WHERE client_id=laporan_client AND laporan_operator='$roperator' AND DATE_FORMAT(laporan_end,'%Y-%m-%d') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m-%d')<= '$date_end' ORDER BY laporan_id $urutan LIMIT $fr,$pages_view");
		$grand_total_biaya = mysql_result(mysql_query("SELECT sum(laporan_biaya) FROM laporan,client WHERE client_id=laporan_client AND laporan_operator='$roperator' AND DATE_FORMAT(laporan_end,'%Y-%m-%d') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m-%d')<= '$date_end'"),0);
		$grand_total_durasi = mysql_result(mysql_query("SELECT sum(laporan_durasi) FROM laporan,client WHERE client_id=laporan_client AND laporan_operator='$roperator' AND DATE_FORMAT(laporan_end,'%Y-%m-%d') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m-%d')<= '$date_end'"),0);
	
	} else
	{
		$data_laporan=mysql_query("SELECT laporan_id, client_id, laporan_client, client_name, DATE_FORMAT(laporan_start,'%Y/%m/%d - %H:%i:%s') as laporan_start, DATE_FORMAT(laporan_end,'%Y/%m/%d - %H:%i:%s') as laporan_end, laporan_durasi, laporan_biaya, laporan_catatan, laporan_operator FROM laporan,client WHERE client_id=laporan_client AND DATE_FORMAT(laporan_end,'%Y-%m-%d') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m-%d')<= '$date_end' ORDER BY laporan_id $urutan LIMIT $fr,$pages_view");
		$grand_total_biaya = mysql_result(mysql_query("SELECT sum(laporan_biaya) FROM laporan,client WHERE client_id=laporan_client AND DATE_FORMAT(laporan_end,'%Y-%m-%d') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m-%d')<= '$date_end'"),0);
		$grand_total_durasi = mysql_result(mysql_query("SELECT sum(laporan_durasi) FROM laporan,client WHERE client_id=laporan_client AND DATE_FORMAT(laporan_end,'%Y-%m-%d') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m-%d')<= '$date_end'"),0);
	
	}


} else
{
	$data_laporan=mysql_query("SELECT laporan_id, client_id, laporan_client, client_name, DATE_FORMAT(laporan_start,'%Y/%m/%d - %H:%i:%s') as laporan_start, DATE_FORMAT(laporan_end,'%Y/%m/%d - %H:%i:%s') as laporan_end, laporan_durasi, laporan_biaya, laporan_catatan, laporan_operator FROM laporan,client WHERE client_id=laporan_client AND laporan_operator='$uid' AND DATE_FORMAT(laporan_end,'%Y-%m-%d')>='$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m-%d')<= '$date_end' ORDER BY laporan_id $urutan LIMIT $fr,$pages_view");
	$grand_total_biaya = mysql_result(mysql_query("SELECT sum(laporan_biaya)  FROM laporan,client WHERE client_id=laporan_client AND laporan_operator='$uid' AND DATE_FORMAT(laporan_end,'%Y-%m-%d') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m-%d')<= '$date_end'"),0);
	$grand_total_durasi = mysql_result(mysql_query("SELECT sum(laporan_durasi) FROM laporan,client WHERE client_id=laporan_client AND laporan_operator='$uid' AND DATE_FORMAT(laporan_end,'%Y-%m-%d') >= '$date_start' AND DATE_FORMAT(laporan_end,'%Y-%m-%d')<= '$date_end'"),0);
	
}

	
?>
    <h3>Transaksi  | 
    <?php  echo $date_start . " -->" . $date_end?></h3>
<b>Total Transaksi : <?php  echo $total?></b><br> 
<?php 
    ################ halaman #####################################################
    $pages=ceil($total/$pages_view);
     echo "Halaman: ";

    for($n=1;$n<=$pages;$n++)
    {
        $from=($n-1)*$pages_view;

        if(($fr/$pages_view)==($n-1))
        {
            echo "<b>$n|</b> ";
        } else
        {                                   
            echo "<a href=\"$operator_file_name" . "?uid=$uid&p=report&sid=$sid&fr=$from&pages_view=$pages_view&date_start=$date_start&date_end=$date_end&urutan=$urutan&roperator=$roperator\">$n</a>| ";
        }
    }
    ################ halaman end ############
?>
<p>

<table width=100% cellspacing=1 cellpadding=3 bgcolor='#999999' border=0>
<tr bgcolor='#E9E9E9'>
<td width="100" class=small>:::</td>
<td width="20" class=small>Nota.</td>
<td class=small>Client</td>
<td class=small>Start</td>
<td class=small>End</td>
<td class=small>Durasi</td>
<td class=small>Internet (Rp.)</td>
<td class=small>Non Internet (Rp.)</td>
<td class=small>Total (Rp.)</td>
<td class=small>Catatan</td>
<td class=small>Operator</td>
</tr>
<?php 
$dump_biaya = 0;
$dump_durasi = 0;
if($total)
{
	while($isi_data_laporan=mysql_fetch_array($data_laporan))
	{
		$hour=floor($isi_data_laporan[laporan_durasi]/3600);
		$minute=floor(($isi_data_laporan[laporan_durasi] - $hour * 3600)/60);
		$second=$isi_data_laporan[laporan_durasi] - $hour*3600 - $minute*60;
	
		if($hour < 10) $hour="0$hour";
		if($minute < 10) $minute="0$minute";
		if($second < 10) $second="0$second";
		
		$time_duration="$hour hr : $minute min : $second sec";
		
		if($data_operator[operator_id]==1 or $data_operator[operator_edit_report]==1)
		{
			$cmd="<a href=\"$operator_file_name" . "?uid=$uid&p=report&sid=$sid&edit=$isi_data_laporan[laporan_id]\"><img src='./img/icon_edit.png' border=0 title='Edit'></a> <a href=\"$operator_file_name" . "?uid=$uid&p=report&sid=$sid&hapus=$isi_data_laporan[laporan_id]\" onClick=\"return confirm('ANDA YAKIN HAPUS TRANSAKSI DARI? " . ereg_replace("\"","","$isi_data_laporan[client_name] - Nota No. $isi_data_laporan[laporan_id]") . " ..')\"><img src='./img/icon_delete.png' border=0 title='Delete'></a>";
		} else
		{
			$cmd='';
		}
		
		$cmd .= " <a href='javascript:void(0);' onclick=\"popup_win('$print_bill_location?uid=$uid&lapid=$isi_data_laporan[laporan_id]&sid=$sid','bill_$isi_data_laporan[laporan_id]','scrollbars=yes,status=yes,width=" . "300" . ",height=" . "400" . "')\"><img src='./img/icon_print.png' border=0 title='Print Nota'></a>";

		$tambahan=@mysql_result(mysql_query("SELECT sum(ct_jumlah*ct_harga) FROM produk, client_tambahan WHERE produk_id=ct_produk_id AND ct_laporan_id=$isi_data_laporan[laporan_id]"),0);
		
		if($tambahan)
		{
			$tambahan_link = "<a href='javascript:void(0);' onclick=\"popup_win('$print_product_location?uid=$uid&lapid=$isi_data_laporan[laporan_id]&sid=$sid','product_$isi_data_laporan[laporan_id]','scrollbars=yes,status=yes,width=" . "600" . ",height=" . "400" . "')\">" . number_format($tambahan,0, ",",".") . "</a>";
		} else
		{
			#$tambahan_link = number_format($tambahan,0, ",",".");
			$tambahan_link = "<a href='javascript:void(0);' onclick=\"popup_win('$print_product_location?uid=$uid&lapid=$isi_data_laporan[laporan_id]&sid=$sid','product_$isi_data_laporan[laporan_id]','scrollbars=yes,status=yes,width=" . "600" . ",height=" . "400" . "')\">" . number_format($tambahan,0, ",",".") . "</a>";
		
		}
		
			print "<tr bgcolor='#FFFFFF'>
					   <td>$cmd</td>
					   <td>$isi_data_laporan[laporan_id]</td>
					   <td>$isi_data_laporan[client_name]</td>
					   <td>$isi_data_laporan[laporan_start]</td>
					   <td>$isi_data_laporan[laporan_end]</td>
					   <td>$time_duration</td>
					   <td align=right>" . number_format($isi_data_laporan[laporan_biaya],0, ",",".") . "</td>
					   <td align=right>" . $tambahan_link . "</td>
					   <td align=right>" . number_format($isi_data_laporan[laporan_biaya] + $tambahan,0, ",",".") . "</td>
					   <td>$isi_data_laporan[laporan_catatan]</td>
					   <td>$isi_data_laporan[laporan_operator]</td>
				   </tr>\n";
		$dump_biaya += $isi_data_laporan[laporan_biaya];
		$dump_tambahan += $tambahan;
		$dump_durasi += $isi_data_laporan[laporan_durasi];
	}
}

    $hour=floor($dump_durasi/3600);
    $minute=floor(($dump_durasi - $hour * 3600)/60);
    $second=$dump_durasi - $hour*3600 - $minute*60;

	if($hour < 10) $hour="0$hour";
	if($minute < 10) $minute="0$minute";
	if($second < 10) $second="0$second";
	
	$time_duration="$hour hr : $minute min : $second sec";
	
	
?>

<tr bgcolor='#FFFFFF'>
					   <td>&nbsp;</td>
					   <td>&nbsp;</td>
					   <td>&nbsp;</td>
					   <td>&nbsp;</td>
					   <td>&nbsp;</td>
					   <td bgcolor="#FFFF00"><b><?php  echo $time_duration?></b></td>
					   <td align=right bgcolor="#FFCCFF"><b><?php  echo number_format($dump_biaya,0, ",",".")?>,-</b></td>
					   <td align=right bgcolor="#CCFF99"><b><?php  echo number_format($dump_tambahan,0, ",",".")?>,-</b></td>
					   <td align=right bgcolor="#99CCFF""><b><?php  echo number_format($dump_biaya + $dump_tambahan,0, ",",".")?>,-</b></td>
					   <td>&nbsp;</td>
					   <td>&nbsp;</td>
</tr>
<tr bgcolor='#FFFFFF'>
					   <td>&nbsp;</td>
					   <td>&nbsp;</td>
					   <td>&nbsp;</td>
					   <td>&nbsp;</td>
					   <td>&nbsp;</td>
					   <td bgcolor="#FFFF00"><b>TOTAL DURASI</b></td>
					   <td align=right bgcolor="#FFCCFF"><b>INTERNET +</b></td>
					   <td align=right bgcolor="#CCFF99"><b>NON INTERNET</b></td>
					   <td align=right bgcolor="#99CCFF"><b>= TOTAL</b></td>
					   <td>&nbsp;</td>
					   <td>&nbsp;</td>
</tr>
</table>


<?php  //<h2>GRAND TOTAL Pemasukan Net: Rp.<?php  echo number_format($grand_total_biaya,0, ",","."),-</h2> ?>
<?php }}?>
<p><a href="#top">Atas</a></p>
