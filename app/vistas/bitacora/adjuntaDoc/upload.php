<?php
if (($_FILES["file"]["type"] == "image/pjpeg")
    || ($_FILES["file"]["type"] == "image/jpeg")
    || ($_FILES["file"]["type"] == "image/pdf")
    || ($_FILES["file"]["type"] == "image/png")
    || ($_FILES["file"]["type"] == "image/gif")
	|| ($_FILES["file"]["type"] == "image/doc")
    || ($_FILES["file"]["type"] == "image/docx")
    || ($_FILES["file"]["type"] == "image/xls")
    || ($_FILES["file"]["type"] == "image/xlsx")) {
    if (move_uploaded_file($_FILES["file"]["tmp_name"], "images/".$_FILES['file']['name'])) {
        echo 'si';
    } else {
        echo 'no';
    }
}