<?php

namespace DamConsultants\BynderTheisens\Controller\Adminhtml\Index;

use DamConsultants\BynderTheisens\Model\ResourceModel\Collection\MetaPropertyCollectionFactory;
use DamConsultants\BynderTheisens\Model\ResourceModel\Collection\BynderSycDataCollectionFactory;

class Psku extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory = false;

    /**
     * Product Sku.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Catalog\Model\Product\Action $action
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     * @param \DamConsultants\BynderTheisens\Model\BynderFactory $bynder
     * @param \DamConsultants\BynderTheisens\Model\BynderSycDataFactory $byndersycData
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param MetaPropertyCollectionFactory $metaPropertyCollectionFactory
     * @param \DamConsultants\BynderTheisens\Helper\Data $DataHelper
     * @param BynderSycDataCollectionFactory $byndersycDataCollection
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Model\Product\Action $action,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \DamConsultants\BynderTheisens\Model\BynderFactory $bynder,
        \DamConsultants\BynderTheisens\Model\BynderSycDataFactory $byndersycData,
        \Magento\Catalog\Model\Product $product,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        MetaPropertyCollectionFactory $metaPropertyCollectionFactory,
        \DamConsultants\BynderTheisens\Helper\Data $DataHelper,
        BynderSycDataCollectionFactory $byndersycDataCollection,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $jsonFactory;
        $this->productAction = $action;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->metaPropertyCollectionFactory = $metaPropertyCollectionFactory;
        $this->datahelper = $DataHelper;
        $this->_byndersycData = $byndersycData;
        $this->_byndersycDataCollection = $byndersycDataCollection;
        $this->bynder = $bynder;
        $this->_productRepository = $productRepository;
        $this->product = $product;
    }
    /**
     * Execute
     *
     * @return $this
     */
    public function execute()
    {

        if (!$this->getRequest()->isAjax()) {
            $this->_forward('noroute');
            return '';
        }
        $property_id = null;
        $productSku = [];
        $collection_data_value = [];
        $collection_data_slug_val = [];
        $product_sku = $this->getRequest()->getParam('product_sku');
        $select_attribute = $this->getRequest()->getParam('select_attribute');
        $result = $this->resultJsonFactory->create();
       
        $collection = $this->metaPropertyCollectionFactory->create()->getData();
        if (count($collection) >= 1) {
            foreach ($collection as $key => $collection_value) {
                $collection_data_value[] = [
                    'id' => $collection_value['id'],
                    'property_name' => $collection_value['property_name'],
                    'property_id' => $collection_value['property_id'],
                    'magento_attribute' => $collection_value['magento_attribute'],
                    'attribute_id' => $collection_value['attribute_id'],
                    'bynder_property_slug' => $collection_value['bynder_property_slug'],
                    'system_slug' => $collection_value['system_slug'],
                    'system_name' => $collection_value['system_name']
                ];
                $collection_data_slug_val[$collection_value['system_slug']] = [
                    'bynder_property_slug' => $collection_value['bynder_property_slug'],
                ];
            }

        } else {
            $result_data = $result->setData(
                ['status' => 0, 'message' => 'Please Select The Metaproperty First.....']
            );
            return $result_data;
        }
        if (strlen($product_sku) > 0) {
            $productSku = explode(",", $product_sku);
            foreach ($productSku as $sku) {
                /* if($select_attribute == "image"){

                }elseif($select_attribute == "video"){

                }else {
                    // it's for documents
                } */
                $get_data = $this->datahelper->getImageSyncWithProperties($sku, $property_id, $collection_data_value);
                
                if (!empty($get_data) && $this->getIsJSON($get_data)) {
                    $respon_array = json_decode($get_data, true);
                    
                    if ($respon_array['status'] == 1) {
                        $convert_array = json_decode($respon_array['data'], true);
                       /*  echo "<pre>";
                        print_r($convert_array);
                        exit;  */
                        $this->getDataItem($select_attribute, $convert_array, $collection_data_slug_val);
                    } else {
                        $result_data = $result->setData(
                            ['status' => 0, 'message' => 'Please Select The Metaproperty First.....']
                        );
                        return $result_data;
                    }
                } else {
                    $result_data = $result->setData(
                        [
                            'status' => 0,
                            'message' => 'Something went wrong from API side, Please contact to support team!'
                        ]
                    );
                    return $result_data;
                }

            }
            $result_data = $result->setData(['status' => 1, 'message' => 'Data Sync Successfully..!']);
            return $result_data;
        } else {
            $result_data = $result->setData(['status' => 0, 'message' => 'Please enter atleast one SKU.']);
            return $result_data;
        }
    }
    /**
     * Is Json
     *
     * @param string $string
     * @return $this
     */
    public function getIsJSON($string)
    {
        return (is_null(json_decode($string))) ? false : true;
    }

    /**
     * Get Data Item
     *
     * @param string $select_attribute
     * @param array $convert_array
     * @param array $collection_data_slug_val
     */
    public function getDataItem($select_attribute, $convert_array, $collection_data_slug_val)
    {
        $data_arr = [];
        $data_val_arr = [];
        $result = $this->resultJsonFactory->create();
        if ($convert_array['status'] == 1) {
            foreach ($convert_array['data'] as $data_value) {
               
                if ($select_attribute == $data_value['type']) {
                    $bynder_media_id = $data_value['id'];
                    $image_data = $data_value['thumbnails'];
                    $bynder_image_role = $image_data['magento_role_options'];
                    $bynder_alt_text = $image_data['img_alt_text'];
                    $sku_slug_name = "property_" . $collection_data_slug_val['sku']['bynder_property_slug'];
                    $data_sku = $data_value[$sku_slug_name];

                     /**
                     * Below code for multiple derivative according to image role
                     * 
                     */
                    $images_urls_list = [];
                    $new_magento_role_list = [];
                    $new_bynder_alt_text =[];
                    if(count($bynder_image_role) > 0){
                        foreach($bynder_image_role as $m_bynder_role){
                            $lower_m_bynder_role = strtolower($m_bynder_role);
                            $original_m_bynder_role = $m_bynder_role;
                            if(isset($data_value["thumbnails"][$original_m_bynder_role])){
                                $images_urls_list[]= $data_value["thumbnails"][$original_m_bynder_role]."\n";
                                $new_magento_role_list[] = $original_m_bynder_role."\n";

                                $alt_text_vl = $data_value["thumbnails"]["img_alt_text"];
                                $new_bynder_alt_text[] = (strlen($alt_text_vl) > 0)?$alt_text_vl."\n":"###\n";
                            }else{
                                $images_urls_list[]= "no image\n";
                                $new_magento_role_list[] = $original_m_bynder_role."\n";
                                $alt_text_vl = $data_value["thumbnails"]["img_alt_text"];
                                $new_bynder_alt_text[] = (strlen($alt_text_vl) > 0)?$alt_text_vl."\n":"###\n";
                            }
                        }
                    }else{
                        $new_magento_role_list[] = "###"."\n";
                    }
                    if(count($images_urls_list) == 0){
                        $images_urls_list[] = $image_data["original"]."\n";
                    }

                    if ($data_value['type'] == "image") {
                        array_push($data_arr, $data_sku[0]);
                        $data_p = [
                            "sku" => $data_sku[0],
                            "url" => $images_urls_list, /* chagne by kuldip ladola for testing perpose */
                            'magento_image_role' => $new_magento_role_list,
                            'image_alt_text' => $new_bynder_alt_text
                        ];
                        array_push($data_val_arr, $data_p);
                    } else {
                        if ($select_attribute == 'video') {
                            $video_link = $image_data["image_link"] . '@@' . $image_data["webimage"];
                            array_push($data_arr, $data_sku[0]);
                            $data_p = ["sku" => $data_sku[0], "url" => $video_link];
                            array_push($data_val_arr, $data_p);

                        } else {
                            $doc_name = $data_value["name"];
                            $doc_name_with_space = preg_replace("/[^a-zA-Z]+/", "-", $doc_name);
                            $doc_link = $image_data["image_link"] . '@@' . $doc_name_with_space;
                            array_push($data_arr, $data_sku[0]);
                            $data_p = ["sku" => $data_sku[0], "url" => $doc_link];
                            array_push($data_val_arr, $data_p);
                        }

                    }
                }
            }
        }
        if (count($data_arr) > 0) {
            $this->getProcessItem($data_arr, $data_val_arr,$bynder_media_id);
        } else {
            $result_data = $result->setData(['status' => 0, 'message' => 'No Data Found...']);
            return $result_data;
        }
    }
    /**
     * Get Process Item
     *
     * @param array $data_arr
     * @param array $data_val_arr
     */
    public function getProcessItem($data_arr, $data_val_arr,$bynder_media_id)
    {
        $result = $this->resultJsonFactory->create();
        $image_value_details_role = [];
        foreach ($data_arr as $key => $skus) {
            $temp_arr[$skus][] = implode("",$data_val_arr[$key]["url"]);
            $image_value_details_role[$skus][] = implode("",$data_val_arr[$key]["magento_image_role"]);
            $image_alt_text[$skus][] = implode("",$data_val_arr[$key]["image_alt_text"]);
        }
        foreach ($temp_arr as $product_sku_key => $image_value) {
            
            $img_json = implode("", $image_value);
            $mg_role = implode("", $image_value_details_role[$product_sku_key]);
            $image_alt_text_value = implode("", $image_alt_text[$product_sku_key]);
            $this->getUpdateImage(
                $img_json,
                $product_sku_key,
                $mg_role,
                $image_alt_text_value,
                $bynder_media_id
            );
        }
    }
    /**
     * Upate Item
     *
     * @return $this
     * @param string $img_json
     * @param string $product_sku_key
     * @param string $magento_image_role_option
     * @param string $image_alt_text
     */
    public function getUpdateImage($img_json, $product_sku_key, $magento_image_role_option, $image_alt_text,$bynder_media_id)
    {
        /* echo "<pre>";
        print_r($img_json);
        print_r($image_alt_text);
        exit; */
        $result = $this->resultJsonFactory->create();
        $select_attribute = $this->getRequest()->getParam('select_attribute');
        $model = $this->_byndersycData->create();

        try {
            $storeId = $this->storeManagerInterface->getStore()->getId();
            $byndeimageconfig = $this->datahelper->byndeimageconfig();

            $img_roles = explode(",", $byndeimageconfig);
            $_product = $this->_productRepository->get($product_sku_key);
            $product_ids = $_product->getId();
            $image_value = $_product->getBynderMultiImg();
            $doc_value = $_product->getBynderDocument();
            if ($select_attribute == "image") {
                if (!empty($image_value)) {
                    $new_image_array = explode("\n", $img_json);
                    $new_alttext_val_array = explode("\n", $image_alt_text);
                    $new_magento_role_option_array = explode("\n", $magento_image_role_option);
                    $all_item_url = [];
                    $item_old_value = json_decode($image_value, true);
                    if (count($item_old_value) > 0) {
                        foreach ($item_old_value as $img) {
                            $all_item_url[] = $img['thum_url'];
                        }
                    }

                    $image_detail = [];
                    
                    foreach ($new_image_array as $vv => $new_image_value) {
                        if(trim($new_image_value) != "" && $new_image_value != "no image"){
                            $item_url = explode("?", $new_image_value);
                            $media_image_explode = explode("/", $item_url[0]);
                            $img_altText_val = $new_alttext_val_array[$vv];
                            if($new_alttext_val_array[$vv] == "###"){
                                $img_altText_val = "";
                            }

                            $curt_img_role = [];
                            if($new_magento_role_option_array[$vv] != "###"){
                                $curt_img_role = [$new_magento_role_option_array[$vv]];
                            }
                            if (!in_array($item_url[0], $all_item_url)) {                                
                                $image_detail[] = [
                                    "item_url" => $new_image_value,
                                    "alt_text" => $img_altText_val,
                                    "image_role" => $curt_img_role,
                                    "item_type" => 'IMAGE',
                                    "thum_url" => $item_url[0],
                                    "bynder_md_id" => $bynder_media_id
                                ];
                                $data_image_data = [
                                    'sku' => $product_sku_key,
                                    'bynder_data' => $new_image_value,
                                    'bynder_data_type' => '1',
                                    'media_id' => $media_image_explode[5],
                                    'remove_for_magento' => '1',
                                    'added_on_cron_compactview' => '1',
                                    'added_date' => time()
                                ];
                                $model->setData($data_image_data);
                                $model->save();

                                
                                if (count($item_old_value) > 0) {
                                    foreach ($item_old_value as $kv => $img) {
                                        if ($img['item_type'] == "IMAGE") {
                                            /* here changes by me but not tested */
                                            if($new_magento_role_option_array[$vv] != "###"){
                                                $new_magento_role_array = (array)$new_magento_role_option_array[$vv];
                                                if (count($img["image_role"])>0 && count($new_magento_role_array)>0) {
                                                    $result_val=array_diff($img["image_role"], $new_magento_role_array);
                                                    $item_old_value[$kv]["image_role"] = $result_val;
                                                }
                                            }
                                        }
                                    }
                                }

                                $total_new_value = count($image_detail);
                                if ($total_new_value > 1) {
                                    foreach ($image_detail as $nn => $n_img) {
                                        if ($n_img['item_type'] == "IMAGE" && $nn != ($total_new_value - 1)) {
                                            if($new_magento_role_option_array[$vv] != "###"){
                                                $new_magento_role_array = (array)$new_magento_role_option_array[$vv];
                                                if (count($n_img["image_role"])>0 && count($new_magento_role_array)>0) {
                                                    $result_val=array_diff($n_img["image_role"], $new_magento_role_array);
                                                    $image_detail[$nn]["image_role"] = $result_val;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $array_merge = array_merge($item_old_value, $image_detail);
                    foreach ($array_merge as $img) {
                        $type[] = $img['item_type'];
                    }
                    if (in_array("IMAGE", $type) && in_array("VIDEO", $type)) {
                        $flag = 1;
                    } elseif (in_array("IMAGE", $type)) {
                        $flag = 2;
                    } elseif (in_array("VIDEO", $type)) {
                        $flag = 3;
                    }
                    $new_value_array = json_encode($array_merge, true);
                    $this->productAction->updateAttributes(
                        [$product_ids],
                        ['bynder_multi_img' => $new_value_array],
                        $storeId
                    );
                    $this->productAction->updateAttributes(
                        [$product_ids],
                        ['bynder_isMain' => $flag],
                        $storeId
                    );

                } else {
                    
                    $new_image_array = explode("\n", $img_json);
                    $new_alttext_val_array = explode("\n", $image_alt_text);
                    $new_magento_role_option_array = explode("\n", $magento_image_role_option);
                   
                    $image_detail = [];
                    foreach ($new_image_array as $vv => $image_value) {

                        if(trim($image_value) != "" && $image_value != "no image"){
                            $item_url = explode("?", $image_value);
                            $media_image_explode = explode("/", $item_url[0]);
                            $img_altText_val = $new_alttext_val_array[$vv];
                            if($new_alttext_val_array[$vv] == "###"){
                                $img_altText_val = "";
                            }
                            $image_detail[] = [
                                "item_url" => $image_value,
                                "alt_text" => $img_altText_val,
                                "image_role" => [$new_magento_role_option_array[$vv]],
                                "item_type" => 'IMAGE',
                                "thum_url" => $item_url[0],
                                "bynder_md_id" => $bynder_media_id
                            ];
                            $data_image_data = [
                                'sku' => $product_sku_key,
                                'bynder_data' => $item_url[0],
                                'bynder_data_type' => '1',
                                'media_id' => $media_image_explode[5],
                                'remove_for_magento' => '1',
                                'added_on_cron_compactview' => '1',
                                'added_date' => time()
                            ];
                            $model->setData($data_image_data);
                            $model->save();

                            $total_new_value = count($image_detail);
                            if ($total_new_value > 1) {

                                foreach ($image_detail as $nn => $n_img) {
                                    if ($n_img['item_type'] == "IMAGE" && $nn != ($total_new_value - 1)) {
                                        $new_magento_role_array = (array)$new_magento_role_option_array[$vv];
                                        if (count($n_img["image_role"]) > 0 && count($new_magento_role_array) > 0) {
                                            $result_val = array_diff($n_img["image_role"], $new_magento_role_array);
                                            $image_detail[$nn]["image_role"] = $result_val;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    foreach ($image_detail as $img) {
                        $type[] = $img['item_type'];
                    }
                    if (in_array("IMAGE", $type) && in_array("VIDEO", $type)) {
                        $flag = 1;
                    } elseif (in_array("IMAGE", $type)) {
                        $flag = 2;
                    } elseif (in_array("VIDEO", $type)) {
                        $flag = 3;
                    }
                    $new_value_array = json_encode($image_detail, true);
                    
                    $this->productAction->updateAttributes(
                        [$product_ids],
                        ['bynder_multi_img' => $new_value_array],
                        $storeId
                    );
                    $this->productAction->updateAttributes(
                        [$product_ids],
                        ['bynder_isMain' => $flag],
                        $storeId
                    );

                }
            } elseif ($select_attribute == "video") {
                if (!empty($image_value)) {
                    $new_video_array = explode(" \n", $img_json);
                    $old_value_array = json_decode($image_value, true);
                    $old_item_url = [];
                    if (!empty($old_value_array)) {
                        foreach ($old_value_array as $value) {
                            $old_item_url[] = $value['item_url'];
                        }
                    }
                    foreach ($new_video_array as $video_value) {
                        $item_url = explode("?", $video_value);
                        $thum_url = explode("@@", $video_value);
                        $media_video_explode = explode("/", $item_url[0]);
                        if (!in_array($item_url[0], $old_item_url)) {
                            $video_detail[] = [
                                "item_url" => $item_url[0],
                                "image_role" => null,
                                "item_type" => 'VIDEO',
                                "thum_url" => $thum_url[1],
                                "bynder_md_id" => $bynder_media_id
                            ];
                            $data_video_data = [
                                'sku' => $product_sku_key,
                                'bynder_data' => $item_url[0],
                                'bynder_data_type' => '3',
                                'media_id' => $media_video_explode[5],
                                'remove_for_magento' => '1',
                                'added_on_cron_compactview' => '1',
                                'added_date' => time()
                            ];
                            $model->setData($data_video_data);
                            $model->save();
                        }
                    }
                    if (!empty($old_value_array)) {
                        $array_merge = array_merge($old_value_array, $video_detail);
                        foreach ($array_merge as $img) {

                            $type[] = $img['item_type'];
                        }
                        if (in_array("IMAGE", $type) && in_array("VIDEO", $type)) {
                            $flag = 1;
                        } elseif (in_array("IMAGE", $type)) {
                            $flag = 2;
                        } elseif (in_array("VIDEO", $type)) {
                            $flag = 3;
                        }
                    }
                    $new_value_array = json_encode($array_merge, true);
                    $this->productAction->updateAttributes(
                        [$product_ids],
                        ['bynder_multi_img' => $new_value_array],
                        $storeId
                    );
                    $this->productAction->updateAttributes(
                        [$product_ids],
                        ['bynder_isMain' => $flag],
                        $storeId
                    );
                } else {
                    $new_video_array = explode(" \n", $img_json);
                    $video_detail = [];
                    foreach ($new_video_array as $video_value) {
                        $item_url = explode("?", $video_value);
                        $thum_url = explode("@@", $video_value);
                        $media_video_explode = explode("/", $item_url[0]);

                        $video_detail[] = [
                            "item_url" => $item_url[0],
                            "image_role" => null,
                            "item_type" => 'VIDEO',
                            "thum_url" => $thum_url[1],
                            "bynder_md_id" => $bynder_media_id
                        ];
                        $data_video_data = [
                            'sku' => $product_sku_key,
                            'bynder_data' => $item_url[0],
                            'bynder_data_type' => '3',
                            'media_id' => $media_video_explode[5],
                            'remove_for_magento' => '1',
                            'added_on_cron_compactview' => '1',
                            'added_date' => time()
                        ];
                        $model->setData($data_video_data);
                        $model->save();

                    }
                    foreach ($video_detail as $img) {
                        $type[] = $img['item_type'];
                    }
                    if (in_array("IMAGE", $type) && in_array("VIDEO", $type)) {
                        $flag = 1;
                    } elseif (in_array("IMAGE", $type)) {
                        $flag = 2;
                    } elseif (in_array("VIDEO", $type)) {
                        $flag = 3;
                    }
                    $new_value_array = json_encode($video_detail, true);
                    $this->productAction->updateAttributes(
                        [$product_ids],
                        ['bynder_multi_img' => $new_value_array],
                        $storeId
                    );
                    $this->productAction->updateAttributes(
                        [$product_ids],
                        ['bynder_isMain' => $flag],
                        $storeId
                    );
                }
            } else {

                if (empty($doc_value)) {
                    $new_doc_array = explode(" \n", $img_json);
                    $doc_detail = [];
                    foreach ($new_doc_array as $doc_value) {
                        $item_url = explode("?", $doc_value);
                        $media_doc_explode = explode("/", $item_url[0]);
                        $doc_detail[] = [
                            "item_url" => $item_url[0],
                            "item_type" => 'DOCUMENT',
                            "bynder_md_id" => $bynder_media_id
                        ];
                        $data_doc_value = [
                            'sku' => $product_sku_key,
                            'bynder_data' => $item_url[0],
                            'bynder_data_type' => '2',
                            'media_id' => $media_doc_explode[4],
                            'remove_for_magento' => '1',
                            'added_on_cron_compactview' => '1',
                            'added_date' => time()
                        ];
                        $model->setData($data_doc_value);
                        $model->save();
                    }
                    $new_value_array = json_encode($doc_detail, true);
                    $this->productAction->updateAttributes(
                        [$product_ids],
                        ['bynder_document' => $new_value_array],
                        $storeId
                    );
                }
            }
        } catch (\Exception $e) {
            return $result->setData(['message' => $e->getMessage()]);
        }
    }
}
