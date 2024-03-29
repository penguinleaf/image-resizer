<?php
set_time_limit(120);
if (!isset($_GET['u']) && !isset($_GET['ue']))
{
	header('HTTP/1.1 400 Bad Request', true, 400);
	die("No image provided");
}

if(isset($_GET['ue'])){
	$u = urldecode($_GET['ue']);
	$u = str_replace(" ","+",$u);
}
else{
	$u = $_GET['u'];
}

$tempname = tempnam(sys_get_temp_dir(), "image");

//error_log(print_r($_SERVER['referrer'], TRUE)); 

// $ch = curl_init($u);
// curl_setopt($ch, CURLOPT_FILE, $temp);
// curl_setopt($ch, CURLOPT_HEADER, 0);
// curl_exec($ch);
// curl_close($ch);
file_put_contents($tempname, file_get_contents($u));
//$temp = fopen($tempname, "w+");
fseek($temp, 0);

if (filesize($tempname) === 0){
	header('HTTP/1.1 404 File Not Found', true, 404);
	die("Could not download $u");
}
//$temp = fopen($u, "rb");

$thumb = new Imagick($tempname);
// try{
//$thumb->readImageFile($temp); 
// }
// catch(Exception $e){
	//print_r($e)."\n";
	//print_r(Imagick::queryFormats());
	//die("Imagik Error");
  //	error_log($e->message());
// 	header("Location: $u");
// 	die();
// } 
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

unlink($tempname);

?>