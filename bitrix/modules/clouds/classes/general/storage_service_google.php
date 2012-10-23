<?
IncludeModuleLangFile(__FILE__);

class CCloudStorageService_GoogleStorage
{
	protected $status = 0;
	protected $errno = 0;
	protected $errstr = '';
	protected $result = '';
	protected $new_end_point;

	function GetLastRequestStatus()
	{
		return $this->status;
	}

	function GetObject()
	{
		return new CCloudStorageService_GoogleStorage();
	}

	function GetID()
	{
		return "google_storage";
	}

	function GetName()
	{
		return "Google Storage";
	}

	function GetLocationList()
	{
		return array(
			"EU" => "Europe",
			"US" => "United States",
		);
	}

	function GetSettingsHTML($arBucket, $bServiceSet, $cur_SERVICE_ID, $bVarsFromForm)
	{
		if($bVarsFromForm)
			$arSettings = $_POST["SETTINGS"][$this->GetID()];
		else
			$arSettings = unserialize($arBucket["SETTINGS"]);

		if(!is_array($arSettings))
			$arSettings = array("PROJECT_ID" => "", "ACCESS_KEY" => "", "SECRET_KEY" => "");

		$htmlID = htmlspecialchars($this->GetID());

		$result = '
		<tr id="SETTINGS_0_'.$htmlID.'" style="display:'.($cur_SERVICE_ID == $this->GetID() || !$bServiceSet? '': 'none').'" class="settings-tr">
			<td><span class="required">*</span>'.GetMessage("CLO_STORAGE_GOOGLE_EDIT_PROJECT_ID").':</td>
			<td><input type="hidden" name="SETTINGS['.$htmlID.'][PROJECT_ID]" id="'.$htmlID.'PROJECT_ID" value="'.htmlspecialchars($arSettings['PROJECT_ID']).'"><input type="text" size="55" name="'.$htmlID.'INP_" id="'.$htmlID.'INP_PROJECT_ID" value="'.htmlspecialchars($arSettings['PROJECT_ID']).'" '.($arBucket['READ_ONLY'] == 'Y'? '"disabled"': '').' onchange="BX(\''.$htmlID.'PROJECT_ID\').value = this.value"></td>
		</tr>
		<tr id="SETTINGS_1_'.$htmlID.'" style="display:'.($cur_SERVICE_ID == $this->GetID() || !$bServiceSet? '': 'none').'" class="settings-tr">
			<td><span class="required">*</span>'.GetMessage("CLO_STORAGE_GOOGLE_EDIT_ACCESS_KEY").':</td>
			<td><input type="hidden" name="SETTINGS['.$htmlID.'][ACCESS_KEY]" id="'.$htmlID.'ACCESS_KEY" value="'.htmlspecialchars($arSettings['ACCESS_KEY']).'"><input type="text" size="55" name="'.$htmlID.'INP_ACCESS_KEY" id="'.$htmlID.'INP_ACCESS_KEY" value="'.htmlspecialchars($arSettings['ACCESS_KEY']).'" '.($arBucket['READ_ONLY'] == 'Y'? '"disabled"': '').' onchange="BX(\''.$htmlID.'ACCESS_KEY\').value = this.value"></td>
		</tr>
		<tr id="SETTINGS_2_'.$htmlID.'" style="display:'.($cur_SERVICE_ID == $this->GetID() || !$bServiceSet? '': 'none').'" class="settings-tr">
			<td><span class="required">*</span>'.GetMessage("CLO_STORAGE_GOOGLE_EDIT_SECRET_KEY").':</td>
			<td><input type="hidden" name="SETTINGS['.$htmlID.'][SECRET_KEY]" id="'.$htmlID.'SECRET_KEY" value="'.htmlspecialchars($arSettings['SECRET_KEY']).'"><input type="text" size="55" name="'.$htmlID.'INP_SECRET_KEY" id="'.$htmlID.'INP_SECRET_KEY" value="'.htmlspecialchars($arSettings['SECRET_KEY']).'" autocomplete="off" '.($arBucket['READ_ONLY'] == 'Y'? '"disabled"': '').' onchange="BX(\''.$htmlID.'SECRET_KEY\').value = this.value"></td>
		</tr>
		<tr id="SETTINGS_3_'.$htmlID.'" style="display:'.($cur_SERVICE_ID == $this->GetID() || !$bServiceSet? '': 'none').'" class="settings-tr">
			<td>&nbsp;</td>
			<td>'.BeginNote().GetMessage("CLO_STORAGE_GOOGLE_EDIT_HELP").EndNote().'</td>
		</tr>
		';
		return $result;
	}

