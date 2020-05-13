<?php
namespace invoice\bo;

interface InvoiceableItem {
	public function getText();
	public function getAmount();
	public function getUnitPrice();
	public function getTaxRate();
	public function getInvoiceable();
}