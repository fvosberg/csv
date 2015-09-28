<?php
namespace Rattazonk\CSV;

/**
 * @author Frederik Vosberg <frederik@rattazonk.com>
 */
class Csv {
	/**
	 * @var resource
	 */
	protected $resource;

	/**
	 * instantiates CSV from string
	 */
	public static function readFromString($string) {
		$instance = new static();
		$instance->setResource(fopen("data://text/plain,$string", 'r'));
		return $instance;
	}

	/**
	 * @param resource $resource
	 */
	public function setResource($resource) {
		$this->resource = $resource;
	}

	/**
	 * @return array
	 */
	public function toArray() {
		return explode(',', fgets($this->resource));
	}
}
