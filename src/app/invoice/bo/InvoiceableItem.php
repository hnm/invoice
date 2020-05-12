<?php
namespace invoice\bo;

interface InvoiceableItem {
	public function getId();
	public function getText();
	public function getAmount();
	public function getUnitPrice();
	public function getTaxRate();
	public function getInvoice();
}