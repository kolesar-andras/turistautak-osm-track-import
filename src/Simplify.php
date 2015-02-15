<?php namespace KolesarAndras\TuristautakOsmTrackImport;

/**
 * nyomvonalak egyszerűsítése
 *
 * @package kolesar-andras/turistautak-osm-track-import
 * @url https://github.com/kolesar-andras/turistautak-osm-track-import
 *
 * @author Kolesár András <kolesar@turistautak.hu>
 * @since 2015.02.07
 *
 */

class Simplify extends Task {

	const CROSSTRACK_GYALOG = 0.0001;
	const CROSSTRACK_BICIKLI = 0.0002;
	const CROSSTRACK_AUTO = 0.0005;

	function process ($id) {
		if (Options::get('verbose'))
			echo sprintf('Simplifying track id=%d', $id), "\n";

		$storage = new Storage($id);
		$storage->rmdir(Storage::DIR_OSM);
		$data = $storage->getData('turistautak');
		$osm = $storage->getData('osm');
		$filter = self::filter($osm['crosstrack']);
		foreach ($data['files'] as $file) {
			if (isset($file['skip'])) continue;
			Babel::process($id,
				Storage::DIR_GPX . $file['name'] . '.gpx',
				Storage::DIR_OSM . $file['name'] . '.gpx',
				$filter
			);
		}
		if (!Options::get('keep-gpx') &&
			!Options::get('keep-all'))
			$storage->rmdir(Storage::DIR_GPX);
	}
	
	static function getCrosstrack ($motion) {
		if (Options::get('crosstrack'))
			return Options::get('crosstrack');
			
		if (preg_match('/gyalog|futva/i', $motion))
			return self::CROSSTRACK_GYALOG;

		if (preg_match('/bicikli/i', $motion))
			return self::CROSSTRACK_BICIKLI;

		if (preg_match('/autó|motor|vonat|enduro/i', $motion))
			return self::CROSSTRACK_AUTO;

		// ha nem tudjuk eldönteni, válasszuk a legbiztosabbat
		return self::CROSSTRACK_GYALOG;
	}
	
	function filter ($crosstrack) {
		return sprintf('-x simplify,crosstrack,error=%sk', $crosstrack);
	}
	
}
