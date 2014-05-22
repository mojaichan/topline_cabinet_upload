<?php
?>
<?php 
include_once '../templates/head_tag.php';
?>
<div id="wrapper" style="position:relative">
<form action="upload_file.php" role="form" enctype="multipart/form-data" method="post" style="width:50%;position:relative;margin-top:100px;left:25%;">
<fieldset>
	<legend>
		Top-Line Furniture - File Cabinet Upload
	</legend>
	<div class="form-group">
		<label for="upload_file" class="col-sm-4 control-label">
			Please choose a CSV file.
		</label>
		<div class="col-sm-8">
			<input type="file" class="form-control" name="file" id="file" required/>
		</div>
	</div>
	<div class="form-group col-sm-offset-4 col-sm-8">
		<button class="btn btn-default" type="submit">Upload</button>
	</div>
</fieldset>
</form>
</div>
</html>