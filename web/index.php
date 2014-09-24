<?php

if (!isset($_GET['u'] && isset($_GET['ue'])))
	return 400;
	die("No image provided");

$u = $_GET['u'];
if(isset($_GET['ue'])){
	$u = urldecode($_GET['ue']);
}
$temp = tmpfile();

$ch = curl_init($u);
curl_setopt($ch, CURLOPT_FILE, $temp);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_exec($ch);
curl_close($ch);
//fwrite($temp, file_get_contents($_GET["u"]));
fseek($temp, 0);

$thumb = new Imagick();
$thumb->readImageFile($temp); 
$thumb->resizeImage($_GET['w'], $_GET['h'],  imagick::FILTER_LANCZOS, 1, TRUE);

$newfile = tmpfile();
$thumb->writeImageFile($newfile);

fseek($newfile, 0);
$mime = getimagesizefromstring(fread($newfile))["mime"];
fseek($newfile, 0);

header('Content-Type:'.$mime);
//header('Content-Type:'."text/plain");
header("Cache-Control: max-age=2592000");
fseek($newfile,0,SEEK_END);
$length = ftell($newfile);
header('Content-Length: ' . $length);
fseek($newfile, 0);
print(fread($newfile, $length));

fclose($temp);
fclose($newfile);

?>