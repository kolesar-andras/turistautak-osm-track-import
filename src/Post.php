<?php namespace KolesarAndras\TuristautakOsmTrackImport;

/**
 * multipart/form-data összeállítása
 *
 * @package kolesar-andras/turistautak-osm-track-import
 * @url https://github.com/kolesar-andras/turistautak-osm-track-import
 *
 * @author Kolesár András <kolesar@turistautak.hu>
 * @since 2015.02.07
 *
 */

class Post {

	const EOL = "\r\n";

	var $boundary;
	var $method = 'POST';
	var $url;
	var $headers = [];
	var $fields;
	var $files;
	var $options;

	function __construct ($url = null) {
		$this->url = $url;
	}

	function header ($key, $value=null) {
		if ($value === null && preg_match('/^([^:]+):\s?(.+)$/', $key, $regs)) {
			$key = $regs[1];
			$value = $regs[2];
		}
		$this->headers[$key] = $value;
	}

	function send () {
		$content = $this->build();
		
		$this->header('Content-Type', 'multipart/form-data; boundary=' . $this->boundary);
		$this->header('Content-Length', strlen($content));

		foreach ($this->headers as $key => $value) {
			$headers[] = $key . ': ' . $value;
		}
		
		$params = [
			'http' => [
				'method' => $this->method,
				'header' => implode(self::EOL, $headers),
				'content' => $content,
			]
		];
		
		if (is_array($this->options))
			$params['http'] = array_merge($params['http'], $options);
						
		$context = stream_context_create($params);
		$response = @file_get_contents($this->url, false, $context);
		if ($response === false)
			throw new PostException('HTTP error', $http_response_header);
			
		return $response;
	}

	function build () {
		$out = '';
		if ($this->boundary === null) $this->boundary = md5(time());
		
		foreach ($this->fields as $key => $value) {
			$out .= '--' . $this->boundary . self::EOL;
			$out .= sprintf('Content-Disposition: form-data; name="%s"', $key) . self::EOL;
			$out .= self::EOL;
			$out .= $value . self::EOL;
		}

		foreach ($this->files as $key => $file) {
			if (isset($file['content-type'])) {
				$contenttype = $file['content-type'];
			} else {
				$contenttype = mime_content_type($file['filename']);
			}

			if (isset($file['content'])) {
				$content = $file['content'];
			} else {
				$content = file_get_contents($file['filename']);
			}

			$out .= '--' . $this->boundary . self::EOL;
			$out .= sprintf('Content-Disposition: form-data; name="%s"; filename="%s"', $key, basename($file['filename'])) . self::EOL;
			$out .= sprintf('Content-Type: %s', $contenttype) . self::EOL;
			$out .= self::EOL;
			$out .= $content . self::EOL;
		}
		$out .= '--' . $this->boundary . '--';
		return $out;
	}	
}

class PostException extends \Exception {
	var $headers;
	public function __construct($message, $headers = null) {
		$this->headers = $headers;
        parent::__construct($message);
    }
}
