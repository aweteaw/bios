<?php
# Deskripsi: 
# Menampilkan informasi tentang server dan php

if(eregi("phpinfo.php",$_SERVER['REQUEST_URI'])) die('hello script kiddies..');
if(@mysql_result(mysql_query("SELECT operator_id FROM operator WHERE operator_name='$uid'"),0) <> 1) #selain admin dilarang..
{
	mysql_close($connection);
	die('hello script kiddies..');
}

phpinfo();
?> 
