<?php 
# Deksripsi: 
# Menampilkan halaman yang berisi kondisi realtime warnet, berupa jumlah pemakaian , biaya ,durasi, dari masing-masing client

if(eregi("billing.php",$_SERVER['REQUEST_URI'])) die('hello script kiddies..');
if($submit_layout && $setting_layouts)
{
	mysql_query("UPDATE setting SET setting_layout='$setting_layouts' LIMIT 1");
	$setting_layout = mysql_result(mysql_query("SELECT setting_layout FROM setting LIMIT 1"),0);
}
?>
<!-- update 25 februari 2008 //-->


<script src="script.js.php?uid=<?php echo $uid?>"></script>

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><img src="./img/icon_billing.png" width="63" height="75" align="left"><strong>Summary:</strong><br>
<?php 
$zone_tarif=date("G",mktime(gmdate("H")+$setting_timezone,gmdate("i"),gmdate("s"),gmdate("m"),gmdate("d"),gmdate("Y")));
$data_tarif=mysql_fetch_array(mysql_query("SELECT tarif_perjam,tarif_min,tarif_min_durasi FROM tarif WHERE tarif_pkl=$zone_tarif"));
?>

 - harga 1 jam = Rp. 
    <b id="summary_tarif_perjam"><?php  echo  number_format($data_tarif[tarif_perjam],0,",",".")?> </b>
	<br>
- tarif minimal = Rp. 
  <b id="summary_tarif_min"><?php  echo  number_format($data_tarif[tarif_min],0,",",".")?></b> untuk <b id="summary_tarif_min_durasi"><?php  echo  number_format($data_tarif[tarif_min_durasi],0,",",".")?></b> menit pertama<br>
  
  - update harga setiap 
  <b id="summary_tarif_refresh"><?php  echo $setting_price_every_second?></b>
  detik
</td>
    <td width="30%">
<h3><div id="billing_clock">Pkl. <?php  echo date("H:i:s - d M Y",mktime(gmdate("H")+$setting_timezone,gmdate("i"),gmdate("s"),gmdate("m"),gmdate("d"),gmdate("Y")))?></div></h3> 
<a href="<?php  echo $operator_file_name?>?uid=<?php  echo $uid?>&p=billing&sid=<?php  echo $sid?>">Klik disini untuk Refresh manual</a>
</td>
  </tr>
</table>
<hr>
<a name="status">
<?php 
## 18 Agustus 3:22 AM

