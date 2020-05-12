<?php
namespace invoice\model;

use invoice\bo\InvoiceableItem;
use invoice\bo\Invoiceable;

class InvoiceUtils {
	public static function getItemTax(InvoiceableItem $invoicableItem) {
		return self::getItemPrice($invoicableItem, false) * $invoicableItem->getTaxRate();
	}
	
	public static function getItemPrice(InvoiceableItem $invoicableItem, bool $taxIncluded = false) {
		$cumulatedPrice = $invoicableItem->getUnitPrice() * $invoicableItem->getAmount();
		if ($taxIncluded == $invoicableItem->getInvoice()->isTaxIncluded()) {
			return $cumulatedPrice;
		}
		
		$taxRateFactor = (1 + $invoicableItem->getTaxRate());
		if (!$taxIncluded) {
			return $cumulatedPrice / $taxRateFactor;
		}
		
		return $cumulatedPrice * $taxRateFactor;
	}
	
	public static function getPrice(Invoiceable $invoicable, bool $taxIncluded = false) {
		$total = 0;
		foreach ($invoicable->getInvoiceableItems() as $invoicableItem) {
			$total += self::getItemPrice($invoicableItem, $taxIncluded);
		}
		
		return $total;
	}
	
	public static function getTax(Invoiceable $invoicable) {
		$tax = 0;
		foreach ($invoicable->getInvoiceableItems() as $invoicableItem) {
			$tax += $invoicableItem->getTax();
		}
		
		return $tax;
	}
}