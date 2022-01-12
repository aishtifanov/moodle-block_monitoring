<?php // $Id: lib_curl.php,v 1.2 2007/12/20 13:33:09 Shtifanov Exp $

// Функция возвращает размер файла или 0
function check_url_file($url)
{
 if (eregi( '^http://', $url)) 	{
	$urlArray = parse_url($url);
	if (!$urlArray[port]) $urlArray[port] = '80';
	if (!$urlArray[path]) $urlArray[path] = '/';
	// if (!$urlArray[path]) return 0;

	$end = false;
	// $fp = fsockopen ($hostname, 80, &$errnr, &$errstr) or die("$errno:$errstr");
	$fp = fsockopen($urlArray[host], $urlArray[port], &$errnum, &$errstr);
	if (!$fp) return 0;

	fputs($fp, "HEAD ".$urlArray[path]." HTTP/1.0\n\n");
	while (!$end) {
		$line = fgets($fp, 2048);
		if (trim($line) == "") {
			$end = true;
		} else {
			$str = explode(": ", $line);
			if ($str[0] == "Content-Length")
				 $res = $str[1];
				// print "Size of ".$filename." file ".$str[1]." bytes";
		}
	}
	fclose($fp);

 } else $res = 0;

 return $res;
}

function check_url($url)
{
	if (eregi( '^http://', $url)) 	{
		$urlArray = parse_url($url);
		if (!$urlArray[port]) $urlArray[port] = '80';
		if (!$urlArray[path]) $urlArray[path] = '/';
		$sock = fsockopen($urlArray[host], $urlArray[port], &$errnum, &$errstr);
		if (!$sock) $res = 'DNS';
		else {
			$dump .= "GET $urlArray[path] HTTP/1.1\r\n";
			$dump .= "Host: $urlArray[host]\r\nConnection: close\r\n";
			$dump .= "Connection: close\r\n";
			fputs($sock, $dump);
			while ($str = fgets($sock, 1024)) {
				if (eregi("^http/[0-9]+.[0-9]+ ([0-9]{3}) [a-z ]*", $str))
					$res[code] = trim(eregi_replace('^http/[0-9]+.[0-9]+([0-9]{3})[a-z ]*', "\\1", $str));
				if (eregi("^Content-Type: ", $str))
					$res[contentType] = trim(eregi_replace("^Content-Type: ", "", $str));
			}
			fclose($sock);
			flush();
			return $res[code];
		}
	} else $res = "N/A";

    return $res;
}
?>