if($start && $start <> 1)
{
        $client_start=date("Y-m-d H:i:s",mktime(gmdate("H")+$setting_timezone,gmdate("i"),gmdate("s"),gmdate("m"),gmdate("d"),gmdate("Y")));
        $client_status=1;
		$status_now=mysql_result(mysql_query("SELECT client_status FROM client WHERE client_id=$start"),0);
		if($status_now <> $client_status && $status_now=='0')
		{
			mysql_query("UPDATE client SET client_start='$client_start',client_status=$client_status WHERE client_id=$start");
			mysql_query("UPDATE client_status SET client_status_old=$client_status WHERE client_status_id=$start");
		}
} elseif($stop || $start==1)
{
	if($start==1) $stop=1;
        $client_status=2;
	$client_stop=date("Y-m-d H:i:s",mktime(gmdate("H")+$setting_timezone,gmdate("i"),gmdate("s"),gmdate("m"),gmdate("d"),gmdate("Y")));

		$status_now=mysql_result(mysql_query("SELECT client_status FROM client WHERE client_id=$stop"),0);
		if($start==1) $status_now=1;
		if($status_now <> $client_status  && $status_now=='1')
		{
			mysql_query("UPDATE client SET client_end='$client_stop',client_status=$client_status WHERE client_id=$stop");
			if($start==1) mysql_query("UPDATE client SET client_start='$client_stop' WHERE client_id=$stop");
			mysql_query("UPDATE client_status SET client_status_old=$client_status WHERE client_status_id=$stop");
			
			$cur_data=mysql_fetch_array(mysql_query("SELECT client_start, client_end, client_desktop, client_login FROM client WHERE client_id=$stop"));
			
            $time_start=strtotime("$cur_data[client_start]");
            $time_end=strtotime("$cur_data[client_end]");
            $time_duration=$time_end - $time_start;
							 

#######
		if($time_duration || $stop==1)
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

			if($stop==1) $total_biaya=0;
					 
			#update laporan harian
			mysql_query("INSERT INTO laporan (laporan_client, laporan_start, laporan_end, laporan_durasi, laporan_biaya, laporan_operator, laporan_catatan) VALUES ($stop, '$cur_data[client_start]','$cur_data[client_end]',$time_duration, $total_biaya,'$uid','stop by op')");

			#PERIKSA STATUS CLIENT HIDUP ATAU MATI, VIA PING REPLY
			$client_ip=@mysql_result(mysql_query("SELECT client_ip FROM client WHERE client_id=$stop"),0);
			$client_ping=shell_exec("ping $client_ip -c 1 -s 1 -t 1");
			if(eregi('bytes from',$client_ping))
			{
				/*
				switch($cur_data[client_desktop])
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
				`sudo ssh -l root $client_ip skill -u $cur_data[client_login]`;

			}
		
		}

        #uncomment baris dibawah bila Anda menginginkan tampilan di client berupa: desktop di lock, ditanyain user dan password
		#$client_ip=@mysql_result(mysql_query("SELECT client_ip FROM client WHERE client_id=$_REQUEST[stop]"),0);
        #`sudo ssh -l root $client_ip 'su - baliwae -c "env DISPLAY=:0.0 dcop ksmserver default logout 0 0 0"'`;

} elseif($reset)
{	
        $client_status=0;
		$status_now=mysql_result(mysql_query("SELECT client_status FROM client WHERE client_id=$reset"),0);
		if($status_now <> $client_status  && $status_now=='2')
		{
			mysql_query("UPDATE client SET client_status=$client_status, client_start=NULL WHERE client_id=$reset");		
			mysql_query("UPDATE client_status SET client_status_old=$client_status WHERE client_status_id=$reset");
		}
		
		#PERIKSA STATUS CLIENT HIDUP ATAU MATI, VIA PING REPLY
		$client_ip=@mysql_result(mysql_query("SELECT client_ip FROM client WHERE client_id=$reset"),0);
		$client_login=@mysql_result(mysql_query("SELECT client_login FROM client WHERE client_id=$reset"),0);
		if($reset<>1) $client_ping=shell_exec("ping $client_ip -c 1 -s 1 -t 1");
		if(eregi('bytes from',$client_ping))
		{
				/*
				$client_desktop=@mysql_result(mysql_query("SELECT client_desktop FROM client WHERE client_id=$reset"),0);
				switch($client_desktop)
				{
				case 'kde':
					`sudo ssh -l root $client_ip 'su - $setting_client_username -c "env DISPLAY=:0.0 dcop ksmserver default logout 0 0 0"'`;
				break;
				case 'gnome':
					#cara dibawah hanya untuk gnome umumnya kayak misal linux mint
					#`sudo ssh -l root $client_ip 'su - $setting_client_username -c "gnome-session-save --kill --silent --display=:0.0 -s bios"'`;
					#cara kedua ini khusus untuk ubuntu, distro lain harusnya sama juga.. upd. 6 april 2008 sore
					#`sudo ssh -l root $client_ip 'su - $setting_client_username -c "export SESSION_MANAGER=$(cat /tmp/gnome-session-manager) && gnome-session-save --kill --silent --display=:0.0 -s bios"'`;

					#untuk hasil optimal pakai cara ketiga ini khusus untuk ubuntu, distro lain harusnya sama juga.. lebih gesit dari cara kedua. upd. 6 april 2008 sore (perhatikan tanda ' dengan " di awal export itu memberi makna beda. kalo " ga bisa jalan, dan dianggap file /tmp/gnomenya dicari di server bukan di client
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
		
} elseif($moving && $pindah_client_run && $pindah_client_idle)
{
		#client idle di start
		$data_client_run=mysql_fetch_array(mysql_query("SELECT client_start, client_status, client_desktop, client_login FROM client WHERE client_id=$pindah_client_run"));
		$data_client_idle=mysql_fetch_array(mysql_query("SELECT client_status FROM client WHERE client_id=$pindah_client_idle"));
		
		if($data_client_run[client_status]==1 and $data_client_idle[client_status]==0)
		{
			# men-start client idle 
			mysql_query("UPDATE client SET client_start='$data_client_run[client_start]',client_status=1 WHERE client_id=$pindah_client_idle");
			mysql_query("UPDATE client_status SET client_status_old=1 WHERE client_status_id=$pindah_client_idle");
		
			# me-reset client run
			mysql_query("UPDATE client SET client_start=NULL,client_status=0 WHERE client_id=$pindah_client_run");
			mysql_query("UPDATE client_status SET client_status_old=0 WHERE client_status_id=$pindah_client_run");
			
			#PERIKSA STATUS CLIENT HIDUP ATAU MATI, VIA PING REPLY
			$client_ip=@mysql_result(mysql_query("SELECT client_ip FROM client WHERE client_id=$pindah_client_run"),0);
			$client_ping=shell_exec("ping $client_ip -c 1 -s 1 -t 1");
			if(eregi('bytes from',$client_ping))
			{

				/*
				switch($data_client_run[client_desktop])
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
				
				`sudo ssh -l root $client_ip skill -u $data_client_run[client_login]`;

			}
		}
			
} elseif($show)
{
    $client_ip=@mysql_result(mysql_query("SELECT client_ip FROM client WHERE client_id=$show"),0);
	$client_login=@mysql_result(mysql_query("SELECT client_login FROM client WHERE client_id=$show"),0);
	$client_name=@mysql_result(mysql_query("SELECT client_name FROM client WHERE client_id=$show"),0);

	$result=`sudo ssh -l root $client_ip 'su - $client_login -c "env DISPLAY=:0.0 firefox $client_location"&'`;
		
	echo "<b>$client_name | $client_ip | $result</b> <p>";

} elseif($showall)
{

	$data_client=mysql_query("SELECT client_ip, client_name, client_login FROM client WHERE client_id <> 1 ORDER BY client_name ASC");
	
	while($isi_data_client=mysql_fetch_array($data_client))
	{
                $client_ip=$isi_data_client['client_ip'];
                $client_name=$isi_data_client['client_name'];
				$client_login=$isi_data_client['client_login'];
	
		$result=`sudo ssh -l root $client_ip 'su - $client_login -c "env DISPLAY=:0.0 firefox $client_location"&'`;
	
		
	echo "<b>$client_name | $client_ip | $result</b><p>";
	flush();
	}
				
} elseif($reboot && (mysql_result(mysql_query("SELECT client_status FROM client WHERE client_id=$reboot"),0) <> 1)) # update 20 september jam 12 siang
{
                $client_ip=@mysql_result(mysql_query("SELECT client_ip FROM client WHERE client_id=$reboot"),0);
                $client_name=@mysql_result(mysql_query("SELECT client_name FROM client WHERE client_id=$reboot"),0);
                #$client_execute=shell_exec("ping $client_ip -c 1");

                # PAKELAH SYSTEM, KALO PAKE SHELL_EXEC GA BISA, GA TAU NAPA.., shell_exec kayaknya buat perintah lokal aja kyak ls, ping ,dll
                # TERUS KALO PAKE SHELL_EXEC GA USAH PAKE /BIN/PING, TAPI LANGSUNG PING AJA
                # JANGAN LUPA PAKE SUDO, TANPA SUDO PERINTAH KAYAK SSH GA BISA. TPI KALO PING, LS MASIH BISA.
                # TANPA SUDO HASILNYA NULL
                # YANG PERLU DI SET DI /ETC/SUDOERS
                # TAMBAHKAN: apache   ALL=NOPASSWD:/usr/bin/ssh
                # jamin ces pleng :) - solved 20 september 2 subuh --> keren kan catatannya developer nye . hehehe.. dibelain tuh mpe jam 2 subuh..

                echo "$client_name | $client_ip | REBOOT: ";

                $client_status=0;
                mysql_query("UPDATE client SET client_status=$client_status WHERE client_id=$reboot");

                #PERIKSA STATUS CLIENT HIDUP ATAU MATI, VIA PING REPLY
                $client_ping=shell_exec("ping $client_ip -c 1 -s 1 -t 1");
                if(!eregi('bytes from',$client_ping))
                {
                                echo "<b><font color='red'>GAGAL</font> - KOMPUTER CLIENT TIDAK BISA DI-REBOOT.. </b><p>";
                } else
                {


                                #dicetak echo dulu soalnya system ini langsung cetak hasil dilayar.., jadi ga usah disimpan di variabel!
                                echo "<pre>SUKSES - ";
                                system("sudo ssh -l root $client_ip reboot");
                                #$client_execute=system("ls -l");
                                echo "</pre>";

                }

                #jalanin 2 kali, untuk memastikan db server sudah di reset, kemungkinan bisa terjadi
                #db server sudah di reset, namun karena hampir bersamaan client request ke server, akibatnya running lagi
                mysql_query("UPDATE client SET client_status=$client_status WHERE client_id=$reboot");
				mysql_query("UPDATE client_status SET client_status_old=$client_status WHERE client_status_id=$reboot");

} elseif($poweroff  && (mysql_result(mysql_query("SELECT client_status FROM client WHERE client_id=$poweroff"),0) <> 1))
{
                $client_ip=@mysql_result(mysql_query("SELECT client_ip FROM client WHERE client_id=$poweroff"),0);
                $client_name=@mysql_result(mysql_query("SELECT client_name FROM client WHERE client_id=$poweroff"),0);

                echo "$client_name | $client_ip | POWEROFF: ";

                $client_status=0;
                mysql_query("UPDATE client SET client_status=$client_status WHERE client_id=$poweroff");
				mysql_query("UPDATE client_status SET client_status_old=$client_status WHERE client_status_id=$poweroff");


                #PERIKSA STATUS CLIENT HIDUP ATAU MATI, VIA PING REPLY
                $client_ping=shell_exec("ping $client_ip -c 1 -s 1 -t 1");
                if(!eregi('bytes from',$client_ping))
                {
                                echo "<b><font color='red'>GAGAL</font> - KOMPUTER CLIENT TIDAK BISA DI-POWEROFF..</b><p>";
                } else
                {

                                echo "<pre>SUKSES - ";
                                system("sudo ssh -l root $client_ip poweroff");
                                echo "</pre>";


                }

                #jalanin 2 kali, untuk memastikan db server sudah di reset, kemungkinan bisa terjadi
                #db server sudah di reset, namun karena hampir bersamaan client request ke server, akibatnya running lagi
                mysql_query("UPDATE client SET client_status=$client_status WHERE client_id=$poweroff");

} elseif($poweroffall && !(mysql_result(mysql_query("SELECT count(*) FROM client WHERE client_status=1"),0)))
{

	$data_client=mysql_query("SELECT client_id, client_ip, client_name FROM client WHERE client_id <> 1 ORDER BY client_name ASC");
	
	while($isi_data_client=mysql_fetch_array($data_client))
	{
                $client_ip=$isi_data_client['client_ip'];
                $client_name=$isi_data_client['client_name'];
		$poweroff=$isi_data_client['client_id'];

                echo "$client_name | $client_ip | POWEROFF: ";

		flush();

                $client_status=0;
                mysql_query("UPDATE client SET client_status=$client_status WHERE client_id=$poweroff");
		mysql_query("UPDATE client_status SET client_status_old=$client_status WHERE client_status_id=$poweroff");


                #PERIKSA STATUS CLIENT HIDUP ATAU MATI, VIA PING REPLY
                $client_ping=shell_exec("ping $client_ip -c 1 -s 1 -t 1");
                if(!eregi('bytes from',$client_ping))
                {
                                echo "<b><font color='red'>GAGAL</font> - KOMPUTER CLIENT TIDAK BISA DI-POWEROFF..</b><p>";
				flush();
                } else
                {

                                echo "<pre>SUKSES - ";
                                system("sudo ssh -l root $client_ip poweroff");
                                echo "</pre>";
				flush();


                }
		

                #jalanin 2 kali, untuk memastikan db server sudah di reset, kemungkinan bisa terjadi
                #db server sudah di reset, namun karena hampir bersamaan client request ke server, akibatnya running lagi
                mysql_query("UPDATE client SET client_status=$client_status WHERE client_id=$poweroff");
	}

}



?>

		<form method="post" action="<?php  echo $operator_file_name?>?uid=<?php  echo $uid?>&p=billing&sid=<?php  echo $sid?>&moving=1">
		
		<?php 
		
		$dump_client_run='';
		$dump_client_idle='';
		
		$data_client_run=mysql_query("SELECT client_id, client_name FROM client WHERE client_status=1 AND client_id<> 1 ORDER by client_name ASC");
		while($isi_data_client_run=mysql_fetch_array($data_client_run))
		{
			$dump_client_run .= "<option value='$isi_data_client_run[client_id]'>$isi_data_client_run[client_name]</option>";
		}
		
		$data_client_idle=mysql_query("SELECT client_id, client_name FROM client WHERE client_status=0  AND client_id<> 1 ORDER by client_name ASC");
		while($isi_data_client_idle=mysql_fetch_array($data_client_idle))
		{
			$dump_client_idle .= "<option value='$isi_data_client_idle[client_id]'>$isi_data_client_idle[client_name]</option>";
		}
		
		?>
		
		  <img src="./img/icon_recycle.png" align="absmiddle"> Pindah kursi Client 
		  <select name="pindah_client_run">
		  <?php  echo $dump_client_run?>
		  </select> 
		  ke Client 
		  <select name="pindah_client_idle">
		  <?php  echo $dump_client_idle?>
		  </select>
		  <input name="submit_pindah_kursi" type="submit" id="submit_pindah_kursi" value="Sekarang!"> 
		  | <img src="./img/icon_warning.png" align="absmiddle"><b>Seluruh Client: 
		  
		  
		  <?php 
		  if(!(mysql_result(mysql_query("SELECT count(*) FROM client WHERE client_status=1"),0)))
		  {?>
		  	<a href="<?php  echo $operator_file_name?>?uid=<?php  echo $uid?>&p=billing&poweroffall=1&sid=<?php  echo $sid?>" onClick="return confirm('YAKIN MATIKAN SEMUA KOMPUTER CLIENT?')">Poweroff</a> 
		  <?php } else
		  {?>
		  	Poweroff
		  <?php }?>
		  
		  | <a href="<?php  echo $operator_file_name?>?uid=<?php  echo $uid?>&p=billing&showall=1&sid=<?php  echo $sid?>" onClick="return confirm('YAKIN PAKSA TAMPILKAN BILLING DI SEMUA KOMPUTER CLIENT?')">Show Billing</a></b> 
		
		
		
		
		</form>
 		
		
		<table width="100%" border="0" cellspacing="1" cellpadding="5" bgcolor="#999999">
		<tr bgcolor="#DDDDDD">
			<td width=125>Command</td>
			<td>Client</td>
			<td><div align="right">Biaya (Rp.)</div></td>
			<td><div align="right">Durasi</div></td>
			<td><div align="right">Mulai</div></td>
			<td><div align="center">System</div></td>
		</tr>


<?php 


$data_client=mysql_query("SELECT client_id, client_ip, client_name, client_status, client_start, client_desktop, date_format(client_start,'%H:%i:%s - %d %b %Y') as client_start2, client_end FROM client ORDER BY client_name ASC");

while($isi_data_client=mysql_fetch_array($data_client))
{
        extract($isi_data_client,EXTR_OVERWRITE);
		$highlight=NULL;

	srand((double)microtime()*1000000);

	#untuk memastikan, kalau ada set di seting_refresh_tmp maka client_status harusnya 2 / stop
	if($setting_refresh_tmp) $client_status=2;	
		
        switch($client_status)
        {
                case 0:
                     $bgcolor='#EFEFEF';
					 
						if($setting_layout=='standard')
						{
							$image_client="<img src='./img/client_off.png' border=0><br>";
						} else
						{
							$image_client="";
						}

                     $client_name_link="<a href='javascript:void(0);' onclick=\"javascript:popup_win('$screenshot_location?uid=$uid&shot=$client_ip&sid=$sid&done=" . md5(uniqid(rand())) . "','screenshot_$client_id','scrollbars=yes,status=yes,width=" . ($setting_screenshot_width + 30) . ",height=" . ($setting_screenshot_height + 60) . "')\">" . $image_client . "$client_name</a>";

				 	 #$cmd="<a href=\"javascript:void(0);\"" . " onClick=\"var answer = confirm('START $client_name ?');if(answer){replace('./function.php?q=getcmd&sid=$_REQUEST[sid]&tipe=stop&start=$client_id','targetDiv')}\">start</a>";
                     
					 #$cmd="<a href=\"$PHP_SELF" . "uid=$uid&p=billing&start=$client_id&sid=$sid#$client_id\" onClick=\"return confirm('START $client_name ?')\">start</a>";
					 $cmd="<a href=\"$PHP_SELF" . "uid=$uid&p=billing&start=$client_id&sid=$sid#$client_id\" onClick=\"return confirm('START $client_name ?')\"><img src='./img/icon_start.png' border=0 title='Start'></a>";

					 #$client_system="<a href=\"$PHP_SELF" . "uid=$uid&p=billing&poweroff=$client_id&sid=$sid#status\" onClick=\"return confirm('Anda yakin ingin me-MATIKAN $client_name ?')\">poweroff</a> - <a href=\"$PHP_SELF" . "uid=$uid&p=billing&reboot=$client_id&sid=$sid#status\" onClick=\"return confirm('Anda yakin ingin me-RESTART $client_name ?')\">reboot</a> - <a href=\"$PHP_SELF" . "uid=$uid&p=billing&show=$client_id&sid=$sid#$client_id\" onClick=\"return confirm('Tampilkan Billing pada $client_name ?')\">show</a>";
                	 $client_system="<a href=\"$PHP_SELF" . "uid=$uid&p=billing&poweroff=$client_id&sid=$sid#status\" onClick=\"return confirm('Anda yakin ingin me-MATIKAN $client_name ?')\"><img src='./img/icon_power.png' border=0 title='Poweroff'></a> <a href=\"$PHP_SELF" . "uid=$uid&p=billing&reboot=$client_id&sid=$sid#status\" onClick=\"return confirm('Anda yakin ingin me-RESTART $client_name ?')\"><img src='./img/icon_reboot.png' border=0 title='Reboot'></a> <a href=\"$PHP_SELF" . "uid=$uid&p=billing&show=$client_id&sid=$sid#$client_id\" onClick=\"return confirm('Tampilkan Billing pada $client_name ?')\"><img src='./img/icon_show.png' border=0 title='Show'></a>";

			if($client_id ==1) $client_system = '';
                
					break;

                case 1:

					 $bgcolor='#F7FEC2';
					 
						if($setting_layout=='standard')
						{
							$image_client="<img src='./img/client_run.png' border=0><br>";
						} else
						{
							$image_client="";
						}
						
                     $client_name_link="<a href='javascript:void(0);' onclick=\"javascript:popup_win('$screenshot_location?uid=$uid&shot=$client_ip&sid=$sid&done=" . md5(uniqid(rand())) . "','screenshot_$client_id','scrollbars=yes,status=yes,width=" . ($setting_screenshot_width + 60) . ",height=" . ($setting_screenshot_height + 30) . "')\">" . $image_client . "$client_name</a>";
		

                     #$cmd="<a href=\"$PHP_SELF" . "uid=$uid&p=billing&stop=$client_id&sid=$sid#$client_id\" onClick=\"return confirm('STOP $client_name ?')\">stop</a>";
					 $cmd="<a href=\"$PHP_SELF" . "uid=$uid&p=billing&stop=$client_id&sid=$sid#$client_id\" onClick=\"return confirm('STOP $client_name ?')\"><img src='./img/icon_stop.png' border=0 title='Stop'></a>";
					 $client_system="<img src='./img/icon_power_gray.png' border=0 title='Poweroff'> <img src='./img/icon_reboot_gray.png' border=0 title='Reboot'> <a href=\"$PHP_SELF" . "uid=$uid&p=billing&show=$client_id&sid=$sid#$client_id\" onClick=\"return confirm('Tampilkan Billing pada $client_name ?')\"><img src='./img/icon_show.png' border=0 title='Show'></a>";
					if($client_id ==1) $client_system = '';

                    
					
				break;

                case 2:
				
					 $bgcolor='#FFFFFF';
					 
						if($setting_layout=='standard')
						{
							$image_client="<img src='./img/client_stop.png' border=0><br>";
						} else
						{
							$image_client="";
						}

                     $client_name_link="<a href='javascript:void(0);' onclick=\"javascript:popup_win('$screenshot_location?uid=$uid&shot=$client_ip&sid=$sid','screenshot_$client_id','scrollbars=yes,status=yes,width=" . ($setting_screenshot_width + 30) . ",height=" . ($setting_screenshot_height + 60) . "')\">" . $image_client . "$client_name</a>";

		     $lapid=mysql_result(mysql_query("SELECT laporan_id FROM laporan WHERE laporan_client=$client_id AND laporan_start='$client_start' AND laporan_end='$client_end'"),0);

                     $cmd="<a href=\"$PHP_SELF" . "uid=$uid&p=billing&reset=$client_id&sid=$sid#$client_id\" onClick=\"return confirm('RESET $client_name ?')\"><img src='./img/icon_reset.png' border=0 title='Reset'></a> <a href='javascript:void(0);' onclick=\"popup_win('$print_bill_location?uid=$uid&lapid=$lapid&sid=$sid','bill_$client_id','scrollbars=yes,status=yes,width=" . "300" . ",height=" . "400" . "')\"><img src='./img/icon_print.png' border=0 title='Print Nota'></a> <a href='javascript:void(0);' onclick=\"popup_win('$print_product_location?uid=$uid&lapid=$lapid&sid=$sid','product_$client_id','scrollbars=yes,status=yes,width=" . "600" . ",height=" . "400" . "')\"><img src='./img/icon_tambah.png' border=0 title='Tambah Pembelian'></a>";
                     
					 $highlight="bgcolor=\"#FFFF00\"";
					 
					 $client_system="<a href=\"$PHP_SELF" . "uid=$uid&p=billing&poweroff=$client_id&sid=$sid#status\" onClick=\"return confirm('Anda yakin ingin me-MATIKAN $client_name ?')\"><img src='./img/icon_power.png' border=0 title='Poweroff'></a> <a href=\"$PHP_SELF" . "uid=$uid&p=billing&reboot=$client_id&sid=$sid#status\" onClick=\"return confirm('Anda yakin ingin me-RESTART $client_name ?')\"><img src='./img/icon_reboot.png' border=0 title='Reboot'></a> <a href=\"$PHP_SELF" . "uid=$uid&p=billing&show=$client_id&sid=$sid#$client_id\" onClick=\"return confirm('Tampilkan Billing pada $client_name ?')\"><img src='./img/icon_show.png' border=0 title='Show'></a>";
					if($client_id ==1) $client_system = '';
                
					
				break;
        }

		if($setting_layout=='standard' && $client_id <> 1)
		{
			$image_desktop="<img src='./img/client/client_distro_" . $client_desktop . ".png'>";
		} else
		{
			$image_desktop="";
		}
		flush();

		if($setting_screenshot_status <> 1) $client_name_link = $image_client . $client_name;
		if($client_id ==1) $client_name_link = $client_name;

        ?>

        <tr bgcolor="<?php  echo $bgcolor?>">
        <td><?php  echo $cmd?></td>
        <td align="center" <?php  echo $highlight?>><table width="100%"  border="0" cellspacing="0" cellpadding="0"><tr><td><?php  echo $client_name_link?></td><td width="25" valign="top"><a name="<?php  echo $client_id?>"><?php  echo $image_desktop?></td></tr></table></td>
        <td align="right" <?php  echo $highlight?>><div id="<?php  echo 'price'.$client_id?>">
<?php 
if($client_start && $client_status > 0)
{
	//echo number_format($setting_minimum_price,0, ",",".");
	echo "";
} else
{
	echo "";
}

?>
</div></td>
		<td align="right"><div id="<?php  echo 'client'.$client_id?>">&nbsp;</div></td>
		<td align="right"><?php if($client_start && $client_status > 0) echo $client_start2?></td>
        <td align="center"><?php  echo $client_system?></td>

        <?php 
}
?>
</table>
<script type="text/javascript">
<!-- panggil rutin penghitungan billing!
	setTimeout('showTime()', <?php  echo $setting_refresh*1000?>);
//-->
</script>

<form action="<?php  echo $operator_file_name?>?uid=<?php  echo $uid?>&p=billing&sid=<?php  echo $sid?>" method="post">
  <div align="right"><br>
    layout : 
      <select name="setting_layouts" id="setting_layouts">
      <option value="standard" <?php if($setting_layout=='standard') echo 'selected'?>>Standard</option>
      <option value="simple" <?php if($setting_layout=='simple') echo 'selected'?>>Simple</option>
                </select>
    <input name="submit_layout" type="submit" id="submit_layout" value="Update!">
  </div>
</form>
<?php 
if($done)
{
mysql_query("UPDATE setting SET setting_refresh_tmp=NULL");
}
?>