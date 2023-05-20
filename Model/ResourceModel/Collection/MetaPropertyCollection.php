<?php

namespace DamConsultants\BynderTheisens\Model\ResourceModel\Collection;

class MetaPropertyCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * MetaPropertyCollection
     *
     * @return $this
     */
    protected function _construct()
    {
        $this->_init(
            \DamConsultants\BynderTheisens\Model\MetaProperty::class,
            \DamConsultants\BynderTheisens\Model\ResourceModel\MetaProperty::class
        );
    }
}
