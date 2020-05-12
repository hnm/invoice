<?php
namespace invoice\model\rocket;

use n2n\persistence\orm\property\EntityProperty;
use rocket\impl\ei\component\prop\adapter\DraftablePropertyEiPropAdapter;
use n2n\util\type\ArgUtils;
use rocket\ei\util\Eiu;
use n2n\web\dispatch\mag\Mag;
use invoice\config\InvoiceConfig;
use n2n\util\type\CastUtils;
use n2n\impl\web\ui\view\html\HtmlView;
use rocket\ei\EiPropPath;
use n2n\impl\web\dispatch\mag\model\EnumMag;

class TaxRatesConfigEiProp extends DraftablePropertyEiPropAdapter {
	
	private $invoiceConfig;
	
	public function setEntityProperty(?EntityProperty $entityProperty) {
		ArgUtils::assertTrue($entityProperty === null || $entityProperty instanceof ScalarEntityProperty
				|| $entityProperty instanceof IntEntityProperty);
		$this->entityProperty = $entityProperty;
	}
	
	public function getTypeName() {
		return 'Tax Rates (Invoice)';
	}
	
	
	private function getOptions(Eiu $eiu) {
		$invoiceConfig = $eiu->lookup(InvoiceConfig::class);
		CastUtils::assertTrue($invoiceConfig instanceof InvoiceConfig);
		
		$options = [];
		
		foreach ($invoiceConfig->getTaxRates() as $taxRate) {
			$options[$taxRate->getValue()] = $taxRate->getName();
		}
		
		return $options;
	}
	
	public function createUiComponent(HtmlView $view, Eiu $eiu)  {
		$html = $view->getHtmlBuilder();
		$options = $this->getOptions($eiu);
		$value = $eiu->field()->getValue(EiPropPath::from($this));
		if (isset($options[$value])) {
			return $html->getEsc($options[$value]);
		}
		return $html->getEsc($value);
	}
	
	public function createMag(Eiu $eiu): Mag {
		$choicesMap = $this->getOptions($eiu);
		foreach (array_values($choicesMap) as $value) {
			if (!$eiu->entry()->acceptsValue($this, $value)) {
				unset($choicesMap[$value]);
			}
		}
	
		return new EnumMag($this->getLabelLstr(), $choicesMap, null,
				$this->isMandatory($eiu));
	}
}
