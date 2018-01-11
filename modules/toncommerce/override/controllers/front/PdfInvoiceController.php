<?php
class PdfInvoiceController extends PdfInvoiceControllerCore{
    /*
    * module: toncommerce
    * date: 2017-02-17 12:15:30
    * version: 1.0.0
    */
    public function display()
    {
		$query = "SELECT * FROM "._DB_PREFIX_."toncommerce_payment_order WHERE id_order = ".$this->order->id;
		$row = Db::getInstance()->getRow($query);
		if($row){
			$entetes=array('Authorization: '.Configuration::get('TONCOMMERCE_API_KEY', ''));
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, "https://api.toncommerce.net/commandes/$row[id_payment]/facturepdf");
			curl_setopt($curl, CURLOPT_HTTPHEADER, $entetes);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$reponse=curl_exec($curl);
			curl_close($curl);
			unset($curl);
			$resultats=json_decode($reponse);
			if($resultats->statut === "ok"){
				header('Content-Description: File Transfer');
				header('Cache-Control: private, must-revalidate, post-check=0, pre-check=0, max-age=1');
				header('Pragma: public');
				header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
				header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
				header("Content-Disposition:attachment;filename=facture.pdf");
				header('Content-Type: application/force-download');
				header('Content-Type: application/octet-stream', false);
				header('Content-Type: application/download', false);
				header('Content-Type: application/pdf', false);
				header('Content-Transfer-Encoding: binary');
				echo base64_decode($resultats->facture_pdf->content);
			}
		}else{
			parent::display();
		}
	
    	
    	/*
        $order_invoice_list = $this->order->getInvoicesCollection();
        Hook::exec('actionPDFInvoiceRender', array('order_invoice_list' => $order_invoice_list));
        $pdf = new PDF($order_invoice_list, PDF::TEMPLATE_INVOICE, $this->context->smarty);
        $pdf->render();*/
    }
}