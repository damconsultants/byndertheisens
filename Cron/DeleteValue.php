<?php

namespace DamConsultants\BynderTheisens\Cron;

use Exception;
use \Psr\Log\LoggerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\Product\Action;
use DamConsultants\BynderTheisens\Model\ResourceModel\Collection\MetaPropertyCollectionFactory;
use Magento\Framework\App\Cache\Manager as CacheManager;

class DeleteValue
{
	/**
	 * @var \Psr\Log\LoggerInterface
	 */
	protected $logger;
	/**
     * @var CacheManager
     */
    protected $cacheManager;
	protected $datahelper;
	protected $configWriter;
	protected $storeManagerInterface;
	protected $_productRepository;
	protected $action;
	protected $_byndersycData;
	protected $collectionFactory;
	protected $metaPropertyCollectionFactory;
	protected $bynderMediaTable;
	protected $bynderMediaTableCollectionFactory;
	protected $resouce;

	/**
	 * Featch Null Data To Magento
	 * @param LoggerInterface $logger
	 * @param ProductRepository $productRepository
	 * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
	 * @param StoreManagerInterface $storeManagerInterface
	 * @param \DamConsultants\BynderTheisens\Helper\Data $DataHelper
	 * @param \DamConsultants\BynderTheisens\Model\BynderSycDataFactory $byndersycData
	 * @param Action $action
	 * @param MetaPropertyCollectionFactory $metaPropertyCollectionFactory
	 * @param CacheManager $cacheManager
	 */
	public function __construct(
		LoggerInterface $logger,
		ProductRepository $productRepository,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
		StoreManagerInterface $storeManagerInterface,
		\DamConsultants\BynderTheisens\Helper\Data $DataHelper,
		\DamConsultants\BynderTheisens\Model\BynderDeleteDataFactory $byndersycData,
		\DamConsultants\BynderTheisens\Model\BynderMediaTableFactory $bynderMediaTable,
        \DamConsultants\BynderTheisens\Model\ResourceModel\Collection\BynderMediaTableCollectionFactory $bynderMediaTableCollectionFactory,
		\DamConsultants\BynderTheisens\Model\ApiBynderMediaTableFactory $ApiBynderMediaTable,
        \DamConsultants\BynderTheisens\Model\ResourceModel\Collection\ApiBynderMediaTableCollectionFactory $ApiBynderMediaTableCollection,
		Action $action,
		MetaPropertyCollectionFactory $metaPropertyCollectionFactory,
		\Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
		\Magento\Framework\App\ResourceConnection $resouce,
		CacheManager $cacheManager
	) {

		$this->logger = $logger;
		$this->_productRepository = $productRepository;
		$this->collectionFactory = $collectionFactory;
		$this->datahelper = $DataHelper;
		$this->action = $action;
		$this->_byndersycData = $byndersycData;
		$this->metaPropertyCollectionFactory = $metaPropertyCollectionFactory;
		$this->bynderMediaTable = $bynderMediaTable;
        $this->bynderMediaTableCollectionFactory = $bynderMediaTableCollectionFactory;
		$this->ApiBynderMediaTable = $ApiBynderMediaTable;
        $this->ApiBynderMediaTableCollection = $ApiBynderMediaTableCollection;
		$this->storeManagerInterface = $storeManagerInterface;
		$this->configWriter = $configWriter;
		$this->resouce = $resouce;
		$this->cacheManager = $cacheManager;
	}
	/**
	 * Execute
	 *
	 * @return boolean
	 */
	public function execute()
	{
		try {
			$enable = $this->datahelper->getDeleteCronEnable();
			if (!$enable) {
				return false;
			}
			$path = 'delete_cron_last_time';
			$isCofigPathExits = $this->datahelper->getStoreConfig($path);
			if (!$isCofigPathExits) {
				$current_time = time();
				$scope = 'default';
				$add_time = $this->configWriter->save($path, $current_time, $scope, $scopeId = 0);
			} else {
				$current_time = $this->datahelper->getDeleteCron($path);
			}
			$bynder_auth["last_cron_time"] = $current_time;
			$get_api_delete_details = $this->datahelper->getCheckBynderSideDeleteData($bynder_auth);
			$response = json_decode($get_api_delete_details, true);
			if (count($response) > 0) {
				if ($response['status'] == 1) {
					if(count($response['data']) > 0){
						foreach ($response['data'] as $delete_api_data) {
							if(isset($delete_api_data["id"])){
								$isDelete = $this->getDeleteMedaiDataTable($delete_api_data['id']);
								if($isDelete) {
									$this->getInsertApiMediaTable($delete_api_data['id']);
								}
							}
						}
					}
				}
			}
			$new_current_time = time();
			$scope = 'default';
			$update_time = $this->configWriter->save($path, $new_current_time, $scope, $scopeId = 0);
			$this->cacheManager->flush($this->cacheManager->getAvailableTypes());
		} catch (\Exception $e) {
			echo $e->getMessage();
		}

	}
	public function getInsertDataTable($id, $sku)
	{
		$model = $this->_byndersycData->create();
		$data_image_data = [
			'sku' => $sku,
			'media_id' => $id
		];
		$model->setData($data_image_data);
		$model->save();
	}
	public function getInsertApiMediaTable($insert_data)
	{
		$model = $this->ApiBynderMediaTable->create();
		$modelCollection = $this->ApiBynderMediaTableCollection->create()->addFieldToFilter('media_id', ['eq' => [$insert_data]])->load();
		if (count($modelCollection) == 0) {
			$data_image_data = [
				'media_id' => trim($insert_data)
			];
			$model->setData($data_image_data);
			$model->save();
		}
	}
	public function getDeleteMedaiDataTable($media_id)
    {
		$image_detail = [];
		$storeId = $this->storeManagerInterface->getStore()->getId();
        $model = $this->bynderMediaTableCollectionFactory->create()->addFieldToFilter('media_id', ['eq' => [$media_id]])->getFirstItem();
		if (count($model->getData()) == 0) {
			return true;
		} else {
			$_product = $this->_productRepository->get($model->getSku());
			$product_ids = $_product->getId();
			$image_value = $_product->getBynderMultiImg();
			if (!empty($image_value)) {
				$item_old_value = json_decode($image_value, true);
				$newArray = $this->removeElementByBynderId($item_old_value, $media_id, $model->getSku());
				$newArrayJson = json_encode($newArray, true);
				$_product->setData('bynder_multi_img', $newArrayJson);
				$this->_productRepository->save($_product);
			}
			$this->bynderMediaTable->create()->load($model->getId())->delete();
			return false;
		}
    }
	public function removeElementByBynderId($array, $id, $sku) 
	{
		foreach ($array as $key => $value) {
			if (trim($value['bynder_md_id']) === $id) {
				$this->getInsertDataTable($id, $sku);
				unset($array[$key]);
				//break;
			}
		}
		return $array;
	}
}