<?php 
# Deskripsi:
# Menampilkan halaman stok produk, diluar jasa internet.

if(eregi("product.php",$_SERVER['REQUEST_URI'])) die('hello script kiddies..');

if(@mysql_result(mysql_query("SELECT operator_id FROM operator WHERE operator_name='$uid'"),0) <> 1) #selain admin dilarang..
{
	mysql_close($connection);
	die('hello script kiddies..');
}
?>
<script language="JavaScript" src="collapse_expand_single_item.js"></script>
<h1><img src="./img/icon_product.png" align="left">Produk</h1>
<p>Gunakan modul ini untuk menambah / mengedit daftar produk yang di jual di warnet Anda.. 
<hr>
<?php 
if($submit_tambah_produk && $produk_nama && $produk_harga && $produk_stok)
{
  
    $result=mysql_query("INSERT INTO produk (produk_nama,produk_harga,produk_stok,produk_tanggal) VALUES ('$produk_nama','$produk_harga','$produk_stok',NOW())");

    echo mysql_error();

    if($result)
    {
        echo "<h4><img src=\"./img/icon_ok.gif\" align=left>Produk -- <b>$produk_nama</b> -- SUKSES ditambahkan!</h4>";
    } else
    {
        echo "<h4><img src=\"./img/icon_error.gif\" align=left>Error! ada kegagalan tampaknya .. Silahkan ulangi!</h4>";
    }

    echo "<p><a href=\"$product_file_name" . "?uid=$uid&p=product&sid=$sid\">Kembali ..</a>";
} elseif($hapus)
{
    $result=mysql_query("UPDATE produk SET produk_show=0 WHERE produk_id = $hapus LIMIT 1");
    
    echo mysql_error();
    
    if($result)
    {
        echo "<h4><img src=\"./img/icon_ok.gif\" align=left>PRODUK BERHASIL DIHAPUS!</h4>";
    } else
    {
        echo "<h4><img src=\"./img/icon_error.gif\" align=left>Error! ada kegagalan tampaknya .. Silahkan ulangi!</h4>";
    }

    echo "<p><a href=\"$product_file_name" . "?uid=$uid&p=product&sid=$sid\">Kembali ..</a>";

} elseif($edit)
{
    
    if(!$confirm)
    {
        $data_produk=mysql_query("SELECT produk_id,produk_nama, produk_harga, produk_stok FROM produk WHERE produk_id=$edit");
        $isi_data_produk=mysql_fetch_array($data_produk);
    ?>
        <form action="<?php  echo "$product_file_name" . "?uid=$uid&p=product&sid=$sid"?>" method=post>
		  <p>
		<input type="hidden" name="confirm" value="baliwae">
		<input type="hidden" name="edit" value="<?php  echo $edit?>">
        Nama Produk :<br> 
        <input name="produk_nama" type=text maxlength=100 size=50 value="<?php  echo $isi_data_produk[produk_nama]?>">
		</p>
		  <p>Harga Produk :<br> 
		    <input name="produk_harga" type=text maxlength=100 size=50 value="<?php  echo $isi_data_produk[produk_harga]?>">
</p>
		  <p>            
		    Jumlah Stok:<br>
		    <input name="produk_stok" type=text maxlength=100 size=50 value="<?php  echo $isi_data_produk[produk_stok]?>">

        <input name="submit_update_produk" type=submit value="Update!">
		</p>
</form>

    <?php 
    } else
    {
        $result=mysql_query("UPDATE produk SET produk_harga='$produk_harga', produk_nama='$produk_nama', produk_stok='$produk_stok', produk_tanggal=NOW() WHERE produk_id=$edit");

        echo mysql_error();
        
        if($result)
        {
            echo "<h4><img src=\"./img/icon_ok.gif\" align=left>Produk -- <b>$product_name</b> -- SUKSES diupdate!</h4>";
        } else
        {
            echo "<h4><img src=\"./img/icon_error.gif\" align=left>Error! ada kegagalan tampaknya .. Silahkan ulangi!</h4>";
        }
    }
	
	echo "<p><a href=\"$product_file_name" . "?uid=$uid&p=product&sid=$sid\">Kembali ..</a><p>";
	
} else{
?>
<img src="./img/u.gif" name="imgfirst" width="9" height="9" border="0" >
<a href="javascript:void(0);" onClick="shoh('first');" >Tambah Produk ..</a>
<div style="display: none;" id="first" >
    <p>
    <form action="<?php  echo "$product_file_name" . "?uid=$uid&p=product&sid=$sid"?>" method=post>
      <p>Nama Produk:<br> 
        <input name="produk_nama" type=text class=form_flat id="produk_nama" size=40 maxlength=100>
</p>
      <p>Harga: (Rp. )<br> 
        <input name="produk_harga" type=text class=form_flat id="produk_harga" value="" size=40 maxlength=7>
</p>
</p>
      <p>Jumlah Stok:<br> 
        <input name="produk_stok" type=text class=form_flat id="produk_stok" value="1" size=7 maxlength=7>
</p>

<input name="submit_tambah_produk" type=submit class=form_flat value="Tambah!">
      </p>
</form>
</div>
<hr>
        Total Produk:
        <?php $total=mysql_result(mysql_query("SELECT count(*) FROM product where produk_show=1"),0);echo $total?>
        <p>
          <?php 

    ################ halaman #####################################################
    #jumlah maksimal per halaman
    #ganti disini (1 dari 2) , jangan lupa lihat total diatas!!
    $pages_view=30;

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
            echo "<a href=\"$product_file_name" . "?uid=$uid&p=product&sid=$sid&fr=$from\">$n</a>| ";
        }
    }
    ################ halaman end ############

?>
<table width=100% cellspacing=1 cellpadding=3 bgcolor='#999999' border=0>
<tr bgcolor='#E9E9E9'>
<td width="100" class=small>:::</td>
<td width="20" class=small>Tanggal</td>
<td width="20" class=small>ID.</td>
<td class=small>Nama Produk</td>
<td class=small>Harga satuan</td>
<td class=small>Sisa Stok</td>
</tr>
<?php 
    $data_produk=mysql_query("SELECT produk_id, produk_nama, produk_stok, produk_harga, produk_tanggal FROM produk WHERE produk_show=1 ORDER BY produk_nama ASC LIMIT $fr,$pages_view");
    while($isi_data_produk=mysql_fetch_array($data_produk))
    {

        print "<tr bgcolor='#FFFFFF'>
                   <td><a href=\"$product_file_name" . "?uid=$uid&p=product&sid=$sid&edit=$isi_data_produk[produk_id]\"><img src='./img/icon_edit.png' border=0 title='Edit'></a> <a href=\"$product_file_name" . "?uid=$uid&p=product&sid=$sid&hapus=$isi_data_produk[produk_id]\" onClick=\"return confirm('ANDA YAKIN HAPUS? " . ereg_replace("\"","",$isi_data_product[produk_nama]) . " ..')\"><img src='./img/icon_delete.png' border=0 title='Delete'></a></td>
                   <td>$isi_data_produk[produk_tanggal]</td>
		   <td>$isi_data_produk[produk_id]</td>
                   <td>$isi_data_produk[produk_nama]</td>
		   <td>$isi_data_produk[produk_harga]</td>
		   <td>$isi_data_produk[produk_stok]</td>
               </tr>\n";
    }

?>
</table>
<?php }?>
<p><a href="#top">Atas</a></p>
