<?php
 
namespace Kitchen\ZipCodes\Block\Adminhtml\System\Config\Form\Field;
 
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
 
class ZipCodeFrontOptions extends AbstractFieldArray
{ 
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }
 
    protected function _prepareToRender()
    {
 
        $this->addColumn(
            'title',
            [
                'label' => __('Title'),
                'id' => 'title',
                'class' => 'daterecuring',
                'style' => 'width:200px'
            ]
        );
 
        $this->addColumn(
            'zipcode',
            [
                'label' => __('ZipCode'),
                'class' => 'required-entry',
                'style' => 'width:300px',
            ]
        );

        $this->addColumn(
            'discount',
            [
                'label' => __('Discount'),
                'class' => 'required-entry',
                'style' => 'width:300px',
            ]
        );
 
        $this->_addAfter = false;
        $this->_addButtonLabel = __('MoreAdd');
    }
 
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];
        $row->setData('option_extra_attrs', $options);
    }
}
 