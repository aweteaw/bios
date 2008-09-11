<?php
# FILE INI TIDAK PERLU DIEDIT - PLEASE DONT EDIT THIS FILE - MATUR SUKSME!!!
######################################################################################################
# Deskripsi:
# Merupakan front end untuk client, guna memantau durasai pemakaian dan biaya yang ditumbulkan

function add_zero($input)
{
        if(strlen($input) < 2)
        {
                $input='0'.$input;
        }

        return $input;
}
require_once('./bios-config.php');
/*
When you use REMOTE_ADDR for getting the IP of the current user, 
sometimes you get the IP of the ISP Cache server.

pastikan setting di squid:

#Default:
forwarded_for on
-----------
supaya tidak di simpan dalam cache

acl magic_words1 url_regex -i 192.168
no_cache deny magic_words1
atau
offline_mode off
*/

if (getenv(HTTP_X_FORWARDED_FOR))
{ 
	$client_ip=substr(escapeshellcmd(mysql_escape_string(strip_tags(trim(getenv(HTTP_X_FORWARDED_FOR))))),0,15); 
} else 
{ 
	$client_ip=substr(escapeshellcmd(mysql_escape_string(strip_tags(trim(getenv(REMOTE_ADDR))))),0,15); 
} 

@$data=mysql_fetch_array(mysql_query("SELECT client_name, client_ip, client_id, client_start, unix_timestamp(client_start) as client_start2, client_status FROM client WHERE client_ip='$client_ip'"));
@extract($data,EXTR_OVERWRITE);

if(!$client_id)
{
	mysql_close($connection);
        die("<b>DISCONNECT! - Komputer Anda tidak terdaftar dalam billing. Silahkan hubungi operator. TQ</b><p>");
}?>

<html><head>
<script src="script.js.php?clients=<?php echo $client_id?>"></script>
<script language="JavaScript" type="text/JavaScript">
window.outerWidth = 400;
window.outerHeight = 250;
netscape.security.PrivilegeManager.enablePrivilege("UniversalBrowserWrite")
window.statusbar.visible = false;
window.menubar.visible = false;
window.locationbar.visible = false;
window.toolbar.visible = false;
window.sidebar.visible = false;
window.personalbar.visible = false;
netscape.security.PrivilegeManager.revertrivilege("UniversalBrowserWrite")
</script>
<title><?php echo $setting_cafe_name?> | Billing</title>
<link href="skin.css" rel="stylesheet" type="text/css"></head><body>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><b><?php echo $client_name?></b></td>
    <td><div id="billing_clock"></div></td>
  </tr>
