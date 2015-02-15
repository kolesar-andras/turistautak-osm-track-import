<?php namespace KolesarAndras\TuristautakOsmTrackImport;

/**
 * nyomvonalak felöltése
 *
 * @package kolesar-andras/turistautak-osm-track-import
 * @url https://github.com/kolesar-andras/turistautak-osm-track-import
 *
 * @author Kolesár András <kolesar@turistautak.hu>
 * @since 2015.02.07
 *
 */

class Upload extends Task {

	function process ($id) {
		if (Options::get('verbose'))
			echo sprintf('Uploading track id=%d', $id), "\n";

		$storage = new Storage($id);
		$data = $storage->getData('turistautak');
		$osm = $storage->getData('osm');

		if (!isset($osm['filename'])) return;
				
		$api = Options::get('api');
		if (substr($api, -1) != '/') $api .= '/';
		$url = $api . 'gpx/create';

		$auth = new Auth;
		$post = new Post($url);
		$post->header($auth->basicHeader());

		$post->fields = [
			'description' => $osm['description'],
			'visibility' => $osm['visibility'],
			'tags' => implode(', ', $osm['tags']),
		];
				
		$post->files = [
			'file' => [
				'filename' => $storage->dir() . $osm['filename'],
				'content-type' => 'application/octet-stream',
			],
		];
		
		if (Options::get('proxy'))
			$post->options = [
				'proxy' => Options::get('proxy'),
				'request_fulluri' => true,
			];

		$ret = null;
		try {
			$ret = $post->send();

		} catch (PostException $e) {
			foreach ($e->headers as $header)
				if (preg_match('/^Error:/iu', $header))
					throw new \Exception($header);
		}

		if (!is_numeric($ret))
			throw new \Exception('Could not get track id');
			
		$id = $ret;
		if (Options::get('verbose'))
			echo 'OSM track id is ' . $id, "\n";
			
		$osm['id'] = (int) $id;
		$osm['url'] = sprintf('https://www.openstreetmap.org/user/%s/traces/%s', urlencode($auth->username), $id);
		$osm['dateuploaded'] = date('Y-m-d H:i:s');
		$storage->putData('osm', $osm);
	}	
}
