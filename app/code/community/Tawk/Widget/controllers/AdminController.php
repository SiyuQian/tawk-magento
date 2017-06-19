<?php

/**
 * Tawk.to
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to support@tawk.to so we can send you a copy immediately.
 *
 * @copyright   Copyright (c) 2014 Tawk.to
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Tawk_Widget_AdminController extends Mage_Adminhtml_Controller_Action {
	public function indexAction() {
		echo "1";
	} 

	protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('widget');
    }

	public function customizationAction() {
		$this->loadLayout();
		$block = $this->getLayout()->createBlock('tawkwidget/admin_customization');
		$this->_addContent($block);
		$this->renderLayout();
	}

	public function redirectAction() {
		$this->_redirectUrl('https://dashboard.tawk.to/');
	}

	public function savewidgetAction() {
		$response = $this->getResponse();

		$response->setHeader('Content-type', 'application/json');

		if(!is_string($_POST['pageId']) || !is_string($_POST['widgetId']) ) {
			return $response->setBody(json_encode(array("success" => FALSE)));
		}

		$model = Mage::getModel('tawkwidget/widget')->loadByForStoreId($_POST['id']);

		if(!$model->hasId()) {
			$model = Mage::getModel('tawkwidget/widget');
		}

		if( ($_POST['pageId'] == '-1') && ($_POST['widgetId'] == '-1') ){

		}else{
			$model->setPageId($_POST['pageId']);
			$model->setWidgetId($_POST['widgetId']);
		}
		$model->setForStoreId($_POST['id']);
		
		$model->setAlwaysDisplay($_POST['alwaysdisplay']);
		$model->setExcludeUrl($_POST['excludeurl']);

		$model->setDoNotDisplay($_POST['donotdisplay']);
		$model->setIncludeUrl($_POST['includeurl']);

		$model->save();

		return $response->setBody(json_encode(array("success" => TRUE)));
	}

	public function removewidgetAction() {
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json');

		Mage::getModel('tawkwidget/widget')->loadByForStoreId($_GET['id'])->delete();

		return $response->setBody(json_encode(array("success" => TRUE)));
	}

	public function getstorewidgetAction() {
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json');

		$model = Mage::getModel('tawkwidget/widget')->loadByForStoreId($_GET['id']);
		$widget_id = $model->getWidgetId();
		$page_id = $model->getPageId();

		$excludeurl = $model->getExcludeUrl();
		$includeurl = $model->getIncludeUrl();
		
		$alwaysdisplay = $model->getAlwaysDisplay();
		$donotdisplay = $model->getDoNotDisplay();

		 if(isset($_SERVER['HTTPS'])){
	        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
	    }
	    else{
	        $protocol = 'http';
	    }
	    $domain = $protocol . "://" . $_SERVER['HTTP_HOST'];

	    $sitedata = array(
	    	"pageid" => $page_id,
	    	'widgetid' => $widget_id,
	    	'domain' => $domain,
	    	'excludeurl' => $excludeurl,
	    	'includeurl' => $includeurl,
	    	'alwaysdisplay' => $alwaysdisplay,
	    	'donotdisplay' => $donotdisplay
	    );

		return $response->setBody( json_encode( $sitedata ) );
	}
}