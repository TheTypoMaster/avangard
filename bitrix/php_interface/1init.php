<?
define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/log.txt");

AddEventHandler("iblock", "OnAfterIBlockElementUpdate","OnAfterIBlockElementUpdateHandler");
AddEventHandler("iblock", "OnAfterIBlockElementAdd","OnAfterIBlockElementUpdateHandler");

//AddEventHandler("iblock", "OnAfterIBlockElementAdd","OnBeforeIBlockElementUpdateHandler");
AddEventHandler("iblock", "OnBeforeIBlockElementUpdate","OnBeforeIBlockElementUpdateHandler");

function OnAfterIBlockElementUpdateHandler(&$arFields)
    {
//      echo "<pre>".print_r($arFields)."</pre>";
            $new_files="";
            $del_files="";
        if($arFields["RESULT"])
            {
            foreach($arFields["PROPERTY_VALUES"]["21"] as $key=>$value)
              {

                if (($value["name"]!=""))/*(strpos($key, "n")===0)&&*/
                    {
                     $new_files=$new_files.$value["name"].",  ";


                          if(CModule::IncludeModule('iblock'))
                            {
                                   $element_=CIBlockElement::GetList(Array(), Array("IBLOCK_ID"=>$arFields["IBLOCK_ID"] , "ID"=>$arFields["ID"]));
                                   if ($ar_element_ = $element_->GetNextElement())
                                    {
                                   $ar_props_=$ar_element_->GetProperties();
                                   //перебираем все море фотов в поисках нужной, дабы записать туды терь мал картинку
              //                     foreach($ar_props_['MORE_PHOTO']['VALUE'] as $k=>$val)
                                   $yahoo=0;
                                   while (list($key, $val)=each($ar_props_['MORE_PHOTO']['VALUE']))
                                   {
                                    $ar_props_0 = CFile::GetFileArray($val);
                                    if ($value["name"]==$ar_props_0["FILE_NAME"]){$yahoo=1; break;}
                                   }
                                   if ($yahoo)
                                      {
                                   $folder=$_SERVER['DOCUMENT_ROOT']."/upload/".$ar_props_0["SUBDIR"];
                                   $path=$_SERVER['DOCUMENT_ROOT'].$ar_props_0["SRC"];
                                   $filename=$ar_props_0["FILE_NAME"];
                                   $new[]=resize_img($path,$folder,$filename,120);
                                   $newm[]=resize_img_medium($path,$folder,$filename,160);
                                      }
                                    }
                            }


                    }
              }

            }
        else
            AddMessage2Log("Ошибка изменения записи ".$arFields["ID"]." (".$arFields["RESULT_MESSAGE"].").");

    }

function OnBeforeIBlockElementUpdateHandler(&$arFields)
    {
//      echo "<pre>".print_r($arFields)."</pre>";
            $del_files="";
        if($arFields["ID"])
            {
            foreach($arFields["PROPERTY_VALUES"]["21"] as $key=>$value)
              {

                if (array_key_exists("del",$value))
                    if ($value["del"]=="Y")
//                      $del_files=$del_files.$key.",  ";

                          if(CModule::IncludeModule('iblock'))
                            {
                                   $element_=CIBlockElement::GetList(Array(), Array("IBLOCK_ID"=>$arFields["IBLOCK_ID"] , "ID"=>$arFields["ID"]));
                                  if ($ar_element_ = $element_->GetNextElement())
                                    {

                                    $ar_props_=$ar_element_->GetProperties();
                                   //перебираем все море фотов в поисках нужной, дабы грохнуть туды терь мал картинку
                                   $yahoo=0;
                                     while (list($key_, $val)=each($ar_props_['MORE_PHOTO']['PROPERTY_VALUE_ID']))
                                      {
                                       if ($key==$val){$yahoo=1; break;}
                                      }
                                   if ($yahoo)
                                      {

                                   $ar_props_0 = CFile::GetFileArray($ar_props_['MORE_PHOTO']['VALUE'][$key_]);

                                   $folder=$_SERVER['DOCUMENT_ROOT']."/upload/".$ar_props_0["SUBDIR"];
                                   $path=$_SERVER['DOCUMENT_ROOT'].$ar_props_0["SRC"];
                                   $filename="s_".$ar_props_0["FILE_NAME"];//получаем имя маленькой картинки
                                   $file=$folder."/".$filename; // тут полностью путь до маленькой картинки
                                   $del=unlink($file);
                                      }

                                    }

                            }

              }

/*
            if ($del_files!="")
            AddMessage2Log("Удалили картинки с номерами: ".$del_files." ");
            else
            AddMessage2Log("Новых картинок не удалялось");
*/
            }
        else
            AddMessage2Log("Ошибка изменения записи ".$arFields["ID"]." (".$arFields["RESULT_MESSAGE"].").");
    }

