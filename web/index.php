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

file_put_contents($tempname, file_get_contents($u));

fseek($temp, 0);

if (filesize($tempname) === 0){
	header('HTTP/1.1 404 File Not Found', true, 404);
	die("Could not download $u");
}

$thumb = new Imagick($tempname);

// Use semaphors to prevent too many simultanious image resizes, which can slow the server to a crawl.
$s = sem_get("imageResizeSemaphor", 2);
sem_acquire($s);
	$thumb->resizeImage($_GET['w'], $_GET['h'],  imagick::FILTER_LANCZOS, 1, TRUE);
sem_release($s);

$newfile = tmpfile();
$thumb->writeImageFile($newfile);

fseek($newfile, 0);
$mime = getimagesizefromstring(fread($newfile))["mime"];
fseek($newfile, 0);

header('Content-Type:'.$mime);
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