	function CheckSettings($arBucket, &$arSettings)
	{
		global $APPLICATION;
		$aMsg = array();

		$result = array(
			"PROJECT_ID" => is_array($arSettings)? trim($arSettings["PROJECT_ID"]): '',
			"ACCESS_KEY" => is_array($arSettings)? trim($arSettings["ACCESS_KEY"]): '',
			"SECRET_KEY" => is_array($arSettings)? trim($arSettings["SECRET_KEY"]): '',
		);

		if($arBucket["READ_ONLY"] !== "Y" && !strlen($result["PROJECT_ID"]))
			$aMsg[] = array("id" => $this->GetID()."INP_PROJECT_ID", "text" => GetMessage("CLO_STORAGE_GOOGLE_EMPTY_PROJECT_ID"));

		if($arBucket["READ_ONLY"] !== "Y" && !strlen($result["ACCESS_KEY"]))
			$aMsg[] = array("id" => $this->GetID()."INP_ACCESS_KEY", "text" => GetMessage("CLO_STORAGE_GOOGLE_EMPTY_ACCESS_KEY"));

		if($arBucket["READ_ONLY"] !== "Y" && !strlen($result["SECRET_KEY"]))
			$aMsg[] = array("id" => $this->GetID()."INP_SECRET_KEY", "text" => GetMessage("CLO_STORAGE_GOOGLE_EMPTY_SECRET_KEY"));

		if(!empty($aMsg))
		{
			$e = new CAdminException($aMsg);
			$APPLICATION->ThrowException($e);
			return false;
		}
		else
		{
			$arSettings = $result;
		}

		return true;
	}

	function CreateBucket($arBucket)
	{
		global $APPLICATION;

		if($arBucket["LOCATION"])
			$content =
				'<CreateBucketConfiguration>'.
				'<LocationConstraint>'.$arBucket["LOCATION"].'</LocationConstraint>'.
				'</CreateBucketConfiguration>';
		else
			$content = '';

		$response = $this->SendRequest(
			$arBucket["SETTINGS"]["ACCESS_KEY"],
			$arBucket["SETTINGS"]["SECRET_KEY"],
			'PUT',
			$arBucket["BUCKET"],
			'/',
			'',
			$content,
			array(
				"x-goog-project-id" => $arBucket["SETTINGS"]["PROJECT_ID"],
			)
		);

		if($this->status == 409/*Already exists*/)
		{
			$APPLICATION->ResetException();
			return true;
		}
		else
		{
			return is_array($response);
		}
	}

	function DeleteBucket($arBucket)
	{
		global $APPLICATION;

		if($arBucket["PREFIX"])
		{
			//Do not delete bucket if there is some files left
			if(!$this->IsEmptyBucket($arBucket))
				return false;

			//Do not delete bucket if there is some files left in other prefixes
			$arAllBucket = $arBucket;
			$arBucket["PREFIX"] = "";
			if(!$this->IsEmptyBucket($arAllBucket))
				return true;
		}

		$response = $this->SendRequest(
			$arBucket["SETTINGS"]["ACCESS_KEY"],
			$arBucket["SETTINGS"]["SECRET_KEY"],
			'DELETE',
			$arBucket["BUCKET"]
		);

		if($this->status == 204/*No content*/ || $this->status == 404/*Not exists*/)
		{
			$APPLICATION->ResetException();
			return true;
		}
		else
		{
			return is_array($response);
		}
	}

	function IsEmptyBucket($arBucket)
	{
		global $APPLICATION;

		$response = $this->SendRequest(
			$arBucket["SETTINGS"]["ACCESS_KEY"],
			$arBucket["SETTINGS"]["SECRET_KEY"],
			'GET',
			$arBucket["BUCKET"],
			'/',
			'?max-keys=1'.($arBucket["PREFIX"]? '&prefix='.$arBucket["PREFIX"]: '')
		);

		if($this->status == 404)
		{
			$APPLICATION->ResetException();
			return true;
		}
		elseif(is_array($response))
		{
			return
				!isset($response["ListBucketResult"])
				|| !is_array($response["ListBucketResult"])
				|| !isset($response["ListBucketResult"]["#"])
				|| !is_array($response["ListBucketResult"]["#"])
				|| !isset($response["ListBucketResult"]["#"]["Contents"])
				|| !is_array($response["ListBucketResult"]["#"]["Contents"]);
		}
		else
		{
			return false;
		}
	}

