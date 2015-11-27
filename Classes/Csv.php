<?php
namespace Rattazonk\CSV;

/**
 * @author Frederik Vosberg <frederik@rattazonk.com>
 */
class Csv {
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
	 * @var resource
	 */
	protected $resource;

	/**
	 * @var string
	 */
	protected $currentCharacter;

	/**
	 * @var string
	 */
	protected $lastCharacter;

	/**
	 * flag to allow getNextFieldInCurrentRow to jump to the next row
	 * @var bool
	 */
	protected $jumpToNextRow = FALSE;

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
		foreach($this->getLines() as $line) {
			$lines[] = $line;
		}
		return $lines;
	}

	public function getLines() {
		while(($line = $this->getNextLine())) {
			yield $line;
		}
	}

	public function getNextLine() {
		$fields = [];
		$this->jumpToNextRow = TRUE;
		while(($field = $this->getNextFieldInCurrentRow()) !== FALSE) {
			$fields[] = $field;
		}
		return $fields;
	}

	/**
	 * returns the value of the next field in the current row
	 * if there is no remaining field it returns FALSE
	 *
	 * @return string|FALSE
	 */
	public function getNextFieldInCurrentRow() {
		$field = '';
		$enclosed = FALSE;
		if($this->currentCharacter != $this->lineTerminator || $this->jumpToNextRow) {
			$this->jumpToNextRow = FALSE;
			while($this->getNextCharacter()) {
				if($this->currentCharacter === $this->separator && !$enclosed) {
					break;
				} else if($this->currentCharacter == $this->enclosure) {
					if(!$field) {
						$enclosed = TRUE;
						continue;
					} else if($enclosed) {
						$enclosed = FALSE;
						continue;
					} else if($this->lastCharacter == $this->enclosure) {
						$enclosed = TRUE;
					}
				} else if($this->currentCharacter == $this->lineTerminator) {
					if(!$enclosed) {
						break;
					}
				}
				$field .= $this->getCurrentCharacter();
			}
		}
		return $field ? $field : FALSE;
	}

	/**
	 * advances the internal character pointer
	 * and returns the new char
	 *
	 * @return string
	 */
	public function getNextCharacter() {
		$this->lastCharacter = $this->currentCharacter;
		$this->currentCharacter = fgetc($this->resource);
		return $this->currentCharacter;
	}

	/**
	 * @return string
	 */
	public function getCurrentCharacter() {
		return $this->currentCharacter;
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

	/**
	 * @param string $path
	 */
	public function readFile($path) {
		$this->resource = fopen($path, 'r');
	}
}
