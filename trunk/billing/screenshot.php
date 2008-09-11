<?php 
# Deskripsi:
# Menampilkan screenshot layar dari client..

include_once('./bios-config.php');
?>
<html><head><title><?php  echo @mysql_result(mysql_query("SELECT client_name FROM client WHERE client_ip='$shot'"),0)?> - Screenshot</title>
<style type="text/css">
<!--
body {
        margin-left: 0px;
        margin-top: 0px;
        margin-right: 0px;
        margin-bottom: 0px;
}
-->
</style>
</head><body>
<script language='JavaScript'>
status='BiOS BALIWAE (c)2005-2008 BALIWAE.COM';
</script>
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

if($session_aktif=mysql_result(mysql_query("SELECT count(*) FROM session WHERE uid='$uid' AND sid='$sid'"),0) && $setting_screenshot_status)
{

              $shot_size="$setting_screenshot_width" . 'x' . "$setting_screenshot_height";

             #6 oktober 2:38 am
             #hehe.. aneh tapi nyata, gunakan langsung backtick, supaya variabel $ bisa dimasukin
             #kalo ditampung dulu dalam variabel dijamin ga bakal bisa.
             #ex: $tambahan = `sudo ssh -l root $_blablabalball..... --> dikasih $tambahan didepannya malah
             #ga bisa nampung gonta ganti variabel!!
			 
	      $setting_client_username=@mysql_result(mysql_query("SELECT client_login FROM client WHERE client_ip='$shot'"),0);

             `sudo ssh -l root $_REQUEST[shot] "su - $setting_client_username -c 'env DISPLAY=:0.0 xwd -silent -root -out .screen ; convert -resize $shot_size .screen ./$setting_screenshot_folder/$setting_screenshot_name'"`;

              $setting_screenshot_folder=eregi_replace("[\]",'',$setting_screenshot_folder);
              $setting_screenshot_folder=rawurlencode($setting_screenshot_folder);

			  if(!$setting_screenshot_protocol)
			  {
				  switch($setting_operator_operating_system)
				  {
						case 'lin':
							$setting_screenshot_protocol = "smb://";
							$img=$setting_screenshot_protocol . "$_REQUEST[shot]/$setting_screenshot_folder/$setting_screenshot_name";
							echo "<a href='$img' target=_blank>Lanjutkan disini</a><p><a href=\"$screenshot_location?uid=$_REQUEST[uid]&shot=$_REQUEST[shot]&sid=$_REQUEST[sid]\">Refresh</a>";

						break;
						
						case 'win':
							$setting_screenshot_protocol = "file://///";
							$img=$setting_screenshot_protocol . "$_REQUEST[shot]/$setting_screenshot_folder/$setting_screenshot_name";
							echo "<div align=center><a href=\"$screenshot_location?uid=$_REQUEST[uid]&shot=$_REQUEST[shot]&sid=$_REQUEST[sid]\"><img src=\"$img\" alt='screenshot not available' width='$setting_screenshot_width' height='$setting_screenshot_height' border='0' onmouseover=\"setstatus('Click to Refresh - BiOS BALIWAE');return document.value\"></a></div>";
						break;
					}
			   }
				
}
?>
<script language="JavaScript">
function setstatus(pesan) {
  status=pesan;
  document.value = true;
}
</script>
</body>
</html>
<?php @mysql_close($connection);?>