function resize_img ($img_src_path, $folder, $filename , $biggest_side_dest)
{

            $arrTypes = array(
                    0 => "GIF",
                    1 => "JPG",
                    2 => "PNG",
                    3 => "SWF",
                    4 => "PSD",
                    5 => "BMP",
                    6 => "TIFF_II",
                    7 => "TIFF_MM",
                    8 => "JPC",
                    9 => "JP2",
                    10 => "JPX",
                    11 => "JB2",
                    12 => "SWC",
                    13 => "IFF",
                    14 => "WBMP",
                    15 => "XBMP"
            );

            $img_attr = getimagesize($img_src_path);
            $width_orig = $img_attr[0];
            $height_orig = $img_attr[1];

            $img_type = $img_attr[2];
            $img_type = $arrTypes[$img_type-1];
            $img_size = $img_attr[3];

            if($img_type == "JPG")
                    {
                            $img_bits = $img_attr['bits'];
                            $img_channels = $img_attr['channels'];
                    }
            $img_mime = $img_attr['mime'];

            if($height_orig < $width_orig)
                {
                            $ratio = $height_orig / $width_orig;
                            $height_dest = $biggest_side_dest * $ratio;
                            $width_dest = $biggest_side_dest;

            } else {

                            $ratio = $width_orig / $height_orig;
                            $height_dest = $biggest_side_dest;
                            $width_dest = $biggest_side_dest * $ratio;
                    }

            $img_dest = imagecreatetruecolor($width_dest, $height_dest) or die("Cannot Initialize new GD image stream");
            $bg = imagecolorallocate($img_dest, 255, 255, 255);

            if($img_type == "JPG")
                    $img_orig = imagecreatefromjpeg($img_src_path);
            elseif($img_type == "GIF")
                    $img_orig = imagecreatefromgif($img_src_path);
            elseif($img_type == "PNG")
                    $img_orig = imagecreatefrompng($img_src_path);
            elseif($img_type == "WBMP")
                    $img_orig = imagecreatefromwbmp($img_src_path);

            //imagecopyresized($img_dest, $img_orig, 0, 0, 0, 0, $width_dest, $height_dest, $width_orig, $height_orig);
            imagecopyresampled($img_dest, $img_orig, 0, 0, 0, 0, $width_dest, $height_dest, $width_orig, $height_orig);

                          $new_img_path = $folder."/s_".$filename;//tempnam("/tmp", "FOO").".".$img_type;

                        if (function_exists("imagegif")) {
                                @header("Content-type: {$img_mime}");
                            imagegif($img_dest, $new_img_path);
                        } elseif (function_exists("imagejpeg")) {
                                   @header("Content-type: {$img_mime}");
                            imagejpeg($img_dest, $new_img_path, 0.5);
                        } elseif (function_exists("imagepng")) {
                                   @header("Content-type: {$img_mime}");
                            imagepng($img_dest, $new_img_path);
                        } elseif (function_exists("imagewbmp")) {
                                    @header("Content-type: {$img_mime}");
                            imagewbmp($img_dest, $new_img_path);
                        } else {
                            die("No image support in this PHP server");
                        }

                        imagedestroy($img_dest);
                        imagedestroy($img_orig);

            return $new_img_path;

}

?>
<?

