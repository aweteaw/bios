<?php 
# Deskripsi: 
# Menampilkan halaman untuk mengirimkan secara online testimonial, masukan, problem dsb berkaitan dengan aplikasi BiOS.
# Email yang terdaftar secara otomatis akan mendapatkan berita update seputar program BiOS

if(eregi("register.php",$_SERVER['REQUEST_URI'])) die('hello script kiddies..');

if(@mysql_result(mysql_query("SELECT operator_id FROM operator WHERE operator_name='$uid'"),0) <> 1) #selain admin dilarang..
{
	mysql_close($connection);
	die('hello script kiddies..');
}
?>
<h1><img src="./img/icon_registrasi.png" width="75" height="75" align="left">Registrasi</h1>
<p>BiOS Baliwae, adalah <strong>billing open source dengan <a href='http://www.gnu.org/licenses/gpl.txt'>lisensi GPL v3</a></strong>. Apabila Anda ingin menjadi yang pertama kali mendapatkan informasi ter-<em>up to date</em> seputar perkembangan program, silahkan isi formulir di bawah ini. GRATIS! 
  <br>
(<strong><em>Informasi akan dikirimkan via email..</em></strong>) </p>
<hr>
<p>
<table width=100% border=0 cellpadding=0 cellspacing=0>
    <tr valign=top>
    <td width=35%>   
    <p>
    <form action="http://bios.baliwae.com/registrasi/index.php" method=post>
      <p>Nama Anda<br>
          <input name="register_nama" type="text" size="50">
</p>
      <p>E-mail<br>
        <input name="register_email" type="text" size="50">
</p>
      <p>Nama Warnet<br>
        <input name="register_warnet" type="text" value="<?php  echo $setting_cafe_name?>" size="50">
</p>
      <p>Alamat <br>
        <input name="register_alamat" type="text" value="<?php  echo $setting_cafe_address?>" size="50">
</p>
      <p> Kota <br>
        <input name="register_kota" type="text" size="50">
</p>
      <p>Distro Linux yang Anda gunakan<br>
        <textarea name="register_distro" cols="60"></textarea>
</p>
      <p>Testimoni Anda tentang linux, dan BiOS khususnya.. <br>
        <textarea name="register_testimonial" cols="60" rows="5" id="register_testimonial"></textarea> 
        </p>
      <hr>
      <p align="left"> 
<input name="setting_priceperhour" type="hidden" value="<?php  echo $setting_priceperhour?>">
<input name="versi" type="hidden" value="<?php  echo $bios_versi?>">
<input name="cpu" type="hidden" value="<?php  echo mysql_result(mysql_query("SELECT count(*) FROM client"),0)?>">
<input name="cpu_linux" type="hidden" value="<?php  echo mysql_result(mysql_query("SELECT count(*) FROM client WHERE client_desktop <> 'win'"),0)?>">
        <input name="submit_register" type=submit value="REGISTER!">
      </p>
      </form>
<img src='./img/gplv3-127x51.png'> <a href='http://groups.google.com/group/biosbaliwae'><img src='./img/icon_googlegroups.gif' border=0></a>
</td>
</table>

