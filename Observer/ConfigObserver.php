<?php


namespace DamConsultants\BynderTheisens\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\App\RequestInterface;

class ConfigObserver implements ObserverInterface
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param Logger $logger
     */
    public function __construct(
        RequestInterface $request,
        Logger $logger
    ) {
        $this->logger = $logger;
        $this->request = $request;
    }

    public function execute(EventObserver $observer)
    {
        $faqParams = $this->request->getParam('groups');
        $this->logger->info("custom_admin_system_config_changed_section_general => ".json_encode($faqParams));
    }
}

?>