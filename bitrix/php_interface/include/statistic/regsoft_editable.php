<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/***************************************************************************
Convertation of the standard Regsoft CSV file to the 
CSV file format of the Statistics module.

Currency: USD
***************************************************************************/

/*
	Input parameters:
	INPUT_CSV_FILE - path to the source file
	OUTPUT_CSV_FILE - path to the resulting file
*/

function CleanUpCsv(&$item)
{
	$item = TrimEx($item, "\"");
}

function PrepareQuotes(&$item)
{
	$item = "\"".str_replace("\"","\"\"", $item)."\"";
}

if ($fp_in = fopen($INPUT_CSV_FILE,"rb"))
{
	if ($fp_out = fopen($OUTPUT_CSV_FILE,"wb"))
	{
		$i = 0; // counter of the read valuable lines
		$j = 0; // counter of the written to the resulting  file lines 
		// date format for the current language
		$lang_date_format = CLang::GetDateFormat("SHORT");
		$event1 = "regsoft";
		$event2 = "buy";
		$EVENT_ID = CStatEventType::ConditionSet($event1, $event2, $arEventType)." (".$event1." / ".$event2.")";
		$SITE_ID = GetEventSiteID(); // short site identifier (ID)
		while (!feof($fp_in)) 
		{
			$arrCSV = fgetcsv($fp_in, 4096, "|");
			if (is_array($arrCSV) && count($arrCSV)>1)
			{
				array_walk($arrCSV, "CleanUpCsv");
				reset($arrCSV);
				$i++;
				// if it is the first line then
				if ($i==1)
				{
					// get an array with the field numbers 
					$arrS = array_flip($arrCSV);
				}
				else // else form the CSV line in module format and write it to the resulting file 
				{
					$arrRes = array();

					// ID of an event type;
					$arrRes[] = $EVENT_ID;

					// event3
					$arrRes[] = $arrCSV[$arrS["Order ID"]]." / ".$arrCSV[$arrS["Product ID"]]." / ".$arrCSV[$arrS["Tracking ID"]];

					// date
					$arrRes[] = $DB->FormatDate($arrCSV[$arrS["Order Date"]], "MM/DD/YYYY", $lang_date_format);

					// additional parameter
					$ADDITIONAL_PARAMETER = $arrCSV[$arrS["Reseller ID"]];
					$arrRes[] = $ADDITIONAL_PARAMETER;

					// money sum
					$arrRes[] = $arrCSV[$arrS["Total"]];

					// currency
					$arrRes[] = "USD";

					// if short site identifier exists in Additional parameter then
					if (strpos($ADDITIONAL_PARAMETER,$SITE_ID)!==false)
					{
						// write the line to the resulting CSV file
						$j++;
						array_walk($arrRes, "PrepareQuotes");
						$str = implode(",",$arrRes);
						if ($j>1) $str = "\n".$str;
						fputs($fp_out, $str);
					}
				}
			}
		}
		@fclose($fp_out);
	}
	@fclose($fp_in);
}

/*

Column headers:

Tracking ID | Order ID | Product ID  | Country | Qty | E-Mail | Customer Name | Company Name | Address 1 | Address 2 | Address 3 | City | State | Zip | Order Date | Phone Number | Total | CD Purchase | Reseller ID | Order IP | Serial Number | Order Referrer | Price Code |  

*/
?>