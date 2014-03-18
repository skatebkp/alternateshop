<?php

class Ophirah_Qquoteadv_Model_Qqadvcustomer extends Mage_Core_Model_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('qquoteadv/qqadvcustomer');
    }
	
	/**
	 * Add quote to qquote_customer table
	 * @param array $params quote created information
	 * @return mixed
	 */
	public function addQuote($params)
	{
		$this->setData($params)
		      ->save()
		      ;		
		return $this;
	}
	
	/**
	 * Add customer address for the particular quote
	 * @param integer $id quote id to be updated
	 * @param array $params array of field(s) to be updated
	 * @return mixed
	 */
	public function addCustomer($id,$params)
	{ 
		$this->load($id)
		      ->addData($params)
		      ->setId($id)
		      ->save()
		      ;
		      
		return $this;		
	}
	
	public function updateQuote($id, $params)
	{
		$this->load($id)
		      ->setData($params)
		      ->setId($id)
		      ->save()
		      ;		
		return $this;
	}
	
	public function getStoreGroupName()
    {
        $storeId = $this->getStoreId(); 
        if (is_null($storeId)) {
            return $this->getStoreName(1); // 0 - website name, 1 - store group name, 2 - store name
        }
        return $this->getStore()->getGroup()->getName();
    }
    
     /**
     * Retrieve store model instance
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if ($storeId = $this->getStoreId()) {
            return Mage::app()->getStore($storeId);
        }
        return Mage::app()->getStore();
    }


     /**
     * Get formated quote created date in store timezone
     *
     * @param   string $format date format type (short|medium|long|full)
     * @return  string
     */
    public function getCreatedAtFormated($format)
    {
        return Mage::helper('core')->formatDate($this->getCreatedAtStoreDate(), $format);
    }

    public function getBillingAddress(){

        //$name = $this->getCustomerName($this->getCustomerId());
        $address = $this->getAddress();
        
        $cityPostCode = $this->getCity();
        if(trim($this->getPostcode()))
           $cityPostCode.= ", ".$this->getPostcode();

        $country = Mage::app()->getHelper('qquoteadv')->getCountryName($this->getCountryId());
        $phone = $this->getTelephone();

        $str = "
            $address<br />
            $cityPostCode<br />
            $country<br />
            $phone<br />
        ";

       return $str; //$this->_formatAddress($str);
    } 
    
     public function getFullPath(){
	
		$valid = Mage::helper('qquoteadv')->isValidHttp($this->getPath()); 
		$path = $this->getPath(); //urlencode($this->getPath());
		if($valid)
			return $path;
		else
			return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $path;  
     }
     
     
     public function sendExpireEmail(){
        $expireTemplateId = Mage::getStoreConfig('qquoteadv/emails/proposal_expire', $this->getStoreId());

        $expiredQuotes = $this->getCollection()
        ->addFieldToFilter('expiry',array('eq'=>date("Y-m-d")));

        foreach($expiredQuotes as $expiredQuote){
            $_quoteadv = Mage::getModel('qquoteadv/qqadvcustomer')->load($expiredQuote->getData('quote_id'));

            $vars['quote'] = $_quoteadv;
            $vars['customer'] = Mage::getModel('customer/customer')->load($_quoteadv->getCustomerId());


            $template = Mage::getModel('qquoteadv/core_email_template');

            $template->loadDefault($expireTemplateId);

            $senderValue = Mage::getStoreConfig('qquoteadv/emails/sender', $this->getStoreId());
            if(!empty($senderValue)) {
                $sender['name'] = Mage::getStoreConfig('trans_email/ident_'.$senderValue.'/name',$this->getStoreId());
                $sender['email'] = Mage::getStoreConfig('trans_email/ident_'.$senderValue.'/email', $this->getStoreId());
                if(empty($sender['email'])) {
                        $sender['email'] = $senderValue;
                        $sender['name']	 = $senderValue;
                }
            } else {
                $contact = $admin[0];
                $sender['name']	= $contact['firstname'] . " " . $contact['lastname'];
                $sender['email']		= $contact['email'];
            }

            $adminEmail = Mage::helper('qquoteadv')->getAdminEmail($_quoteadv->getQuoteId());
            $adminName = Mage::helper('qquoteadv')->getAdminName($_quoteadv->getUserId());
            
            
            $template->setSenderName($adminName);
            $template->setSenderEmail($adminEmail);


            $subject = sprintf("Your Quote # %s will expire today", $_quoteadv->getIncrementId());
            $template->setTemplateSubject($subject);
            
            $template2 = clone $template;
            
            $template2->setSenderName($_quoteadv->getFirstname()." ".$_quoteadv->getLastname());
            $template2->setSenderEmail($_quoteadv->getEmail());
                   
            /**
             * Opens the qquote_request.html, throws in the variable array
             * and returns the 'parsed' content that you can use as body of email
             */
            $processedTemplate = $template->getProcessedTemplate($vars);
            $processedTemplate2 = $template2->getProcessedTemplate($vars);

            /*
             * getProcessedTemplate is called inside send()
             */
            $res = $template->send($_quoteadv->getEmail(), $_quoteadv->getFirstname(), $vars);
            
            $res2 = $template2->send($adminEmail, $adminName, $vars);
            
        }

     }
     
     
    public function exportQuotes($qquoteIds, $filePath){

        $csv_titles = array(
              "id",
              "timestamp",
              "name",
              "address",
              "zipcode",
              "city",
              "country",
              "phone",
              "email",
              "remarks",
              "product id",
              "product name",
              "product attributes",
              "quantity",
              "product sku"
        );

        $file = fopen($filePath, 'w'); //open, truncate to 0 and create if doesnt exist

        if(!$this->writeCsvRow($csv_titles, $file)) return false;

        foreach ($qquoteIds as $qquoteId) {
                $qquote = $this->load($qquoteId);


                $quoteId = $qquote->getQuoteId();
                $timestamp = $qquote->getCreatedAt();

                // build name
                $nameArr = array();
                if($qquote->getPrefix()) array_push($nameArr, $qquote->getPrefix());
                if($qquote->getFirstname()) array_push($nameArr, $qquote->getFirstname());
                if($qquote->getMiddlename()) array_push($nameArr, $qquote->getMiddlename());
                if($qquote->getLastname()) array_push($nameArr, $qquote->getLastname());
                if($qquote->getSuffix()) array_push($nameArr, $qquote->getSuffix());
                $name = join($nameArr, " ");
                $email = $qquote->getEmail();
                $city = $qquote->getCity();
                $address = $qquote->getAddress();
                $postcode = $qquote->getPostcode();
                $tel = $qquote->getTelephone();
                $country = $qquote->getCountryId();
                $remarks = $qquote->getClientRequest();


                $collection = Mage::getModel('qquoteadv/qqadvproduct')->getQuoteProduct($quoteId);

                $basicFields = array(
                        $quoteId, $timestamp, $name, $address, $postcode,
                        $city, $country, $tel, $email,  $remarks
                );

                foreach($collection as $item){
                        $baseProductId = $item->getProductId();
                        $productObj = Mage::getModel('catalog/product')->load($baseProductId);

                        $productName = $productObj->getName();
                        $productAttributes = "";

                        $productObj->setStoreId($item->getStoreId()?$item->getStoreId():1);
                        $quoteByProduct = Mage::helper('qquoteadv')->getQuoteItem($productObj, $item->getAttribute());

                        foreach($quoteByProduct->getAllItems() as $_unit) {

                                // Mage::Log($_unit->debug());
                                if( $_unit->getProductId() == $productObj->getId() ) {
                                     if($_unit->getProductType() == "bundle"){
                                        $_helper = Mage::helper('bundle/catalog_product_configuration');
                                        $_options = $_helper->getOptions($_unit);   
                                     }else{
                                          $_helper = Mage::helper('catalog/product_configuration');
                                          $_options = $_helper->getCustomOptions($_unit);
                                     }

                                     foreach($_options as $option){
                                         if(is_array($option['value']))$option['value'] = implode(",", $option['value']);
                                         $productAttributes .= $option['label'].":".strip_tags($option['value']);
                                         $productAttributes .= "|";
                                     }		
                                }
                        }

                        $requestItem = Mage::getModel('qquoteadv/requestitem')->getCollection()
                        ->addFieldToFilter('quote_id',$quoteId)
                        ->addFieldToFilter('product_id',$baseProductId)
                        ->getFirstItem();


                        $qty =$requestItem->getRequestQty();
                        $SKU = $productObj->getSku();

                        $productFields = array(	$baseProductId, $productName, $productAttributes, $qty, $SKU);

                        $fields = array_merge($basicFields, $productFields);

                        if(!$this->writeCsvRow($fields, $file)){
                                Mage::Log("could not write:".print_r($fields, 1));
                                return false;
                        }
                }
        }
        return true;
    }
		 
    public function writeCsvRow($row, $filePointer) {
        if(is_array($row)) $row = '"'.implode('","',$row).'"';
        $row = $row."\n";
        try {
               fwrite($filePointer, $row);
        } catch(Exception $e) {
               Mage::Log($e->getMessage());
               return false;
        }
        return true;
    }
     
     
}