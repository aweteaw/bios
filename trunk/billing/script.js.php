<?php 
header('Content-type: application/x-javascript');
include_once('./bios-config.php');
?>
var xmlHttp

function showTime()
{
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request")
		return
	} 

	var url="function.php?q=getduration&uid=<?php echo $uid?>&sid="+Math.random()
	
	xmlHttp.onreadystatechange=function()
	{															
			if ((xmlHttp.readyState==4 || xmlHttp.readyState=="complete"))
			{ 
				var response = xmlHttp.responseXML.documentElement
				<?php 				
					$data_client=mysql_query("SELECT client_id,client_name FROM `client` ORDER BY client_ip ASC");
					$n=0;
					while($isi_data_client=mysql_fetch_array($data_client))
					{
				
						if($clients == $isi_data_client[client_id])
						{
						
						
						?>
						document.getElementById('client<?php  echo $isi_data_client[client_id]?>').innerHTML = response.getElementsByTagName('duration')[<?php  echo $n?>].firstChild.nodeValue
						document.getElementById('price<?php  echo $isi_data_client[client_id]?>').innerHTML = response.getElementsByTagName('price')[<?php  echo $n?>].firstChild.nodeValue
				
						<?php 
						
						} elseif(!$clients)
						{
				?>
						document.getElementById('client<?php  echo $isi_data_client[client_id]?>').innerHTML = response.getElementsByTagName('duration')[<?php  echo $n?>].firstChild.nodeValue
						document.getElementById('price<?php  echo $isi_data_client[client_id]?>').innerHTML = response.getElementsByTagName('price')[<?php  echo $n?>].firstChild.nodeValue
				<?php 
						}
						$n++;
					}
				?>
					document.getElementById('billing_clock').innerHTML = response.getElementsByTagName('clock')[0].firstChild.nodeValue;

					<?php
					#kalo ada clientsnya pasti yg manggil client.php. kalau tidak dibatasini dengan var ini, bisa bikin
					# Client.php freeze.. ajaxnya ga jalan karena nyari objek dibawah tapi ga ketemu..

					if(!$clients)
					{
					?>
					
					document.getElementById('summary_tarif_perjam').innerHTML = response.getElementsByTagName('tarif_perjam')[0].firstChild.nodeValue;	

					document.getElementById('summary_tarif_min').innerHTML = response.getElementsByTagName('tarif_min')[0].firstChild.nodeValue;

					document.getElementById('summary_tarif_min_durasi').innerHTML = response.getElementsByTagName('tarif_min_durasi')[0].firstChild.nodeValue;

					document.getElementById('summary_tarif_refresh').innerHTML = response.getElementsByTagName('tarif_refresh')[0].firstChild.nodeValue;	

					<?php
					}
					?>	

					setTimeout('showTime()', <?php  echo $setting_refresh*1000?> )
					
			} 
	
	} 
  
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function GetXmlHttpObject()
{ 
	var objXMLHttp=null
	if (window.XMLHttpRequest)
	{
		objXMLHttp=new XMLHttpRequest()
	}
	else if (window.ActiveXObject)
	{
		objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP")
	}
	return objXMLHttp
}

var http = GetXmlHttpObject();

function popup_win(shot_location,shot_name,shot_feature) {
  window.open(shot_location,shot_name,shot_feature);
}

status='Billing Open Source Baliwae (c)2005-2008 BALIWAE';
