<?php

namespace DamConsultants\BynderTheisens\Block\Adminhtml\Metaproperty;

use DamConsultants\BynderTheisens\Model\ResourceModel\Collection\MetaPropertyCollectionFactory;

class Index extends \Magento\Backend\Block\Template
{
    /**
     * @var \DamConsultants\BynderTheisens\Helper\Data
     */
    protected $helperdata;

    /**
     * @var \DamConsultants\BynderTheisens\Model\MetaPropertyFactory
     */
    protected $metaProperty;

    /**
     * @var \DamConsultants\BynderTheisens\Model\ResourceModel\Collection\MetaPropertyCollectionFactory
     */
    protected $metaPropertyCollectionFactory;

    /**
     * Metaproperty
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \DamConsultants\BynderTheisens\Helper\Data $helperdata
     * @param \DamConsultants\BynderTheisens\Model\MetaPropertyFactory $metaProperty
     * @param MetaPropertyCollectionFactory $metaPropertyCollectionFactory
     * @param array $data
     */

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \DamConsultants\BynderTheisens\Helper\Data $helperdata,
        \DamConsultants\BynderTheisens\Model\MetaPropertyFactory $metaProperty,
        MetaPropertyCollectionFactory $metaPropertyCollectionFactory,
        array $data = []
    ) {
        $this->_helperdata = $helperdata;
        $this->_metaProperty = $metaProperty;
        $this->_metaPropertyCollectionFactory = $metaPropertyCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * SubmitUrl.
     *
     * @return $this
     */
    public function getSubmitUrl()
    {
        return $this->getUrl("bynder/index/submit");
    }
    /**
     * Get MetaData.
     *
     * @return $this
     */
    public function getMetaData()
    {
        $property_name = "";
        $response_data = [];
        $metadata = $this->_helperdata->getBynderMetaProperites();
        $data = json_decode($metadata, true);
        $response_data['message'] = "Success";
        if (null !== $data && $data['status'] == 1) {
            $response_data['metadata'] = $data['data'];
        } else {
            $response_data['metadata'] = [];
            $response_data['message'] = $data['data'] ?? "";
        }

        $collection = $this->_metaPropertyCollectionFactory->create();
        $colletion_get_data = $collection->getData();
        $properties_details = [];
        if (count($colletion_get_data) > 0) {
            foreach ($colletion_get_data as $metacollection) {
                $properties_details[$metacollection['system_slug']] = [
                    "id" => $metacollection['id'],
                    "property_name" => $metacollection['property_name'],
                    "property_id" => $metacollection['property_id'],
                    "magento_attribute" => $metacollection['magento_attribute'],
                    "attribute_id" => $metacollection['attribute_id'],
                    "bynder_property_slug" => $metacollection['bynder_property_slug'],
                    "system_slug" => $metacollection['system_slug'],
                    "system_name" => $metacollection['system_name'],
                ];
            }

            $response_data['sku_selected'] = $properties_details["sku"]["bynder_property_slug"];
            $response_data['image_role_selected']= $properties_details["image_role"]["bynder_property_slug"];
            $response_data['image_alt_text']= $properties_details["alt_text"]["bynder_property_slug"];
        } else {
            $response_data['sku_selected'] = '0';
            $response_data['image_role_selected'] = '0';
            $response_data['image_alt_text'] = '0';
        }

        return $response_data;
    }
    /**
     * Get Select Property.
     *
     * @return $this
     */
    public function getSelectProperites()
    {
        $collection = $this->_metaPropertyCollectionFactory->create();
        $colletion_get_data = $collection->getData();
        $select_properites_array = [];
        if (count($colletion_get_data) > 0) {
            foreach ($colletion_get_data as $key => $value) {
                $select_properites_array[] = [
                    'property_name' => $value['property_name'],
                    'bynder_property_slug' => $value['bynder_property_slug']
                ];
            }
        } else {
            $select_properites_array = [
                'property_name' => 0,
                'bynder_property_slug' => 0
            ];
        }
        return $select_properites_array;
    }
}
