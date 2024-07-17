<?php
namespace Kitchen\ZipCodes\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\AddressFactory;
use Magento\Framework\Controller\Result\JsonFactory;

class Save extends Action
{
    protected $customerSession;
    protected $addressFactory;
    protected $jsonFactory;

    public function __construct(
        Context $context,
        Session $customerSession,
        AddressFactory $addressFactory,
        JsonFactory $jsonFactory
    ) {
        $this->customerSession = $customerSession;
        $this->addressFactory = $addressFactory;
        $this->jsonFactory = $jsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->jsonFactory->create();

        if (!$this->customerSession->isLoggedIn()) {
            return $result->setData(['error' => true, 'message' => __('Customer is not logged in.')]);
        }

        try {
            $data = $this->getRequest()->getContent();
            $data = json_decode($data, true);

            if (empty($data)) {
                throw new \Exception(__('Invalid data.'));
            }

            $address = $this->addressFactory->create();
            $address->setCustomerId($this->customerSession->getCustomerId())
                ->setFirstname($this->customerSession->getCustomer()->getFirstname())
                ->setLastname($this->customerSession->getCustomer()->getLastname())
                ->setStreet($data['street'])
                ->setCity($data['city'])
                ->setRegion($data['state'])
                ->setCountryId($data['country_id'])
                ->setPostcode($data['postcode'])
                ->setTelephone($data['telephone'])
                ->setIsDefaultBilling(isset($data['default_billing']) ? 1 : 0)
                ->setIsDefaultShipping(isset($data['default_shipping']) ? 1 : 0)
                ->save();

            return $result->setData(['success' => true, 'message' => __('Address saved successfully.')]);
        } catch (\Exception $e) {
            return $result->setData(['error' => true, 'message' => $e->getMessage()]);
        }
    }
}
