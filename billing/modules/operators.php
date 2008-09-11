<?php 
# Deskripsi:
# Menampilkan halaman untuk menambah, mengedit, atau mengurangi operator yang bekerja.

if(eregi("operators.php",$_SERVER['REQUEST_URI'])) die('hello script kiddies..');

if(@mysql_result(mysql_query("SELECT operator_id FROM operator WHERE operator_name='$uid'"),0) <> 1) #selain admin dilarang..
{
	mysql_close($connection);
	die('hello script kiddies..');
}

?>
<script language="JavaScript" src="collapse_expand_single_item.js"></script>
<h1><img src="./img/icon_operator.png" width="75" height="75" align="left">Operator</h1>
<p>Gunakan modul ini untuk menambah / mengedit karyawan operator di warnet Anda.. 
<hr>
<?php 
if($submit_tambah_operator && $operator_name && $operator_name_full && ($operator_password == $operator_password2))
{
  
    $result=mysql_query("INSERT INTO operator (operator_name,operator_name_full,operator_password,operator_edit_report ) VALUES ('$operator_name','$operator_name_full',MD5('$operator_password'),$operator_edit_report)");

    echo mysql_error();

    if($result)
    {
        echo "<h4><img src=\"./img/icon_ok.gif\" align=left>Operator -- <b>$operator_name_full ($operator_name)</b> -- SUKSES ditambahkan!</h4>";
    } else
    {
        echo "<h4><img src=\"./img/icon_error.gif\" align=left>Error! ada kegagalan tampaknya .. Silahkan ulangi!</h4>";
    }

    echo "<p><a href=\"$operator_file_name" . "?uid=$uid&p=operator&sid=$sid\">Kembali ..</a>";
} elseif($hapus && $hapus > 1)
{
    $result=mysql_query("DELETE FROM operator WHERE operator_id=$hapus LIMIT 1");
    
    echo mysql_error();
    
    if($result)
    {
        echo "<h4><img src=\"./img/icon_ok.gif\" align=left>Operator BERHASIL DIHAPUS!</h4>";
    } else
    {
        echo "<h4><img src=\"./img/icon_error.gif\" align=left>Error! ada kegagalan tampaknya .. Silahkan ulangi!</h4>";
    }

    echo "<p><a href=\"$operator_file_name" . "?uid=$uid&p=operator&sid=$sid\">Kembali ..</a>";

} elseif($edit)
{
    echo "<p><a href=\"$operator_file_name" . "?uid=$uid&p=operator&sid=$sid\">Kembali ..</a>";
    
    if(!$confirm)
    {
        $data_operator=mysql_query("SELECT operator_id,operator_name, operator_name_full,operator_edit_report FROM operator WHERE operator_id=$edit");
        $isi_data_operator=mysql_fetch_array($data_operator);
    ?>
        <form action="<?php  echo "$operator_file_name" . "?uid=$uid&p=operator&sid=$sid"?>" method=post>
		  <p>
		<input type="hidden" name="confirm" value="baliwae">
		<input type="hidden" name="edit" value="<?php  echo $edit?>">
        Nickname :<br> 
        <input name="operator_name" type=text maxlength=255 size=50 value="<?php  echo $isi_data_operator[operator_name]?>">
		</p>
		  <p>Nama Lengkap  :<br> 
		    <input name="operator_name_full" type=text maxlength=255 size=50 value="<?php  echo $isi_data_operator[operator_name_full]?>">
</p>
		  <p>		    Password<br>
            <input name="operator_password" type=password class=form_flat size=40 maxlength=255> 
            (kosongkan bila tidak ingin diganti)</p>
          <p>Retype-Password<br>
              <input name="operator_password2" type=password class=form_flat size=40 maxlength=255>
</p>
          <p>Dapat mengedit laporan hariannya<br>
            <select name="operator_edit_report">
              <option value="0" <?php if($isi_data_operator[operator_edit_report]==0) echo 'selected'?>>TIDAK (default)</option>
              <option value="1" <?php if($isi_data_operator[operator_edit_report]==1) echo 'selected'?>>YA</option>
            </select>
</p>
          <p>
              <input name="submit_update_operator" type=submit value="Update!">
          </p>
        </form>

    <?php 
    } elseif($operator_password == $operator_password2)
    {
        if($operator_password)
		{
			$result=mysql_query("UPDATE operator SET operator_name='$operator_name', operator_name_full='$operator_name_full', operator_password=MD5('$operator_password'), operator_edit_report=$operator_edit_report WHERE operator_id=$edit");
		} else
		{
			$result=mysql_query("UPDATE operator SET operator_name='$operator_name', operator_name_full='$operator_name_full', operator_edit_report=$operator_edit_report WHERE operator_id=$edit");
		
		}
        echo mysql_error();
        
        if($result)
        {
            echo "<h4><img src=\"./img/icon_ok.gif\" align=left>Client -- <b>$operator_name</b> -- SUKSES diupdate!</h4>";
        } else
        {
            echo "<h4><img src=\"./img/icon_error.gif\" align=left>Error! ada kegagalan tampaknya .. Silahkan ulangi!</h4>";
        }
    }
} else{
?>

<img src="./img/u.gif" name="imgfirst" width="9" height="9" border="0" >
<a href="javascript:void(0);" onClick="shoh('first');" >Tambah Operator ..</a>
<div style="display: none;" id="first" >


    <p>
    <form action="<?php  echo "$operator_file_name" . "?uid=$uid&p=operator&sid=$sid"?>" method=post>
      <p>Nickname::<br> 
        <input name="operator_name" type=text class=form_flat size=40 maxlength=255>
</p>
      <p>Nama Lengkap:<br>
        <input name="operator_name_full" type=text class=form_flat size=40 maxlength=255>
      </p>
      <p>Password<br>
        <input name="operator_password" type=password class=form_flat size=40 maxlength=255>
</p>
      <p>Retype-Password<br>
        <input name="operator_password2" type=password class=form_flat size=40 maxlength=255>
      </p>
      <p>        Dapat mengedit laporan hariannya<br>
        <select name="operator_edit_report">
          <option value="0" selected>TIDAK (default)</option>
          <option value="1">YA</option>
        </select>
      </p>
      <p>        <input name="submit_tambah_operator" type=submit class=form_flat value="Tambah!">
      </p>
    </form>
</div>
<hr>
        Total Operator:
        <?php $total=mysql_result(mysql_query("SELECT count(*) FROM operator"),0);echo $total?>
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
            echo "<a href=\"$operator_file_name" . "?uid=$uid&p=operator&sid=$sid&fr=$from\">$n</a>| ";
        }
    }
    ################ halaman end ############

