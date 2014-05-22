<pre>
<?php
print_r($_POST);
print_r($_FILES);

/*** File Upload ***/
$allowedExts = array("gif", "jpeg", "jpg", "png", "csv", "js");
//$allowedExts = array("csv");
$temp = explode(".", $_FILES["file"]["name"]);
$extension = end($temp);
if (($_FILES["file"]["size"] < 2000000) && in_array($extension, $allowedExts))
{
	if ($_FILES["file"]["error"] > 0)
	{
		echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
	}
	else
	{
		echo "Upload: " . $_FILES["file"]["name"] . "<br>";
		echo "Type: " . $_FILES["file"]["type"] . "<br>";
		echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
		echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>";

		/*if (file_exists("upload/" . $_FILES["file"]["name"]))
		{
		echo $_FILES["file"]["name"] . " already exists. ";
		}
		else
		{*/
		move_uploaded_file($_FILES["file"]["tmp_name"],"upload/" . $_FILES["file"]["name"]);
		$store_filedir = "upload/" . $_FILES["file"]["name"];
		$uploaded = true;
		echo "Stored in: " . ".upload/" . $_FILES["file"]["name"];
		//}
	}
}
else
{
	echo "Invalid file";
}
/*** End of File Upload ***/  

/*** File Read ***/
if ($uploaded){
	$file = fopen($store_filedir,"r");
	
	/** column header handling **/
	$firstline = fgetcsv($file);
	print_r($firstline);
	$columns = array();
		if (array_search("Item Number", $firstline) !== false){
		$columns["itemno"] = array_search("Item Number", $firstline);
	}
	if (array_search("Dropbox URL", $firstline) !== false){
		$columns["fileurl"] = array_search("Dropbox URL", $firstline);
	}
	print_r($columns);
	/** end of column header handling **/
	
	$items = array();
	while(! feof($file))
		{
		$current_line = fgetcsv($file);
		if (strlen($current_line[$columns["itemno"]]) > 0)
			{
			array_push($items,$current_line);
			}
		}
	
	print_r($items);
	fclose($file);
}
/*** End of File Read ***/

/*** Web Service to Netsuite ***/
require_once '../PHPToolkit/NetSuiteService.php';
$request = new AddListRequest();
$request->record = $fileList;
//$addResponse = $service->addList($request);

$request = new UpdateListRequest();
$request->record = $itemList;
//$addResponse = $service->updateList($request);

/*** End of Web Service to Netsuite ***/
?>
</pre>