function resize_img_medium ($img_src_path, $folder, $filename , $biggest_side_dest)
{

            $arrTypes = array(
                    0 => "GIF",
                    1 => "JPG",
                    2 => "PNG",
                    3 => "SWF",
                    4 => "PSD",
                    5 => "BMP",
                    6 => "TIFF_II",
                    7 => "TIFF_MM",
                    8 => "JPC",
                    9 => "JP2",
                    10 => "JPX",
                    11 => "JB2",
                    12 => "SWC",
                    13 => "IFF",
                    14 => "WBMP",
                    15 => "XBMP"
            );

            $img_attr = getimagesize($img_src_path);
            $width_orig = $img_attr[0];
            $height_orig = $img_attr[1];

            $img_type = $img_attr[2];
            $img_type = $arrTypes[$img_type-1];
            $img_size = $img_attr[3];

            if($img_type == "JPG")
                    {
                            $img_bits = $img_attr['bits'];
                            $img_channels = $img_attr['channels'];
                    }
            $img_mime = $img_attr['mime'];

                            $ratio = $width_orig / $height_orig;
                            $height_dest = $biggest_side_dest;
                            $width_dest = $biggest_side_dest * $ratio;


            $img_dest = imagecreatetruecolor($width_dest, $height_dest) or die("Cannot Initialize new GD image stream");
            $bg = imagecolorallocate($img_dest, 255, 255, 255);

            if($img_type == "JPG")
                    $img_orig = imagecreatefromjpeg($img_src_path);
            elseif($img_type == "GIF")
                    $img_orig = imagecreatefromgif($img_src_path);
            elseif($img_type == "PNG")
                    $img_orig = imagecreatefrompng($img_src_path);
            elseif($img_type == "WBMP")
                    $img_orig = imagecreatefromwbmp($img_src_path);

            //imagecopyresized($img_dest, $img_orig, 0, 0, 0, 0, $width_dest, $height_dest, $width_orig, $height_orig);
            imagecopyresampled($img_dest, $img_orig, 0, 0, 0, 0, $width_dest, $height_dest, $width_orig, $height_orig);

                          $new_img_path = $folder."/m_".$filename;//tempnam("/tmp", "FOO").".".$img_type;

                        if (function_exists("imagegif")) {
                                @header("Content-type: {$img_mime}");
                            imagegif($img_dest, $new_img_path);
                        } elseif (function_exists("imagejpeg")) {
                                   @header("Content-type: {$img_mime}");
                            imagejpeg($img_dest, $new_img_path, 0.5);
                        } elseif (function_exists("imagepng")) {
                                   @header("Content-type: {$img_mime}");
                            imagepng($img_dest, $new_img_path);
                        } elseif (function_exists("imagewbmp")) {
                                    @header("Content-type: {$img_mime}");
                            imagewbmp($img_dest, $new_img_path);
                        } else {
                            die("No image support in this PHP server");
                        }

                        imagedestroy($img_dest);
                        imagedestroy($img_orig);

            return $new_img_path;

}

?>


<?
//    echo "<pre>".print_r($arFields)."</pre>";
//AddEventHandler("iblock", "OnAfterIBlockElementAdd", "BXIBlockAfterSave", 100,"/bitrix/php_interface/include/iblock_element_edit_before_save.php");
//AddEventHandler("iblock", "OnAfterIBlockElementUpdate", "BXIBlockAfterSave", 100,"/bitrix/php_interface/include/iblock_element_edit_before_save.php");




function send_mail($to, $thm, $html, $path)
 {
 $path=$_SERVER["DOCUMENT_ROOT"].$path;
   $fp = fopen($path,"r+");

   if (!$fp)

     {
       print "Файл $path не может быть прочитан";
       exit();
     }

   $file = fread($fp, filesize($path));

   fclose($fp);

   $boundary = "--".md5(uniqid(time())); // генерируем разделитель

   $headers = "MIME-Version: 1.0\n";
   $headers .="Content-Type: multipart/mixed; boundary=\"$boundary\"\n";

   $multipart .= "--$boundary\n";

   $kod = 'windows-1251'; // или $kod = 'koi8-r;';

   $multipart .= "Content-Type: text/html; charset=$kod\n";

   $multipart .= "Content-Transfer-Encoding: Quot-Printed\n\n";

   $multipart .= "$html\n\n";



   $message_part = "--$boundary\n";

   $message_part .= "Content-Type: application/octet-stream\n";

   $message_part .= "Content-Transfer-Encoding: base64\n";

   $message_part .= "Content-Disposition: attachment; filename = \"".$path."\"\n\n";

   $message_part .= chunk_split(base64_encode($file))."\n";

   $multipart .= $message_part."--$boundary--\n";



   if(!mail($to, $thm, $multipart, $headers))
      {
        echo "<font color='red'><b>К сожалению, письмо не отправлено. Попробуйте позже.</b></font>";
        return false;//exit();
      }
     return true;
 }

?>
<?

function CaptchaCheckCode($captcha_word, $captcha_sid)
{
    include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");

    $cpt = new CCaptcha();
    return $cpt->CheckCode($captcha_word, $captcha_sid);
}

?>
