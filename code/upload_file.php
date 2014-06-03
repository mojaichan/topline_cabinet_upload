<?php

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

		move_uploaded_file($_FILES["file"]["tmp_name"],"upload/" . $_FILES["file"]["name"]);
		$store_filedir = "upload/" . $_FILES["file"]["name"];
		$uploaded = true;
		echo "Stored in: " . ".upload/" . $_FILES["file"]["name"];
	}
}
else
{
	echo "Invalid file";
}
/*** End of File Upload ***/  

/*** File Read & CSV parsing ***/
if ($uploaded){
	$file = fopen($store_filedir,"r");
	/** column header handling **/
	$firstLine = fgetcsv($file);
	//print_r($firstLine);
	$columns = array();
	$matchingColumn = 0;
	if (array_search("Item Number", $firstLine) !== false){
		$columns["itemno"] = array_search("Item Number", $firstLine);
		$matchingColumn++;
	}
	if (array_search("Dropbox URL", $firstLine) !== false){
		$columns["fileurl"] = array_search("Dropbox URL", $firstLine);
		$matchingColumn++;
	}
	if (array_search("File Name", $firstLine) !== false){
		$columns["filename"] = array_search("File Name", $firstLine);
		$matchingColumn++;
	}
	
	if ($matchingColumn < 3){
		echo "Error : CSV header is incorrect.Please ensure 3 columns below are included.";
		echo "<ul><li>Item Number</li><li>Dropbox URL</li><li>File Name</li></ul>";
	}
	//print_r($columns);
	/** end of column header handling **/
	
	$items = array();
	while(! feof($file)){
		$currentLine = fgetcsv($file);
		if (strlen($currentLine[$columns["itemno"]]) > 0){
			array_push($items,$currentLine);
		}
	}
	
	//print_r($items);
	fclose($file);
}
/*** End of File Read & CSV parsing ***/

/*** Building array of File() objects, stored in $addFileArray ***/
require_once 'PHPToolkit/NetSuiteService.php';
$addFileArray = array();
$uploadFolder = new RecordRef();
$uploadFolder->internalId = "6616";
$uploadFolder->type = "folder";

foreach ($items as $item){
	$nsFile = new File();
	$nsFile->attachFrom = "_web";
	//$nsFile->content = "dGhpcyBpcyBhIHRlc3Qu";
	$nsFile->description = $item[$columns["filename"]];
	$nsFile->folder = $uploadFolder;
	$nsFile->isOnline = true;
	$nsFile->name = $item[$columns["filename"]];
	$nsFile->url = $item[$columns["fileurl"]];
	$nsFile->urlComponent = $item[$columns["fileurl"]];
	array_push($addFileArray,$nsFile);
}
/*** end of builing File() objects ***/

/*** Web Service to Netsuite ***/
$service = new NetSuiteService();
$request = new AddRequest();

echo "<p>File Update Results</p>";
foreach ($addFileArray as $addFileRecord){
	$request->record = $addFileRecord;
	$addResponse = $service->add($request);
	if ($addResponse->writeResponse->status->isSuccess == true){
		echo "File ".$addFileRecord->name," is added to Netsuite successfully.<br/>";
	} else {
		echo "File ".$addFileRecord->name," is not added due to error.<br/>";
	}
}
/*** End of Web Service to Netsuite ***/
?>