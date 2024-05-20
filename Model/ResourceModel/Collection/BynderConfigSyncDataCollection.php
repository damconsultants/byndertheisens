<?php

namespace DamConsultants\BynderTheisens\Model\ResourceModel\Collection;

class BynderConfigSyncDataCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    
    /**
     * BynderConfigSyncDataCollection
     *
     * @return $this
     */
    protected function _construct()
    {
        $this->_init(
            \DamConsultants\BynderTheisens\Model\BynderConfigSyncData::class,
            \DamConsultants\BynderTheisens\Model\ResourceModel\BynderConfigSyncData::class
        );
    }
}