	function GetFileSRC($arBucket, $arFile)
	{
		global $APPLICATION;

		if($arBucket["CNAME"])
		{
			$host = $arBucket["CNAME"];
		}
		else
		{
			switch($arBucket["LOCATION"])
			{
			case "EU":
				$host = $arBucket["BUCKET"].".commondatastorage.googleapis.com";
				break;
			case "US":
				$host = $arBucket["BUCKET"].".commondatastorage.googleapis.com";
				break;
			default:
				$host = $arBucket["BUCKET"].".commondatastorage.googleapis.com";
				break;
			}
		}

		$URI = $arBucket["PREFIX"]? $arBucket["PREFIX"].'/': '';
		if(is_array($arFile))
			$URI .= ltrim($arFile["SUBDIR"]."/".$arFile["FILE_NAME"], "/");
		else
			$URI .= ltrim($arFile, "/");

		$proto = $APPLICATION->IsHTTPS()? "https": "http";

		return $proto."://$host/".CCloudUtil::URLEncode($URI, LANG_CHARSET);
	}

	function FileExists($arBucket, $filePath)
	{
		global $APPLICATION;

		if($arBucket["PREFIX"])
		{
			if(substr($filePath, 0, strlen($arBucket["PREFIX"])+2) != "/".$arBucket["PREFIX"]."/")
				$filePath = "/".$arBucket["PREFIX"]."/".ltrim($filePath, "/");
		}

		$response = $this->SendRequest(
			$arBucket["SETTINGS"]["ACCESS_KEY"],
			$arBucket["SETTINGS"]["SECRET_KEY"],
			'HEAD',
			$arBucket["BUCKET"],
			$filePath
		);

		if($this->status == 200)
		{
			return true;
		}
		elseif($this->status == 206)
		{
			$APPLICATION->ResetException();
			return true;
		}
		else//if($this->status == 404)
		{
			$APPLICATION->ResetException();
			return false;
		}
	}

	function FileCopy($arBucket, $arFile, $filePath)
	{
		global $APPLICATION;

		if($arBucket["PREFIX"])
		{
			if(substr($filePath, 0, strlen($arBucket["PREFIX"])+2) != "/".$arBucket["PREFIX"]."/")
				$filePath = "/".$arBucket["PREFIX"]."/".ltrim($filePath, "/");
		}

		$response = $this->SendRequest(
			$arBucket["SETTINGS"]["ACCESS_KEY"],
			$arBucket["SETTINGS"]["SECRET_KEY"],
			'PUT',
			$arBucket["BUCKET"],
			$filePath,
			'',
			'',
			array(
				"x-goog-acl"=>"public-read",
				"x-goog-copy-source"=>CCloudUtil::URLEncode("/".$arBucket["BUCKET"]."/".($arBucket["PREFIX"]? $arBucket["PREFIX"]."/": "").$arFile["SUBDIR"]."/".$arFile["FILE_NAME"], LANG_CHARSET),
				"Content-Type"=>$arFile["CONTENT_TYPE"]
			)
		);

		if($this->status == 200)
		{
			return $this->GetFileSRC($arBucket, $filePath);
		}
		else//if($this->status == 404)
		{
			$APPLICATION->ResetException();
			return false;
		}
	}

	function DownloadToFile($arBucket, $arFile, $filePath)
	{
		$obRequest = new CHTTP;
		$obRequest->follow_redirect = true;
		return $obRequest->Download($this->GetFileSRC($arBucket, $arFile), $filePath);
	}

	function DeleteFile($arBucket, $filePath)
	{
		global $APPLICATION;

		if($arBucket["PREFIX"])
		{
			if(substr($filePath, 0, strlen($arBucket["PREFIX"])+2) != "/".$arBucket["PREFIX"]."/")
				$filePath = "/".$arBucket["PREFIX"]."/".ltrim($filePath, "/");
		}

		$response = $this->SendRequest(
			$arBucket["SETTINGS"]["ACCESS_KEY"],
			$arBucket["SETTINGS"]["SECRET_KEY"],
			'DELETE',
			$arBucket["BUCKET"],
			$filePath
		);

		if($this->status == 204 || $this->status == 404)
		{
			$APPLICATION->ResetException();
			return true;
		}
		else
		{
			$APPLICATION->ResetException();
			return false;
		}
	}

