<?php

namespace DamConsultants\BynderTheisens\Model;

class MetaProperty extends \Magento\Framework\Model\AbstractModel
{
    protected const CACHE_TAG = 'DamConsultants_BynderTheisens';

    /**
     * @var $_cacheTag
     */
    protected $_cacheTag = 'DamConsultants_BynderTheisens';

    /**
     * @var $_eventPrefix
     */
    protected $_eventPrefix = 'DamConsultants_BynderTheisens';

    /**
     * Meta Property
     *
     * @return $this
     */
    protected function _construct()
    {
        $this->_init(\DamConsultants\BynderTheisens\Model\ResourceModel\MetaProperty::class);
    }
}
