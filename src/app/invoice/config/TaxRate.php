<?php
namespace invoice\config;

class TaxRate {
	private $name;
	private $int;
	
	public function __construct($name, $value) {
		$this->name = $name;
		$this->value = $value;
	}
	public function getName() {
		return $this->name;
	}
	
	public function getValue() {
		return $this->value;
	}
}
