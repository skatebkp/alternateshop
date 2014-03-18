<?php
class Ophirah_Qquoteadv_Block_Qquoteadv_Email_Items extends Mage_Sales_Block_Items_Abstract//Mage_Core_Block_Template
{
	public $autoproposal = 0;
  
    public function getQuote() {
        $quoteId = $this->getRequest()->getParam('id');
        if(!$quoteId){
            $quoteObj   = $this->getData('quote');
            $quoteId    = $quoteObj->getQuoteId();
        }
        $quoteData = Mage::getModel('qquoteadv/qqadvcustomer')->getCollection()
                        ->addFieldToFilter('quote_id', $quoteId)
        ;

        foreach ($quoteData as $key => $quote) {
            $this->setQuoteId($quoteId);
            return $quote;
        }

        return;
    }

     /**
     * Get Product information from qquote_product table
     * @return quote object
     */
    public function getAllItems() {
        $collection = Mage::getModel('qquoteadv/qqadvproduct')->getQuoteProduct($this->getQuoteId());

        return  $collection;
    }
	//#set value from template
	public function setAutoproposal($val){
      $this->autoproposal = $val;
      return $this;
    }
    
    public function isSetAutoProposal(){
      return $this->autoproposal;
    }   
    
    
    /**
    * Get Totals of a quote
    * @return float/int if a total
    * 
    * or 
    *
    * @return false if tier pricing is used
    */
    public function getSubtotal(){
    	 $total = 0;
    	 
    	 $requestedProducts = Mage::getModel('qquoteadv/requestitem')
			                        	->getCollection()
																->addFieldToFilter('quote_id', $this->getQuoteId());
			                   
    	 $requestedProducts->getSelect()->order(array('product_id ASC', 'request_qty ASC'));
    	 $product_ids  = array();
    	 foreach($requestedProducts as $line){
    	 	 $prod_id = $line->getProductId();
    	 	 if(in_array( $prod_id,  $product_ids)) return false; // tiers used if product_id exists twice 
    	 	 $product_ids[] = $prod_id;
    	 	 
    	 	 $productQty     = $line->getRequestQty()*1;
         $priceProposal  = $line->getOwnerBasePrice(); 
         $lineTotal =  $productQty *  $priceProposal;
    		 $total += $lineTotal;	
    	 }
    	 
    	 return $total;
    }
    
		/**
		* Get Shipping total of a quote
		* @return float/int if a total
		* 
		* or 
		*
		* @return false if varuable pricing is used
		*/
		public function getShippingtotal(){
				$sPrice        =  $this->getQuote()->getShippingPrice();
				$shippingType =  $this->getQuote()->getShippingType();

				if($shippingType=='I'){
						$qty = 0;
						$requestedProducts = Mage::getModel('qquoteadv/requestitem')
																									->getCollection()
																									->addFieldToFilter('quote_id', $this->getQuoteId());

						$requestedProducts->getSelect()->order(array('product_id ASC', 'request_qty ASC'));
						foreach($requestedProducts as $line) $qty += $line->getRequestQty()*1;
						
						$sTotal = $sPrice * $qty;
				}elseif($shippingType=='O'){
						$sTotal = $sPrice;
				}else{
						$sTotal = false;
				}   
				
				return $sTotal;

    	}
    
			/**
			* Get Totals of a quote
			* @return float/int if a total
			* 
			* or 
			*
			* @return false if tier pricing is used
			*/
			public function getGrandtotal(){
					
					$subtotal = $this->getSubTotal();
					if($subtotal === false) return false;
					
					$shipping = $this->getShippingTotal();
					$tax = 0; //TODO add Tax amount
					
					$grandTotal = $subtotal + $shipping + $tax;
					return $grandTotal;
			}
}
