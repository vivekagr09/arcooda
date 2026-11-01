<?php

defined('BASEPATH') OR exit('No direct script access allowed');


require 'AffModules/MarketTools.php';
require 'AffModules/UserProfile.php';

class AffiliateScript {

	public static function usersProfileStatus($data): array
	{
		try {
			$userProfile = new UserProfile($data);
			return [
				'status' => $userProfile->status()
			];
		} catch (Exception $e) {
			return self::prepareErrorDataFromException($e);	
		}
	}

	public static function usersMarketTools($data): array
	{
		try {

			$marketTools = new MarketTools($data);
			return $marketTools->list();

		} catch (Exception $e) {
			return self::prepareErrorDataFromException($e);	
		}
	}


	private static function prepareErrorDataFromException(Exception $e): array
	{
		return [
			'status' => false,
			'message' => 'Something went wrong',
			'err_messge' => $e->getMessage(),
			'err_line' => $e->getLine(),
			'err_file' => $e->getFile(),
			'err_code' => $e->getCode() ?? 500
		];
	}
}