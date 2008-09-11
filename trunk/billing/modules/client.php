<?php
# Deskripsi:
# Menampilkan halaman untuk menambah / mengurangi / mengedit daftar client komputer yang ada.
 
if(eregi("client.php",$_SERVER['REQUEST_URI'])) die('hello script kiddies..');

if(@mysql_result(mysql_query("SELECT operator_id FROM operator WHERE operator_name='$uid'"),0) <> 1) #selain admin dilarang..
{
	mysql_close($connection);
	die('hello script kiddies..');
}
?>
<script language="JavaScript" src="collapse_expand_single_item.js"></script>
<h1><img src="./img/icon_client.png" width="75" height="75" align="left">Client</h1>
<p>Gunakan modul ini untuk menambah / mengedit daftar client di warnet Anda.. 
<hr>
<?php 
if($submit_tambah_client && $client_name && $client_ip && $client_login)
{
  
    $result=mysql_query("INSERT INTO client (client_ip,client_name,client_login,client_desktop) VALUES ('$client_ip','$client_name','$client_login','$client_desktop')");

    echo mysql_error();

    if($result)
    {
        echo "<h4><img src=\"./img/icon_ok.gif\" align=left>Client -- <b>$client_name</b> -- SUKSES ditambahkan!</h4>";
    } else
    {
        echo "<h4><img src=\"./img/icon_error.gif\" align=left>Error! ada kegagalan tampaknya .. Silahkan ulangi!</h4>";
    }

    echo "<p><a href=\"$operator_file_name" . "?uid=$uid&p=client&sid=$sid\">Kembali ..</a>";
} elseif($hapus && $hapus <>1)
{
    $result=mysql_query("DELETE FROM client WHERE client_id=$hapus LIMIT 1");
    
    echo mysql_error();
    
    if($result)
    {
        echo "<h4><img src=\"./img/icon_ok.gif\" align=left>Client BERHASIL DIHAPUS!</h4>";
    } else
    {
        echo "<h4><img src=\"./img/icon_error.gif\" align=left>Error! ada kegagalan tampaknya .. Silahkan ulangi!</h4>";
    }

    echo "<p><a href=\"$operator_file_name" . "?uid=$uid&p=client&sid=$sid\">Kembali ..</a>";

} elseif($edit && $edit<>1)
{
   
    
    if(!$confirm)
    {
        $data_client=mysql_query("SELECT client_id,client_name,client_login, client_desktop, client_ip FROM client WHERE client_id=$edit");
        $isi_data_client=mysql_fetch_array($data_client);
    ?>
        <form action="<?php  echo "$operator_file_name" . "?uid=$uid&p=client&sid=$sid"?>" method=post>
		  <p>
		<input type="hidden" name="confirm" value="baliwae">
		<input type="hidden" name="edit" value="<?php  echo $edit?>">
        Nama Client:<br> 
        <input name="client_name" type=text maxlength=255 size=50 value="<?php  echo $isi_data_client[client_name]?>">
</p>
		  <p>Login username :<br>
            <input name="client_login" type=text id="client_login" value="<?php  echo $isi_data_client[client_login]?>" size=50 maxlength=255>
          </p>
		  <p>IP Client:<br> 
		    <input name="client_ip" type=text maxlength=255 size=50 value="<?php  echo $isi_data_client[client_ip]?>">
</p>
		  <p>            
		    Desktop:<br>
		    <select name="client_desktop">

	<?php
	$handle=opendir('./img/client/');
	$isidir="";
	$tipefile="";
	$isitmp="";

	while ($isidir=readdir($handle))
	{
		$isitmp[]=$isidir;
	}
	
	sort($isitmp);	

	

	foreach($isitmp as $isi)
	{
		if ($isi<>"." && $isi<>".." && eregi('client_distro_',$isi))
		{
			$isi=eregi_replace('client_distro_','',$isi);
			$isi=eregi_replace('.png','',$isi);
		
		?>
			<option value="<?php echo $isi?>" <?php if($isi_data_client[client_desktop]==$isi) echo 'selected'?>>
			<?php echo strtolower("$isi")?>
			</option>
		<?php
		}
	}
	
	?>
            </select>

        <input name="submit_update_client" type=submit value="Update!">
		</p>
</form>

    <?php 
    } else
    {
        $result=mysql_query("UPDATE client SET client_ip='$client_ip', client_name='$client_name',client_login='$client_login',client_desktop='$client_desktop' WHERE client_id=$edit");

        echo mysql_error();
        
        if($result)
        {
            echo "<h4><img src=\"./img/icon_ok.gif\" align=left>Client -- <b>$client_name</b> -- SUKSES diupdate!</h4>";
        } else
        {
            echo "<h4><img src=\"./img/icon_error.gif\" align=left>Error! ada kegagalan tampaknya .. Silahkan ulangi!</h4>";
        }
    }
	
	echo "<p><a href=\"$operator_file_name" . "?uid=$uid&p=client&sid=$sid\">Kembali ..</a><p>";
} else{
?>
<img src="./img/u.gif" name="imgfirst" width="9" height="9" border="0" >
<a href="javascript:void(0);" onClick="shoh('first');" >Tambah Client ..</a>
<div style="display: none;" id="first" >

    <p>
    <form action="<?php  echo "$operator_file_name" . "?uid=$uid&p=client&sid=$sid"?>" method=post>
      <p>Nama Client:<br> 
        <input name="client_name" type=text class=form_flat id="client_name" size=40 maxlength=255>
    </p>
      <p>Login username:<br>
          <input name="client_login" type=text class=form_flat value="guest" size=40 maxlength=255>
      </p>
      <p>        IP Client:<br> 
        <input name="client_ip" type=text class=form_flat id="client_ip" value="192.168.0." size=40 maxlength=255>
</p>
      <p>        Desktop:        <br>
        <select name="client_desktop">

	<?php
	$handle=opendir('./img/client/');
	$isidir="";
	$tipefile="";
	$isitmp="";

	while ($isidir=readdir($handle))
	{
		$isitmp[]=$isidir;
	}
	
	sort($isitmp);	

	

	foreach($isitmp as $isi)
	{
		if ($isi<>"." && $isi<>".." && eregi('client_distro_',$isi))
		{
			$isi=eregi_replace('client_distro_','',$isi);
			$isi=eregi_replace('.png','',$isi);
		
		?>
			<option value="<?php echo $isi?>">
			<?php echo strtolower("$isi")?>
			</option>
		<?php
		}
	}
	
	?>

        </select>
<input name="submit_tambah_client" type=submit class=form_flat value="Tambah!">
      </p>
</form>
</div>
<hr>
        Total Client:
        <?php $total=mysql_result(mysql_query("SELECT count(*) FROM client"),0);echo $total?>
        <p>
          <?php 

    ################ halaman #####################################################
    #jumlah maksimal per halaman
    #ganti disini (1 dari 2) , jangan lupa lihat total diatas!!
    $pages_view=25;

    #klo fromnya ga ada diisi default dari 0 (awal)
    if(!isset($fr)) $fr=0;

    #buletin ke atas
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
            echo "<a href=\"$operator_file_name" . "?uid=$uid&p=client&sid=$sid&fr=$from\">$n</a>| ";
        }
    }
    ################ halaman end ############