	function SaveFile($arBucket, $filePath, $arFile)
	{
		global $APPLICATION;

		if($arBucket["PREFIX"])
		{
			if(substr($filePath, 0, strlen($arBucket["PREFIX"])+2) != "/".$arBucket["PREFIX"]."/")
				$filePath = "/".$arBucket["PREFIX"]."/".ltrim($filePath, "/");
		}

		$response = $this->SendRequest(
			$arBucket["SETTINGS"]["ACCESS_KEY"],
			$arBucket["SETTINGS"]["SECRET_KEY"],
			'PUT',
			$arBucket["BUCKET"],
			$filePath,
			'',
			(
				array_key_exists("content", $arFile)
				?$arFile["content"]
				:file_get_contents($arFile["tmp_name"])
			),
			array(
				"x-goog-acl"=>"public-read",
				"Content-Type"=>$arFile["type"]
			)
		);

		if($this->status == 200)
		{
			return true;
		}
		else
		{
			$APPLICATION->ResetException();
			return false;
		}
	}

	function ListFiles($arBucket, $filePath, $bRecursive = false)
	{
		global $APPLICATION;

		$result = array(
			"dir" => array(),
			"file" => array(),
			"file_size" => array(),
		);

		$filePath = trim($filePath, '/');
		if(strlen($filePath))
			$filePath .= '/';

		if($arBucket["PREFIX"])
		{
			if(substr($filePath, 0, strlen($arBucket["PREFIX"])+2) != "/".$arBucket["PREFIX"]."/")
				$filePath = $arBucket["PREFIX"]."/".ltrim($filePath, "/");
		}

		$marker = '';
		while(true)
		{
			$response = $this->SendRequest(
				$arBucket["SETTINGS"]["ACCESS_KEY"],
				$arBucket["SETTINGS"]["SECRET_KEY"],
				'GET',
				$arBucket["BUCKET"],
				'/',
				'?'.($bRecursive? '': 'delimiter=/&').'prefix='.urlencode($filePath).'&marker='.urlencode($marker)
			);

			if(
				$this->status == 200
				&& is_array($response)
				&& isset($response["ListBucketResult"])
				&& is_array($response["ListBucketResult"])
				&& isset($response["ListBucketResult"]["#"])
				&& is_array($response["ListBucketResult"]["#"])
			)
			{
				if(
					isset($response["ListBucketResult"]["#"]["CommonPrefixes"])
					&& is_array($response["ListBucketResult"]["#"]["CommonPrefixes"])
				)
				{
					foreach($response["ListBucketResult"]["#"]["CommonPrefixes"] as $a)
					{
						$dir_name = substr(rtrim($a["#"]["Prefix"][0]["#"], "/"), strlen($filePath));
						$result["dir"][] = urldecode($dir_name);
					}
				}

				if(
					isset($response["ListBucketResult"]["#"]["Contents"])
					&& is_array($response["ListBucketResult"]["#"]["Contents"])
				)
				{
					foreach($response["ListBucketResult"]["#"]["Contents"] as $a)
					{
						$file_name = substr($a["#"]["Key"][0]["#"], strlen($filePath));
						$result["file"][] = urldecode($file_name);
						$result["file_size"][] = $a["#"]["Size"][0]["#"];
					}
				}

				if(
					isset($response["ListBucketResult"]["#"]["IsTruncated"])
					&& is_array($response["ListBucketResult"]["#"]["IsTruncated"])
					&& $response["ListBucketResult"]["#"]["IsTruncated"][0]["#"] === "true"
					&& strlen($response["ListBucketResult"]["#"]["NextMarker"][0]["#"]) > 0
				)
				{
					$marker = $response["ListBucketResult"]["#"]["NextMarker"][0]["#"];
					continue;
				}
				else
				{
					break;
				}
			}
			else
			{
				$APPLICATION->ResetException();
				return false;
			}
		}

		return $result;
	}

