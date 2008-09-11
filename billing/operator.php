<?php 
# Deskripsi:
# Halaman utama akses control panel website.

include_once('./bios-config.php');
$bios_versi='1.4B SP1 (BiOS = Life!)';
?>
<html><head><title>OPERATOR - <?php echo strtoupper($setting_cafe_name)?></title><link href="skin.css" rel="stylesheet" type="text/css">
<link rel="icon" href="./img/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="./img/favicon.ico" type="image/x-icon" />
</head>
<body><a name="top"></a>
<?php
#17 september
# FILE INI TIDAK PERLU DIEDIT - PLEASE DONT EDIT THIS FILE - MATUR SUKSME!!!
######################################################################################################

# Cek IP yang berhak mengakses
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

# Login 
if($submit_login && $login_member_id && $login_pass || ($uid && $sid))
{
	$operator_id=@mysql_result(mysql_query("SELECT operator_id FROM operator WHERE operator_name='$login_member_id' AND operator_password=md5('$login_pass') LIMIT 1"),0);

    if(!isset($uid) || !isset($sid))
    {
        if($operator_id)
        {

            if (!isset($sid))
            {
                srand((double)microtime()*1000000);
                $sid = md5(uniqid(rand()));
            }

            mysql_query("DELETE FROM session WHERE uid='$login_member_id'");
            mysql_query("INSERT INTO session(uid,sid) VALUES('$login_member_id','$sid')");

            $log_admin[ip]=@mysql_result(mysql_query("SELECT operator_last_ip FROM operator WHERE operator_id=$operator_id"),0);
			
	    #menghindar dari penggunaan unix_timestamp, karena default php akan mereset timezone ke waktu UTC nya bukan waktu lokal
            $log_admin[date]=@mysql_result(mysql_query("SELECT date_format(operator_last_date,'%d %b %Y - %H:%i:%s') FROM operator WHERE operator_id=$operator_id"),0);
            mysql_query("UPDATE operator SET operator_last_ip='$operator_ip', operator_last_date=NOW() WHERE operator_id=$operator_id LIMIT 1");

            $uid=$login_member_id;


        } else
        {
            echo "<h2>Oops.. not for script kiddies!</h2>";
            mysql_close($connection);
            die();
        }
    }



    @$result_cek=mysql_result(mysql_query("SELECT count(*) FROM session WHERE uid='$uid' AND sid='$sid'"),0);
    if(!$result_cek)
    {
        @mysql_close($connection);
        die ("<h3>Anda belum login!<br><a href=\"" . $_SERVER[PHP_SELF] . "\">login</a> ..</h3>");
    }

    if($p=='logout')
    {
                mysql_query("DELETE FROM session WHERE uid='$uid'");
                mysql_close($connection);
                die("<h3><img src='./img/icon_logout.png' align='left'> Matur suksme! Logout Sukses</h3> <a href=\"" . $_SERVER[PHP_SELF] . "\">Login ..</a> - <a href=\"http://toko.baliwae.com\">cuci mata, lihat pernak-pernik linux di toko.baliwae.com..</a>");
    }


    $data_operator=mysql_fetch_array(mysql_query("SELECT * FROM operator WHERE operator_name='$uid' LIMIT 1"));

    ##################################

    $PHP_SELF="$operator_file_name?";

    ##################################

    // Tampilkan menu, berdasarkan module yang ada..
    ?>
<p><b><?php if($data_operator[operator_id]==1) echo "<img src='./img/icon_admin.png' align='absmiddle'>"?> <?php echo strtoupper($uid)?> | IP: <?php echo "$operator_ip"?></b></p>
<table width="90%"  border="0" align="center" cellpadding="5" cellspacing="1" bgcolor="#999999">
  <tr>
    <td valign="top" bgcolor="#EFEFEF">

<a href="<?php echo $operator_file_name?>?uid=<?php echo $uid?>&sid=<?php echo $sid?>" title='Welcome Page'>Info</a> | 
<a href="<?php echo $operator_file_name?>?uid=<?php echo $uid?>&p=billing&sid=<?php echo $sid?>" title='Klik untuk melihat informasi billing client, dsb'>Billing</a> | 
<a href="<?php echo $operator_file_name?>?uid=<?php echo $uid?>&p=report&print_one_page=1&sid=<?php echo $sid?>" title='Klik untuk melihat laporan pemasukan'>Laporan</a> |
 
<?php
# Cetak menu yang khusus hanya dapat diakses oleh ADMIN saja..
if($data_operator[operator_id]==1)
{
?>
	<a href="<?php echo $operator_file_name?>?uid=<?php echo $uid?>&p=client&sid=<?php echo $sid?>" title='Klik untuk menambah/mengedit konfigurasi client warnet'> Client</a> | 
	<a href="<?php echo $operator_file_name?>?uid=<?php echo $uid?>&p=operator&sid=<?php echo $sid?>" title='Klik untuk menambah/mengedit hak akses operator, password, dsb'> Operator</a> |
	<a href="<?php echo $product_file_name?>?uid=<?php echo $uid?>&p=product&sid=<?php echo $sid?>" title='Klik untuk menambah/mengedit daftar produk yang dijual..'> Produk</a> | 
	<a href="<?php echo $operator_file_name?>?uid=<?php echo $uid?>&p=setting&sid=<?php echo $sid?>" title='Klik untuk mengatur program billing, seperti harga, security, dsb..'> Setting</a> |
	<a href="<?php echo $operator_file_name?>?uid=<?php echo $uid?>&p=infoserver&sid=<?php echo $sid?>" title='Informasi server'> Server</a> |
	<a href="<?php echo $operator_file_name?>?uid=<?php echo $uid?>&p=register&sid=<?php echo $sid?>" title='Daftarkan BiOS Anda sekarang juga!'> Registrasi Online</a> |

<?php
}
?>

<a href="<?php echo $PHP_SELF?>uid=<?php echo $uid?>&p=logout&sid=<?php echo $sid?>"><strong>LOGOUT</strong></a>
    </td>
  </tr>
  <tr>
    <td valign="top" bgcolor="#FFFFFF"><?php
	#Loading Module..
        switch($p)
        {
        case 'billing':
                include './modules/billing.php';
                break;
        case 'report';
                include './modules/report.php';
                break;
        case 'client';
                include './modules/client.php';
                break;
        case 'operator';
                include './modules/operators.php';
                break;
        case 'setting';
                include './modules/setting.php';
                break;
        case 'register';
                include './modules/register.php';
                break;
        case 'infoserver';
                include './modules/phpinfo.php';
                break;
        case 'product';
                include './modules/product.php';
                break;
        default:
        ###############################################?>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr valign="top">
            <td width="145"><img src="img/logo-bios-besar.jpg" width="145" height="166"></td>
            <td><p><strong>Billing Open Source (BiOS) Baliwae</strong> <font color='red'>versi <?php echo $bios_versi?></font> <br>
  jalan diatas mesin: <b>
  <?php echo addslashes(strip_tags($_SERVER[SERVER_SOFTWARE]))?>
  </b> <br>
  diakses menggunakan:
  <?php echo addslashes(strip_tags($_SERVER[HTTP_USER_AGENT]))?>
  <?php
# Sedikit propaganda :) bila si operator pake Window$.. otomatis muncul pesan agar segera beralih ke GNU/Linux..
if(eregi('windows', addslashes(strip_tags($_SERVER[HTTP_USER_AGENT]))))
{
	echo '<p><em>Hari gene belum beralih pake linux Om? duh kuno deh.. </em></p>';
}
?>
            </p>
              <p>
                <?php
#last record for ADMIN only
if($log_admin[ip])
{?>
              </p>
              <p>Login terakhir Anda dari IP:<u>
                <?php echo $log_admin[ip]?>
                </u><br>
  pada tanggal - <u>
  <?php echo $log_admin[date]?>
  </u>
  <?php }?>
              <p>Ingin ngobrol dengan developernya langsung? Silahkan chat via Y!M dibawah :)              
              <p>                <a href="ymsgr:sendIM?online_baliwae"><img src="http://opi.yahoo.com/online?u=online_baliwae&m=g&t=1" border="0"></a> - Y!M: online_baliwae <p>
</td>
          </tr>
        </table>
<!-- mohon untuk tidak menghapus atau mengedit bagian google adsense di bawah ini :) 
why? ya hitung hitung ini untuk donasi dan partisipasi anda secara tak langsung terhadap pengembangan program. 
//-->
<hr>
<script type="text/javascript"><!--
google_ad_client = "pub-5458666368713507";
//728x90, created 12/17/07
google_ad_slot = "5036748506";
google_ad_width = 728;
google_ad_height = 90;
//--></script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
<hr>
Merasa terbantu dengan adanya Billing Bebas Lisensi ini? <br>Silahkan kirim donasi via Paypal / Kartu Kredit melalui link dibawah ini..<p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
</form> 
<a href='http://bios.baliwae.com/donasi/'>Donasi lewat transfer bank silahkan klik disini..</a>

