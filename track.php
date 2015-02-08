<?php namespace KolesarAndras\TuristautakOsmTrackImport;

/**
 * turistautak.hu nyomvonalak áttöltése OSM-re
 *
 * @package kolesar-andras/turistautak-osm-track-import
 * @url https://github.com/kolesar-andras/turistautak-osm-track-import
 *
 * @author Kolesár András <kolesar@turistautak.hu>
 * @since 2015.02.07
 *
 */

require_once __DIR__.'/vendor/autoload.php';

try {
	$commandLine = new CommandLine();
	$commandLine->process();

} catch (\Exception $e) {
	echo $e->getMessage(), "\n";

}
