<?php

/**
 * Trait cjwpbldr_helpers_utils
 */
trait cjwpbldr_helpers_utils
{

	/**
	 * @return string
	 */
	public function postMaxSize()
	{
		return @ini_get('post_max_size');
	}

	/**
	 * @return string
	 */
	public function uploadMaxFileSize()
	{
		return @ini_get('upload_max_filesize');
	}

	/**
	 * @param $val
	 *
	 * @return int|string
	 */
	public function getBytes($val)
	{
		$val = trim($val);
		$last = strtolower($val[strlen($val) - 1]);
		$val = (int) str_replace($last, '', $val);
		switch ($last) {
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}

		return $val;
	}

	/**
	 * 
	 * $heading_array should be in the following format: 
	 * 
	 * $heading_array = [
	 * 		'Heading 1',
	 * 		'Heading 2',
	 * 		'Heading 3',
	 * 		'Heading 4',
	 * ];
	 * 
	 * $row_array should be in the following format: 
	 * 
	 * $row_array = [
	 * 		[ 'data', 'data', 'data', 'data' ],
	 * 		[ 'data', 'data', 'data', 'data' ],
	 * 		[ 'data', 'data', 'data', 'data' ],
	 * 		[ 'data', 'data', 'data', 'data' ],
	 * ];
	 * 
	 * @param null $filename
	 * @param null $headings_array
	 * @param null $row_array
	 */
	public function createCSV($filename = null, $headings_array = null, $row_array = null)
	{
		ob_get_clean();
		$output_file = (is_null($filename)) ? date('Y-m-d-H-i-s') : $filename;
		if (is_null($row_array)) {
			return;
		}
		// output headers so that the file is downloaded rather than displayed
		header("Content-type: Application/CSV");
		header('Content-Disposition: attachment; filename=' . $output_file . '.csv');
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0
		ob_start();
		ob_flush();
		// create a file pointer connected to the output stream
		$output = @fopen('php://output', 'w');
		ob_end_clean();
		// output the column headings
		fputcsv($output, $headings_array);
		// loop over the rows, outputting them
		foreach ($row_array as $key => $value) {
			fputcsv($output, $value);
		}
		fclose($output);
		exit;
	}

	/**
	 * @param null $agent
	 * @param null $var
	 *
	 * @return array|mixed
	 */
	public function getBrowser($agent = null, $var = null)
	{
		$u_agent = ($agent != null) ? $agent : $_SERVER['HTTP_USER_AGENT'];
		$browser_name = 'Unknown';
		$platform = 'Unknown';
		$version = "";

		//First get the platform?
		if (preg_match('/linux/i', $u_agent)) {
			$platform = 'linux';
		} elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
			$platform = 'mac';
		} elseif (preg_match('/windows|win32/i', $u_agent)) {
			$platform = 'windows';
		}

		// Next get the name of the useragent yes seperately and for good reason
		if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
			$browser_name = 'Internet Explorer';
			$ub = "MSIE";
		} elseif (preg_match('/Firefox/i', $u_agent)) {
			$browser_name = 'Mozilla Firefox';
			$ub = "Firefox";
		} elseif (preg_match('/Chrome/i', $u_agent)) {
			$browser_name = 'Google Chrome';
			$ub = "Chrome";
		} elseif (preg_match('/Safari/i', $u_agent)) {
			$browser_name = 'Apple Safari';
			$ub = "Safari";
		} elseif (preg_match('/Opera/i', $u_agent)) {
			$browser_name = 'Opera';
			$ub = "Opera";
		} elseif (preg_match('/Netscape/i', $u_agent)) {
			$browser_name = 'Netscape';
			$ub = "Netscape";
		}

		// finally get the correct version number
		$known = ['Version', $ub, 'other'];
		$pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
		if (!preg_match_all($pattern, $u_agent, $matches)) {
			// we have no matching number just continue
		}

		// see how many we have
		$i = count($matches['browser']);
		if ($i != 1) {
			//we will have two since we are not using 'other' argument yet
			//see if version is before or after the name
			if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
				$version = $matches['version'][0];
			} else {
				$version = $matches['version'][1];
			}
		} else {
			$version = $matches['version'][0];
		}

		// check if we have a number
		if ($version == null || $version == "") {
			$version = "?";
		}

		$return = [
			'userAgent' => $u_agent,
			'name' => $browser_name,
			'type' => sanitize_title($browser_name),
			'version' => $version,
			'platform' => $platform,
			'pattern' => $pattern,
		];

		return (is_null($var)) ? $return : $return[$var];
	}

	/**
	 * @param        $service
	 * @param        $url_to_share
	 * @param string $title
	 * @param string $description
	 *
	 * @return mixed|string
	 */
	public function getSharingUrl($service, $url_to_share, $title = '', $description = '')
	{
		$url = $url_to_share;
		$services = [
			'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . $url,
			'twitter' => 'https://twitter.com/home?status=' . $url,
			'google' => 'https://plus.google.com/share?url=' . $url,
			'linkedin' => 'https://www.linkedin.com/shareArticle?mini=true&url=' . $url . '&title=' . $title . '&summary=' . $description . '&source=' . $url,
			'pinterest' => 'http://pinterest.com/pin/create/button/?url=' . $url . '&description=' . $description,
		];

		return (isset($services[$service])) ? $services[$service] : '#service-not-available';
	}

	public function getUrlParts($string, $var = 'host')
	{
		$array = parse_url($string);
		if (isset($array[$var])) {
			return $array[$var];
		}

		return false;
	}
}
