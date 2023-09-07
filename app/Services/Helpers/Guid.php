<?php
namespace App\Services\Helpers;

class Guid
{
	/**
	 * Generate GUID
	 * @return string
	 */
	static public function get()
	{
		$sGuid = strtoupper(md5(uniqid(rand(), TRUE)));
		$separator = chr(45);

		return substr($sGuid, 0, 8) . $separator .
			substr($sGuid, 8, 4) . $separator .
			substr($sGuid, 12, 4) . $separator .
			substr($sGuid, 16, 4) . $separator .
			substr($sGuid, 20, 12);
	}
}