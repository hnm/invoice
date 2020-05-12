<?php
namespace invoice\config;

use n2n\context\RequestScoped;
use n2n\web\http\HttpContext;
use n2n\l10n\DynamicTextCollection;

class InvoiceConfig implements RequestScoped {
	
	const ALIGNMENT_LEFT = 'left';
	const ALIGNMENT_RIGHT = 'right';
	const ALIGNMENT_CENTER = 'center';
	
	private $httpContext;
	private $dtc;
	
	private function _init(HttpContext $httpContext, DynamicTextCollection $dtc) {
		$this->httpContext = $httpContext;
		$this->dtc = $dtc;
	}
	
	/**
	 * @return \n2n\io\fs\File
	 */
	public function getFileLogo() {
		return $this->httpContext->getAssetsUrl('invoice')->pathExt('logo.png');
	}

	public function getInvoicingPartyAddress1() {
		return 'Fa. Fischers Fritzle';
	}

	public function getInvoicingPartyAddress2() {
		return 'Witzlestr. 123';
	}
	
	public function getInvoicingPartyAddress3() {
		return '12345 Holeradio';
	}

	/**
	 * @param Heading Offset (mm)
	 */
	public function getHeadingOffset() {
		return 0;
	}

	public function isPrintCountry() {
		return true;
	}

	public function getHeadingAlignment() {
		return self::ALIGNMENT_CENTER;
	}
	
	public function getDynamicTextCollection() {
		return $this->dtc;
	}
	
	public function getTaxNumber() {
		return '{taxNumber}';
	}
	
	public function getCustomerAddressHeader() {
		return 'Customer Adress Header';
	}
	
	/**
	 * @return \invoice\config\TaxRate []
	 */
	public function getTaxRates() {
		return [new TaxRate('Mehrwertsteuer 8 %', '0.08'), 
				new TaxRate('Mehrwertsteuer 2%', '0.02')];
	}

}