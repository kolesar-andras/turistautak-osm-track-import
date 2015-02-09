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

$autoload = __DIR__.'/vendor/autoload.php';
if (!is_file($autoload)) {
	echo "Setup incomplete, please run the following command:\n";
	echo "\n";
	echo "composer update\n";
	echo "\n";
	echo "If you do not have composer, run this command to get it:\n";
	echo "\n";
	echo 'php -r "readfile(\'https://getcomposer.org/installer\');" | php', "\n";
	echo "\n";
	echo "This installs itself as composer.phar in the current directory, then call this way:\n";
	echo "\n";
	echo "php composer.phar update\n";
	echo "\n";
	exit(1);
}
require_once($autoload);

try {
	$commandLine = new CommandLine();
	$commandLine->process();

} catch (\Exception $e) {
	echo $e->getMessage(), "\n";

}
