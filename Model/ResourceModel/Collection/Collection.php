<?php

namespace DamConsultants\BynderTheisens\Model\ResourceModel\Collection;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Collection
     *
     * @return $this
     */
    protected function _construct()
    {
        $this->_init(
            \DamConsultants\BynderTheisens\Model\Bynder::class,
            \DamConsultants\BynderTheisens\Model\ResourceModel\Bynder::class
        );
    }
}
