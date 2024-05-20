<?php

namespace DamConsultants\BynderTheisens\Ui\DataProvider\Product;

use DamConsultants\BynderTheisens\Model\ResourceModel\Collection\BynderDeleteDataCollectionFactory;

class DeleteProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    /**
     * @param BynderDeleteDataCollectionFactory $BynderDeleteDataCollectionFactory
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        BynderDeleteDataCollectionFactory $BynderDeleteDataCollectionFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $collection = $BynderDeleteDataCollectionFactory;
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
        return $this->collection = $BynderDeleteDataCollectionFactory->create();
    }
}