</table><hr>
<table width="100%"  border="0" cellspacing="0" cellpadding="5"><tr><td width="100" valign="top"><div align="center"><img src="img/LOGO-WARNET.png" border="0" />
<?php
if($submit && $cmd=='selesai')
{
       		$client_status_set=2;
		$status_now=mysql_result(mysql_query("SELECT client_status FROM client WHERE client_id=$client_id"),0);

		#perbaikan disini, menjadi hanya cek parameter status saja. refreshing di operator jadi lebih responsif! 7 april 2008
		if($status_now==1)
		{
			$client_stop=date("Y-m-d H:i:s",mktime(gmdate("H")+$setting_timezone,gmdate("i"),gmdate("s"),gmdate("m"),gmdate("d"),gmdate("Y")));
			mysql_query("UPDATE client SET client_end='$client_stop',client_status=$client_status_set WHERE client_id=$client_id");
			
			
			$cur_data=mysql_fetch_array(mysql_query("SELECT client_start, client_end, client_desktop FROM client WHERE client_id=$client_id"));
			
			$time_start=strtotime("$cur_data[client_start]");
			$time_end=strtotime("$cur_data[client_end]");
			$time_duration=$time_end - $time_start;
							 
			###
			if($time_duration)
			{
				# pre penghitungan tarif.. disini kita hitung dulu biaya dan waktu minimum..
				$total_biaya = 0;
				
				$zone_start=date("G",$time_start);
				$data_tarif=mysql_fetch_array(mysql_query("SELECT tarif_perjam,tarif_min,tarif_min_durasi FROM tarif WHERE tarif_pkl=$zone_start"));
				$data_tarif_lama=$data_tarif[tarif_perjam];
				
				$total_biaya += $data_tarif[tarif_min];
				$total_biaya_lama = $data_tarif[tarif_min];
	
				$dump_total_biaya='';
				$dump_total_biaya[]=$total_biaya;
	
				if($time_duration > $data_tarif[tarif_min_durasi] * 60)
				{
					$time_start = $time_start + $data_tarif[tarif_min_durasi] * 60;
					
					#ubah waktu start dan berakhir ke dalam unixstamp - tentukan jam awal nya.
					$scan_start=mktime(date("G",$time_start),0,0,date("n",$time_start),date("j",$time_start),date("Y",$time_start));
					$scan_end=mktime(date("G",$time_end),0,0,date("n",$time_end),date("j",$time_end),date("Y",$time_end));
							
					#jumlah zona tarif yang akan discan:
					$scan_zona = floor(($scan_end - $scan_start) / 3600) + 1;
					
					$used_duration = $data_tarif[tarif_min_durasi] * 60;
					#$used_duration_lama = $data_tarif[tarif_min_durasi] * 60;
					$used_duration_lama = $used_duration;
					
					//$used_duration_awal = $used_duration;
	
					$total_used = $used_duration; # ---> penting untuk dideklarasikan nilai 0 nya. soalnya kalau ganti jadi null alias tidak dideklarasikan, hasilnya akan beda saat menggunakan operand +=
	
					# bagian baris dalam for .. dibawah adalah bagian inti dari billing BiOS ini. Inti karena merupakan yang bertanggung jawab
					# dalam merumuskan hitung hitungan billing. logika sederhana, tapi implementasi dalam programming perlu waktu 2 hari 2 malem,
					# beberapa gelas kopi dan 1 kaleng pocari, tidur hanya 4 jam :D - 11-12 juni 2008
	
					$toggle_jam_pertama = 0;
					
					for($n=0;$n < $scan_zona; $n++)
					{
						$total_biaya2=0;
						
						$zone_start=date("G",$scan_start + $n * 3600);
						$zone_end=date("G",$scan_start + ($n+1) * 3600);
						
						$data_tarif=mysql_fetch_array(mysql_query("SELECT tarif_perjam,tarif_min,tarif_min_durasi FROM tarif WHERE tarif_pkl=$zone_start"));
		
						$check_time_end=mktime(date("G",$scan_start + ($n+1) * 3600),0,0,date("n",$scan_start + ($n+1) * 3600),date("j",$scan_start + ($n+1) * 3600),date("Y",$scan_start + ($n+1) * 3600));
						
						if(($time_start + ($time_duration - $total_used)) > $check_time_end)
						{
		
							$time_end = $check_time_end;
	
						} else
						{
							$time_end = $time_start + ($time_duration - $total_used) ;
						}
						
		
						$used_duration = $time_end - $time_start;
						
						$total_used += $used_duration;
						$posisi=0;
						
						#23/6/2008 patch
						if($data_tarif[tarif_perjam]==$data_tarif_lama && $total_used > 3600 && $toggle_jam_pertama ==0)
						{
							$toggle_jam_pertama=1;
							$posisi=1;
							$total_biaya = $data_tarif[tarif_perjam];
	
							$total_biaya += ceil(($total_used - 3600) / $setting_price_every_second) * ($data_tarif[tarif_perjam] / (3600 / $setting_price_every_second));
	
							//$a=$total_used - 3600; $b=$used_duration;
	
							$total_biaya2 = ceil(($used_duration - ($total_used - 3600)) / $setting_price_every_second) * (($data_tarif[tarif_perjam] - $total_biaya_lama) / ((3600 - $used_duration_lama) / $setting_price_every_second)) + ceil(($total_used - 3600) / $setting_price_every_second) * ($data_tarif[tarif_perjam] / (3600 / $setting_price_every_second));;
						
							
						# PATCH SP1 : ditambahkan n=0 dengan syarat tarif lama dan baru sama. sebelumnya tidak dicek apakah tarif lama sama dengan tarif baru, akibatnya bila berbeda, menggunakan tarif baru. seharusnya bila tarif berbeda  =zona berbeda menggunakan kondisi posisi nomor 3
						} elseif(($data_tarif[tarif_perjam]==$data_tarif_lama && $n==0) || ($data_tarif[tarif_perjam]==$data_tarif_lama && $total_used <= 3600))
						{
							$posisi=2;
	
							$total_biaya += ceil($used_duration / $setting_price_every_second) * (($data_tarif[tarif_perjam] - $total_biaya_lama) / ((3600 - $used_duration_lama) / $setting_price_every_second));
	
							#$total_biaya2 = $total_biaya; #ditambahkan dengan total biaya yang minimum
							$total_biaya2 = ceil($used_duration / $setting_price_every_second) * (($data_tarif[tarif_perjam] - $total_biaya_lama) / ((3600 - $used_duration_lama) / $setting_price_every_second)); #--> tidak ditambah biaya minimum
	
						} else
						{
							$toggle_jam_pertama=1; #PATCH SP1
							$posisi=3;
							$total_biaya += ceil($used_duration / $setting_price_every_second) * ($data_tarif[tarif_perjam] / (3600 / $setting_price_every_second));
	
							$total_biaya2 = ceil($used_duration / $setting_price_every_second) * ($data_tarif[tarif_perjam] / (3600 / $setting_price_every_second));
						}
	
						$dump_total_biaya[]="zona: " . $zone_start . " = Rp." . ceil($total_biaya2) . " - $used_duration detik - perjam $data_tarif[tarif_perjam],pos=$posisi";
						
						$time_start = $time_end;
					}
				
				}
				# ga kepengen susah cari kembalian kan? fungsi dibawah ini terbukti sangat membantu :D
				if($setting_receh <= 0 ) $setting_receh = 1;
				$total_biaya = ceil($total_biaya / $setting_receh) * $setting_receh;
			}
			#######
			# pilih operator selain admin
			$uid=@mysql_result(mysql_query("SELECT operator_name FROM operator,session WHERE operator_name=uid and operator_id <> 1 ORDER BY operator_last_date DESC"),0);

			# bila tidak ada cek apakah admin adalah operator yg bertugas
			if(!$uid)
			{
				$uid=@mysql_result(mysql_query("SELECT operator_name FROM operator,session WHERE operator_name=uid and operator_id = 1 ORDER BY operator_last_date DESC"),0);
			}

			# bila tidak ada, berarti tidak ada yang standby..
			if(!$uid)
			{
				$uid='no operator';
			}			
					 
			#update laporan harian
			mysql_query("INSERT INTO laporan (laporan_client, laporan_start, laporan_end, laporan_durasi, laporan_biaya, laporan_operator, laporan_catatan) VALUES ($client_id, '$cur_data[client_start]','$cur_data[client_end]',$time_duration, $total_biaya,'$uid','stop by client')");


		}
		
		$client_status=2;

}

