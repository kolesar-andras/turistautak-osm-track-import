<?php namespace KolesarAndras\TuristautakOsmTrackImport;

/**
 * nyomvonalak felöltése
 *
 * @package kolesar-andras/turistautak-osm-track-import
 * @url https://github.com/kolesar-andras/turistautak-osm-track-import
 *
 * @author Kolesár András <kolesar@turistautak.hu>
 * @since 2015.02.07
 *
 */

class Upload extends Task {

	function process ($id) {
		if (Options::get('verbose'))
			echo sprintf('Uploading track id=%d', $id), "\n";

		$storage = new Storage($id);
		$data = $storage->getData('turistautak');
		$osm = $storage->getData('osm');
		
		$username = null;
		$password = null;

		if (Options::get('osm-username') == '' ||
		    Options::get('osm-password') == '') {

			$josm = Options::get('josm');
			$josm = preg_replace('/^~/', $_SERVER['HOME'], $josm);
		    $prefs = simplexml_load_file($josm);

		    foreach ($prefs->tag as $tag) {
				if ($tag['key'] == 'osm-server.username')
					$username = (string) $tag['value'];

				if ($tag['key'] == 'osm-server.password')
					$password = (string) $tag['value'];
			}
		}

		if (Options::get('osm-username') != '')
			$username = Options::get('osm-username');

		if (Options::get('osm-password') != '')
			$password = Options::get('osm-password');

		$api = Options::get('api');		

	}	
}
