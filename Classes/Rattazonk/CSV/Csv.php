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
	 * @var string
	 */
	protected $lineTerminator = "\n";

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
		$lines = [];
		$currentLine = '';
		while(($char = fgetc($this->resource)) !== FALSE) {
			if($char !== $this->lineTerminator){
				$currentLine .= $char;
			} else {
				$lines[] = $currentLine;
				$currentLine = '';
			}
		}
		// if the last char is not a line terminator the last line must be added 
		// separatly
		if($currentLine) {
			$lines[] = $currentLine;
		}
		foreach($lines as &$line) {
			$line = explode($this->separator, $line);
			foreach($line as &$column) {
				$column = trim($column, $this->enclosure);
			}
		}
		return $lines;
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
	 * @param string $lineTerminator line terminator
	 */
	public function setLineTerminator($lineTerminator) {
		$this->lineTerminator = $lineTerminator;
	}

	/**
	 * @param string $input
	 */
	public function readString($input) {
		$this->resource = fopen("data://text/plain,$input", 'r');
	}
}