<!-- // mohon untuk tidak menghapus atau mengedit bagian google adsense di atas ini :) //-->


     

<?php }?> 
    </p></td>
  </tr>
  <tr>
    <td valign="top" bgcolor="#CCCCCC"><strong>&copy;2005-2008 Budi Baliwae</strong> | <img src="img/icon_gnu.png" width="22" height="22" align="absmiddle"> <a href="http://www.gnu.org/licenses/gpl.txt" target="_blank" title='Jaminan program Open Source'>Under GPL License</a> | 
    <img src="img/icon_update.png" width="22" height="22" align="absmiddle"> <a href="http://bios.googlecode.com" target="_blank" title='Cek update program'>Cek Update</a> | <img src="img/icon_donasi.png" width="22" height="22" align="absmiddle"> <a href="http://bios.baliwae.com/donasi" title='Dukung program ini dengan donasi Anda..' target="_blank">Donasi..</a> | <img src="img/icon_bug.png" width="22" height="22" align="absmiddle"> <a href="http://code.google.com/p/bios/issues/list" target="_blank" title='Menemukan bugs? Laporkan disini..'>Bug Report..</a> | <img src="img/icon_help.png" width="22" height="22" align="absmiddle"> <a href="http://code.google.com/p/bios/w/list" title='Klik untuk Dokumentasi Online' target="_blank">Dokumentasi.. </a> + <img src="img/icon_forum.png" width="22" height="22" align="absmiddle"> <a href="http://mandriva-user.or.id/forum/viewforum.php?f=25" title='Klik untuk Bantuan Online' target="_blank"><b>Tanya Jawab..</b></a></td>
  </tr>
</table>

<?php } else
{?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="128"><img src="img/welcome.png" width="96" height="96"></td>
    <td><form action="<?php echo $PHP_SELF?>" method="post" name="" id="">
      <p>
        <input name="login_member_id" type="text" id="login_member_id" value="">
      </p>
      <p>
        <input name="login_pass" type="password" id="login_pass">
        <input name="submit_login" type="submit" id="submit_login" value="    login     ">
      </p>
    </form></td>
  </tr>
</table>
<p>
<?php }
# Tutup koneksi database..
@mysql_close($connection);
?>
</body></html>