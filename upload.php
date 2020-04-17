<?php

//Folder for uploads
$target_dir = "uploads/";
//Source
$target_path = $target_dir . basename($_FILES["file"]["name"]);
//Uppload ok 
$upload_ok = 1;
//File extension in lower case
$file_type = strtolower(pathinfo($target_path,PATHINFO_EXTENSION));

//Global variables (messages) 
$unvalid_image;
$file_exist;
$unvalid_file_extension;
$upload_unknown_error;
$upload_success;


// Check that the file is an image
    //getimagesize() vs exif_imagetype() ? - båda verkar vara "stränga" med vilka filer de tillåter.
if(isset($_POST["upload"])) {
    $check = getimagesize($_FILES["file"]["tmp_name"]);
    if($check !== false) {
        $upload_ok = 1;
    } else {
        $unvalid_image = "Detta är inte en tillåten bildfil alternativt det gick inte att bestämma filtypen, ";
        $upload_ok = 0;
    }

    // Check if file already exists
    if (file_exists($target_path)) {
        $file_exist = "Filen existerar redan, ";
        $upload_ok = 0;
    }
 
    // Allow certain file formats
    if($file_type != "jpg" && $file_type != "png" && $file_type != "jpeg"
    && $file_type != "gif" ) {
        $unvalid_file_extension = "Endast JPG, JPEG, PNG & GIF filer är tillåtna, ";
        $upload_ok = 0;
    }
    // Check if $upload_ok is set to 0
    if ($upload_ok == 0) {
        $upload_error = "filen har inte blivit uppladdad.";
    // else, try to upload image file
    } else {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_path)) {
            $upload_success = "Filen ". basename( $_FILES["file"]["name"]). " har nu laddats upp.";
        } else {
            $upload_unknown_error = "Ett okänt fel inträffade, pröva att ladda upp en ny bild";
        }
    }
}