	function SendRequest($access_key, $secret_key, $verb, $bucket, $file_name='/', $params='', $content='', $additional_headers=array())
	{
		global $APPLICATION;
		$this->status = 0;

		$Content = $content;
		if(isset($additional_headers["Content-Type"]))
		{
			$ContentType = $additional_headers["Content-Type"];
			unset($additional_headers["Content-Type"]);
		}
		else
		{
			$ContentType = $content? 'text/plain': '';
		}

		if(!array_key_exists("x-goog-api-version", $additional_headers))
			$additional_headers["x-goog-api-version"] = "1";

		$RequestMethod = $verb;
		$RequestURI = CCloudUtil::URLEncode($file_name, LANG_CHARSET);
		$RequestDATE = gmdate('D, d M Y H:i:s', time()).' GMT';

		//Prepare Signature
		$CanonicalizedAmzHeaders = "";
		ksort($additional_headers);
		foreach($additional_headers as $key => $value)
			if(preg_match("/^x-goog-/", $key))
				$CanonicalizedAmzHeaders .= $key.":".$value."\n";

		$CanonicalizedResource = "/".$bucket.$RequestURI;

		$StringToSign = "$RequestMethod\n\n$ContentType\n$RequestDATE\n$CanonicalizedAmzHeaders$CanonicalizedResource";
		//$utf = $APPLICATION->ConvertCharset($StringToSign, LANG_CHARSET, "UTF-8");

		$Signature = base64_encode($this->hmacsha1($StringToSign, $secret_key));
		$Authorization = "GOOG1 ".$access_key.":".$Signature;

		$obRequest = new CHTTP;
		$obRequest->additional_headers["Date"] = $RequestDATE;
		$obRequest->additional_headers["Authorization"] = $Authorization;
		foreach($additional_headers as $key => $value)
			if(!preg_match("/^option-/", $key))
			$obRequest->additional_headers[$key] = $value;

		if(
			$this->new_end_point
			&& preg_match('#^(http|https)://'.preg_quote($bucket, '#').'(.+)/#', $this->new_end_point, $match))
		{
			$host = $match[2];
		}
		else
		{
			$host = $bucket.".commondatastorage.googleapis.com";
		}

		$was_end_point = $this->new_end_point;
		$this->new_end_point = '';

		$obRequest->Query($RequestMethod, $host, 80, $RequestURI.$params, $Content, '', $ContentType);
		$this->status = $obRequest->status;
		$this->errno = $obRequest->errno;
		$this->errstr = $obRequest->errstr;
		$this->result = $obRequest->result;

		if($obRequest->status == 200)
		{
			if(isset($additional_headers["option-raw-result"]))
			{
				return $obRequest->result;
			}
			elseif($obRequest->result)
			{
				$obXML = new CDataXML;
				$text = preg_replace("/<"."\\?XML.*?\\?".">/i", "", $obRequest->result);
				if($obXML->LoadString($text))
				{
					$arXML = $obXML->GetArray();
					if(is_array($arXML))
					{
						return $arXML;
					}
				}
				//XML parse error
				$APPLICATION->ThrowException(GetMessage('CLO_STORAGE_GOOGLE_XML_PARSE_ERROR', array('#errno#'=>1)));
				return false;
			}
			else
			{
				//Empty success result
				return array();
			}
		}
		elseif(
			$obRequest->status == 307  //Temporary redirect
			&& isset($obRequest->headers["Location"])
			&& !$was_end_point //No recurse yet
		)
		{
			$this->new_end_point = $obRequest->headers["Location"];
			return $this->SendRequest(
				$access_key,
				$secret_key,
				$verb,
				$bucket,
				$file_name,
				$params,
				$content
			);
		}
		elseif($obRequest->status > 0)
		{
			if($obRequest->result)
			{
				$obXML = new CDataXML;
				if($obXML->LoadString($obRequest->result))
				{
					$arXML = $obXML->GetArray();
					if(is_array($arXML) && is_string($arXML["Error"]["#"]["Message"][0]["#"]))
					{
						$APPLICATION->ThrowException(GetMessage('CLO_STORAGE_GOOGLE_XML_ERROR', array('#errmsg#'=>trim($arXML["Error"]["#"]["Message"][0]["#"], '.'))));
						return false;
					}
				}
			}
			$APPLICATION->ThrowException(GetMessage('CLO_STORAGE_GOOGLE_XML_PARSE_ERROR', array('#errno#'=>2)));
			return false;
		}
		else
		{
			$APPLICATION->ThrowException(GetMessage('CLO_STORAGE_GOOGLE_XML_PARSE_ERROR', array('#errno#'=>3)));
			return false;
		}
	}

	function hmacsha1($data, $key)
	{
		if(strlen($key)>64)
			$key=pack('H*', sha1($key));
		$key = str_pad($key, 64, chr(0x00));
		$ipad = str_repeat(chr(0x36), 64);
		$opad = str_repeat(chr(0x5c), 64);
		$hmac = pack('H*', sha1(($key^$opad).pack('H*', sha1(($key^$ipad).$data))));
		return $hmac;
	}
}
?>