<?php

namespace DamConsultants\BynderTheisens\Model\ResourceModel\Collection;

class ApiBynderMediaTableCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    
    /**
     * BynderConfigSyncDataCollection
     *
     * @return $this
     */
    protected function _construct()
    {
        $this->_init(
            \DamConsultants\BynderTheisens\Model\ApiBynderMediaTable::class,
            \DamConsultants\BynderTheisens\Model\ResourceModel\ApiBynderMediaTable::class
        );
    }
}
