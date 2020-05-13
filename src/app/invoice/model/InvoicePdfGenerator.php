<?php
namespace invoice\model;

use invoice\config\InvoiceConfig;
use n2n\io\fs\FsPath;
use invoice\bo\Invoiceable;
use n2n\io\managed\impl\FsFileSource;
use n2n\io\managed\impl\CommonFile;

class InvoicePdfGenerator {
	private $invoiceable;
	private $invoiceConfig;
	private $pdf;
	
	public function __construct(Invoiceable $invoiceable, InvoiceConfig $invoiceConfig) {
		$this->invoiceable = $invoiceable;
		$this->invoiceConfig = $invoiceConfig; 
	}
	
	public function generatePdf($filePath) {
		$dtc = $this->invoiceConfig->getDynamicTextCollection();
		$pdf = new InvoicePdf($this->invoiceable, $dtc->translate('pdf_invoice_footer'), $this->invoiceConfig->getTaxNumber(), 
				$dtc, $this->invoiceConfig->getFileLogo(), $this->invoiceConfig->getInvoicingPartyAddress());
		$pdf->setHeadingAlignment($this->invoiceConfig->getHeadingAlignment());
		$pdf->setHeadingOffset($this->invoiceConfig->getHeadingOffset());
		$pdf->setPrintCountry($this->invoiceConfig->isPrintCountry());
		$pdf->setCustomerAddressHeader($this->invoiceConfig->getCustomerAddressHeader());
		$pdf->create()->Output($filePath, 'F');
		
		return new FsPath($filePath);
	}
	
	public function generateTmpPdf() {
		$file = $this->generatePdf(tempnam(sys_get_temp_dir(), 'invoice') . '.pdf');
		
		return new CommonFile(new FsFileSource($file), $this->invoiceable->getInvoiceNumber() . '.pdf');
	}
}
