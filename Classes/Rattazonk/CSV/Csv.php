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
	 * field separator
	 * @var string
	 */
	protected $separator = ',';

	/**
	 * field enclosure
	 * @var string
	 */
	protected $enclosure = '"';

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
		$array = explode($this->separator, fgets($this->resource));
		foreach($array as &$v) {
			$v = trim($v, $this->enclosure);
		}
		return $array;
	}

	/**
	 * @param string $separator separator for fields
	 */
	public function setSeparator($separator) {
		$this->separator = $separator;
	}

	/**
	 * @param string $enclosure field enclosure
	 */
	public function setEnclosure($enclosure) {
		$this->enclosure = $enclosure;
	}

	/**
	 * @param string $input
	 */
	public function readString($input) {
		$this->resource = fopen("data://text/plain,$input", 'r');
	}
}
