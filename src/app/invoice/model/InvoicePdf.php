<?php
namespace invoice\model;

use n2n\l10n\DateTimeFormat;
use n2n\l10n\L10nUtils;
use n2n\io\managed\File;
use n2n\l10n\DynamicTextCollection;
use n2n\io\managed\img\ImageFile;
use invoice\config\InvoiceConfig;
use invoice\bo\Invoiceable;

class InvoicePdf extends \TCPDF {
	
	const MARGIN_TOP = 15;
	const MARGIN_LEFT = 25;
	const MARGIN_RIGHT = 10;
	const DEFAULT_FONT_SIZE = 10;
	const DEFAULT_FONT_FAMILY = 'helvetica';
	const DEFAULT_CELL_HEIGHT = 5;
	const DATE_STYLE = DateTimeFormat::STYLE_MEDIUM;
	const HEADING_FONT_SIZE = 14;
	const DEFAULT_ALIGNMENT = 'C';
	
	private $invoiceable;
	private $fileLogo;
	private $invoicingPartyAddress1;
	private $invoicingPartyAddress2;
	private $invoicingPartyAddress3;
	private $dtc;
	private $pageName;
	private $headingAlignment = self::DEFAULT_ALIGNMENT;
	private $headingOffset;
	private $dateFormatter;
	private $taxNumber;
	private $printCountry = true;
	private $customerAddressHeader;
	
	public function __construct(Invoiceable $invoiceable, $pageName, $taxNumber, DynamicTextCollection $dtc, 
			File $fileLogo = null, $invoicingPartyAddress1 = null, $invoicingPartyAddress2 = null) {
		parent::__construct();
		$this->invoiceable = $invoiceable;
		$this->fileLogo = $fileLogo;
		$this->invoicingPartyAddress1 = $invoicingPartyAddress1;
		$this->invoicingPartyAddress2 = $invoicingPartyAddress2;
		$this->pageName = $pageName;
		$this->taxNumber = $taxNumber;
		$this->dtc = $dtc;
		$locales = $this->dtc->getLocales();
		$this->dateFormatter = DateTimeFormat::createDateInstance(reset($locales), self::DATE_STYLE);
	}
	
	public function setHeadingAlignment($headingAlignment) {
		if (null === $headingAlignment) return;
		switch ($headingAlignment) {
			case 'L':
			case 'C':
			case 'R':
				$this->headingAlignment = $headingAlignment;
				break;
			case InvoiceConfig::ALIGNMENT_CENTER:
				$this->headingAlignment = 'C';
				break;
			case InvoiceConfig::ALIGNMENT_LEFT:
				$this->headingAlignment = 'L';
				break;
			case InvoiceConfig::ALIGNMENT_RIGHT:
				$this->headingAlignment = 'R';
				break;
			default:
				throw new \InvalidArgumentException($headingAlignment . 'is not a valid heading Alignment');
		}
	}
	
	public function setHeadingOffset($headingOffset) {
		$this->headingOffset = $headingOffset;
	}
	
	public function setPrintCountry($printCountry) {
		$this->printCountry = $printCountry;
	}
	
	public function setInvoicingPartyAddress3($invoicingPartyAddress3) {
		$this->invoicingPartyAddress3 = $invoicingPartyAddress3;
	}
	
	public function setCustomerAddressHeader($customerAddressHeader) {
		$this->customerAddressHeader = $customerAddressHeader;
	}
	
	public function create() {
		$this->initialize();
		$this->printHeader();
		$this->printAddressAndInvoiceMetas();
		$this->printInvoiceDetails();
		$this->printInvoiceDescription();
		return $this;
	}
	
	public function Footer() {
		return null;
	}
	
	public function Header()  {
		return false;
	}
	
	private function initialize() {
		$this->SetCreator(PDF_CREATOR);
		$this->SetAuthor('Hofmänner New Media');
		$this->SetTitle($this->invoiceable->getTitle() . ' - ' . $this->pageName);
		$this->SetMargins(self::MARGIN_LEFT, self::MARGIN_TOP, self::MARGIN_RIGHT);
		// set default header data
		$this->SetHeaderMargin(PDF_MARGIN_HEADER);
		$this->SetFooterMargin(PDF_MARGIN_FOOTER);
		$this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		//set image scale factor
		$this->setImageScale(PDF_IMAGE_SCALE_RATIO);
		// set default font subsetting mode
		$this->setFontSubsetting(true);
		$this->AddPage();
	}
	
