<?php namespace KolesarAndras\TuristautakOsmTrackImport;

/**
 * parancssori paraméterek elérhetősége
 *
 * @package kolesar-andras/turistautak-osm-track-import
 * @url https://github.com/kolesar-andras/turistautak-osm-track-import
 *
 * @author Kolesár András <kolesar@turistautak.hu>
 * @since 2015.02.07
 *
 */

class Options {

	var $getopt;

	function __construct ($getopt) {
		$this->getopt = $getopt;
	}
	
}
