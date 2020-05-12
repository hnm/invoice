<?php
namespace invoice\controller\rocket;

use n2n\web\http\controller\ControllerAdapter;
use rocket\ei\util\EiuCtrl;
use n2n\util\ex\IllegalStateException;
use invoice\bo\Invoiceable;
use invoice\model\InvoicePdfGenerator;

class InvoicePdfGenerationController extends ControllerAdapter {
	private $eiuCtrl;

	public function prepare(EiuCtrl $eiuCtrl) {
		$this->eiuCtrl = $eiuCtrl;
	}
	
	public function index($pid) {
		$eiuEntry = $this->eiuCtrl->lookupEntry($pid);
		$entityObject = $eiuEntry->getEntityObj();
		IllegalStateException::assertTrue($entityObject instanceof Invoiceable);
		$invoicePdfGenerator = new InvoicePdfGenerator($entityObject, $this->invoiceConfig);
		
		$this->sendFile($invoicePdfGenerator->generateTmpPdf());
	}
	
}
