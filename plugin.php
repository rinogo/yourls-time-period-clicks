<?php
/*
Plugin Name: Time-Period Clicks
Plugin URI: https://github.com/rinogo/yourls-time-period-clicks
Description: A simple plugin for reporting clicks in a specific time period.
Version: 1.0
Author: Rich Christiansen
Author URI: http://endorkins.com
*/

// Define custom action "url-stats-period"
yourls_add_filter("api_action_url-stats-period", "tpc_get_stats");

//Get stats for `shorturl` between `since` and `until`.
function tpc_get_stats() {
	try {
		global $ydb;

		//Process `shorturl` parameter
		if(!isset($_REQUEST["shorturl"])) {
			return array(
				"errorCode" => 400,
				"message"    => "error: Missing 'shorturl' parameter.",
			);	
		}
		$shorturl = $_REQUEST["shorturl"];
		$keyword = str_replace(YOURLS_SITE . "/" , "", $shorturl); //Accept either 'http://sho.rt/abc' or 'abc'

		if(!yourls_is_shorturl($keyword)) {
			return array(
				"errorCode" => 404,
				"message"    => "error: not found",
			);	
		}

		//Process `since` and `until` parameters.
		if(isset($_REQUEST["since"])) {
			$since = intval($_REQUEST["since"]);
		} else {
			$since = 0; //Default to the Unix Epoch
		}

		if(isset($_REQUEST["until"])) {
			$until = intval($_REQUEST["until"]);
		} else {
			$until = time(); //Default to now
		}

		if($since >= $until) {
			return array(
				"errorCode" => 400,
				"message"    => "error: The 'since' value ($since) must be smaller than the 'until' value ($until).",
			);
		}
		
		$params = array(
			"shorturl" => $keyword,
			"since"    => date("Y-m-d H:i:s", $since),
			"until"    => date("Y-m-d H:i:s", $until),
		);
	
	  $sql = "SELECT COUNT(*)
	      FROM " . YOURLS_DB_TABLE_LOG . "
	      WHERE
					shorturl = :shorturl AND
					click_time > :since AND
					click_time <= :until";

		$result = $ydb->fetchValue($sql, $params);

		return array(
			"statusCode"       => 200,
			"message"          => "success",
			"url-stats-period" => array(
				"clicks"         => $result
			)
		);
	} catch (Exception $e) {
		return array(
			"errorCode" => 500,
			"message"    => "error: " . $e->getMessage(),
		);
	}
}
