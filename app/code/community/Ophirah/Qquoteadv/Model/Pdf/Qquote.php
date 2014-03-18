<?php

class Ophirah_Qquoteadv_Model_Pdf_Qquote extends Mage_Sales_Model_Order_Pdf_Abstract
{
    //highest point by vercical axis
    public $y = 750;	
	
	//lowest position by vercical axis
    public $_minPosY     = 15;
    
    public $_quoteadvId = null;
    public $_quoteadv   = null;   
    
    public $_leftRectPad     = 45;
    public $_leftTextPad     = 55;    
    
    //save product name to avoid dublicate display produc't names 
    public $_prevItemName       = '';
    public $_prevItemOptions   = array();
    
	public $_itemNamePosY   = 0;
    public $_itemId         = null;
    public $columns         = array();
    public $pdf;
    public $requestId;
    public $latestY = null;
    public $totalPrice = 0;
	public $isSetTierPrice = 0;
    
    /*
    protected function insertLogo(&$page, $store = null) {
        $this->y-=40;
        $imageWidth = 300; $imageHeight = 80;
        $image = Mage::getStoreConfig('sales/identity/logo', $store); 
        if ($image) {
            $imageFile = Mage::getStoreConfig('system/filesystem/media', $store) . '/sales/store/logo/' . $image; 
            try{
                $image = Zend_Pdf_Image::imageWithPath($imageFile,$imageWidth,$imageHeight);
                $x = $this->_leftRectPad; $y = $this->y;
                $page->drawImage($image, $x, $y, $x+$imageWidth, $y+$imageHeight);                

            }catch(Exception $e){ Mage::log($e->getMessage()); }            
        }
    }     

    */
    protected function insertLogo(&$page, $store = null)
    {

        $image = Mage::getStoreConfig('sales/identity/logo', $store);
        if ($image) {
            $this->y=780; 
            $x = $this->_leftRectPad;  
            $y = $this->y; 
            $image = Mage::getBaseDir('media') . '/sales/store/logo/' . $image;
            if (is_file($image)) {
                $image = Zend_Pdf_Image::imageWithPath($image);
                $page->drawImage($image, $x, $y, $x+100, $y+25);
            }
        }
        //return $page;
    }
    
    protected function insertAddress(&$page, $store = null) {
        $y = 780+20; 
        $x = $this->_leftRectPad+345;
        
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 7);

