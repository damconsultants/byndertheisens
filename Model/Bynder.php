<?php

namespace DamConsultants\BynderTheisens\Model;

class Bynder extends \Magento\Framework\Model\AbstractModel
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
     * BynderTheisens
     *
     * @return $this
     */
    protected function _construct()
    {
        $this->_init(\DamConsultants\BynderTheisens\Model\ResourceModel\Bynder::class);
    }
}
