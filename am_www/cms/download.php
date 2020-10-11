<?php
///////////////////////////////////////
if ($_REQUEST['sampleid'])
{
	include_once '../obj/samples.class.php';
	$mysamp=new Sample($_REQUEST['sampleid']);
	$mysamp->More_DVNom();
}
//////////////////////////////////////

include_once '../db/mysqlcon.php';
$file=$_GET['fpath'];

$NNAM=$_GET['fname'];
$NNAM=str_ireplace(" ","_",urldecode($NNAM));
$NTYP=Get_File_type_D($file);
if(!file_exists($file))	{echo "File Not Found!";exit();}
$len = filesize($file);

    switch( strtolower($NTYP)) {
          case "pdf": $ctype="application/pdf"; break;
      case "exe": $ctype="application/octet-stream"; break;
      case "zip": $ctype="application/zip"; break;
      case "rar": $ctype="application/rar"; break;
      case "txt": $ctype="text/plain"; break;
      case "pps": $ctype="application/pps"; break;
      case "mdb": $ctype="application/mdb"; break;
      case "doc": $ctype="application/msword"; break;
      case "xls": $ctype="application/vnd.ms-excel"; break;
      case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
      case "gif": $ctype="image/gif"; break;
      case "png": $ctype="image/png"; break;
      case "jpeg":
      case "jpg": $ctype="image/jpg"; break;
      case "mp3": $ctype="audio/mpeg"; break;
      case "wav": $ctype="audio/x-wav"; break;
      case "mpeg":
      case "mpg":
      case "mpe": $ctype="video/mpeg"; break;
      case "mov": $ctype="video/quicktime"; break;
      case "avi": $ctype="video/x-msvideo"; break;
      case "bmp": $ctype="image/bmp"; break;
      
      //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
      case "php":
      case "htm":
      case "html": die("<b>Cannot be used for ".  $_GET['NTYP'] ." files!</b>"); break;

      default: $ctype="application/force-download";
    }

header("Content-disposition: attachment; filename={$NNAM}.{$NTYP}");
header("Content-type: $ctype");
header("Content-Length: ".$len);
readfile($file);
function Get_File_type_D($FileName){
	$mFn=explode(".", $FileName);
	$mFn=array_reverse($mFn);
	$rtrn=$mFn[0];
	return strtolower($rtrn);
}
?>