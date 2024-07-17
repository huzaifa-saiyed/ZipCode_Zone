<?php
namespace Kitchen\ZipCodes\Model\Config\Source;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Zones extends AbstractSource
{
    /**
     * Configuration path for the zip codes.
     */
    const CONFIG_PATH = 'zipcodeSection/zipcodeGroup/dynamic_options';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        
            $configData = $this->scopeConfig->getValue(self::CONFIG_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $options = [];

            if (!empty($configData)) {
                $configData = json_decode($configData, true);
                foreach ($configData as $data) {
                    $options[] = ['value' => $data['title'], 'label' => $data['title'] . ' - ' . $data['discount'] . '%'];
                }
            }
        

        return $options;
    }
}
?>
