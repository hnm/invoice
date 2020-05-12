<?php
namespace invoice\bo;

interface Invoiceable {
	public function getId();
	public function getTitle();
	public function getInvoiceNumber();
	public function getInvoiceDate();
	public function getStatus();
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
	
	/**
	 *  \n2n\intl\Locale
	 */
	public function getN2nLocale();
}