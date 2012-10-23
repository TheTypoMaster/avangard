<?
define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/log.txt");
//AddEventHandler("iblock", "OnAfterIBlockElementUpdate", "CreatePreviewFromDetail");
//AddEventHandler("iblock", "OnAfterIBlockElementAdd", "CreatePreviewFromDetail");

AddEventHandler("iblock", "OnAfterIBlockElementUpdate", "OnAfterIBlockElementUpdateHandler");
AddEventHandler("iblock", "OnAfterIBlockElementAdd", "OnAfterIBlockElementUpdateHandler");
AddEventHandler("iblock", "OnBeforeIBlockElementUpdate","OnBeforeIBlockElementUpdateHandler");

function CreatePreviewFromDetail(&$arFields) {
	if ($arFields["IBLOCK_ID"]==5) {
		//     print_r($arFields);
		//значения по умолчанию
		
		// нужно ли приводить к нужным размерам превьюшку, если она грузится отдельно
		$peview="y";
		
		// нужно ли приводить к нужным размерам детаил, если он грузится отдельно
		$detail="y";
		
		//Задаем максимальные размеры превьюшки
		//Свойства PREV_WIDTH и PREV_HEIGHT типа "число"
		$prev_width = "";
		$prev_height = "150";
		
		//Задаем максимальные размеры детальной картинки
		//СвойстваDET_WIDTH и DET_HEIGHT типа "число"
		$det_width = "1000";
		$det_height = "800";
		
		//=========
		
		//разрешить увеличивать детальные картинки
		//Свойство DET_ZOOM типа "список", чекбокс
		$det_zoom = "";
		 if(CModule::IncludeModule('iblock'))
		 {
		
			 $res = CIBlockElement::GetByID($arFields["ID"]);
			 $obRes = $res->GetNextElement();
		
		//     print_r($obRes);
		
		//превьюшка
				  //считываем значения из инфоблока, если пусто - исполььзуются значения по-умолчанию
		
				  if(($ar_res = $obRes->GetProperty("PREV_WIDTH"))&&($ar_res["VALUE"]!="")){
					 $prev_w = $ar_res["VALUE"];
				}
		
				  if(($ar_res = $obRes->GetProperty("PREV_HEIGHT"))&&($ar_res["VALUE"]!="")){
					 $prev_h = $ar_res["VALUE"];
				}
		
		   // если задан один из параметров высота-ширина, он считается основным, второй - несущественным
				if(($prev_w!="")||($prev_h)!=""){
				 $prev_width  = $prev_w;
				 $prev_height = $prev_h;
				}
		
		//картинка
		
				 if(($ar_res = $obRes->GetProperty("DET_WIDTH"))&&($ar_res["VALUE"]!="")){
					 $det_w = $ar_res["VALUE"];
				}
		
				 if(($ar_res = $obRes->GetProperty("DET_HEIGHT"))&&($ar_res["VALUE"]!="")){
					 $det_h = $ar_res["VALUE"];
				}
				  if(($det_w!="")||($det_h)!=""){
				 $det_width  = $det_w;
				 $det_height = $det_h;
				}
		
		//увеличивать картинку
				if(($ar_res = $obRes->GetProperty("DET_ZOOM"))&&($ar_res["VALUE"]!="")){
					 $det_zoom = "Y";
				}
		
		
				$dbr = CIBlockElement::GetByID($arFields['ID']);
				if ($ar = $dbr->Fetch()):
		
			  // тут меняется размер отдельно-загружаемой превьюшки
		/*      if (($arFields["PREVIEW_PICTURE"]["name"]!="")&&($arFields["DETAIL_PICTURE"]["name"]=="")&&$peview="y")
				{
				   $img_path = $_SERVER['DOCUMENT_ROOT'].CFile::GetPath($ar['PREVIEW_PICTURE']);
				   $arImgPath=explode("/",$img_path);
				   $filename = array_pop ($arImgPath);
				   $folderImg = implode ("/",$arImgPath);
				   $small_img_path = resize_img($img_path,$folderImg,$filename,"",$prev_width, $prev_height, "", 80);
				   $be = new CIBlockElement();
				   $be->Update($arFields['ID'], Array('PREVIEW_PICTURE'=>CFile::MakeFileArray($small_img_path)), false);
				   @unlink($small_img_path);
		
				}
		*/
			  if(($ar['DETAIL_PICTURE']>0)&&($ar['PREVIEW_PICTURE']==0))
			   {
				   $img_path = $_SERVER['DOCUMENT_ROOT'].CFile::GetPath($ar['DETAIL_PICTURE']);
				   $arImgPath=explode("/",$img_path);
				   $filename = array_pop ($arImgPath);
				   $folderImg = implode ("/",$arImgPath);
				   $small_img_path = resize_img($img_path,$folderImg,$filename,"s_",$prev_width, $prev_height, "", 80);
				   $large_img_path = resize_img($img_path,$folderImg,$filename,"",$det_width, $det_height, $det_zoom, 95);
				   $be = new CIBlockElement();
				   $be->Update($arFields['ID'], Array('PREVIEW_PICTURE'=>CFile::MakeFileArray($small_img_path)), false);
				   $be->Update($arFields['ID'], Array('DETAIL_PICTURE'=>CFile::MakeFileArray($large_img_path)), false);
				   @unlink($small_img_path);
				   @unlink($large_img_path);
			   }
			   endif;
			}
		}
	}


