<?php
namespace invoice\bo;

interface Invoiceable {
	public function getTitle();
	public function getInvoiceNumber();
	public function getInvoiceDate();
	public function getTextHtml();
	public function getCurrency();
	/**
	 * @return InvoiceableItem[]
	 */
	public function getInvoiceableItems();
	/**
	 * @return \invoice\bo\Invoic
	 */
	public function getInvoiceableAddress();
	public function isTaxIncluded();
	public function getN2nLocale();
}