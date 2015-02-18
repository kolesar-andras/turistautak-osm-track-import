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
 
class Format {

	static function isTrackFile ($filename) {
		return self::formatForFilename($filename) != '';
	}

	static function extension ($filename) {
		if (!preg_match('/\.([^.]+)$/', $filename, $regs))
			return null;		
		return $regs[1];
	}
	
	static function formatForFilename ($filename) {
		$extension = self::extension($filename);
		$format = self::formatForExtension($extension);
		return $format;
	}

	static function formatForExtension ($extension) {
		switch ($extension) {
			case 'gdb': return 'gdb';
			case 'mps': return 'mapsource';
			case 'gpx': return 'gpx';
			case 'wpt':	return 'ozi';
			case 'plt':	return 'ozi';

			case 'mp':
			case 'jpg':
			case 'png':
			case 'zip':
				return false;

			default: 
				return null;
		}
	}
}
