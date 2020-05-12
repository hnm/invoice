<?php
namespace invoice\model;

use invoice\config\InvoiceConfig;
use n2n\io\fs\FsPath;
use invoice\bo\Invoiceable;

class InvoicePdfGenerator {
	private $invoiceable;
	private $invoiceConfig;
	private $pdf;
	
	public function __construct(Invoiceable $invoice, InvoiceConfig $invoiceConfig) {
		$this->invoice = $invoice;
		$this->invoiceConfig = $invoiceConfig; 
	}
	
	public function generatePdf($filePath) {
		$dtc = $this->invoiceConfig->getTextCollection($this->invoice->getLocale());
		$pdf = new InvoicePdf($this->invoice, $dtc->translate('pdf_invoice_footer'), $this->invoiceConfig->getTaxNumber(), 
				$dtc, $this->invoiceConfig->getFileLogo(), 
				$this->invoiceConfig->getInvoicingPartyAddress1(), 
				$this->invoiceConfig->getInvoicingPartyAddress2());
		$pdf->setHeadingAlignment($this->invoiceConfig->getHeadingAlignment());
		$pdf->setHeadingOffset($this->invoiceConfig->getHeadingOffset());
		$pdf->setPrintCountry($this->invoiceConfig->isPrintCountry());
		$pdf->setInvoicingPartyAddress3($this->invoiceConfig->getInvoicingPartyAddress3());
		$pdf->setCustomerAddressHeader($this->invoiceConfig->getCustomerAddressHeader());
		$pdf->create()->Output($filePath, 'F');
		
		return new FsPath($filePath);
	}
	
	public function generateTmpPdf() {
		$file = $this->generatePdf(tempnam(sys_get_temp_dir(), 'invoice') . '.pdf');
		$file->setOriginalName($this->invoiceable->getInvoiceNumber() . '.pdf');
		
		return $file;
	}
}
