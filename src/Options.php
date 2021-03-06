<?php namespace KolesarAndras\TuristautakOsmTrackImport;

/**
 * nyomvonalak átalakítása
 *
 * @package kolesar-andras/turistautak-osm-track-import
 * @url https://github.com/kolesar-andras/turistautak-osm-track-import
 *
 * @author Kolesár András <kolesar@turistautak.hu>
 * @since 2015.02.07
 *
 */

class Options {

	static $getopt;

	function init ($getopt) {
		self::$getopt = $getopt;
	}
	
	function all () {
		return self::$getopt;
	}
	
	function exists ($option) {
		return isset(self::$getopt[$option]);
	}

	function get ($option) {
		return self::$getopt[$option];
	}
		
}
