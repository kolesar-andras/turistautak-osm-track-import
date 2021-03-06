<?php namespace KolesarAndras\TuristautakOsmTrackImport;

/**
 * nyomvonalak címkézése osm-re
 *
 * @package kolesar-andras/turistautak-osm-track-import
 * @url https://github.com/kolesar-andras/turistautak-osm-track-import
 *
 * @author Kolesár András <kolesar@turistautak.hu>
 * @since 2015.02.14
 *
 */

class Tag extends Task {

	const OSM_DESCRIPTION_LIMIT = 255;

	var $data;

	function process ($id) {
		if (Options::get('verbose'))
			echo sprintf('Tagging track id=%d', $id), "\n";

		$storage = new Storage($id);
		$this->data = $storage->getData('turistautak');
		
		$tags = [];
		$tags[] = $this->data['member'];
		$tags[] = $this->data['motion'];
		$tags[] = $this->data['gps'];
		$tags[] = $this->tagLog();
		$tags = array_merge($tags, $this->mapAttributes());
		// $tags[] = sprintf('trackid: %d', $this->data['id']);
		// $tags[] = sprintf('userid: %d', $this->data['owner']);
				
		$osm = [];
		$osm['name'] = self::getOsmName($this->data);
		$osm['description'] = $this->data['name'];
		if ($this->data['description'] != '')
			$osm['description'] .= ' | ' . $this->data['description'];
		
		// az osm korlátozza a leírás hosszát
		if (mb_strlen($osm['description']) > self::OSM_DESCRIPTION_LIMIT)
			$osm['description'] = mb_substr($osm['description'], 0, self::OSM_DESCRIPTION_LIMIT-3) . '...';
			
		$osm['visibility'] = Options::get('visibility');
		$osm['crosstrack'] = Simplify::getCrosstrack($this->data['motion']);
		$osm['tags'] = self::cleanTags($tags);
		
		$storage->putData('osm', $osm);
	}

	static function getOsmName ($data) {
		return self::stripFileName($data['id'] . ' ' . $data['name']);
	}
	
	static function stripFileName ($name) {
		$name = self::deaccent($name);
		$name = str_replace(' - ', '-', $name);
		return $name;
	}
	
	static function cleanTags ($tags) {
		$out = [];
		foreach ($tags as $tag) {
			if ($tag === null || $tag === '') continue;
			$out[] = $tag;
		}
		return $out;
	}
	
	function mapAttributes () {
		$tags = [];
		foreach (self::attributes() as $kulcs => $szempont)
			if (@$this->data['attributes'][$kulcs] == '+')
				$tags[] = $szempont;
		return $tags;
	}
	
	function tagLog () {
		$tag = @self::log()[$this->data['log']];
		$suffix = null;
		switch ($this->data['log']) {
			case 'auto':
				$suffix = self::log_auto()[$this->data['log_setting']];
				break;
				
			case 'time':
				$suffix = sprintf('%s mp', $this->data['log_setting']);
				break;
				
			case 'dist':
				$suffix = sprintf('%s m', $this->data['log_setting']);
				break;
				
			case 'save':
				break;
				
			default:
				if ($tag === null) $tag = $this->data['log'];
				if ($this->data['log_setting']) {
					$suffix = $this->data['log_setting'];
				}
		
			}
		
		if ($suffix === null) {
			return $tag;
		} else {
			return sprintf('%s (%s)', $tag, $suffix);
		}
	}
	
	static function attributes () {
		return [
			1	=> 'nem volt GPS-vételt zavaró körülmény',
			2	=> 'kalibrált barométeres magasság-adatok',
			3	=> 'végig jó műholdállás',
			4	=> 'nem szakad a track menet közben',
			5	=> 'oda-vissza track a teljes útvonalról',
			6	=> 'útkereszteződések bejárva',
			7	=> 'felesleges részek levágva',
			8	=> 'trackek és útpontok elnevezései beszédesek',
			9	=> 'vázlat készült a csomópontokról',
			10	=> 'minden lehetséges csomópont megjelölve',
			11	=> 'magam szerkesztem a térképre',
		];	
	}
	
	static function log () {
		return [
			'auto' => 'automata',
			'time' => 'időalapú',
			'dist' => 'távolság-alapú',
			'save' => 'mentett track',
			'other' => 'egyéb',
		];
	}

	static function log_auto () {
		return [
			5	=> 'leggyakrabban',
			4	=> 'gyakrabban',
			3	=> 'normál',
			2	=> 'ritkábban',
			1	=> 'legritkábban',
		];
	}

	static function deaccent ($string) {
		if (is_array($string)) {
			foreach ($string as $k=>$v) {
				$out[$k] = deaccent($v);
			}
			return $out;
		} else {
			return str_replace(
				array('á', 'é', 'í', 'ó', 'ú', 'ö', 'ő', 'ü', 'ű',
					  'Á', 'É', 'Í', 'Ó', 'Ú', 'Ö', 'Ő', 'Ü', 'Ű'),
				array('a', 'e', 'i', 'o', 'u', 'o', 'o', 'u', 'u',
					  'A', 'E', 'I', 'O', 'U', 'O', 'O', 'U', 'U'),
				$string);
		}
	}
}