// функции для работы с доп. изображениями
function OnAfterIBlockElementUpdateHandler(&$arFields){
	if ($arFields["IBLOCK_ID"]==5) {

		$new_files="";
		$del_files="";
		//значения по умолчанию
		
		//Задаем максимальные размеры превьюшки
		//Свойства PREV_WIDTH и PREV_HEIGHT типа "число"
		$prev_width = "";
		$prev_height = "150";
		$prev="y"; // делать ли превьюхи
		
		//Задаем максимальные размеры детальной картинки
		//СвойстваDET_WIDTH и DET_HEIGHT типа "число"
		$det_width = "1000";
		$det_height = "800";
		
		//=========
		
		//разрешить увеличивать детальные картинки
		//Свойство DET_ZOOM типа "список", чекбокс
		$det_zoom = "";
	
		if($arFields["RESULT"]){
			$res = CIBlockElement::GetByID($arFields["ID"]);
			$obRes = $res->GetNextElement();
			if(($ar_res = $obRes->GetProperty("MORE_PHOTO"))&&($ar_res["ID"]!="")){
			$ID = $ar_res["ID"];
		}
	
		foreach($arFields["PROPERTY_VALUES"][$ID] as $key=>$value)
			  {
	
				if (($value["name"]!=""))
					{
					 $new_files=$new_files.$value["name"].",  ";
	
	
						  if(CModule::IncludeModule('iblock'))
							{
								   $element_=CIBlockElement::GetList(Array(), Array("IBLOCK_ID"=>$arFields["IBLOCK_ID"] , "ID"=>$arFields["ID"]));
								   if ($ar_element_ = $element_->GetNextElement())
									{
								   $ar_props_=$ar_element_->GetProperties();
								   //перебираем все море фотов в поисках нужной, дабы записать туды терь мал картинку
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
								   //$new[]=resize_img2($path,$folder,$filename,150);
								   if ($prev!="n")
								   $small_img_path = resize_img($path,$folder,$filename,"s_",$prev_width, $prev_height, "", 90);
								   $large_img_path = resize_img($path,$folder,$filename,"",$det_width, $det_height, $det_zoom, 95);
	
									  }
									}
							}
	
	
					}
			  }

		}
		else
			AddMessage2Log("Ошибка изменения записи ".$arFields["ID"]." (".$arFields["RESULT_MESSAGE"].").");
	}
}

