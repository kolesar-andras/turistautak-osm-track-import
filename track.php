#!/usr/bin/env php
<?php

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

use Ulrichsg\Getopt\Getopt;
use Ulrichsg\Getopt\Option;

$getopt = new Getopt(array(
    Option::create(null, 'userid')->setDescription('numeric user id at turistautak.hu'),
    Option::create(null, 'username')->setDescription('user name at turistautak.hu'),
    Option::create(null, 'osm-username')->setDescription('OSM username for upload'),
    Option::create(null, 'osm-password')->setDescription('OSM password for upload'),
    Option::create(null, 'dev')->setDescription('use OSM development API'),
    Option::create(null, 'merge')->setDescription('merge files into single gpx file'),
    Option::create(null, 'merge-all')->setDescription('merge all processed files into a single gpx file'),
    Option::create(null, 'merge-regions')->setDescription('find and merge parts from all regions'),
    Option::create(null, 'crosstrack')->setDescription('set treshold for gpsbabel simplify crosstrack'),
    Option::create('h', 'help')->setDescription('this help'),
));

try {
	$getopt->parse();
} catch (UnexpectedValueException $e) {
	die($e->getMessage() . "\n");
}

if (!$getopt->getOperands()
	&& !$getopt->getOptions()
	|| $getopt['help']) {
	echo $getopt->getHelpText();
	exit(1);
}

