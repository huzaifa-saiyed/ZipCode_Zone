<?php

namespace Kitchen\ZipCodes\Plugin\Model;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ProductPricePlugin
{
    protected $customerSession;
    protected $scopeConfig;

    public function __construct(
        CustomerSession $customerSession,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->customerSession = $customerSession;
        $this->scopeConfig = $scopeConfig;
    }

    public function afterGetPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        $customer = $this->customerSession->getCustomer();
        
        $billingAddress = $customer->getDefaultBillingAddress();

        if ($billingAddress) {
            $customerZipCode = $billingAddress->getPostcode();
            $configValue = $this->scopeConfig->getValue('zipcodeSection/zipcodeGroup/dynamic_options', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $customAttribute = $customer->getCustomCustomerAttribute(); 

            $zipCodeArray = json_decode($configValue, true);
            foreach ($zipCodeArray as $option) {
                $zipCodeArray = explode(",", $option['zipcode']);
                if (in_array($customerZipCode, $zipCodeArray) && $customAttribute == $option['title'] ) {
                    $discount = isset($option['discount']) ? floatval($option['discount']) : 0;
                    $discountPrice = $result * ((100 - $discount) / 100);
                    return $discountPrice;
                }
            }
        }

        return $result;
    }
}