function OnBeforeIBlockElementUpdateHandler(&$arFields){
	if ($arFields["IBLOCK_ID"]==5) {

     // echo "<pre>";
     // print_r($arFields);
     // echo"</pre>";


            $del_files="";
        if($arFields["ID"])
            {


                    $res = CIBlockElement::GetByID($arFields["ID"]);
                                $obRes = $res->GetNextElement();

                          if(($ar_res = $obRes->GetProperty("MORE_PHOTO"))&&($ar_res["ID"]!="")){
                     $ID = $ar_res["ID"];
                                }

                        foreach($arFields["PROPERTY_VALUES"][$ID] as $key=>$value)


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

            }
        else
            AddMessage2Log("Ошибка изменения записи ".$arFields["ID"]." (".$arFields["RESULT_MESSAGE"].").");
    }
}


function resize_img ($img_src_path, $folder, $filename, $prefix, $width, $height, $zoom, $quality)
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


                        if ($height!=""){
                                $crop = $height / $height_orig;
                                $height_dest = $height;
                                $width_dest = $width_orig * $crop;

                    }

                        if ($width!=""){
                                   $crop = $width / $width_orig;
                                $width_dest = $width;
                                $height_dest = $height_orig * $crop;

                                if(($height!="") && ($height_dest > $height)){
                                        $crop = $height / $height_dest;
                                        $height_dest = $height;
                                        $width_dest = $width_dest * $crop;
                                }
                        }


    if((($width_orig > $width)&&($height_orig > $height))||($zoom!=""))
   {
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


           imagecopyresampled($img_dest, $img_orig, 0, 0, 0, 0, $width_dest, $height_dest, $width_orig, $height_orig);

                         $new_img_path = $folder."/".$prefix.$filename;

            if($img_type == "JPG")
                        if (function_exists("imagejpeg"))
                                {
                            imagejpeg($img_dest, $new_img_path, $quality);
                                }
            if($img_type == "GIF")
                        if (function_exists("imagegif"))
                                {
                            imagegif($img_dest, $new_img_path);
                                }
            if($img_type == "PNG")
                        if (function_exists("imagepng"))
                                {
                            imagepng($img_dest, $new_img_path);
                                }
            if($img_type == "WBMP")
                        if (function_exists("imagewbmp"))
                                {
                            imagewbmp($img_dest, $new_img_path);
                                 }

                       imagedestroy($img_dest);
                       imagedestroy($img_orig);

           return $new_img_path;
   }

          else {
                  return $img_src_path;
        }
}


function send_mail($to, $thm, $html, $path, $from, $from_name)
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
   $headers .="From: $from_name <$from>\n";

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

function CaptchaCheckCode($captcha_word, $captcha_sid)
{
    include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");

    $cpt = new CCaptcha();
    return $cpt->CheckCode($captcha_word, $captcha_sid);
}

function ruslat($str)
{
    $arCyr = Array("ё","й","ц","у","к","е","н","г","ш","щ","з","х","ъ","ф",
"ы","в","а","п","р","о","л","д","ж","э","я","ч","с","м","и","т","ь","б","ю","Ё",
"Й","Ц","У","К","Е","Н","Г","Ш","Щ","З","Х","Ъ","Ф","Ы","В","А","П","Р","О","Л",
"Д","Ж","Э","Я","Ч","С","М","И","Т","Ь","Б","Ю");
    $arLat = Array("e","i","ts","u","k","e","n","g","sh","sch","z","h","",
"f","y","v","a","p","r","o","l","d","zh","e","ya","ch","s","m","i","t","","b",
"yu","e","i","ts","u","k","e","n","g","sh","sch","z","h","","f","y","v","a","p",
"r","o","l","d","zh","e","ya","ch","s","m","i","t","","b","yu");

    $str = preg_replace("/[^a-zа-яА-Я0-9 ]/i","",$str);
    $str = preg_replace("/ +/"," ",$str);
    $str = str_replace($arCyr,$arLat,$str);

    if(strlen($str)<=0) return "";

    $str = preg_replace("/w+/ei","ucfirst('\0')",$str);
    $str = str_replace(" ","",$str);
    return $str;
}
?>
