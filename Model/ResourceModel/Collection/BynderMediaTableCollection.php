<?php

namespace DamConsultants\BynderTheisens\Model\ResourceModel\Collection;

class BynderMediaTableCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    
    /**
     * BynderConfigSyncDataCollection
     *
     * @return $this
     */
    protected function _construct()
    {
        $this->_init(
            \DamConsultants\BynderTheisens\Model\BynderMediaTable::class,
            \DamConsultants\BynderTheisens\Model\ResourceModel\BynderMediaTable::class
        );
    }
}
