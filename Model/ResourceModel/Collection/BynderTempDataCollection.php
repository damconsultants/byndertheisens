<?php

namespace DamConsultants\BynderTheisens\Model\ResourceModel\Collection;

class BynderTempDataCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    
    /**
     * BynderConfigSyncDataCollection
     *
     * @return $this
     */
    protected function _construct()
    {
        $this->_init(
            \DamConsultants\BynderTheisens\Model\BynderTempData::class,
            \DamConsultants\BynderTheisens\Model\ResourceModel\BynderTempData::class
        );
    }
}
