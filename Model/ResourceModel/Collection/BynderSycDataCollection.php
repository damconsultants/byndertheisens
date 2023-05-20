<?php

namespace DamConsultants\BynderTheisens\Model\ResourceModel\Collection;

class BynderSycDataCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    
    /**
     * BynderSycDataCollection
     *
     * @return $this
     */
    protected function _construct()
    {
        $this->_init(
            \DamConsultants\BynderTheisens\Model\BynderSycData::class,
            \DamConsultants\BynderTheisens\Model\ResourceModel\BynderSycData::class
        );
    }
}