	private function printHeader() {
		if (null !== $this->fileLogo) {
			$imageFileLogo = new ImageFile($this->fileLogo);
			$logoHeight = $this->pixelsToUnits($imageFileLogo->getHeight());
			$this->Image((string) $this->fileLogo->getPath(), $this->x, $this->y, $this->getPrintablePageWidth(), $logoHeight, 
					'', '', '', true, 300, '', false, false, 0, $this->headingAlignment . 'M');
			$this->SetY($this->y + $logoHeight + 5);
		}
		$this->setDefaultFont();
		if (null !== $this->invoicingPartyAddress1) {
			$this->Cell(0, 6, $this->invoicingPartyAddress1, 0, 0, $this->headingAlignment);
			$this->Ln();
		}
		if (null !== $this->invoicingPartyAddress2) {
			$this->Cell(0, 6, $this->invoicingPartyAddress2, 0, 0, $this->headingAlignment);
			$this->Ln();
		}
		if (null !== $this->invoicingPartyAddress3) {
			$this->Cell(0, 6, $this->invoicingPartyAddress3, 0, 0, $this->headingAlignment);
			$this->Ln();
		}
		
		if (null !== $this->headingOffset) {
			$this->SetY($this->y + intval($this->headingOffset));
		}
		
		$this->SetY($this->y + 10);
	}
	
	private function printAddressAndInvoiceMetas() {
		$yStartPosition = $this->GetY();
		$this->setDefaultFont();
		
		$xPosition1Metas = 135;
		$xPosition2Metas = 170;
		$this->SetX($xPosition1Metas);
		$this->writeDefaultCell($this->dtc->translate('pdf_invoice_number'));
		$this->SetX($xPosition2Metas);
		$this->writeDefaultCell($this->invoiceable->getInvoiceNumber());
		$this->Ln();
		$this->SetX($xPosition1Metas);
		$this->writeDefaultCell($this->dtc->translate('pdf_invoice_date'));
		$this->SetX($xPosition2Metas);
		$this->writeDefaultCell($this->dateFormatter->format($this->invoiceable->getInvoiceDate()));
		
		//print Invoice Address
		$this->SetY($yStartPosition);
		$address = $this->invoiceable->getInvoiceableAddress();
		if (null !== $this->customerAddressHeader) {
			$this->setDefaultFont(false, true);
			$this->writeDefaultCell($this->customerAddressHeader);
			$this->Ln();
			$this->setDefaultFont();
		}
		if (null !== ($organisation = $address->getOrganisation())) {
			$this->writeDefaultCell($organisation);
			$this->Ln();
		}
		$this->writeDefaultCell($address->getName());
		$this->Ln();
		$this->writeDefaultCell($address->getAddress1());
		$this->Ln();
		$this->writeDefaultCell($address->getAddress2());
		$this->Ln();
		if (null !== $address3 = $address->getAddress3()) {
			$this->writeDefaultCell($address3);
			$this->Ln();
		}
		if ($this->printCountry && null !== $country = $address->getCountry()) {
			$this->writeDefaultCell($country);
			$this->Ln();
		}
		$this->SetY($this->y + 15);
		
	}
	