//echo "---- $client_status ---";
switch($client_status)
{
                case 0:					 
					$client_start=date("Y-m-d H:i:s",mktime(gmdate("H")+$setting_timezone,gmdate("i"),gmdate("s"),gmdate("m"),gmdate("d"),gmdate("Y")));
					$client_status=1;
					$status_now=mysql_result(mysql_query("SELECT client_status FROM client WHERE client_id=$client_id"),0);
					if($status_now <> $client_status && $status_now=='0')
					{
						mysql_query("UPDATE client SET client_start='$client_start',client_status=$client_status WHERE client_id=$client_id");
					}
					
					?>
					<p><form action="./client.php" method="GET">
					<input name="cmd" type="hidden" value="selesai">
					<input name="submit" type="submit" value="SELESAI" onClick="return confirm('Anda yakin SUDAH SELESAI maen internetnya?\n\nklik [CANCEL] untuk membatalkan')">
					</form></p>
					<?php
				break; 
				
                case 1:					
				?>
					<p><form action="./client.php" method="GET">
					<input name="cmd" type="hidden" value="selesai">
					<input name="submit" type="submit" value="SELESAI" onClick="return confirm('Anda yakin SUDAH SELESAI maen internetnya?\n\nklik [CANCEL] untuk membatalkan')">
					</form></p>
				<?php
				break; 


                case 2:					 
					#Paksa desktop untuk dilogout
					$client_ip=@mysql_result(mysql_query("SELECT client_ip FROM client WHERE client_id=$client_id"),0);
					$client_login=@mysql_result(mysql_query("SELECT client_login FROM client WHERE client_id=$client_id"),0);
					$client_ping=shell_exec("ping $client_ip -c 1 -s 1 -t 1");
					if(eregi('bytes from',$client_ping))
					{
						/*
						$client_desktop=@mysql_result(mysql_query("SELECT client_desktop FROM client WHERE client_id=$client_id"),0);
						switch($client_desktop)
						{
						case 'kde':
							`sudo ssh -l root $client_ip 'su - $setting_client_username -c "env DISPLAY=:0.0 dcop ksmserver default logout 0 0 0"'`;
						break;
						case 'gnome':
							#`sudo ssh -l root $client_ip 'su - $setting_client_username -c "gnome-session-save --kill --silent --display=:0.0 -s bios"'`;
							`sudo ssh -l $setting_client_username $client_ip 'export SESSION_MANAGER=$(cat /tmp/gnome-session-manager) && gnome-session-save --kill --silent --display=:0.0 -s bios'`;
						break;
						case 'xfce':
							`sudo ssh -l root $client_ip 'su - $setting_client_username -c "xfce4-session-logout --display=:0.0"'`;
						break;
						}
						*/

						#apabila cara dibawah ini gagal, silahkan uncomment perintah case diatas, dan
						#beri comment pada perintah dibawah. - 6 april 2008
						`sudo ssh -l root $client_ip skill -u $client_login`;

					}
					
					echo "Silahkan minta Operator, untuk mereset billing Anda..";
					mysql_query("REPLACE INTO client_status VALUES ($client_id,9)");
				break; 
				
}
?>
</div></td>
<td valign="top"><br>
  


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr valign="top">
    <td><b>Mulai:</b>      </td>
    <td>
<?php
$client_start2=mysql_result(mysql_query("SELECT DATE_FORMAT(client_start,'%d %M %Y, %H:%i:%s') FROM client WHERE client_id=$client_id"),0);

//$client_start2=date("d M Y, H:i:s",$client_start2 + $setting_timezone * 3600);


	echo "$client_start2";
?></td>
  </tr>
  <tr valign="top">
    <td>Durasi: </td>
    <td><div id="<?php echo 'client'.$client_id?>">00 hr : 00 min : 00 sec</div></td>
  </tr>
  <tr valign="top">
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr valign="top">
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr valign="top">
    <td><h2 align="center">Rp. </h2></td>
    <td><h2><div id="<?php echo 'price'.$client_id?>"></div></h2></td>
  </tr>
</table>
<p><em>&quot;
    <?php echo $setting_motd?> &quot;</em></p></td></tr></table>

<script type="text/javascript">
<!-- panggil rutin penghitungan billing!
	setTimeout('showTime()', <?php echo $setting_refresh*1000?>);
//-->
</script></body></html>
<?php
		mysql_close($connection);
?>
