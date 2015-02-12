<?php namespace KolesarAndras\TuristautakOsmTrackImport;

/**
 * nyomvonalak összevetéseaz OSM állományával
 *
 * @package kolesar-andras/turistautak-osm-track-import
 * @url https://github.com/kolesar-andras/turistautak-osm-track-import
 *
 * @author Kolesár András <kolesar@turistautak.hu>
 * @since 2015.02.07
 *
 */

class Compare extends Task {

	const API = 'http://api.openstreetmap.org/api/0.6';
	const PRECISION = 4; // ennyi tizedesre számoljuk a befoglalót
	const SAMPLES = 3; // ennyi mintát veszünk a nyomvonalból
	const MARGIN = 0.05; // ekkora részét nem vizsgáljuk a nyomvonalnak
	const OSMDIGITS = 7; // ennyi tizedesig tárol az OSM pontot
	const FILTER = '-x simplify,crosstrack,error=0.0020k'; // ezzel szűrjük az állományt az összehasonításhoz

	function process ($id) {
		if (Options::get('verbose'))
			echo sprintf('Comparing track id=%d', $id), "\n";

		$storage = new Storage($id);
		$data = $storage->getData('turistautak');
		foreach ($data['files'] as $file) {
			// egyszerűsítjük a nyomvonalat, hogy lehetőleg olyan pontot vizsgáljunk,
			// amit nem szűrtek ki hasonló módszerrel a feltöltés előtt
			// egyúttal kisebb xml-t kell végignéznünk
			Babel::process($id, $file['name'] . '.gpx', $file['name'] . '.compare.gpx', self::FILTER);
		
			// a gpsbabel leszedi a Garmin MapSource által használt kiterjesztések névtér-hivatkozását
			// amit viszont a simplexml hiányol, ezért pótoljuk röptében
			$content = $storage->get($file['name'] . '.compare.gpx');
			$content = preg_replace(
				'/<gpxx:([a-z]+)Extension/i',
				'<gpxx:\1Extension xmlns:gpxx="http://www.garmin.com/xmlschemas/GpxExtensions/v3">',
				$content);
			$gpx = simplexml_load_string($content);

			$count = 0;
			foreach ($gpx->trk as $trk)
				foreach ($trk->trkseg as $trkseg)
					foreach ($trkseg->trkpt as $trkpt)
						$count++;

			$start = $count * self::MARGIN;
			$range = (int) floor($count - $start*2);
			$steps = (int) floor($range / (self::SAMPLES-1));
			$i = 0;
			$points = array();
			foreach ($gpx->trk as $trk)
				foreach ($trk->trkseg as $trkseg)
					foreach ($trkseg->trkpt as $trkpt)
						if (($i++-$start) % $steps == 0) $points[$i] = $trkpt;
			
			$power = pow(10, self::PRECISION);
			$format = sprintf('%%1.%1$df,%%1.%1$df,%%1.%1$df,%%1.%1$df',
				self::PRECISION);
			
			$matches = array();
			$same = 0;
			$count = 0;
			$duplicate = 0;
			foreach ($points as $point) {
				$lat = (float) $point['lat'];
				$lon = (float) $point['lon'];

				$bbox = sprintf($format,
					floor($lon * $power)/$power,
					floor($lat * $power)/$power,
					(floor($lon * $power)+1)/$power,
					(floor($lat * $power)+1)/$power);
				
				$url = self::API . sprintf('/trackpoints?bbox=%s&page=0', $bbox);
				$gpxfile = Query::fetchUrl($url);
				if ($gpxfile === false)
					throw new \Exception('Could not get trackpoints from OSM');

				$osm = simplexml_load_string($gpxfile);
				$hasSame = 0;
				foreach ($osm->trk as $trk) {
				foreach ($trk->trkseg as $trkseg) {
				foreach ($trkseg->trkpt as $trkpt) {
					
					if ($this->samePoint($point, $trkpt)) $hasSame++;
					$osmlat = (float) $trkpt['lat'];
					$osmlon = (float) $trkpt['lon'];
					
					$match = array(
						'gpx' => $point,
						'osm' => $trkpt,
					);
					
					$matches[] = $match;
				}
				}
				}
				if ($hasSame) $same++;
				if ($hasSame > 1) $duplicate++;
				$count++;
			}
			$warning = '';
			if ($duplicate) $warning = sprintf(' %d duplicates on OSM', $duplicate);
			if (Options::get('verbose')) echo sprintf('%d same from %d points%s [%s]', $same, $count, $warning, $file['name']), "\n";
		}
	}
	
	function samePoint ($point1, $point2) {
		return
			$this->sameTime($point1, $point2) &&
			$this->samePosition($point1, $point2);
	}
	
	function sameTime ($point1, $point2) {
		return (string) $point1->time == (string) $point2->time;
	}

	function samePosition ($point1, $point2) {
		return (
			$this->osmDigits($point1['lat']) == 
			$this->osmDigits($point1['lat']) &&
			$this->osmDigits($point1['lon']) == 
			$this->osmDigits($point1['lon'])
		);			
	}

	function osmDigits ($coord) {
		$format = sprintf('%%1.%df', self::OSMDIGITS);
		return sprintf($format, $coord);
	}
	
}
