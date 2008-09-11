<?php 
# Deskripsi:
# Menampilkan halaman untuk mensetting program billing, termasuk diantaranya mengatur tarif minimum, durasi, dsb

if(eregi("setting.php",$_SERVER['REQUEST_URI'])) die('hello script kiddies..');


if(@mysql_result(mysql_query("SELECT operator_id FROM operator WHERE operator_name='$uid'"),0) <> 1) #selain admin dilarang..
{
	mysql_close($connection);
	die('hello script kiddies..');
}
?>
<script language="JavaScript" src="collapse_expand_single_item.js"></script>

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><img src="./img/icon_setting.png" width="75" height="75" align="left"><h1>Setting</h1>
Di halaman ini Anda dapat dengan leluasa mengatur setting billing BiOS ;)
<br>
Jangan lupa baca panduan manual bila Anda menemukan kesulitan. </td>
    <td width="300"><h3><div id="billing_clock">Pkl. <?php  echo date("H:i:s - d M Y",mktime(gmdate("H")+$setting_timezone,gmdate("i"),gmdate("s"),gmdate("m"),gmdate("d"),gmdate("Y")))?></div>
    </h3> </td>
  </tr>
</table>
<hr>
<p>
<?php 
if($submit_update_setting)
{

			if($timezone =='' && $timezone2)
			{
				$timezone=$timezone2;
			} elseif($timezone =='' && $timezone2=='')
			{	
				$timezone="+0";
			}
			
			if($setting_receh <= 0 ) $setting_receh = 1;
			
			$result_pass=mysql_query("UPDATE setting SET setting_cafe_name='$setting_cafe_name', setting_cafe_address='$setting_cafe_address', setting_operator_operating_system='$setting_operator_operating_system', setting_domain_operator='$setting_domain_operator', setting_motd='$setting_motd', setting_refresh=$setting_refresh, setting_price_every_second=$setting_price_every_second, setting_receh=$setting_receh, setting_screenshot_status=$setting_screenshot_status, setting_screenshot_width=$setting_screenshot_width, setting_screenshot_height=$setting_screenshot_height, setting_screenshot_name='$setting_screenshot_name', setting_screenshot_folder='$setting_screenshot_folder', setting_screenshot_protocol='$setting_screenshot_protocol', setting_error_reporting=$pesan_error, setting_timezone='$timezone' LIMIT 1");

			$tarif = $_REQUEST['tarif'];
			$minimal = $_REQUEST['minimal'];
			$durasi = $_REQUEST['durasi'];
			
			for($n=0; $n < 24; $n++)
			{
				if($durasi[$n] >= 60) $durasi[$n] = 59;
			
				mysql_query("UPDATE tarif SET tarif_perjam='$tarif[$n]', tarif_min='$minimal[$n]', tarif_min_durasi='$durasi[$n]' WHERE tarif_pkl=$n");
			}
			
			if($result_pass)
			{
				echo "<h4><img src=\"./img/icon_ok.gif\" align=left>SETTING DIUPDATE!</h4>";
			} else
			{
			
				echo mysql_error();
					
				echo "<h4><img src=\"./img/icon_error.gif\" align=left>Error! ada kegagalan tampaknya .. Silahkan ulangi!</h4>";
			}

	
		echo "<p><a href=\"$operator_file_name" . "?uid=$uid&p=setting&sid=$sid\">Kembali ..</a>";
		
} else{


?>
<table width=100% border=0 cellpadding=0 cellspacing=0>
    <tr valign=top>
    <td width=35%>   
    <p>
    <form action="<?php  echo "$operator_file_name" . "?uid=$uid&p=setting&sid=$sid"?>" method=post>
      <table width="100%"  border="0" cellspacing="0" cellpadding="5">
        <tr valign="top">
          <td width="50%" class="vr"><h2><strong>Umum </strong></h2>
            <p>Nama Warnet :<br>
                <input name="setting_cafe_name" type="text" value="<?php  echo $setting_cafe_name?>" size="50">
</p>
            <p>Alamat:<br>
              <input name="setting_cafe_address" type="text" value="<?php  echo $setting_cafe_address?>" size="50">
</p>
            <p>
			<p>Sistem operasi Operator
                <select name="setting_operator_operating_system" id="select">
                  <option value="lin" <?php if($setting_operator_operating_system=='lin') echo 'selected'?>>GNU/Linux</option>
                  <option value="win" <?php if($setting_operator_operating_system=='win') echo 'selected'?>>Windows</option>
                </select>
            </p>
            <img src="./img/u.gif" name="imgfive" width="9" height="9" border="0" > <a href="javascript:void(0);" onClick="shoh('five');" >Motd ..</a>
            <div style="display: none;" id="five" >
			</p>            <p>MOTD: (pesan ini akan ditampilkan di billing client) <br>
<textarea name="setting_motd" cols="40" rows="3" id="textarea">
<?php  echo $setting_motd?>
</textarea>
            </p>
</div>
            <hr>
            <h2>Timezone</h2>
            <p>Silahkan pilih lokasi waktu Anda:<br>
              <select name="timezone" id="timezone">
                <option value="+7" <?php if($setting_timezone=='+7') echo 'selected'?>>Waktu Indonesia Barat (WIB) - GMT +7</option>
                <option value="+8" <?php if($setting_timezone=='+8') echo 'selected'?>>Waktu Indonesia Tengah (WITA) - GMT +8</option>
                <option value="+9" <?php if($setting_timezone=='+9') echo 'selected'?>>Waktu Indonesia Timur (WIT) - GMT +9</option>
                <option value="" <?php if($setting_timezone<>'+7' && $setting_timezone<>'+8' && $setting_timezone<>'+9' ) echo 'selected'?>>Lainnya..</option>
              </select> 
            </p><p>
            <img src="./img/u.gif" name="imgfour" width="9" height="9" border="0" > <a href="javascript:void(0);" onClick="shoh('four');" >Timezone lainnya ..</a>
            <div style="display: none;" id="four" >
            <p>Bila Anda berada pada time zone lain, <br>
              silahkan set manual selisih 
              waktu Anda dengan <a href="http://id.wikipedia.org/wiki/GMT" target="_blank">GMT</a> :<br> 
              GMT 
              <input name="timezone2" type="text" id="timezone2" <?php if($setting_timezone<>'+7' && $setting_timezone<>'+8' && $setting_timezone<>'+9' ) echo "value=\"$setting_timezone\""?>>
jam | contoh: +12 </p></div>
            <hr>
            <h2><strong>Screenshot</strong></h2>
              <select name="setting_screenshot_status">
                <option value="1" <?php if($setting_screenshot_status=='1') echo 'selected'?>>Aktifkan</option>
                <option value="0" <?php if(!$setting_screenshot_status) echo 'selected'?>>Matikan</option>
              </select><p>
<img src="./img/u.gif" name="imgtwo" width="9" height="9" border="0" >
<a href="javascript:void(0);" onClick="shoh('two');" >Detail Screenshot ..</a>
<div style="display: none;" id="two" >
<p>Resolusi:
                <input name="setting_screenshot_width" type="text" id="setting_screenshot_widht2" value="<?php  echo $setting_screenshot_width?>" size="10">
  x
  <input name="setting_screenshot_height" type="text" id="setting_screenshot_height2" value="<?php  echo $setting_screenshot_height?>" size="10">
  pixel (misal : 640 x 480) </p>
            <p>Screenshot name:
                <input name="setting_screenshot_name" type="text" id="setting_screenshot_name3" value="<?php  echo $setting_screenshot_name?>" size="20">
            </p>
            <p>Screenshot folder:
                <input name="setting_screenshot_folder" type="text" id="setting_screenshot_folder4" value="<?php  echo $setting_screenshot_folder?>" size="20">
            </p>
            <p>Protocol File Sharing yang digunakan: <br>
                <input name="setting_screenshot_protocol" type="text" id="setting_screenshot_protocol3" value="<?php  echo $setting_screenshot_protocol?>" size="30">
                <br>
  (kosongkan bila Anda ragu..) </p>
  </div>
            </td>
          <td><h2><strong>Tarif</strong></h2>
            <table width="100%"  border="0" cellspacing="0" cellpadding="5">
              <tr valign="top">
                <td width="50%"><p>Bulatkan tarif keatas dengan <br>
  pecahan paling kecil (Rp.) <br>
  <input name="setting_receh" type="text" value="<?php  echo $setting_receh?>" size="30">
</p>
                  <p>Charge pemakaian setiap<br>
                    <input name="setting_price_every_second" type="text" id="setting_price_every_second" value="<?php  echo $setting_price_every_second?>" size="10">
detik </p></td>
                <td><p>Refresh durasi waktu setiap<br>
                      <input name="setting_refresh" type="text" id="setting_refresh3" value="<?php  echo $setting_refresh?>" size="5">
  detik </p>
                  </td>
              </tr>
            </table>            
            <br>
            <img src="./img/u.gif" name="imgfirst" width="9" height="9" border="0" > <a href="javascript:void(0);" onClick="shoh('first');" ><strong>Setting Tarif ..</strong></a>
         <div style="display: none;" id="first" ><p>   
  <?php 
  #tampilkan zona tarif
  $data_tarif=mysql_query("SELECT tarif_perjam, tarif_min, tarif_min_durasi FROM tarif ORDER BY tarif_pkl");
  
  while($isi_data_tarif=mysql_fetch_array($data_tarif))
  {
  	$tarif[]=$isi_data_tarif[tarif_perjam];
	$minimal[]=$isi_data_tarif[tarif_min];
	$durasi[]=$isi_data_tarif[tarif_min_durasi];
  }
  
  
  ?>            <table width="100%"  border="0" cellpadding="5" cellspacing="1" bgcolor="#666666">
              <tr bgcolor="#CCCCCC">
                <td width="130"><strong>Pukul</strong></td>
                <td align="right"><strong>Tarif /jam (Rp.) </strong></td>
                <td align="right"><strong>Tarif Minimal (Rp.) </strong></td>
                <td align="right"><strong>Durasi Minimal (menit) (max. 59') </strong></td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td>00.00 - 00.59</td>
                <td align="right"><input name="tarif[0]" type="text" value="<?php  echo $tarif[0]?>" size="10"></td>
                <td align="right"><input name="minimal[0]" type="text" value="<?php  echo $minimal[0]?>" size="10"></td>
                <td align="right"><input name="durasi[0]" type="text" value="<?php  echo $durasi[0]?>" size="10"></td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td>01.00 - 01.59</td>
                <td align="right"><input name="tarif[1]" type="text" value="<?php  echo $tarif[1]?>" size="10"></td>
                <td align="right"><input name="minimal[1]" type="text" value="<?php  echo $minimal[1]?>" size="10"></td>
                <td align="right"><input name="durasi[1]" type="text" value="<?php  echo $durasi[1]?>" size="10"></td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td>02.00 - 02.59</td>
                <td align="right"><input name="tarif[2]" type="text" value="<?php  echo $tarif[2]?>" size="10"></td>
                <td align="right"><input name="minimal[2]" type="text" value="<?php  echo $minimal[2]?>" size="10"></td>
                <td align="right"><input name="durasi[2]" type="text" value="<?php  echo $durasi[2]?>" size="10"></td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td>03.00 - 03.59</td>
                <td align="right"><input name="tarif[3]" type="text" value="<?php  echo $tarif[3]?>" size="10"></td>
                <td align="right"><input name="minimal[3]" type="text" value="<?php  echo $minimal[3]?>" size="10"></td>
                <td align="right"><input name="durasi[3]" type="text" value="<?php  echo $durasi[3]?>" size="10"></td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td>04.00 - 04.59</td>
                <td align="right"><input name="tarif[4]" type="text" value="<?php  echo $tarif[4]?>" size="10"></td>
                <td align="right"><input name="minimal[4]" type="text" value="<?php  echo $minimal[4]?>" size="10"></td>
                <td align="right"><input name="durasi[4]" type="text" value="<?php  echo $durasi[4]?>" size="10"></td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td>05.00 - 05.59</td>
                <td align="right"><input name="tarif[5]" type="text" value="<?php  echo $tarif[5]?>" size="10"></td>
                <td align="right"><input name="minimal[5]" type="text" value="<?php  echo $minimal[5]?>" size="10"></td>
                <td align="right"><input name="durasi[5]" type="text" value="<?php  echo $durasi[5]?>" size="10"></td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td>06.00 - 06.59</td>
                <td align="right"><input name="tarif[6]" type="text" value="<?php  echo $tarif[6]?>" size="10"></td>
                <td align="right"><input name="minimal[6]" type="text" value="<?php  echo $minimal[6]?>" size="10"></td>
                <td align="right"><input name="durasi[6]" type="text" value="<?php  echo $durasi[6]?>" size="10"></td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td>07.00 - 07.59</td>
                <td align="right"><input name="tarif[7]" type="text" value="<?php  echo $tarif[7]?>" size="10"></td>
                <td align="right"><input name="minimal[7]" type="text" value="<?php  echo $minimal[7]?>" size="10"></td>
                <td align="right"><input name="durasi[7]" type="text" value="<?php  echo $durasi[7]?>" size="10"></td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td>08.00 - 08.59</td>
                <td align="right"><input name="tarif[8]" type="text" value="<?php  echo $tarif[8]?>" size="10"></td>
                <td align="right"><input name="minimal[8]" type="text" value="<?php  echo $minimal[8]?>" size="10"></td>
                <td align="right"><input name="durasi[8]" type="text" value="<?php  echo $durasi[8]?>" size="10"></td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td>09.00 - 09.59</td>
                <td align="right"><input name="tarif[9]" type="text" value="<?php  echo $tarif[9]?>" size="10"></td>
                <td align="right"><input name="minimal[9]" type="text" value="<?php  echo $minimal[9]?>" size="10"></td>
                <td align="right"><input name="durasi[9]" type="text" value="<?php  echo $durasi[9]?>" size="10"></td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td>10.00 - 10.59</td>
                <td align="right"><input name="tarif[10]" type="text" value="<?php  echo $tarif[10]?>" size="10"></td>
                <td align="right"><input name="minimal[10]" type="text" value="<?php  echo $minimal[10]?>" size="10"></td>
                <td align="right"><input name="durasi[10]" type="text" value="<?php  echo $durasi[10]?>" size="10"></td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td>11.00 - 11.59</td>
                <td align="right"><input name="tarif[11]" type="text" value="<?php  echo $tarif[11]?>" size="10"></td>
                <td align="right"><input name="minimal[11]" type="text" value="<?php  echo $minimal[11]?>" size="10"></td>
                <td align="right"><input name="durasi[11]" type="text" value="<?php  echo $durasi[11]?>" size="10"></td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td>12.00 - 12.59</td>
                <td align="right"><input name="tarif[12]" type="text" value="<?php  echo $tarif[12]?>" size="10"></td>
                <td align="right"><input name="minimal[12]" type="text" value="<?php  echo $minimal[12]?>" size="10"></td>
                <td align="right"><input name="durasi[12]" type="text" value="<?php  echo $durasi[12]?>" size="10"></td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td>13.00 - 13.59</td>
                <td align="right"><input name="tarif[13]" type="text" value="<?php  echo $tarif[13]?>" size="10"></td>
                <td align="right"><input name="minimal[13]" type="text" value="<?php  echo $minimal[13]?>" size="10"></td>
                <td align="right"><input name="durasi[13]" type="text" value="<?php  echo $durasi[13]?>" size="10"></td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td>14.00 - 14.59</td>
                <td align="right"><input name="tarif[14]" type="text" value="<?php  echo $tarif[14]?>" size="10"></td>
                <td align="right"><input name="minimal[14]" type="text" value="<?php  echo $minimal[14]?>" size="10"></td>
                <td align="right"><input name="durasi[14]" type="text" value="<?php  echo $durasi[14]?>" size="10"></td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td>15.00 - 15.59</td>
                <td align="right"><input name="tarif[15]" type="text" value="<?php  echo $tarif[15]?>" size="10"></td>
                <td align="right"><input name="minimal[15]" type="text" value="<?php  echo $minimal[15]?>" size="10"></td>
                <td align="right"><input name="durasi[15]" type="text" value="<?php  echo $durasi[15]?>" size="10"></td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td>16.00 - 16.59</td>
                <td align="right"><input name="tarif[16]" type="text" value="<?php  echo $tarif[16]?>" size="10"></td>
                <td align="right"><input name="minimal[16]" type="text" value="<?php  echo $minimal[16]?>" size="10"></td>
                <td align="right"><input name="durasi[16]" type="text" value="<?php  echo $durasi[16]?>" size="10"></td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td>17.00 - 17.59</td>
                <td align="right"><input name="tarif[17]" type="text" value="<?php  echo $tarif[17]?>" size="10"></td>
                <td align="right"><input name="minimal[17]" type="text" value="<?php  echo $minimal[17]?>" size="10"></td>
                <td align="right"><input name="durasi[17]" type="text" value="<?php  echo $durasi[17]?>" size="10"></td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td>18.00 - 18.59</td>
                <td align="right"><input name="tarif[18]" type="text" value="<?php  echo $tarif[18]?>" size="10"></td>
                <td align="right"><input name="minimal[18]" type="text" value="<?php  echo $minimal[18]?>" size="10"></td>
                <td align="right"><input name="durasi[18]" type="text" value="<?php  echo $durasi[18]?>" size="10"></td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td>19.00 - 19.59</td>
                <td align="right"><input name="tarif[19]" type="text" value="<?php  echo $tarif[19]?>" size="10"></td>
                <td align="right"><input name="minimal[19]" type="text" value="<?php  echo $minimal[19]?>" size="10"></td>
                <td align="right"><input name="durasi[19]" type="text" value="<?php  echo $durasi[19]?>" size="10"></td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td>20.00 - 20.59</td>
                <td align="right"><input name="tarif[20]" type="text" value="<?php  echo $tarif[20]?>" size="10"></td>
                <td align="right"><input name="minimal[20]" type="text" value="<?php  echo $minimal[20]?>" size="10"></td>
                <td align="right"><input name="durasi[20]" type="text" value="<?php  echo $durasi[20]?>" size="10"></td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td>21.00 - 21.59</td>
                <td align="right"><input name="tarif[21]" type="text" value="<?php  echo $tarif[21]?>" size="10"></td>
                <td align="right"><input name="minimal[21]" type="text" value="<?php  echo $minimal[21]?>" size="10"></td>
                <td align="right"><input name="durasi[21]" type="text" value="<?php  echo $durasi[21]?>" size="10"></td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td>22.00 - 22.59</td>
                <td align="right"><input name="tarif[22]" type="text" value="<?php  echo $tarif[22]?>" size="10"></td>
                <td align="right"><input name="minimal[22]" type="text" value="<?php  echo $minimal[22]?>" size="10"></td>
                <td align="right"><input name="durasi[22]" type="text" value="<?php  echo $durasi[22]?>" size="10"></td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td>23.00 - 23.59</td>
                <td align="right"><input name="tarif[23]" type="text" value="<?php  echo $tarif[23]?>" size="10"></td>
                <td align="right"><input name="minimal[23]" type="text" value="<?php  echo $minimal[23]?>" size="10"></td>
                <td align="right"><input name="durasi[23]" type="text" value="<?php  echo $durasi[23]?>" size="10"></td>
              </tr>
            </table>            <p>
              <input name="submit_update_setting" type=submit value="Update!">
            </p></div>
  <hr>            <h2><strong>Sekuriti</strong></h2>
            <p>Hanya ijinkan akses control panel dari IP:<br>
  misal: 192.168.0.2 - isi dengan IP Operator<br>(bila lebih dari 1 IP, pisahkan dengan koma)<br>
  <input name="setting_domain_operator" type="text" id="setting_domain_operator2" value="<?php  echo $setting_domain_operator?>" size="60">
</p>

            <img src="./img/u.gif" name="imgthree" width="9" height="9" border="0" > <a href="javascript:void(0);" onClick="shoh('three');" >Error Reporting ..</a>
            <div style="display: none;" id="three" >

            <h3>Error Reporting</h3>
            <p>Silahkan pilih apakah bila terjadi error / kesalahan pada program php, peringatan akan ditampilkan atau tidak. Bila Anda menginstall di mesin produksi, sebaiknya Anda sembunyikan pesan error yang ada demi keamanan.</p>
            <p>
              <input name="pesan_error" type="radio" value="1" <?php if($setting_error_reporting ==1) echo 'checked'?>>
  Matikan sama sekali<br>
  <input name="pesan_error" type="radio" value="2" <?php if($setting_error_reporting ==2) echo 'checked'?>>
  Hanya tampilkan error penting saja<br>
  <input name="pesan_error" type="radio" value="3" <?php if($setting_error_reporting ==3) echo 'checked'?>>
  Tampilkan seluruh error termasuk peringatan ringan <br> 
            </p>       </div>     </td>
        </tr>
      </table>
      <hr>
      <p align="center"> 
        <input name="submit_update_setting" type=submit id="submit_update_setting" value="Update!">
      </p>
      </form>    
    <p>&nbsp;</p></td>
</table>
<?php }?>