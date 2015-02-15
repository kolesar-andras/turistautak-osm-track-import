<?php namespace KolesarAndras\TuristautakOsmTrackImport;

/**
 * parancssori felület
 *
 * @package kolesar-andras/turistautak-osm-track-import
 * @url https://github.com/kolesar-andras/turistautak-osm-track-import
 *
 * @author Kolesár András <kolesar@turistautak.hu>
 * @since 2015.02.07
 *
 */

use Ulrichsg\Getopt\Getopt;
use Ulrichsg\Getopt\Option;

class CommandLine {

	function process () {
		$getopt = new Getopt(array(
			Option::create(null, 'id', Getopt::OPTIONAL_ARGUMENT)
				->setDescription('numeric track id at turistautak.hu'),

			Option::create(null, 'userid', Getopt::OPTIONAL_ARGUMENT)
				->setDescription('numeric user id at turistautak.hu'),

			Option::create(null, 'username', Getopt::OPTIONAL_ARGUMENT)
				->setDescription('user name at turistautak.hu'),

			Option::create(null, 'osm-username', Getopt::OPTIONAL_ARGUMENT)
				->setDescription('OSM username for upload'),

			Option::create(null, 'osm-password', Getopt::OPTIONAL_ARGUMENT)
				->setDescription('OSM password for upload'),

			Option::create(null, 'josm', Getopt::OPTIONAL_ARGUMENT)
				->setDescription('path for JOSM preferences')
				->setDefaultValue('~/.josm/preferences.xml'),

			Option::create(null, 'visibility', Getopt::OPTIONAL_ARGUMENT)
				->setDescription('visibility of uploaded data [public]')
				->setDefaultValue('public')
				->setValidation(function($value){
					return in_array($value, [
						'private',
						'public',
						'trackable',
						'identifiable'
					]);
				}),

			Option::create(null, 'api', Getopt::OPTIONAL_ARGUMENT)
				->setDescription('use OSM API url')
				->setDefaultValue('http://api.openstreetmap.org/api/0.6'),

			Option::create(null, 'proxy', Getopt::OPTIONAL_ARGUMENT)
				->setDescription('http proxy'),

			Option::create(null, 'merge', Getopt::NO_ARGUMENT)
				->setDescription('merge files into single gpx file'),

			Option::create(null, 'merge-all', Getopt::NO_ARGUMENT)
				->setDescription('merge all processed files into a single gpx file'),

			Option::create(null, 'do-not-merge-regions', Getopt::NO_ARGUMENT)
				->setDescription('find and merge parts from all regions'),

			Option::create(null, 'crosstrack', Getopt::OPTIONAL_ARGUMENT)
				->setDescription('set threshold for gpsbabel simplify crosstrack'),

			Option::create(null, 'by-tasks', Getopt::NO_ARGUMENT)
				->setDescription('process by tasks'),

			Option::create('v', 'verbose', Getopt::NO_ARGUMENT)
				->setDescription('verbose output'),

			Option::create('d', 'debug', Getopt::NO_ARGUMENT)
				->setDescription('debug output'),

			Option::create('p', 'progress', Getopt::NO_ARGUMENT)
				->setDescription('debug output'),

			Option::create('h', 'help')
				->setDescription('this help'),
		));
		
		Options::init($getopt);

		try {
			$getopt->parse();
		} catch (UnexpectedValueException $e) {
			throw new \Exception($e->getMessage());
		}

		if ($GLOBALS['argc']<=1 || $getopt['help']) {
			echo $getopt->getHelpText();
			exit(1);
		}

		$taskManager = new TaskManager;
		$taskManager->process();
	
	}
}
