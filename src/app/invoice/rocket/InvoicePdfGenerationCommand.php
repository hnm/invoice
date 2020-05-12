<?php
namespace invoice\model\rocket;

use invoice\controller\rocket\InvoicePdfGenerationController;
use rocket\impl\ei\component\command\IndependentEiCommandAdapter;
use rocket\ei\component\command\control\EntryControlComponent;
use n2n\web\http\controller\Controller;
use rocket\ei\util\Eiu;
use n2n\core\container\N2nContext;
use n2n\l10n\N2nLocale;
use n2n\l10n\DynamicTextCollection;
use n2n\impl\web\ui\view\html\HtmlView;
use rocket\ei\manage\control\ControlButton;
use rocket\ei\manage\control\IconType;
use n2n\util\uri\Path;
use n2n\util\ex\IllegalStateException;
use invoice\bo\Invoiceable;

class InvoicePdfGenerationCommand extends IndependentEiCommandAdapter implements EntryControlComponent {
	const CONTROL_KEY = 'invoice-generate-pdf';

	public function getTypeName(): string {
		return 'Generate Invoice Pdf';
	}
	
	/**
	 * {@inheritDoc}
	 * @see \rocket\ei\component\command\EiCommand::lookupController()
	 */
	public function lookupController(Eiu $eiu): Controller {
		return $eiu->lookup(InvoicePdfGenerationController::class);
	}
	
	public function getEntryControlOptions(N2nContext $n2nContext, N2nLocale $n2nLocale): array {
		$dtc = new DynamicTextCollection('invoice', $n2nLocale);
		return array(self::CONTROL_KEY => $dtc->translate('rocket_ei_cmd_generate_invoice_pdf_label'));
	}
	
	public function createEntryControls(Eiu $eiu, HtmlView $view): array {
		$eiuControlFactory = $eiu->frame()->controlFactory($this);
		$eiuEntry = $eiu->entry();
		$invoice = $eiuEntry->getEntityObj();
		IllegalStateException::assertTrue($invoice instanceof Invoiceable);
		
		$dtc = new DynamicTextCollection('invoice', $view->getN2nLocale());
		$controlButton = new ControlButton($dtc->t('rocket_script_cmd_generate_invoice_pdf_label'),
				$dtc->t('rocket_script_cmd_generate_invoice_pdf_tooltip'),
				false, ControlButton::TYPE_SECONDARY, IconType::ICON_PLAY_CIRCLE);
			
		return [$eiuControlFactory->createJhtml($controlButton, 
				(new Path([$eiuEntry->getPid(), 'invoice-' . $invoice->getInvoiceNumber() . '.pdf']))->toUrl())];
	}
}