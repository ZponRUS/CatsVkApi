<?php 

	$token = "Standalone Token";
	$groupid = "id Group";
	$v = "5.107";

	$url = json_decode(file_get_contents("https://api.thecatapi.com/v1/images/search"), 1)["0"]["url"];
	$ext = pathinfo($url)['extension'];
	$filename = "cat.".$ext;
	copy($url, $filename) or die("Fail get Cat :(");

	$upload_url = json_decode(file_get_contents("https://api.vk.com/method/photos.getWallUploadServer?group_id=".$groupid."&access_token=".$token."&v=".$v), 1)["response"]["upload_url"];

	$ch = curl_init($upload_url);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, ['file1' => new \CURLFile($filename)]);
	$data = json_decode(curl_exec($ch), 1);
	curl_close($ch);

	$save = json_decode(file_get_contents("https://api.vk.com/method/photos.saveWallPhoto?group_id=".$groupid."&photo=".$data["photo"]."&server=".$data["server"]."&hash=".$data["hash"]."&access_token=".$token."&v=".$v), 1);

	$att = "photo".$save["response"]["0"]["owner_id"]."_".$save["response"]["0"]["id"];

	$pid = json_decode(file_get_contents("https://api.vk.com/method/wall.post?attachments=".$att."&from_group=1&signed=1&owner_id=-".$groupid."&access_token=".$token."&v=".$v), 1)["response"]["post_id"];
	
	print("<center>Success! PostID: ".$pid."<br><img src='".$url."'></center>");

	unlink($filename);
?>