?>
<table width=100% cellspacing=1 cellpadding=3 bgcolor='#999999' border=0>
<tr bgcolor='#E9E9E9'>
<td width="100" class=small>:::</td>
<td width="20" class=small>ID.</td>
<td class=small>Nickname</td>
<td class=small>Nama Lengkap</td>
<td class=small>Login Terakhir</td>
<td class=small>Dari IP</td>
</tr>
<?php 
    $data_operator=mysql_query("SELECT operator_id, operator_name, operator_name_full, operator_last_ip, date_format(operator_last_date,'%d %b %Y - %H:%i:%s') as operator_last_date2,operator_edit_report FROM operator ORDER BY operator_id ASC LIMIT $fr,$pages_view");
    while($isi_data_operator=mysql_fetch_array($data_operator))
    {

		if($isi_data_operator[operator_id] ==1) 
		{
			$isi_data_operator[operator_id] = $isi_data_operator[operator_id] . "<img src='./img/icon_admin.png'>";
			$isi_data_operator[operator_name] = "<font color='red'><b>$isi_data_operator[operator_name]</b></font>";
		} else
		{
			if($isi_data_operator[operator_edit_report] ==1)
			{
				$isi_data_operator[operator_id] = $isi_data_operator[operator_id] . "<img src='./img/icon_edit_report.png'>";
			}
		}
	
	if($isi_data_operator[operator_id] > 1)
	{
		$tambahan=" <a href=\"$operator_file_name" . "?uid=$uid&p=operator&sid=$sid&hapus=$isi_data_operator[operator_id]\" onClick=\"return confirm('ANDA YAKIN HAPUS? " . ereg_replace("\"","","$isi_data_operator[operator_name_full]") . " ..')\"><img src='./img/icon_delete.png' border=0 title='Delete'></a>";
	} else
	{
		$tambahan='';
	}
	
        print "<tr bgcolor='#FFFFFF'>
                   <td><a href=\"$operator_file_name" . "?uid=$uid&p=operator&sid=$sid&edit=$isi_data_operator[operator_id]\"><img src='./img/icon_edit.png' border=0 title='Edit'></a>$tambahan</td>
                   <td>$isi_data_operator[operator_id]</td>
                   <td>$isi_data_operator[operator_name]</td>
		   <td>$isi_data_operator[operator_name_full]</td>
		   <td>" . $isi_data_operator[operator_last_date2] . "</td>
		   <td>$isi_data_operator[operator_last_ip]</td>
               </tr>\n";
    }

?>
</table>
<div align="center"><br>
  Ket. <img src="./img/icon_admin.png" width="25" height="25" align="absmiddle"> = Administrator | <img src="./img/icon_edit_report.png" width="25" height="25" align="absmiddle"> = Dapat Mengedit Laporan Harian <?php }?>
</div>
<p><a href="#top">Atas</a></p>
