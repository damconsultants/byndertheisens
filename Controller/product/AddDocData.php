<?php
namespace DamConsultants\BynderTheisens\Controller\Product;

class AddDocData extends \Magento\Framework\App\Action\Action
{
    /**
     * @var string $_pageFactory;
     */
    protected $_pageFactory;

    /**
     * Add Data.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param \Magento\Framework\Filesystem\Driver\File $driverFile
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Magento\Catalog\Model\Product\Action $productActionObject
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\Filesystem\Driver\File $driverFile,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Catalog\Model\Product\Action $productActionObject,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->_pageFactory = $pageFactory;
        $this->_product = $product;
        $this->file = $file;
        $this->resultJsonFactory = $jsonFactory;
        $this->driverFile = $driverFile;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->cookieManager = $cookieManager;
        $this->productActionObject = $productActionObject;
        $this->_registry = $registry;
        $this->_resource = $resource;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        return parent::__construct($context);
    }
    /**
     * Execute
     *
     * @return $this
     */
    public function execute()
    {
        $connection = $this->_resource->getConnection();
        $table_name = $connection->getTableName('bynder_temp_doc_data');
        $product_id = $this->getRequest()->getParam('product_id');
        $coockie_id = $this->getRequest()->getParam('doc_coockie_id');
        $widen_doc = $this->getRequest()->getParam('doc');
        if ($coockie_id == 0) {
            $data = [
                "value" => $widen_doc,
                "product_id" => $product_id
            ];
            $connection->insert($table_name, $data);
            $lastAddedId = $connection->lastInsertId($table_name);
        } else {
            $select = $connection->select()
            ->from(
                ['c' => $table_name],
                ['*']
            )
            ->where("c.product_id = ?", $product_id);
            $records = $connection->fetchAll($select);
            if (empty($records)) {
                $data = [
                    "value" => $widen_doc,
                    "product_id" => $product_id
                ];
                $connection->insert($table_name, $data);
                $lastAddedId = $connection->lastInsertId($table_name);
            } else {
                $new_data = [
                    "value" => $widen_doc,
                    "product_id" => $product_id
                ];
                $where = ["id = ?" => $coockie_id];
                $connection->update($table_name, $new_data, $where);
                $lastAddedId = $coockie_id;
            }
        }
        $publicCookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
        $publicCookieMetadata->setDurationOneYear();
        $publicCookieMetadata->setPath('/');
        $publicCookieMetadata->setHttpOnly(false);
        $this->cookieManager->setPublicCookie(
            'doc_coockie_id',
            $lastAddedId,
            $publicCookieMetadata
        );
    }
}