        $itemRequest = wordwrap(Mage::getStoreConfig('sales/identity/address', $store), 50, "\n");
        foreach (explode("\n", $itemRequest) as $value){
            if ($value!=='') {
                $page->drawText(trim(strip_tags($value)), $x, $y, 'UTF-8');
                $y-=7;
            }
        }
        #case when address text height much more logo height
//        if($this->y > $y){ 
//            $this->y = $y;  
//        }
    }
    
    public function addNewPage(){
        /* Add new table head */
        $page = $this->pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $this->pdf->pages[] = $page;
        $this->y = 800;

        $this->_setFontRegular($page);
        $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle($this->_leftRectPad, $this->y, 570, $this->y-15);
        $this->y -=10;
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        foreach($this->columns as $item){
            $textLabel 	=  $item[0];
            $xx			=  $item[1];
            $yy 		=  $this->y;
            $textEncod 	=  $item[3];                        
                        
            $page->drawText($textLabel, $xx, $yy, $textEncod);                
        }

        $this->y-=20;
		return $page;
    }
    
    public function getPdf($quotes = array())
    { 
        $this->_beforeGetPdf();        

        $this->pdf  = new Zend_Pdf();
        $style      = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

 		if ($quotes instanceof Ophirah_Qquoteadv_Model_Qqadvcustomer) {
            	$this->_quoteadvId = $quotes->getId();
        }else{ 
		    foreach ($quotes as $item){
				$this->_quoteadvId = $item['quote_id'];
            }
		}
 			
 		$this->_quoteadv  = Mage::getModel('qquoteadv/qqadvcustomer')->load($this->_quoteadvId);
            
        if ($this->_quoteadv->getStoreId()) {
          Mage::app()->getLocale()->emulate($this->_quoteadv->getStoreId());
        }
            
        $page = $this->pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $this->pdf->pages[] = $page;

            /* Add image */
            $this->insertLogo($page, $this->_quoteadv->getStoreId());
           
            /* Add address */
            $this->insertAddress($page, $this->_quoteadv->getStoreId());
            
            /* Add head */
            $this->insertTitles($page, $this->_quoteadvId, $this->_quoteadv->getStoreId());     
            
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
            $this->_setFontRegular($page);
            
            $this->y -=10; $rectHeight = 15;
            /* Add table */
            $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);
           
            $page->drawRectangle($this->_leftRectPad, $this->y, 570, $this->y-$rectHeight);
            
            $this->y -=10;
            /* Add table head */
            $page->setFillColor(new Zend_Pdf_Color_Rgb(0.4, 0.4, 0.4));
          
            $this->columns = array(
                    array(Mage::helper('qquoteadv')->__('Product name'),    $this->_leftTextPad, $this->y, 'UTF-8'),
                    array(Mage::helper('qquoteadv')->__('Atr. nr.'),        290, $this->y, 'UTF-8'),
                    array(Mage::helper('qquoteadv')->__('QTY'),             400, $this->y, 'UTF-8'),
                    array(Mage::helper('qquoteadv')->__('Price'),           480, $this->y, 'UTF-8'),
                    array(Mage::helper('qquoteadv')->__('Subtotal'),        535, $this->y, 'UTF-8')
            );            
            
            //#draw TABLE TITLES
            foreach($this->columns as $item){
                $textLabel =  $item[0];
                $textPosX =   $item[1];
                $textPosY =   $item[2];
                $textEncod =  $item[3];                
                
                $page->drawText($textLabel, $textPosX, $textPosY, $textEncod);                
            }            

            $this->y -=15; 
            if ($this->_minPosY+60 > $this->y) { $page = $this->addNewPage(); }	
			
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

            $requestItems = Mage::getModel('qquoteadv/qqadvproduct')->getCollection()
                                    ->addFieldToFilter('quote_id', $this->_quoteadvId);
                       
            /* Add body */
            foreach ($requestItems as $product){
			 
               if ($this->_minPosY > $this->y ) { $page = $this->addNewPage(); }
				
                /* Draw item */
                $page = $this->draw($product, $page);
            }
                
			if ($this->_minPosY+60 > $this->y) { $page = $this->addNewPage(); }	
			
				
            /* Add total */
    		$this->insertTotal($page);
            
            if ($this->_minPosY+30 > $this->y){ $page = $this->addNewPage(); }
            
            /* Add quote2cart general remark */
            $this->insertGeneralRemark($page);            
            
            if ($this->getStoreId()) {
                Mage::app()->getLocale()->revert();
            }

        $this->_afterGetPdf();

        return $this->pdf;
    }
    
    protected function _renderConfigurable($product, $item) {
      $html = array();
        
      $x = Mage::helper('qquoteadv')->getQuoteItem($product, $item->getAttribute());

      foreach($x->getAllItems() as $_zz) {
		if( $_zz->getProductId() == $product->getId() ) {
			$obj = new Ophirah_Qquoteadv_Block_Item_Renderer_Configurable;
            $obj->setTemplate('qquoteadv/item/configurable.phtml');
            $obj->setItem($_zz);
                   
            if ($_options = $obj->getOptionList()) { 
               foreach ($_options as $_option) { 
                 $_formatedOptionValue = $obj->getFormatedOptionValue($_option);
                 $html[] = $_option['label'];
                 $html[] = '  '.$_formatedOptionValue['value'];
               }
            }					
		}
      }
        
      return $html;    
    }
    
    protected function _renderBundle($product, $item) {
        $html = array();        
        $product->setStoreId($item->getStoreId()?$item->getStoreId():1);
		
        $virtualQuote = Mage::helper('qquoteadv')->getQuoteItem($product, $item->getAttribute());
		$_helper = Mage::helper('bundle/catalog_product_configuration');
		
		foreach($virtualQuote->getAllItems() as $_unit) {
			if( $_unit->getProductId() == $product->getId() ) {

				$_options = $_helper->getOptions($_unit);
				if( is_array($_options) ) {  
					
					 foreach ($_options as $_option): 

						//$_formatedOptionValue = $this->getFormatedOptionValue($_option);
                        $helperx = Mage::helper('catalog/product_configuration');
                        $params = array(
                            'max_length' => 55,
                            'cut_replacer' => ' <a href="#" class="dots" onclick="return false">...</a>'
                        );
                        $x = $helperx->getFormattedOptionValue($_option, $params);
						
                        $html[] = $_option['label'];
                        
                        $simple = explode("\n", $x['value']);
                        foreach($simple as $opt) {
                            $html[] = '  '.$opt;
                        }                        
						 
					endforeach;		
                   		  				
			 }
         }       
        }
        
        return $html;
    }
        
    public function draw($unit, $page) {
		$line		= array();
		$drawItems	= array();		
        $attr		= array(); 
        
        $this->_setFontRegular($page);
        
        $productId      = $unit->getProductId();
        $itemRequest    = $unit->getClientRequest(); 
        
		if(!$productId) return;
        $item  = Mage::getModel('catalog/product')->load($productId);         

        if ($item->getTypeId() == 'bundle') {
            $attr = $this->_renderBundle($item, $unit);
        } elseif ($item->getTypeId() == 'configurable') {
            $attr = $this->_renderConfigurable($item, $unit);
        } else {
            $superAttribute = $this->getOption($item, $unit->getAttribute());          
        
            //render custom options 
            $attr = $this->retrieveOptions($item, $superAttribute);              
        }

		/* in case Product name is longer than 55 chars - it is written in a few lines */
		$name = $item->getName();
		$line[] = array(
           'text'  => Mage::helper('core/string')->str_split(strip_tags($name), 55, true, true),
           'feed'  => $this->_leftTextPad,
		   'font'  => 'bold',
		   'font_size' => 9,
		   'height' => 10
        );
		
		// draw SKUs
        $sku = $this->getSku($item);	  
		$text = array();
        foreach (Mage::helper('core/string')->str_split($item->getSku(), 30) as $part) {
          $text[] = $part;
        }
        $line[] = array(
          'text'  => $text,
          'feed'  => 290
        );
			
        $requestedProductData = Mage::getModel('qquoteadv/requestitem')
                        ->getCollection()
						->addFieldToFilter('quote_id', $unit->getQuoteId())
                        ->addFieldToFilter('quoteadv_product_id', $unit->getId())        
                        ->addFieldToFilter('product_id', $productId)       
        ; 
        $requestedProductData->getSelect()->order(array('product_id ASC', 'request_qty ASC'));
        
        $_quote =  Mage::getModel('qquoteadv/qqadvcustomer')->load($unit->getQuoteId());
        $currency = $_quote->getData('currency');
        $currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
        Mage::app()->getStore()->setCurrentCurrencyCode($currency);
        
        //#tier price section
        $k=0;
		$txt1 = $txt2 = $txt3 = array();
        foreach($requestedProductData as $product) {
            if($k>0){ $this->isSetTierPrice = 1; } 
            //set first line
            $productQty     = $product->getRequestQty()*1;
            $priceProposal  = $product->getOwnerCurPrice(); 

            $price  = Mage::helper('core')->formatPrice($priceProposal, false);
            $row    = $productQty*$priceProposal;
            $rowTotal 	= Mage::helper('core')->formatPrice($row, false); 

            $this->totalPrice+=$row; 
			$txt1[] = $productQty;  //405
			$txt2[] = $price;       //480
			$txt3[] = $rowTotal;    //542
            
            $k++;
        }
	
        Mage::app()->getStore()->setCurrentCurrencyCode($currentCurrencyCode);
        
		$line[] = array(
          'text'  => $txt1,
          'feed'  => 405
        );
		
		$line[] = array(
          'text'  => $txt2,
          'feed'  => 480
        );
		
		$line[] = array(
          'text'  => $txt3,
          'feed'  => 542
        );			
		
		$drawItems[0]['lines'][] = $line; 		
        unset($line); 
		//#section 2
		$desc = Mage::getStoreConfig('qquoteadv/attach/short_desc', $this->_quoteadv->getStoreId());
        if ($desc) {
		   $shortDesc = $item->getShortDescription();
		   $shortDesc = str_replace("&nbsp;", ' ', $shortDesc);
		   $shortDesc = preg_replace("/&#?[a-z0-9]{2,8};/i", "", $shortDesc);
		   $line[] = array(
			 'text'  => Mage::helper('core/string')->str_split($shortDesc, 80, true, true),
			 'feed'  => $this->_leftTextPad,
			 'font'  => 'italic',
			 'font_size' => 6,
			 'height' => 7
		   );
		$drawItems[1]['lines'][] = $line; 		
        unset($line); 
		}
		
		//#section 3
		$text = array();
		if (count($attr) > 0 ) {
		   
		   foreach ($attr as $value){            
                if ($value!=='') { 
                    $value = str_replace('&quot;', '"', $value);
					$value = strip_tags($value);
					$str = Mage::helper('core/string')->str_split($value, 55, true, true);
					foreach($str as $value) {
                        if(!empty($value)) {
                            $text[] = $value;
                        }
		  			}
                }                  
           }
		}
		
		$itemRequest = strip_tags($itemRequest);
		$itemRequest = wordwrap($itemRequest, 80, "\n");
        foreach (explode("\n", $itemRequest) as $value) {
           if(!empty($value)) {
            $text[] = $value;
           }
        }
		$text[] = '';
		$line[] = array(
			 'text'  => $text,
			 'feed'  => $this->_leftTextPad,
			 'font'  => 'italic',
			 
		);	
		$drawItems[2]['lines'][] = $line; 
		$page = $this->drawLineBlocks($page, $drawItems, array('table_header' => true));
       
		return $page;
    }
    
    protected function insertTitles(&$page, $source, $storeId)
    {
        $quoteadvId = $source;
        if(is_null($this->_quoteadv)){
          $this->_quoteadv = Mage::getModel('qquoteadv/qqadvcustomer')->load($quoteadvId);          
        }
        
        $this->y -=30;
        $y = $this->y;
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.7));
        $page->setLineWidth(0.5);
        $page->drawRectangle($this->_leftRectPad, $y, 570, $this->y-=45);

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page);
        $topPosY = $y - 10; 
        $page->drawText(Mage::helper('qquoteadv')->__('TO:'), $this->_leftTextPad, $topPosY, 'UTF-8');
        
        
        $shippingCompany = $this->_quoteadv->getShippingCompany();
        $value = trim($shippingCompany);
        if(!empty($value)) {
            $y-=10;
            $this->_setFontBold($page);
            $page->drawText($shippingCompany, $this->_leftTextPad + 20, $y, 'UTF-8'); 
        }
        $y-=10;
        $state 		= $this->_quoteadv->getShippingRegion();
        $country 	= Mage::app()->getHelper('qquoteadv')->getCountryName($this->_quoteadv->getShippingCountryId());
   
        $lastLine = $country;
        if( $state ){ $lastLine = "$state, $lastLine"; }  
        
        $shipTo = array(            
            $this->_quoteadv->getShippingAddress(),
            $this->_quoteadv->getShippingCity() . ", ". $this->_quoteadv->getShippingPostcode(),
        	$lastLine
        );
        
               
        $this->_setFontRegular($page);
        foreach($shipTo as $value){
            if ($value!=='') {
                  $page->drawText($value, $this->_leftTextPad + 20, $y, 'UTF-8'); 
                  $y-=10;
            }        
        }       
        
        $x = 400; $xPad = $x + 80;
        $y = $topPosY;
                 
        $page->drawText(Mage::helper('qquoteadv')->__('Quote Proposal'), $x, $topPosY, 'UTF-8');              
        
        $realQuoteadvId = $this->_quoteadv->getIncrementId()?$this->_quoteadv->getIncrementId():$this->_quoteadv->getId();
		$page->drawText($realQuoteadvId, $xPad, $y, 'UTF-8');    

        $y-=10;
        $proposalDate = $this->_quoteadv->getUpdatedAt();  //Mage::helper('core')->formatDate( date( 'D M j Y')
        $page->drawText(Mage::helper('qquoteadv')->__('Date of Proposal'), $x, $y, 'UTF-8');
        $page->drawText(Mage::helper('core')->formatDate( $proposalDate, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, false), $xPad, $y, 'UTF-8');               
       
        $expDays = (int) Mage::getStoreConfig('qquoteadv/general/expirtime_proposal', $this->_quoteadv->getStoreId());
        if ($expDays) {
            $y-=10; 
	        $page->drawText(Mage::helper('qquoteadv')->__('Proposal valid until'), $x, $y, 'UTF-8');
            
            $validDate = date('D M j Y', strtotime("+$expDays days", strtotime($proposalDate)) );
	        $note = "( ".Mage::helper('qquoteadv')->__("%s days", $expDays)." )";
            $value = Mage::helper('core')->formatDate( $validDate, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, false). '    ' . $note;
	        $page->drawText($value, $xPad, $y, 'UTF-8'); 
        }
        
        $this->_setFontRegular($page);
    }   
    
    /**
     * Total information by quoteadv
     *
     * @param  $page
     */
    protected function insertTotal(&$page){
        $this->y -=20;
    	$currentY = $this->y;
        $excl = Mage::helper('qquoteadv')->__('(excl. TAX)');
        
        if(!$this->isSetTierPrice){
            $this->_setFontBold($page,9); 
            $sLabel     = Mage::helper('qquoteadv')->__('Total price')." ".$excl;
            $totalPrice = Mage::app()->getStore()->formatPrice($this->totalPrice, false); 

            $page->drawText($sLabel, 430, $this->y, 'UTF-8');
            $page->drawText(strip_tags($totalPrice), 530, $this->y, 'UTF-8');
        
            $this->y -= 10;        
        }
        
        $this->_setFontRegular($page);    	
        //$this->_setFontItalic($page);
        $price        =  $this->_quoteadv->getShippingPrice();
        $shippingType =  $this->_quoteadv->getShippingType();
        if($shippingType=='I'){
        	    $sLabel = Mage::helper('qquoteadv')->__('Shipping & Handling price per Item')." ".$excl;
                $sPrice = Mage::helper('checkout')->formatPrice($price);  
                
                $page->drawText($sLabel, 390, $this->y, 'UTF-8');
        	    $page->drawText(strip_tags($sPrice), 545, $this->y, 'UTF-8');
                              
        }elseif($shippingType=='O'){
        	    $sLabel = Mage::helper('qquoteadv')->__('Fixed Shipping & Handling price'." ".$excl);
                $sPrice = Mage::helper('checkout')->formatPrice($price);
        	   
                $page->drawText($sLabel, 390, $this->y, 'UTF-8');
        	    $page->drawText(strip_tags($sPrice), 545, $this->y, 'UTF-8');
             
        }else{
        	    $sLabel    = Mage::helper('qquoteadv')->__("Shipping & Handling price varies");
        		$sLabelExt = Mage::helper('qquoteadv')->__("select required qty and check out online");
        	   
        		$page->drawText($sLabel, 430, $this->y, 'UTF-8');  
        		
        		$this->y -=10;    	   
        	    $page->drawText(Mage::helper('qquoteadv')->__("select required qty and check out online"), 430, $this->y, 'UTF-8');
        	    
        	    $this->y -=10;    	   
        	    $page->drawText(Mage::helper('qquoteadv')->__("to see applicable price**"), 430, $this->y, 'UTF-8');       	   
        	                
        }     
        	
        $this->y = $currentY;
        	
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.9));
        $page->setLineWidth(0.5);
        $lowPoint = $this->y-55;
        $page->drawRectangle($this->_leftRectPad, $this->y, $this->_leftRectPad+215, $lowPoint);
    
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page);
                        
        $this->y-=10;                 
            
        $tmpY 		= $this->y; 
        $this->y 	= $lowPoint;
		
        $remark = $this->_quoteadv->getClientRequest();
		if($remark) {
		  $remark = strip_tags($remark);
		  $remark = wordwrap($remark, 55, "\n");
		  $data = explode("\n", $remark);
		  foreach($data as $value) {
			$value = trim($value);
			if ($value!=='') {
			   $page->drawText($value, $this->_leftTextPad, $tmpY, 'UTF-8');
			   $tmpY -=7;
			}
		  }
		}
        
        if( $this->y > $tmpY )
        	$this->y = $tmpY;
    }
    
    /**
     * Quote reneral remark
     *
     * @param  $page
     */
    protected function insertGeneralRemark(&$page){        
        $this->y -=10;
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $page->setLineWidth(0.5);
        $page->drawRectangle($this->_leftRectPad, $this->y, 570, $this->y-25);
    
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page);
        
        $this->y -=10;            
		
        $qquoteadvRemark = Mage::getStoreConfig('qquoteadv/general/qquoteadv_remark', $this->_quoteadv->getStoreId()); 
		if($qquoteadvRemark) {
		  $qquoteadvRemark = strip_tags($qquoteadvRemark);		
		  $qquoteadvRemark = wordwrap($qquoteadvRemark, 165, "\n");
		  $data = explode("\n", $qquoteadvRemark);
		  foreach ($data as $value) {   
			$value = trim($value);
			if ($value!=='') { 
				 $page->drawText($value, $this->_leftTextPad, $this->y, 'UTF-8');
				 $this->y -=7;
			}
		  }
		}
    }
    
    public function getSku($item)
    {
        if ($item->getProductOptionByCode('simple_sku'))
            return $item->getProductOptionByCode('simple_sku');
        else
            return $item->getSku();
    }
    
    
    /**
	 * Get attribute options array 
	 * @param object $product 
	 * @param string $attribute
	 * @return array 
	 */
	public function getOption($product, $attribute)
	{
		$superAttribute = array(); 
		if($product->getTypeId() == 'simple') {
			$superAttribute = Mage::helper('qquoteadv')->getSimpleOptions($product, unserialize($attribute)); 	
		}		
		return $superAttribute;
	}
    
    protected function retrieveOptions($product, $superAttribute) {
        $attr  = array();

        if ($product->getTypeId() == 'simple') {
            if ($superAttribute) {
                foreach($superAttribute as $option => $value) { 
                    if(!empty($value)) {
                        $attr[]= $option;  
                        $attr[]= '   '.$value;
                    }
                }
            }
        }
        
        return $attr;
    }
	
	 /**
     * Draw lines
     *
     * draw items array format:
     * lines        array;array of line blocks (required)
     * shift        int; full line height (optional)
     * height       int;line spacing (default 10)
     *
     * line block has line columns array
     *
     * column array format
     * text         string|array; draw text (required)
     * feed         int; x position (required)
     * font         string; font style, optional: bold, italic, regular
     * font_file    string; path to font file (optional for use your custom font)
     * font_size    int; font size (default 7)
     * align        string; text align (also see feed parametr), optional left, right
     * height       int;line spacing (default 10)
     *
     * @param Zend_Pdf_Page $page
     * @param array $draw
     * @param array $pageSettings
     * @throws Mage_Core_Exception
     * @return Zend_Pdf_Page
     */
	 
    public function drawLineBlocks(Zend_Pdf_Page $page, array $draw, array $pageSettings = array())
    {
        foreach ($draw as $itemsProp) {
            if (!isset($itemsProp['lines']) || !is_array($itemsProp['lines'])) {
                Mage::throwException(Mage::helper('sales')->__('Invalid draw line data. Please define "lines" array.'));
            }
            $lines  = $itemsProp['lines'];
            $height = isset($itemsProp['height']) ? $itemsProp['height'] : 10;

            if (empty($itemsProp['shift'])) {
                $shift = 0;
                foreach ($lines as $line) {
                    $maxHeight = 0;
                    foreach ($line as $column) {
                        $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                        if (!is_array($column['text'])) {
                            $column['text'] = array($column['text']);
                        }
                        $top = 0;
                        foreach ($column['text'] as $part) {
                            $top += $lineSpacing;
                        }

                        $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                    }
                    $shift += $maxHeight;
                }
                $itemsProp['shift'] = $shift;
            }

            if ($this->y - $itemsProp['shift'] < 15) {
                $page = $this->addNewPage(); //$this->newPage($pageSettings);
            }

            foreach ($lines as $line) {
                $maxHeight = 0;
                foreach ($line as $column) {
                    $fontSize = empty($column['font_size']) ? 7 : $column['font_size'];
                    if (!empty($column['font_file'])) {
                        $font = Zend_Pdf_Font::fontWithPath($column['font_file']);
                        $page->setFont($font, $fontSize);
                    }
                    else {
                        $fontStyle = empty($column['font']) ? 'regular' : $column['font'];
                        switch ($fontStyle) {
                            case 'bold':
                                $font = $this->_setFontBold($page, $fontSize);
                                break;
                            case 'italic':
                                $font = $this->_setFontItalic($page, $fontSize);
                                break;
                            default:
                                $font = $this->_setFontRegular($page, $fontSize);
                                break;
                        }
                    }

                    if (!is_array($column['text'])) {
                        $column['text'] = array($column['text']);
                    }

                    $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                    $top = 0;
                    foreach ($column['text'] as $part) {
                        $feed = $column['feed'];
                        $textAlign = empty($column['align']) ? 'left' : $column['align'];
                        $width = empty($column['width']) ? 0 : $column['width'];
                        switch ($textAlign) {
                            case 'right':
                                if ($width) {
                                    $feed = $this->getAlignRight($part, $feed, $width, $font, $fontSize);
                                }
                                else {
                                    $feed = $feed - $this->widthForStringUsingFontSize($part, $font, $fontSize);
                                }
                                break;
                            case 'center':
                                if ($width) {
                                    $feed = $this->getAlignCenter($part, $feed, $width, $font, $fontSize);
                                }
                                break;
                        }
                        $page->drawText($part, $feed, $this->y-$top, 'UTF-8');
                        $top += $lineSpacing;
                    }

                    $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                }
                $this->y -= $maxHeight;
            }
        }

        return $page;
    }
	
}