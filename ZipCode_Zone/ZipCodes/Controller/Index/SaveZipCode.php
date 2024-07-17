<?php

namespace Kitchen\ZipCodes\Controller\Index;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Model\AddressFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\Cache\Manager;

class SaveZipCode extends Action
{
    protected $jsonFactory;
    protected $customerRepository;
    protected $addressFactory;
    protected $customerSession;
    protected $customerFactory;
    protected $cacheManager;

    public function __construct(
        Context $context,
        Manager $cacheManager,
        JsonFactory $jsonFactory,
        AddressFactory $addressFactory,
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepository,
        CustomerFactory $customerFactory
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->cacheManager = $cacheManager;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->addressFactory = $addressFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->jsonFactory->create();

        if (!$this->customerSession->isLoggedIn()) {
            $result->setData(['success' => false, 'message' => 'Customer is not logged in.']);
            return $result;
        }

        $zipCode = $this->getRequest()->getParam('zip_code');
        $customerId = $this->customerSession->getCustomerId();

        $addressCollection = $this->addressFactory->create()->getCollection()
            ->addFieldToFilter('postcode', $zipCode);

        $addressId = null;

        foreach ($addressCollection as $address) {
            $addressId = $address->getEntityId();
            break;
        }

        if ($addressId === null) {
            $customer = $this->customerFactory->create()->load($customerId);
            $customer->setDefaultBilling(null);
            $customer->setDefaultShipping(null);
            $customer->save($customer);
            $this->cacheManager->clean(['block_html']);
            $result->setData(['success' => true, 'message' => 'No address found with the provided zip code.']);
            return $result;
        }

        try {
            $customer = $this->customerFactory->create()->load($customerId);
            $customer->setDefaultBilling($addressId);
            $customer->setDefaultShipping($addressId);
            $customer->save($customer);
            $this->cacheManager->clean(['block_html']);
            return $result->setData(['success' => true, 'message' => __('Cache cleaned successfully.')]);
        } catch (\Exception $e) {
            return $result->setData(['success' => false, 'message' => $e->getMessage()]);
        }

        return $result;
    }
}
