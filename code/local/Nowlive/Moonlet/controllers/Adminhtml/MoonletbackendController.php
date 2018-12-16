<?php
class Nowlive_Moonlet_Adminhtml_MoonletbackendController extends Mage_Adminhtml_Controller_Action
{

	protected function _isAllowed()
	{
		//return Mage::getSingleton('admin/session')->isAllowed('moonlet/moonletbackend');
		return true;
	}

	public function indexAction()
    {
       $this->loadLayout();
	   $this->_title($this->__("Moonlet Backend Page Title"));
	   $this->renderLayout();
    }
}