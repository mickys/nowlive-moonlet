<?php

class Nowlive_Moonlet_Block_Form extends Nowlive_Moonlet_Block_Abstract
{

    public function getProduct()
    {
        if (!Mage::registry('product') && $this->getProductId()) {
            $product = Mage::getModel('catalog/product')->load($this->getProductId());
            Mage::register('product', $product);
        }
        return Mage::registry('product');
    }
    
    public function getPhoneCodeHtml($name, $class = '', $withCountryName = true, $emptyLabel = 'Code')
    {
        $codesCollection = $this->getModuleHelper()->getPhoneCodes();
        $codes = $codesCollection->toOptionArray($withCountryName);

        if (count($codes) > 1) {
            $options = array_merge(
                array(array(
                    'value' => '',
                    'label' => $emptyLabel,
                )),
                $codes
            );

            $selectedValue = '';

            $html = $this->getLayout()->createBlock('core/html_select')
                ->setName($name)
                ->setId('oneclickorder-phone-code')
                ->setClass($class)
                ->setValue($selectedValue)
                ->setOptions($options)
                ->getHtml();
        } else {
            $item = $codesCollection->getFirstItem();
            $html = "<input type=\"hidden\" name=\"$name\" value=\"{$item->getCountryCode()}\" /><span class=\"$class\">+{$item->getPhoneCode()}</span> ";
        }
        return $html;
    }

    /**
     * need o render email field. Only for guest and if magento order is enabled
     */
    public function isShowEmailField()
    {
        return $this->getModuleHelper()->isSaveMagentoOrder() && $this->isGuest();
    }
}