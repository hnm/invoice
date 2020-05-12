<?php
namespace inovice\bo;

interface InvoiceableAddress {
	public function getId();
	public function getName();
	public function getAddress1();
	public function getAddress2();
	public function getAddress3();
	public function getOrganisation();
	public function getCountry();	
	public function getEmail();
	public function getPhone();
}