<?php namespace KolesarAndras\TuristautakOsmTrackImport;

/**
 * azonosítók letöltése
 *
 * @package kolesar-andras/turistautak-osm-track-import
 * @url https://github.com/kolesar-andras/turistautak-osm-track-import
 *
 * @author Kolesár András <kolesar@turistautak.hu>
 * @since 2015.02.07
 *
 */

class Query {

	function process () {
		$params = array();

		if (Options::exists('userid')) {
			if (!is_numeric(Options::get('userid'))
				|| Options::get('userid') <= 0)
				throw new \Exception(sprintf('Invalid userid: %s', Options::get('userid')));
			$params['owner'] = Options::get('userid');
		}

		if (Options::exists('username')) {
			// a turistautak.hu ISO-8859-2-ben várja
			// feltételezzük, hogy a felhasználó UTF-8 kódolásban adta meg
			// ha mégsem, akkor megadhatja ékezet nélkül is, úgy is működik
			$params['member'] = iconv('UTF-8', 'ISO-8859-2', Options::get('username'));
		}
		
		if (count($params)) {		
			$query = http_build_query($params);

			$url = 'http://turistautak.hu/tracks.php?json&egylapon=999999&' . $query;
			
			$data = self::fetchData($url);
			if (!is_array(@$data['ids']))
				throw new \Exception(sprintf('Failed to get ids from url: %s', $url));
			return $data['ids'];

		} else if (Options::get('id')) {
			return array(Options::get('id'));

		}
	}

	static function fetchData ($url) {
		$json = self::fetchUrl($url);
		if ($json === false)
			throw new \Exception(sprintf('Failed to fetch url: %s', $url));
			
		$data = json_decode($json, true);

		if (!@$data['success'])
			throw new \Exception(sprintf('Server reported error for request %s: %s',
				$url, @$data['message']));

		return($data);
	}
	
	static function fetchUrl ($url) {
		$context = null;
		if (Options::get('proxy') != '') {
			$settings = array(
				'http' => array(
					'proxy' => Options::get('proxy'),
					'request_fulluri' => true,
				),
			);
			$context = stream_context_create($settings);
		}
		return @file_get_contents($url, false, $context);
	}
}
