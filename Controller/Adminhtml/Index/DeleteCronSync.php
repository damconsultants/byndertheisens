<?php
namespace DamConsultants\BynderTheisens\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class DeleteCronSync extends Action
{
    /**
     * @var $bynderdelete
     */
    public $bynderdelete;
    /**
     * Closed constructor.
     *
     * @param Context $context
     * @param DamConsultants\BynderTheisens\Model\BynderDeleteDataFactory $BynderSycDataFactory
     */
    public function __construct(
        Context $context,
        \DamConsultants\BynderTheisens\Model\BynderDeleteDataFactory $BynderSycDataFactory
    ) {
        $this->bynderdelete = $BynderSycDataFactory;
        parent::__construct($context);
    }
    /**
     * Execute
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');
        try {
            $syncModel = $this->bynderdelete->create();
            $syncModel->load($id);
            $syncModel->delete();
            $this->messageManager->addSuccessMessage(__('You deleted the sync data.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $resultRedirect->setPath('bynder/index/deletecrongrid');
    }
    /**
     * Execute
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('DamConsultants_BynderTheisens::delete');
    }
}