?>
<table width=100% cellspacing=1 cellpadding=3 bgcolor='#999999' border=0>
<tr bgcolor='#E9E9E9'>
<td width="100" class=small>:::</td>
<td width="20" class=small>ID.</td>
<td class=small>Nama Client</td>
<td class=small>Login Username</td>
<td class=small>Client IP</td>
</tr>
<?php 
    $data_client=mysql_query("SELECT client_id, client_name, client_login, client_desktop, client_ip FROM client ORDER BY client_name ASC LIMIT $fr,$pages_view");
    while($isi_data_client=mysql_fetch_array($data_client))
    {
	
		$isi_data_client[client_desktop] =  "<img src=\"./img/client/client_distro_" . $isi_data_client[client_desktop] . ".png\">";
		if($isi_data_client[client_id] ==1) $isi_data_client[client_desktop]='';
		if($isi_data_client[client_id]==1)
		{
			$client_link='';
		} else
		{
			$client_link="<a href=\"$operator_file_name" . "?uid=$uid&p=client&sid=$sid&edit=$isi_data_client[client_id]\"><img src='./img/icon_edit.png' border=0 title='Edit'></a> <a href=\"$operator_file_name" . "?uid=$uid&p=client&sid=$sid&hapus=$isi_data_client[client_id]\" onClick=\"return confirm('ANDA YAKIN HAPUS? " . ereg_replace("\"","",$isi_data_client[client_name]) . " ..')\"><img src='./img/icon_delete.png' border=0 title='Delete'></a>";
		}
		
        print "<tr bgcolor='#FFFFFF'>
                   <td>$client_link</td>
                   <td>$isi_data_client[client_id]</td>
                   <td>$isi_data_client[client_desktop] $isi_data_client[client_name]</td>
				   <td>$isi_data_client[client_login]</td>
				   <td>$isi_data_client[client_ip]</td>
               </tr>\n";
    }

?>
</table>
<?php }?>
<p><a href="#top">Atas</a></p>
