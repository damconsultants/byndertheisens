<?php

namespace DamConsultants\BynderTheisens\Model\ResourceModel\Collection;

class BynderDeleteDataCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    
    /**
     * BynderConfigSyncDataCollection
     *
     * @return $this
     */
    protected function _construct()
    {
        $this->_init(
            \DamConsultants\BynderTheisens\Model\BynderDeleteData::class,
            \DamConsultants\BynderTheisens\Model\ResourceModel\BynderDeleteData::class
        );
    }
}
