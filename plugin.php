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

//Get stats for `url` or `shorturl` between `since` and `until`.
function tpc_get_stats() {
	try {
		global $ydb;

		//Process `url` or `shorturl` parameter
		if(!isset($_REQUEST["url"]) && !isset($_REQUEST["shorturl"])) {
			return array(
				"errorCode" => 400,
				"message" => "error: Missing 'url' or 'shorturl' parameter.",
			);	
		}

		if(isset($_REQUEST["url"])) {
			$keywords = tpc_get_url_keywords($_REQUEST["url"]);
		} else {
			$pos = strrpos($_REQUEST["shorturl"], "/");
			//Accept "http://sho.rt/abc"
			if($pos !== false) {
				$keywords = substr($_REQUEST["shorturl"], $pos + 1);
			//Accept "abc"
			} else {
				$keywords = $_REQUEST["shorturl"];
			}

			if(!yourls_is_shorturl($keywords[0])) {
				return array(
					"errorCode" => 404,
					"message" => "error: not found",
				);	
			}
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
				"message" => "error: The 'since' value ($since) must be smaller than the 'until' value ($until).",
			);
		}
		
		$params = array(
			"shorturls" => $keywords,
			"since" => date("Y-m-d H:i:s", $since),
			"until" => date("Y-m-d H:i:s", $until),
		);
	
	  $sql = "SELECT COUNT(*)
	      FROM " . YOURLS_DB_TABLE_LOG . "
	      WHERE
					shorturl IN (:shorturls) AND
					click_time > :since AND
					click_time <= :until";

		$result = $ydb->fetchValue($sql, $params);

		return array(
			"statusCode" => 200,
			"message" => "success",
			"url-stats-period" => array(
				"clicks" => $result
			)
		);
	} catch (Exception $e) {
		return array(
			"errorCode" => 500,
			"message" => "error: " . $e->getMessage(),
		);
	}
}


//Get all keywords associated with `$url`
function tpc_get_url_keywords( $url ) {
	global $ydb;
	$table = YOURLS_DB_TABLE_URL;
	$url = yourls_sanitize_url($url);
	$rows = $ydb->fetchObjects("SELECT `keyword` FROM `$table` WHERE `url` = :url", array("url" => $url));

	$keywords = array_map(function($r) { return $r->keyword; }, $rows);
	
	return $keywords;
}
