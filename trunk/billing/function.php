<?php

include_once('./bios-config.php');

if($q=='getduration')
{
	header('Content-type: text/xml'); #tampilkan sebagai file xml

	# set status old
	$var_refresh =mysql_result(mysql_query("SELECT setting_refresh_tmp FROM setting"),0);
	if($setting_status_old)
	{
		$data_status=mysql_query("SELECT client_id, client_status FROM client");
		while($isi_data_status=mysql_fetch_array($data_status))
		{
			$status_old = @mysql_result(mysql_query("SELECT client_status_old FROM client_status WHERE client_status_id=$isi_data_status[client_id]"),0);
			

			if($status_old != $isi_data_status[client_status] && $uid) 
			{
				$sids=@mysql_result(mysql_query("SELECT sid FROM session WHERE uid='$uid'"),0);
				#uncomment baris dibawah ini bila ingin mengunakan fitur autoload dengan memanfaatkan flash
				srand((double)microtime()*1000000);
				$var_refresh="<object width=1 height=1><param name=movie value='loader.swf?url=$operator_location?uid=$uid&p=billing&sid=$sids&done=" . md5(uniqid(rand())) . "$isi_data_status[client_id]'><embed src='loader.swf?url=$operator_location?uid=$uid&p=billing&sid=$sids&done=" . md5(uniqid(rand())) . "#$isi_data_status[client_id]' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash' width=1 height=1></embed></object>";
						    
				#uncomment baris dibawah ini bila ingin menggunakan fitur autoload dengan javascript
				#fitur ini tidak digunakan, karena javascript tidak bisa meload otomatis.. :( 25 feb
				/*$var_refresh="<script>window.location.href=\"$operator_location?uid=$uid&p=billing&sid=$sids&done=1\"</script>";*/
				
				$var_refresh=htmlentities($var_refresh);
				mysql_query("UPDATE setting SET setting_refresh_tmp='$var_refresh' LIMIT 1");
			}
			
			mysql_query("REPLACE INTO client_status VALUES ($isi_data_status[client_id],$isi_data_status[client_status])");
		}
	}

	//WHERE client_id=$_REQUEST[uid] LIMIT 1
	$data_client=mysql_query("SELECT * FROM client ORDER BY client_ip ASC");
	
	while($isi_data_client=mysql_fetch_array($data_client))
	{
		$total_biaya = 0;

        extract($isi_data_client,EXTR_OVERWRITE);

        switch($client_status)
        {
                case 0:
                     $time_duration=0;
                     $hour=0;
                     $minute=0;
                     $second=0;
                     break;

                case 1:
                     $time_start=strtotime("$client_start");
					 $client_end=date("Y-m-d H:i:s",mktime(gmdate("H")+$setting_timezone,gmdate("i"),gmdate("s"),gmdate("m"),gmdate("d"),gmdate("Y")));
                     $time_end=strtotime($client_end);
                     
                     $time_duration=$time_end - $time_start;
				
					
                     $hour=floor($time_duration/3600);
                     $minute=floor(($time_duration - $hour * 3600)/60);
                     $second=$time_duration - $hour*3600 - $minute*60;

					 break;

                case 2:
                     $time_start=strtotime("$client_start");
                     $time_end=strtotime("$client_end");
                     $time_duration=$time_end - $time_start;

                     $hour=floor($time_duration/3600);
                     $minute=floor(($time_duration - $hour * 3600)/60);
                     $second=$time_duration - $hour*3600 - $minute*60;
		     
		     # untuk total biaya non internet (client non internet)
		    	
		     	if($client_id == 1)
			{
				 $lapid=mysql_result(mysql_query("SELECT laporan_id FROM laporan WHERE laporan_client=1 ORDER BY laporan_id DESC LIMIT 1"),0);
				 $total_biaya=mysql_result(mysql_query("SELECT sum(ct_jumlah*ct_harga) FROM client_tambahan WHERE ct_laporan_id=$lapid"),0);
			}

                     break;
        }
		
		if($time_duration && $client_id <> 1)
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
								
		# CORE RUMUS PENGHITUNGAN BILLING - rumus kuno, yang hanya mendukung 1 model tarif dan tidak mendukung happy hours! basbang!
        // $price_pertime=$setting_priceperhour/(3600 / $setting_price_every_second);
		//$price = ceil($time_duration/ $setting_price_every_second) * $price_pertime;
		# 13 januari 2006
		# set harga minimum
		//if($price < $setting_minimum_price && $client_status <> 0)
		//{
			//$price = $setting_minimum_price;
		//}

		if($hour < 10) $hour="0$hour";
		if($minute < 10) $minute="0$minute";
		if($second < 10) $second="0$second";


		$dump_cetak = '';

		if($billing_debug_mode && $client_status <> 0)
		{
			
	
			foreach($dump_total_biaya as $cetak)
			{
				$dump_cetak = $dump_cetak . " | $cetak";
			}

			$dump_cetak .= " - TOTAL WAKTU=$total_used detik - Jumlah zona=$scan_zona - Selesai= $client_end";
		}

		if(!$show_second)
		{
			$second='';
		} else
		{
			$second = " : $second sec";
		}

		// . "$time_duration - $scan_zona - end: $zone_end -  $total_used - total durasi $used_duration - lama $total_biaya_lama
		//  . $dump_cetak . " - TOTAL=$total_used - zona=$scan_zona - $client_start
		$dumps.= "<client" . $client_id . ">\n";
		//if($client_id == "1") 
		//{
			//$dumps.= "<duration></duration>\n";
		//} else
		//{
			$dumps.= "<duration>$hour hr : $minute min $blink_notation $second</duration>\n";
		//}
		$dumps.= "<name>$client_name</name>\n";
		$dumps.= "<price>". number_format(ceil($total_biaya),0, ",",".") . $dump_cetak . "</price>\n";
		$dumps.= "</client" . $client_id . ">\n";

		$dump_total_biaya='';$total_used='';$scan_zona='';$client_end='';

	}

$billing_clock = "<setting><clock>Pkl. " . date("H:i:s - d M Y",mktime(gmdate("H")+$setting_timezone,gmdate("i"),gmdate("s"),gmdate("m"),gmdate("d"),gmdate("Y"))) . " $var_refresh</clock></setting>";

#info summary
#PATCH SP1
$zone_summary=date("G",mktime(gmdate("H")+$setting_timezone,gmdate("i"),gmdate("s"),gmdate("m"),gmdate("d"),gmdate("Y")));
$data_tarif_summary=mysql_fetch_array(mysql_query("SELECT tarif_perjam,tarif_min,tarif_min_durasi FROM tarif WHERE tarif_pkl=$zone_summary"));

$summary = "<summary><tarif_perjam>" . number_format($data_tarif_summary[tarif_perjam],0, ",",".") . "</tarif_perjam><tarif_min>" . number_format($data_tarif_summary[tarif_min],0, ",",".") . "</tarif_min><tarif_min_durasi>" . number_format($data_tarif_summary[tarif_min_durasi],0, ",",".") . "</tarif_min_durasi><tarif_refresh>$setting_price_every_second</tarif_refresh></summary>";

echo "<data>\n$dumps\n$billing_clock\n$summary</data>";

} elseif ($q == 'cekstatus' && $uid && ($sids=@mysql_result(mysql_query("SELECT sid FROM session WHERE uid='$uid'"),0)))
{
	header('Content-type: text/xml'); #tampilkan sebagai file xml
	# set status old
	if($setting_status_old && (date("s") % $setting_status_old))
	{
		$data_status=mysql_query("SELECT client_id, client_status FROM client");
		while($isi_data_status=mysql_fetch_array($data_status))
		{
			$status_old = @mysql_result(mysql_query("SELECT client_status_old FROM client_status WHERE client_status_id=$isi_data_status[client_id]"),0);

			if($status_old != $isi_data_status[client_status]) 
			{
				$var_refresh="<object width=100 height=100><param name=movie value='loader.swf?url=$operator_location?uid=$uid&p=billing&sid=$sids'><embed src='loader.swf?url=$operator_location?uid=$uid&p=billing&sid=$sids' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash' width=100 height=100></embed></object>";
			    $var_refresh=htmlentities($var_refresh);

			}
			
			mysql_query("REPLACE INTO client_status VALUES ($isi_data_status[client_id],$isi_data_status[client_status])");
		}
	}

	$var_refresh = "<setting><status>$var_refresh</status></setting>";
	echo $var_refresh;
}