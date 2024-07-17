<?php

namespace Kitchen\ZipCodes\Block;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\AddressFactory;
use Magento\Framework\App\Request\Http as HttpRequest;

class Address extends Template
{
    protected $customerSession;
    protected $addressRepository;
    protected $addressFactory;
    protected $httpRequest;

    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        AddressFactory $addressFactory,
        HttpRequest $httpRequest,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->addressFactory = $addressFactory;
        $this->httpRequest = $httpRequest;
        parent::__construct($context, $data);
    }

    public function getAddressesCount()
    {
        if ($this->customerSession->isLoggedIn()) {
            $customerId = $this->customerSession->getCustomerId();
            try {
                $addressCollection = $this->addressFactory->create()->getCollection()
                    ->addFieldToFilter('parent_id', $customerId);
                return $addressCollection->count();
            } catch (\Exception $e) {
                return false;
            }
        }
        return 0;
    }

    public function isCustomerAccountIndex()
    {
        return $this->httpRequest->getFullActionName() === 'customer_account_index';
    }

    // public function getCustomerZipCodes()
    // {
    //     $zipCodes = [];
    //     if ($this->customerSession->isLoggedIn()) {
    //         $customer = $this->customerSession->getCustomerId();

    //         $addressCollection = $this->addressFactory->create()->getCollection()
    //             ->addFieldToFilter('parent_id', $customer);


    //         foreach ($addressCollection as $address) {
    //             $zipCodes[] = $address->getPostcode();
    //         }
    //     }

    //     return $zipCodes;
    // }

    public function isLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }
}
