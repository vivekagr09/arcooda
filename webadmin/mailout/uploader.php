<?php
/***********************************************************************
** Title.........:	Upload Functions
** Version.......:	1.0
** Author........:	Xiang Wei ZHUO <c9807224@studentmail.newcastle.edu.au>
** Filename......:	uploader.php
** Last changed..:	08/12/2000
** Notes.........:	Before calling this file, you should specify the $attachment_path
**					These functions provide the basic file uploading scheme.
**
***********************************************************************/

//the path where the files are stored. Make sure the path has the valid permissions
$attachment_path = $_SERVER['DOCUMENT_ROOT'] . "/webadmin/mailout/attachments/";

$the_path = $attachment_path;

//if the path does not exist or incorrect permissions, exit program with errors
if (!$the_path) {
	echo "Path: $the_path <BR>";
	echo "Path not defined or You are not authorised.";
	exit;
}/*if*/

//Max File Size to be allowed
//approximately 100Kb, shouldn't make this too big for emails.
$my_max_file_size     = "102400";     # in bytes


function file_type($file)
{
	$types = array(
					'gif'	=> 'image/gif',
					'jpg'	=> 'image/jpeg',
					'jpeg'	=> 'image/jpeg',
					'jpe'	=> 'image/jpeg',
					'bmp'	=> 'image/bmp',
					'png'	=> 'image/png',
					'tif'	=> 'image/tiff',
					'tiff'	=> 'image/tiff',
					'swf'	=> 'application/x-shockwave-flash'
				  );
	$default = 'application/octet-stream';

	$file_split = explode(".", $file);

	$file_ext = $file_split[count($file_split)-1];

	$type = $types[$file_ext];

	if (strlen($type) > 0)
		return $type;
	else
		return $default;
}


//This function validates the files uploaded.
//The files must be within size, type, and must exist.
//returns false if everything is OK
function validate_upload($the_file)
{
	global $my_max_file_size, $image_max_width, $image_max_height,$allowed_types,$the_file_type,$registered_types;

	$start_error = "\n<b>Error:</b>\n<ul>";

    if ($the_file == "none")
	{
        $error .= "\n<li>You did not upload anything!</li>";
    }/*if*/
	else
	{
		list($foo,$width,$bar,$height) = explode("\"",$size[3]);

        if ($error)
		{
			$error = $start_error . $error . "\n</ul>";
            return $error;
        }/*if*/
		else
		{
			return false;
        }/*else*/
	}/*else*/
}/*validate_upload*/


//Uploads the specified file.
function upload($the_file)
{
	global $the_path,$the_file_name;

	//check the files for any errors
    $error = validate_upload($the_file);

    if ($error)
	{
		echo $error;
    }
	else //or we can continue
	{

		// check if file exists, if then delete the old one before upload
		$ffile = $the_path . '' . $the_file;

		if ( file_exists("$ffile") )
		{
			unlink ("$ffile");
		}

		// upload the actuall file here after all checks
		if (!copy($the_file, $the_path . "/" . $the_file_name))
		{
            echo ("\n<b>Error with uploading the files. Please try again.<BR>If errors presist, please contact the Administrator.");
        }

    }/*else*/
}/*upload*/


//returns the list of files in the attachement directory
function fileList() {
	global $the_path;
	$handle = dir($the_path);
	$num_files = 0;
	$files_uploaded;
	while ($file = $handle->read())
	{
		if (($file != ".") && ($file != ".."))
		{
			$files_uploaded[$num_files] = $file;
			$num_files++;
		}/*if*/
	}/*while*/

	return $files_uploaded;
}/*fileList*/


//display the files in an SELECTION LIST
function fileOptionsList() {

	$files = fileList();

	for ($i = 0;$i < count($files) ;$i++)
	{
		echo '<OPTION VALUE="'.$files[$i].'">'.$files[$i].'</OPTION>';
		echo "\n";
	}/*for*/
}/*fileOptionsList*/

//delete the slected file
function deleteFile($file_to_be_deleted)
{
	global $the_path;
	$fileDeleted = "$the_path$file_to_be_deleted";

	if ( file_exists($fileDeleted) )
	{
		unlink($fileDeleted);
	}/*if*/
	else
	{
		echo "\n$file_to_be_deleted does not exist, can't delete.<BR>";
	}/*else*/
}/*deleteFile*/

function fileList_text()
{
	$files = fileList();
	for ($i = 0 ;$i < count($files)  ;$i++)
	{
		$files_attached .= "[$files[$i]] ";
	}
	return $files_attached;
}


switch ($task)
{
	case 'Attach' :

		if ($the_file != "none" && $the_file != '')
		{
			upload($the_file);
		}
		break;
	case 'Remove' :
		if (isset($files) && $files != "")
		{
			deleteFile($files);
		}
		break;
	default :
		break;

}/*switch*/

?>

