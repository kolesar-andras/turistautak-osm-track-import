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

class Convert extends Task {

	function process ($id) {
		if (Options::get('verbose'))
			echo sprintf('Converting track id=%d', $id), "\n";

		$storage = new Storage($id);
		$data = $storage->getData('turistautak');
		$dir = $storage->dir();
		foreach ($data['files'] as $file) {
			if (!preg_match('/\\.([^.]+)$/', $file['name'], $regs))
				throw new \Exception('Could not parse filename for extension');
			$extension = $regs[1];
			$format = $this->formatForExtension($extension);
			$cmd = sprintf('gpsbabel -i %s -f %s -o gpx -F %s 2>&1',
				escapeshellarg($format),
				escapeshellarg($dir . $file['name']),
				escapeshellarg($dir . $file['name'] . '.gpx'));
			$output = shell_exec($cmd);
			if ($output != '') 
				$storage->put('cgpsmapper.log', $output);
		}
	}
	
	function formatForExtension ($extension) {
		switch ($extension) {
			case 'gdb': return 'gdb';
			case 'mps': return 'mapsource';
			case 'gpx': return 'gpx';
			case 'wpt':	return 'oziexplorer';
			case 'plt':	return 'oziexplorer';

			case 'mp':
			case 'jpg':
			case 'png':
			case 'zip':
				return false;

			default: throw new \Exception('Unknown file type: ' . $extension);
		}
	}
}