	private function printInvoiceDetails() {
		$this->setHeadingsFont();
		$this->Write(self::DEFAULT_CELL_HEIGHT, $this->invoiceable->getTitle());
		$this->Ln();
		$this->Ln();
		
		$widths = array(90, 20, 30, 35);
		$tableHeadingAlignments = array('L', 'C', 'C', 'C');
		$tableBodyAlignments = array('L', 'C', 'R', 'R');
		
		//headline
		$this->setDefaultFont(true);
		$this->printTableRow($widths, $tableHeadingAlignments, array($this->dtc->translate('pdf_invoice_item_text'), 
				$this->dtc->translate('pdf_invoice_item_amount'), $this->dtc->translate('pdf_invoice_item_unit_price'),
				$this->dtc->translate('pdf_invoice_item_price')));
		
		//tableContent
		$this->setDefaultFont(false);
		if (null != $invoicableItems = $this->invoiceable->getInvoiceableItems()) {
			foreach ($invoicableItems as $invoicableItem) {
				$this->printTableRow($widths, $tableBodyAlignments, [$invoicableItem->getText(), $invoicableItem->getAmount(), 
						$this->formatPrice(InvoiceUtils::getItemPrice($invoicableItem)), 
						$this->formatPrice(InvoiceUtils::getItemPrice($invoicableItem, $this->invoiceable->isTaxIncluded()))]);
			}
		}
		//table Footer
		//empty parmas with empty strings (TCPDF doesn't handle it correct)
		if ($this->invoiceable->isTaxIncluded()) {
			$this->setDefaultFont(true);
			$this->printTableRow($widths, $tableBodyAlignments, [
					$this->dtc->translate('pdf_invoice_total'), '', '', 
					$this->formatPrice(InvoiceUtils::getPrice($this->invoiceable, true))]);
			$this->setDefaultFont(false);
			if ($this->taxNumber) {
				$this->printTableRow($widths, $tableBodyAlignments, array(
						$this->dtc->translate('pdf_invoice_vat_in_total', array('tax_number' => $this->taxNumber)), '', '',
						$this->formatPrice(InvoiceUtils::getTax($this->invoiceable))));
			}
		} else {
			$this->printTableRow($widths, $tableBodyAlignments, array(
					$this->dtc->translate('pdf_invoice_total_excl_vat'), '', '',
					$this->formatPrice(InvoiceUtils::getPrice($this->invoiceable, false))));
			if ($this->taxNumber) {
				$this->printTableRow($widths, $tableBodyAlignments, array(
						$this->dtc->translate('pdf_invoice_vat', array('tax_number' => $this->taxNumber)), '', '',
						$this->formatPrice(InvoiceUtils::getTax($this->invoiceable))));
				$this->setDefaultFont(true);
				$this->printTableRow($widths, $tableBodyAlignments, array(
						$this->dtc->translate('pdf_invoice_total_incl_vat'), '', '',
						$this->formatPrice(InvoiceUtils::getPrice($this->invoiceable, true))));
			}
			$this->setDefaultFont(false);
		}
		
		$this->Ln();
	}
	
	private function printInvoiceDescription() {
		$this->writeHTMLCell($this->getPrintablePageWidth(), '', '','', '<style>*{line-height: 12px}</style>' . 
				$this->invoiceable->getTextHtml(), 0, 2, false, false);
		if (mb_strlen($merci = $this->dtc->translate('pdf_invoice_merci')) > 0) {
			$this->Ln();
			$this->writeDefaultCell($merci);
		}
		$this->Ln();
		$this->Ln();
		$this->writeDefaultCell($this->pageName);
	}
	
	private function printTableRow(array $widths, array $aligns, array $contents) {
		foreach ($contents as $i => $content) {
			$h = $this->getNumLines($contents[0], $widths[0]) * self::DEFAULT_CELL_HEIGHT + 2;
			if (($this->GetY() + $h) > 270) $this->AddPage();
			$this->MultiCell($widths[$i], $h, $content, 1, $aligns[$i], false, 0, '', '', true, 0, false, true, $h, 'M');
		}
		$this->Ln();
	}
	
	private function getPrintablePageWidth() {
		$margins = $this->getMargins();
		return $this->getPageWidth() - $margins['left'] - $margins['right'];
	}
	
	private function setDefaultFont($bold = false, $underline = false) {
		$style = '';
		if ($bold !== false) {
			$style .= 'B';
		}
		if ($underline !== false) {
			$style .= 'U';
		}
		$this->SetFont(self::DEFAULT_FONT_FAMILY, $style, self::DEFAULT_FONT_SIZE);
	}
	
	private function setHeadingsFont() {
		$this->SetFont(self::DEFAULT_FONT_FAMILY, 'B', self::HEADING_FONT_SIZE);
	}
	
	private function writeDefaultCell($text) {
		$this->Write(self::DEFAULT_CELL_HEIGHT, $text);
	}
	
	private function formatPrice($price){
		return L10nUtils::formatCurrency($this->invoiceable->getLocale(), $price, $this->invoiceable->getCurrency());
	}
}