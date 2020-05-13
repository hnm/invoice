<?php
namespace invoice\bo;

class CommonInvoiceableAddress implements InvoiceableAddress {
	private $name;
	private $address1;
	private $address2;
	private $address3;
	private $organisation;
	private $country;
	private $email;
	private $phone;
	
	public function getName() {
		return $this->name;
	}
	
	public function setName($name) {
		$this->name = $name;
	}
	
	public function getAddress1() {
		return $this->address1;
	}
	
	public function setAddress1($address1) {
		$this->address1 = $address1;
	}
	
	public function getAddress2() {
		return $this->address2;
	}
	
	public function setAddress2($address2) {
		$this->address2 = $address2;
	}
	
	public function getAddress3() {
		return $this->address3;
	}
	
	public function setAddress3($address3) {
		$this->address3 = $address3;
	}
	
	public function getOrganisation() {
		return $this->organisation;
	}
	
	public function setOrganisation($organisation) {
		$this->organisation = $organisation;
	}
	
	public function getCountry() {
		return $this->country;
	}
	
	public function setCountry($country) {
		$this->country = $country;
	}
	
	public function getEmail() {
		return $this->email;
	}
	
	public function setEmail($email) {
		$this->email = $email;
	}
	
	public function getPhone() {
		return $this->phone;
	}
	
	public function setPhone($phone) {
		$this->phone = $phone;
	}
}