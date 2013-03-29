<?
class CSaleHelper
{
	public static function IsAssociativeArray($ar)
	{
		if (count($ar) <= 0)
			return false;

		$fl = false;

		$arKeys = array_keys($ar);
		$ind = -1;
		foreach ($arKeys as $key)
		{
			$ind++;
			if ($key."!" !== $ind."!" && "".$key !== "n".$ind)
			{
				$fl = true;
				break;
			}
		}

		return $fl;
	}
}
