<?php namespace KolesarAndras\TuristautakOsmTrackImport;

/**
 * felhasználó hitelesítő adatainak begyűjtése
 *
 * @package kolesar-andras/turistautak-osm-track-import
 * @url https://github.com/kolesar-andras/turistautak-osm-track-import
 *
 * @author Kolesár András <kolesar@turistautak.hu>
 * @since 2015.02.07
 *
 */

class Auth {

	var	$username;
	var $password;

	function __construct () {

		if (Options::get('osm-username') == '' ||
		    Options::get('osm-password') == '') {

			$josm = Options::get('josm');
			$josm = preg_replace('/^~/', $_SERVER['HOME'], $josm);
		    $prefs = simplexml_load_file($josm);

		    foreach ($prefs->tag as $tag) {
				if ($tag['key'] == 'osm-server.username')
					$this->username = (string) $tag['value'];

				if ($tag['key'] == 'osm-server.password')
					$this->password = (string) $tag['value'];
			}
		}

		if (Options::get('osm-username') != '')
			$this->username = Options::get('osm-username');

		if (Options::get('osm-password') != '')
			$this->password = Options::get('osm-password');
			
	}
	
	function basicData () {
		return base64_encode($this->username . ':' . $this->password);
	}

	function basicHeader () {
		return 'Authorization: Basic ' . $this->basicData();
	}

}

