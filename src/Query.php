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

class Query extends Options {

	function process () {
		$params = array();

		if (isset($this->getopt['userid'])) {
			if (!is_numeric($this->getopt['userid'])
				|| $this->getopt['userid'] <= 0)
				throw new \Exception(sprintf('Invalid userid: %s', $this->getopt['userid']));
			$params['owner'] = $this->getopt['userid'];
		}
		
		if (count($params)) {		
			$query = http_build_query($params);

			$url = 'http://turistautak.hu/tracks.php?json&egylapon=999999&' . $query;
			
			$data = self::fetchData($url);
			if (!is_array(@$data['ids']))
				throw new \Exception(sprintf('Failed to get ids from url: %s', $url));
			return $data['ids'];

		} else if ($this->getopt['id']) {
			return array($this->getopt['id']);

		}
	}

	static function fetchData ($url) {
		$json = @file_get_contents($url);
		if ($json === false)
			throw new \Exception(sprintf('Failed to fetch url: ', $url));
			
		$data = json_decode($json, true);
		if (!@$data['success']) var_dump($url, $data);

		if (!@$data['success'])
			throw new \Exception(sprintf('Server reported error for request %s: %s',
				$url, @$data['message']));

		return($data);
	}
}
