<?php

namespace DamConsultants\BynderTheisens\Model\ResourceModel\Collection;

class BynderTempDocDataCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    
    /**
     * BynderConfigSyncDataCollection
     *
     * @return $this
     */
    protected function _construct()
    {
        $this->_init(
            \DamConsultants\BynderTheisens\Model\BynderTempDocData::class,
            \DamConsultants\BynderTheisens\Model\ResourceModel\BynderTempDocData::class
        );
    }
}
