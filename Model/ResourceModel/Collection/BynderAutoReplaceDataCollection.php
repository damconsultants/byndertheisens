<?php

namespace DamConsultants\BynderTheisens\Model\ResourceModel\Collection;

class BynderAutoReplaceDataCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    
    /**
     * BynderConfigSyncDataCollection
     *
     * @return $this
     */
    protected function _construct()
    {
        $this->_init(
            \DamConsultants\BynderTheisens\Model\BynderAutoReplaceData::class,
            \DamConsultants\BynderTheisens\Model\ResourceModel\BynderAutoReplaceData::class
        );
    }
}
