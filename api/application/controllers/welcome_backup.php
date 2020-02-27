<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Welcome extends CI_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->model('admin_model');
		$this->load->model('all_model');
        $this->load->helper('string');
        $this->load->library('image_lib');

	}

    protected function _detect_method() {
        $method = strtolower($this->input->server('REQUEST_METHOD'));

        if ($this->config->item('enable_emulate_request')) {
            if ($this->input->post('_method')) {
                $method = strtolower($this->input->post('_method'));
            } else if ($this->input->server('HTTP_X_HTTP_METHOD_OVERRIDE')) {
                $method = strtolower($this->input->server('HTTP_X_HTTP_METHOD_OVERRIDE'));
            }
        }

        if (in_array($method, array('get', 'delete', 'post', 'put'))) {
            return $method;
        }
        
        return 'get';
    }

	public function index(){
		if(($this->session->userdata('user_name')!="")){
			$this->welcome();
		}
		else{
			$data['title']= 'Home';
			$this->load->view("index.php", $data);
		}
	}

	public function welcome(){
		$data['title']= 'Welcome';
		$this->load->view("index.php", $data);
	}
	 
	public function adminDetails() {
		
		$admin_details=$this->admin_model->getData('salo_admin_login',array('role' => 0),null);
		return $admin_details;
	}
	
	public function websiteDetails() {
		
		$admin_details=$this->admin_model->getData('salo_admin_login',array('role' => 0),null);
		
		$data['status']="True";
		$data['type'] = '200';
		$data['data'] = $admin_details;
	    $data['msg'] = 'Website Details';
	    
	    echo json_encode($data);
	}

	public function splash($mobileDeviceId,$tokenid,$loggedInUserId)
	{
		
		$data['status']="true";
		
		//******************  First Slider Starts ********************************//
		
		$a=0;
		$firstSlider=array();
		$mainSlider = $this->admin_model->getBasic("SELECT * FROM salo_slider WHERE `status`='0' and `slidertype`='1'"); 
		foreach($mainSlider as $row)
		{
			$title=$row['title'];
			$image=$this->config->item('base_image_url')."/assets/uploads/slider/".$row['image'];
			
			$arr=array('banner_text' => $title,'banner_image' => $image,'task' => '0' , 'link' => '0');
			array_push($firstSlider,$arr);
			$a++;
		}
		
		$data['banner']=$firstSlider;
		
			//****************** First Slider Ends ********************************//
			
		
			if($loggedInUserId==0)
			{
			  $colName="device_id";
			  $colVal=$mobileDeviceId;
			  
			      $getTotalCartQty=$this->admin_model->getBasic("SELECT SUM(quantity) as total_qty FROM  `salo_temp_cart` WHERE `device_id`='$mobileDeviceId' and user_id='0' and `status`='0'");
    			  if((!empty($getTotalCartQty[0]['total_qty'])) || ($getTotalCartQty[0]['total_qty']!="") || ($getTotalCartQty[0]['total_qty']!='null') || ($getTotalCartQty[0]['total_qty']!="0"))
    			  {
    				$total_prod_cart_qty=$getTotalCartQty[0]['total_qty'];
    			  }
    			  else
    			  {
    				$total_prod_cart_qty="0";
    			  }
    			  
    			  $data['totalCartQty']=$total_prod_cart_qty;
			}
			else
			{
			  $colName="user_id";
			  $colVal=$loggedInUserId;

			   $dataUpdate['token_id']=$tokenid;
			   $this->db->update('salo_users', $dataUpdate, array('id' => $loggedInUserId));
			   
			      $getTotalCartQty=$this->admin_model->getBasic("SELECT SUM(quantity) as total_qty FROM  `salo_temp_cart` WHERE `user_id`='$loggedInUserId' and `status`='0'");
    			  if((!empty($getTotalCartQty[0]['total_qty'])) || ($getTotalCartQty[0]['total_qty']!="") || ($getTotalCartQty[0]['total_qty']!='null') || ($getTotalCartQty[0]['total_qty']!="0"))
    			  {
    				$total_prod_cart_qty=$getTotalCartQty[0]['total_qty'];
    			  }
    			  else
    			  {
    				$total_prod_cart_qty="0";
    			  }
    			  
    			  $data['totalCartQty']=$total_prod_cart_qty;
			}
	
	     
        $brandList=array();
        $getBrands=$this->admin_model->getData('salo_brands',array('status' => 0),null);
        foreach($getBrands as $br)
		{
		    $id=$br['id'];
			$name=$br['name'];
			$image=$br['logo'];
            if($image!="")
			{
			  $imageUrl=$this->config->item('base_image_url')."/assets/uploads/brands/".$br['logo'];
			}
			else
			{
				$imageUrl=$this->config->item('base_image_url')."/assets/uploads/logo/logo.png";
			}
			
			$arr=array('id' => $id,'name' => $name,'img' => $imageUrl);
			array_push($brandList,$arr);

        }	
		
        $data['brandList']=$brandList;		
     
	 
	    $offerbannerList=array();
        $getOfferBanner=$this->admin_model->getData('salo_offer_banner',array('status' => 0),null);
        foreach($getOfferBanner as $or)
		{
		    $id=$or['id'];
			$name=$or['title'];
			$bannerFor=$or['banner_for'];
			$image=$or['image'];
            if($image!="")
			{
			  $imageUrl=$this->config->item('base_image_url')."/assets/uploads/offer/".$or['image'];
			}
			else
			{
				$imageUrl=$this->config->item('base_image_url')."/assets/uploads/logo/logo.png";
			}
			
			$arrOff=array('id' => $id,'name' => $name,'img' => $imageUrl,'bannerFor' => $bannerFor);
			array_push($offerbannerList,$arrOff);

        }	
		
        $data['offerbannerList']=$offerbannerList;		
     
	 
	 
		//******************  Menu Starts ********************************//
		
		$a=0;
		$menuList=array();
		$firstCat = $this->admin_model->getData('salo_category',array('status' => 0),null);
		foreach($firstCat as $row)
		{
			$cid=$row['category_id'];
			$category_name=$row['category_name'];
			$image=$row['image'];
			$caticon=$row['cat_icon'];
    		  if($caticon!="")
    			{
    			  $caticonUrl=$this->config->item('base_image_url')."/assets/uploads/category/icon/".$row['cat_icon'];
    			}
    			else
    			{
    			  $caticonUrl=$this->config->item('base_image_url')."/assets/lupras/assets/img/extra/breadcrumb-1.jpg";
    			}
					
					
			if($image!="")
			{
			  $imageUrl=$this->config->item('base_image_url')."/assets/uploads/category/crop_img_460x346/".$row['image'];
			}
			else
			{
				$imageUrl=$this->config->item('base_image_url')."/assets/lupras/assets/img/extra/breadcrumb-1.jpg";
			}
			
             
		    $secondCat = $this->admin_model->getData('salo_sub_category',array('status' => 0,'category_id' => $cid),'AND');
			if(count($secondCat)!=0)
			{
			    $islastFirst="false";
				
				$secondcatArray=array();
				$c=1;
				$count=count($secondCat);
				foreach($secondCat as $rt)
				{
				  $sid=$rt['sub_category_id'];
				  $sub_category_name=$rt['sub_category_name'];
				  $image=$rt['image'];
				  $subicon=$rt['cat_icon'];
				  if($subicon!="")
					{
					  $subiconUrl=$this->config->item('base_image_url')."/assets/uploads/category/icon/".$rt['cat_icon'];
					}
					else
					{
					  $subiconUrl=$this->config->item('base_image_url')."/assets/lupras/assets/img/extra/breadcrumb-1.jpg";
					}
				  if($image!="")
					{
					  $subimageUrl=$this->config->item('base_image_url')."/assets/uploads/category/crop_img_460x346/".$rt['image'];
					}
					else
					{
					  $subimageUrl=$this->config->item('base_image_url')."/assets/lupras/assets/img/extra/breadcrumb-1.jpg";
					}
					
					 $thirdCat = $this->admin_model->getData('salo_sub_sub_category',array('status' => 0,'sub_category_id' => $sid),'AND');
					 if(count($thirdCat)!=0)
					 {
					    $islastSecond="false";
						
						 $thirdcatArray=array();
						 $d=1;
						 $count=count($thirdCat);
						 foreach($thirdCat as $rtt)
						 {
						      $ssid=$rtt['id'];
							  $sub_sub_category_name=$rtt['name'];
							  $subimage=$rtt['image'];
							  if($subimage!="")
								{
								  $subsubimageUrl=$this->config->item('base_image_url')."/assets/uploads/category/crop_img_460x346/".$rtt['image'];
								}
								else
								{
								  $subsubimageUrl=$this->config->item('base_image_url')."/assets/lupras/assets/img/extra/breadcrumb-1.jpg";
								}
								
								
						    $arrsubsub=array('id' => $ssid,'name' => $sub_sub_category_name,'img' => $subsubimageUrl,'islast' => 'true');
							array_push($thirdcatArray,$arrsubsub);
							$d++;
						 }
						
					 }
					 else
					 {
					    $islastSecond="true";
						$thirdcatArray="";
					 }
					 
					$arrsub=array('id' => $sid,'name' => $sub_category_name,'img' => $subimageUrl,'icon'=>$subiconUrl,'islast' => $islastSecond,'sub_sub_cat' => $thirdcatArray);
					array_push($secondcatArray,$arrsub);
					$c++;
				}
			}
			else
			{
			    $islastFirst="true";
				$secondcatArray="";
			}
			$arr=array('id' => $cid,'name' => $category_name,'img' => $imageUrl,'icon'=>$caticonUrl,'islast' => $islastFirst,'sub_cat' => $secondcatArray);
			array_push($menuList,$arr);
			$a++;
		}
		
		$data['menuList']=$menuList;
		
			//******************  Menu Ends ********************************//
			
			
		//***** Discounted Product List STARTS *******//

        $discountedProduct=array();
		$productData=$this->admin_model->getBasic("SELECT DISTINCT product_id FROM salo_product_attribute WHERE `discount` != '' and status='1' AND product_id IN (SELECT `id` FROM salo_products WHERE status='0') group by product_id ORDER by product_id DESC LIMIT 0,3");
		if(count($productData)!=0)
		{
		 
		  foreach($productData as $rt)
		  {
		    $prodId=$rt['product_id'];
            
			$getProduct=$this->admin_model->getBasic("SELECT * FROM salo_products WHERE id='$prodId'");
			$lr=$getProduct[0];
		    $id=$lr['id'];
			$title=$lr['title'];
			$image=$lr['image'];
			$stock_status=$lr['stock_status'];
			$description=$lr['description'];
			$catId=$lr['category_id'];
			$subcatId=$lr['sub_category_id'];
			$subsubcatId=$lr['sub_sub_category_id'];
			$brandId=$lr['brand_id'];
			
			if($image!="")
			{
			 $imageUrl=$this->config->item('base_image_url')."assets/uploads/products/".$image;
			}
			else
			{
			 $imageUrl="";
			}
			
			 $prodId=$lr['id'];
			 if($loggedInUserId==0)
    		 {
    			$colName="device_id";
    			$colVal=$mobileDeviceId;
    			  
    			$whereArr=array($colName => $colVal,'user_id' => '0' , 'pro_id' => $prodId,'status' => 0);
    			  
    		}
    		else
    		{
    			$colName="user_id";
    			$colVal=$loggedInUserId;
    			  
    			$whereArr=array($colName => $colVal,'pro_id' => $prodId,'status' => 0);
    		}
			
			$getCartData=$this->admin_model->getData('salo_temp_cart',$whereArr,'AND');
			if(count($getCartData)!=0)
			{
			  $prod_cart_qty=$getCartData[0]['quantity'];
			}
			else
			{
			  $prod_cart_qty="0";
			}
			
			$Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE product_id='$prodId' and `status`='1' ORDER BY price ASC LIMIT 1");
			$pr=$Productprice[0];
		 
		    $attributeid = $pr['id'];
			$attribute_value = $pr['attribute_value'];
			$unit_id = $pr['unit_id'];
			$price = $pr['price'];
			  
			if($price=="")
			{
			 $price="0.00";
			}
			else
			{
			  $price=$price;
			}
			  
			  $discount = $pr['discount'];
			  if($discount=="")
			  {
				  $discount="0";
				  $sellAmt=$price;
			  }
			  else
			  {
				   $discount=$discount;
				   $disper=$discount / 100;
				   $disamt = $price * $disper;
				   $sellAmt=$price - $disamt;
				   $sellAmt=round($sellAmt);
			  }
			  
			
			  $unit = $this->admin_model->getData('salo_unit',array('id' => $unit_id),null);
			  $unitName=$unit[0]['unit'];
			  
			 $attribute = $attribute_value." ".$unitName;
			 $attributePrice=array();
			 $Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE product_id='$prodId' and `status`='1' ORDER BY price ASC");
			 foreach($Productprice as $pr_pr)
			 {
			      $attributeid = $pr_pr['id'];
			      $prodAttid = $pr_pr['product_id'];
				  $attribute_value = $pr_pr['attribute_value'];
				  $unit_id = $pr_pr['unit_id'];
				  $priceAtt = $pr_pr['price'];
				  
				  if($priceAtt=="")
				  {
					$priceAtt="0.00";
				  }
				  else
				  {
					$priceAtt=$priceAtt;
				  }
				  
				  $discountAtt = $pr_pr['discount'];
				  if($discountAtt=="")
				  {
					  $discountAtt="0";
					  $sellAmtAtt=$priceAtt;
				  }
				  else
				  {
					   $discountAtt=$discountAtt;
					   $disper=$discountAtt / 100;
					   $disamt = $priceAtt * $disper;
					   $sellAmtAtt=$priceAtt - $disamt;
					   $sellAmtAtt=round($sellAmtAtt);
				  }
				  
				
				if($loggedInUserId==0)
				{
					$colName="device_id";
					$colVal=$mobileDeviceId;
					  
					$whereArr=array($colName => $colVal,'user_id' => '0' ,'product_attribute' => $attributeid,'status' => 0);
					  
				}
				else
				{
					$colName="user_id";
					$colVal=$loggedInUserId;
					  
					$whereArr=array($colName => $colVal,'product_attribute' => $attributeid,'status' => 0);
				}
				
				$getCartDataAtt=$this->admin_model->getData('salo_temp_cart',$whereArr,'AND');
				if(count($getCartDataAtt)!=0)
				{
				  $prod_cart_qty_att=$getCartDataAtt[0]['quantity'];
				}
				else
				{
				  $prod_cart_qty_att="0";
				}
			
				  $unit = $this->admin_model->getData('salo_unit',array('id' => $unit_id),null);
				  $unitName=$unit[0]['unit'];
				  
				  

				 $attributeName = $attribute_value." ".$unitName;
				 $arrAtt=array('attribute_id' => $attributeid,'attribute_name' => $attributeName,'listprice' => $priceAtt,'saleprice' => $sellAmtAtt,'discount' => $discountAtt,'attribute_cart_qty' => $prod_cart_qty_att);
				 array_push($attributePrice,$arrAtt);
			 }
			 
			$arr = array('id' => $id,'title' => $title,'image' => $imageUrl,'prod_cart_qty' => $prod_cart_qty,'attribute' => $attribute , 'listprice' => $price, 'saleprice' => $sellAmt, 'discount' => $discount,'stock_status' => $stock_status,'attributeList' => $attributePrice);
			
			array_push($discountedProduct,$arr);
		  }

		}
				  
		$data['discountedProduct']=$discountedProduct;
	  
	   //***** Discounted Product List ENDS *******//
	   
	   
	           //***** Most Popular Product List STARTS *******//

        $mostPopularProduct=array();
		$productListData=$this->admin_model->getBasic("SELECT pro_id,COUNT(pro_id) as cnt FROM `salo_temp_cart` WHERE payment_status='1' and status='1' AND pro_id IN (SELECT `id` FROM salo_products WHERE status='0') GROUP BY pro_id ORDER BY cnt DESC LIMIT 0,3");
		if(count($productListData)!=0)
		{
		 
		  foreach($productListData as $rt)
		  {
		    $prodId=$rt['pro_id'];
            
			$getProduct=$this->admin_model->getBasic("SELECT * FROM salo_products WHERE id='$prodId'");
			$lr=$getProduct[0];
		    $id=$lr['id'];
			$title=$lr['title'];
			$image=$lr['image'];
			$stock_status=$lr['stock_status'];
			$description=$lr['description'];
			$catId=$lr['category_id'];
			$subcatId=$lr['sub_category_id'];
			$subsubcatId=$lr['sub_sub_category_id'];
			$brandId=$lr['brand_id'];
			
			if($image!="")
			{
			 $imageUrl=$this->config->item('base_image_url')."assets/uploads/products/".$image;
			}
			else
			{
			 $imageUrl="";
			}
			
			 $prodId=$lr['id'];
			 if($loggedInUserId==0)
    		 {
    			$colName="device_id";
    			$colVal=$mobileDeviceId;
    			  
    			$whereArr=array($colName => $colVal,'user_id' => '0' , 'pro_id' => $prodId,'status' => 0);
    			  
    		}
    		else
    		{
    			$colName="user_id";
    			$colVal=$loggedInUserId;
    			  
    			$whereArr=array($colName => $colVal,'pro_id' => $prodId,'status' => 0);
    		}
			
			$getCartData=$this->admin_model->getData('salo_temp_cart',$whereArr,'AND');
			if(count($getCartData)!=0)
			{
			  $prod_cart_qty=$getCartData[0]['quantity'];
			}
			else
			{
			  $prod_cart_qty="0";
			}
			
			$Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE product_id='$prodId' and `status`='1' ORDER BY price ASC LIMIT 1");
			$pr=$Productprice[0];
		 
		    $attributeid = $pr['id'];
			$attribute_value = $pr['attribute_value'];
			$unit_id = $pr['unit_id'];
			$price = $pr['price'];
			  
			if($price=="")
			{
			 $price="0.00";
			}
			else
			{
			  $price=$price;
			}
			  
			  $discount = $pr['discount'];
			  if($discount=="")
			  {
				  $discount="0";
				  $sellAmt=$price;
			  }
			  else
			  {
				   $discount=$discount;
				   $disper=$discount / 100;
				   $disamt = $price * $disper;
				   $sellAmt=$price - $disamt;
				   $sellAmt=round($sellAmt);
			  }
			  
			
			  $unit = $this->admin_model->getData('salo_unit',array('id' => $unit_id),null);
			  $unitName=$unit[0]['unit'];
			  
			 $attribute = $attribute_value." ".$unitName;
			 $attributePrice=array();
			 $Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE product_id='$prodId' and `status`='1' ORDER BY price ASC");
			 foreach($Productprice as $pr_pr)
			 {
			      $attributeid = $pr_pr['id'];
			      $prodAttid = $pr_pr['product_id'];
				  $attribute_value = $pr_pr['attribute_value'];
				  $unit_id = $pr_pr['unit_id'];
				  $priceAtt = $pr_pr['price'];
				  
				  if($priceAtt=="")
				  {
					$priceAtt="0.00";
				  }
				  else
				  {
					$priceAtt=$priceAtt;
				  }
				  
				  $discountAtt = $pr_pr['discount'];
				  if($discountAtt=="")
				  {
					  $discountAtt="0";
					  $sellAmtAtt=$priceAtt;
				  }
				  else
				  {
					   $discountAtt=$discountAtt;
					   $disper=$discountAtt / 100;
					   $disamt = $priceAtt * $disper;
					   $sellAmtAtt=$priceAtt - $disamt;
					   $sellAmtAtt=round($sellAmtAtt);
				  }
				  
				
				if($loggedInUserId==0)
				{
					$colName="device_id";
					$colVal=$mobileDeviceId;
					  
					$whereArr=array($colName => $colVal,'user_id' => '0' ,'product_attribute' => $attributeid,'status' => 0);
					  
				}
				else
				{
					$colName="user_id";
					$colVal=$loggedInUserId;
					  
					$whereArr=array($colName => $colVal,'product_attribute' => $attributeid,'status' => 0);
				}
				
				$getCartDataAtt=$this->admin_model->getData('salo_temp_cart',$whereArr,'AND');
				if(count($getCartDataAtt)!=0)
				{
				  $prod_cart_qty_att=$getCartDataAtt[0]['quantity'];
				}
				else
				{
				  $prod_cart_qty_att="0";
				}
			
				  $unit = $this->admin_model->getData('salo_unit',array('id' => $unit_id),null);
				  $unitName=$unit[0]['unit'];
				  
				  

				 $attributeName = $attribute_value." ".$unitName;
				 $arrAtt=array('attribute_id' => $attributeid,'attribute_name' => $attributeName,'listprice' => $priceAtt,'saleprice' => $sellAmtAtt,'discount' => $discountAtt,'attribute_cart_qty' => $prod_cart_qty_att);
				 array_push($attributePrice,$arrAtt);
			 }
			 
			$arr = array('id' => $id,'title' => $title,'image' => $imageUrl,'prod_cart_qty' => $prod_cart_qty,'attribute' => $attribute , 'listprice' => $price, 'saleprice' => $sellAmt, 'discount' => $discount,'stock_status' => $stock_status,'attributeList' => $attributePrice);
			
			array_push($mostPopularProduct,$arr);
		  }

		}
				  
		$data['mostPopularProduct']=$mostPopularProduct;
	  
	   //***** Most Popular Product List ENDS *******//
	   
	   
        $data['msg']="Done";
		
		echo json_encode($data);
	}

    public function brandProductList($mobileDeviceId,$loggedInUserId,$brandId,$page=1)
	{
	   $data['status']="true";
	   $data['brandId']=$brandId;


	   $data['fetchLimit']=10;
	   if($page==1)
       {
         $limit=0;
       }
       else
       {
          $limit=($page - 1) * $data['fetchLimit'];
       }
			

	   $productCount=$this->admin_model->getBasic("SELECT id FROM salo_products where status='0' and product_type='1' and brand_id='$brandId'");
	   $total_records=count($productCount);
	   $productData = $this->admin_model->getwithLimitOrderBy('salo_products',array('status' => '0','product_type' => 1,'brand_id' => $brandId),$data['fetchLimit'],$limit,'id','DESC'); 
	   
	   
	   $fetchLimit=$data['fetchLimit'];
	   $total_pages = ceil($total_records / $fetchLimit);   
	   $data['total_pages']=$total_pages;
	   $data['current_page']=$page;
		
	    $productArray=array();
	    foreach($productData as $lr)
		{
			$id=$lr->id;
			$title=$lr->title;
			$image=$lr->image;
			$stock_status=$lr->stock_status;
			$description=$lr->description;
			$catId=$lr->category_id;
			$subcatId=$lr->sub_category_id;
			$subsubcatId=$lr->sub_sub_category_id;
			$brandId=$lr->brand_id;
			
			if($image!="")
			{
			 $imageUrl=$this->config->item('base_image_url')."assets/uploads/products/".$image;
			}
			else
			{
			 $imageUrl="";
			}
			
			    $prodId=$lr->id;
			    if($loggedInUserId==0)
    			{
    			  $colName="device_id";
    			  $colVal=$mobileDeviceId;
    			  
    			  $whereArr=array($colName => $colVal,'user_id' => '0' , 'pro_id' => $prodId,'status' => 0);
    			  
    			}
    			else
    			{
    			  $colName="user_id";
    			  $colVal=$loggedInUserId;
    			  
    			  $whereArr=array($colName => $colVal,'pro_id' => $prodId,'status' => 0);
    			}
			
			  $getCartData=$this->admin_model->getData('salo_temp_cart',$whereArr,'AND');
			  if(count($getCartData)!=0)
			  {
				$prod_cart_qty=$getCartData[0]['quantity'];
			  }
			  else
			  {
				$prod_cart_qty="0";
			  }
			
			
			$Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE product_id='$id' and `status`='1' ORDER BY price ASC LIMIT 1");
			$pr=$Productprice[0];
		 
		    $attributeid = $pr['id'];
			$attribute_value = $pr['attribute_value'];
			$unit_id = $pr['unit_id'];
			$price = $pr['price'];
			  
			if($price=="")
			{
			 $price="0.00";
			}
			else
			{
			  $price=$price;
			}
			  
			  $discount = $pr['discount'];
			  if($discount=="")
			  {
				  $discount="0";
				  $sellAmt=$price;
			  }
			  else
			  {
				   $discount=$discount;
				   $disper=$discount / 100;
				   $disamt = $price * $disper;
				   $sellAmt=$price - $disamt;
				   $sellAmt=round($sellAmt);
			  }
			  
			
			  $unit = $this->admin_model->getData('salo_unit',array('id' => $unit_id),null);
			  $unitName=$unit[0]['unit'];
			  
			 $attribute = $attribute_value." ".$unitName;
			 $attributePrice=array();
			 $Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE product_id='$id' and `status`='1' ORDER BY price ASC");
			 foreach($Productprice as $pr_pr)
			 {
			      $attributeid = $pr_pr['id'];
				  $attribute_value = $pr_pr['attribute_value'];
				  $unit_id = $pr_pr['unit_id'];
				  $priceAtt = $pr_pr['price'];
				  
				  if($priceAtt=="")
				  {
					$priceAtt="0.00";
				  }
				  else
				  {
					$priceAtt=$priceAtt;
				  }
				  
				  $discountAtt = $pr_pr['discount'];
				  if($discountAtt=="")
				  {
					  $discountAtt="0";
					  $sellAmtAtt=$priceAtt;
				  }
				  else
				  {
					   $discountAtt=$discountAtt;
					   $disper=$discountAtt / 100;
					   $disamt = $priceAtt * $disper;
					   $sellAmtAtt=$priceAtt - $disamt;
					   $sellAmtAtt=round($sellAmtAtt);
				  }
				  
				  
				   if($loggedInUserId==0)
				   {
						$colName="device_id";
						$colVal=$mobileDeviceId;
						  
						$whereArr=array($colName => $colVal,'user_id' => '0' ,'product_attribute' => $attributeid,'status' => 0);
						  
				   }
				   else
				   {
						$colName="user_id";
						$colVal=$loggedInUserId;
						  
						$whereArr=array($colName => $colVal,'product_attribute' => $attributeid,'status' => 0);
				   }
					
					$getCartDataAtt=$this->admin_model->getData('salo_temp_cart',$whereArr,'AND');
					if(count($getCartDataAtt)!=0)
					{
					  $prod_cart_qty_att=$getCartDataAtt[0]['quantity'];
					}
					else
					{
					  $prod_cart_qty_att="0";
					}
				
				  $unit = $this->admin_model->getData('salo_unit',array('id' => $unit_id),null);
				  $unitName=$unit[0]['unit'];
				  

				 $attributeName = $attribute_value." ".$unitName;
				 $arrAtt=array('attribute_id' => $attributeid,'attribute_name' => $attributeName,'listprice' => $priceAtt,'saleprice' => $sellAmtAtt,'discount' => $discountAtt,'attribute_cart_qty' => $prod_cart_qty_att);
				 array_push($attributePrice,$arrAtt);
			 }
			 
			$arr = array('id' => $id,'title' => $title,'image' => $imageUrl,'prod_cart_qty' => $prod_cart_qty,'attribute' => $attribute , 'listprice' => $price, 'saleprice' => $sellAmt, 'discount' => $discount,'stock_status' => $stock_status,'attributeList' => $attributePrice);
			
			array_push($productArray,$arr);
		}
       
       $data['products']=$productArray;
	   $data['msg']="Done";
	   echo json_encode($data);
	}
	
	public function productList($mobileDeviceId,$loggedInUserId,$catLvl,$catId,$page=1)
	{
	   $data['status']="true";
	   $data['catId']=$catId;

	   $data['fetchLimit']=10;
	   if($page==1)
       {
         $limit=0;
       }
       else
       {
          $limit=($page - 1) * $data['fetchLimit'];
       }
	   
       switch($catLvl)
		{
		   case "cat_lvl1":
		   
		     $productCount=$this->admin_model->getBasic("SELECT id FROM salo_products where status='0' and product_type='1' and category_id='$catId'");
		     $total_records=count($productCount);
	         $productData = $this->admin_model->getwithLimitOrderBy('salo_products',array('status' => '0','product_type' => 1,'category_id' => $catId),$data['fetchLimit'],$limit,'id','DESC');
		 
		   break;
		   
		   case "cat_lvl2":
		     
			  $productCount=$this->admin_model->getBasic("SELECT id FROM salo_products where status='0' and product_type='1' and sub_category_id='$catId'");
		      $total_records=count($productCount);
	          $productData = $this->admin_model->getwithLimitOrderBy('salo_products',array('status' => '0','product_type' => 1,'sub_category_id' => $catId),$data['fetchLimit'],$limit,'id','DESC'); 
		 
		   break;
		   
		   case "cat_lvl3":
		     
			 $productCount=$this->admin_model->getBasic("SELECT id FROM salo_products where status='0' and product_type='1' and sub_sub_category_id='$catId'");
		     $total_records=count($productCount);
	         $productData = $this->admin_model->getwithLimitOrderBy('salo_products',array('status' => '0','product_type' => 1,'sub_sub_category_id' => $catId),$data['fetchLimit'],$limit,'id','DESC'); 
		 
		   break;
		}	   
	   
	   $fetchLimit=$data['fetchLimit'];
	   $total_pages = ceil($total_records / $fetchLimit);   
	   $data['total_pages']=$total_pages;
	   $data['current_page']=$page;
		
	    $productArray=array();
	    foreach($productData as $lr)
		{
			$id=$lr->id;
			$title=$lr->title;
			$image=$lr->image;
			$stock_status=$lr->stock_status;
			$description=$lr->description;
			$catId=$lr->category_id;
			$subcatId=$lr->sub_category_id;
			$subsubcatId=$lr->sub_sub_category_id;
			$brandId=$lr->brand_id;
			
			if($image!="")
			{
			 $imageUrl=$this->config->item('base_image_url')."assets/uploads/products/".$image;
			}
			else
			{
			 $imageUrl="";
			}
			
			    $prodId=$lr->id;
			    if($loggedInUserId==0)
    			{
    			  $colName="device_id";
    			  $colVal=$mobileDeviceId;
    			  
    			  $whereArr=array($colName => $colVal,'user_id' => '0' , 'pro_id' => $prodId,'status' => 0);
    			  
    			}
    			else
    			{
    			  $colName="user_id";
    			  $colVal=$loggedInUserId;
    			  
    			  $whereArr=array($colName => $colVal,'pro_id' => $prodId,'status' => 0);
    			}
			
			  $getCartData=$this->admin_model->getData('salo_temp_cart',$whereArr,'AND');
			  if(count($getCartData)!=0)
			  {
				$prod_cart_qty=$getCartData[0]['quantity'];
			  }
			  else
			  {
				$prod_cart_qty="0";
			  }
			
			
			$Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE product_id='$id' and `status`='1' ORDER BY price ASC LIMIT 1");
			$pr=$Productprice[0];
		 
		    $attributeid = $pr['id'];
			$attribute_value = $pr['attribute_value'];
			$unit_id = $pr['unit_id'];
			$price = $pr['price'];
			  
			if($price=="")
			{
			 $price="0.00";
			}
			else
			{
			  $price=$price;
			}
			  
			  $discount = $pr['discount'];
			  if($discount=="")
			  {
				  $discount="0";
				  $sellAmt=$price;
			  }
			  else
			  {
				   $discount=$discount;
				   $disper=$discount / 100;
				   $disamt = $price * $disper;
				   $sellAmt=$price - $disamt;
				   $sellAmt=round($sellAmt);
			  }
			  
			
			  $unit = $this->admin_model->getData('salo_unit',array('id' => $unit_id),null);
			  $unitName=$unit[0]['unit'];
			  
			 $attribute = $attribute_value." ".$unitName;
			 $attributePrice=array();
			 $Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE product_id='$id' and `status`='1' ORDER BY price ASC");
			 foreach($Productprice as $pr_pr)
			 {
			      $attributeid = $pr_pr['id'];
				  $attribute_value = $pr_pr['attribute_value'];
				  $unit_id = $pr_pr['unit_id'];
				  $priceAtt = $pr_pr['price'];
				  
				  if($priceAtt=="")
				  {
					$priceAtt="0.00";
				  }
				  else
				  {
					$priceAtt=$priceAtt;
				  }
				  
				  $discountAtt = $pr_pr['discount'];
				  if($discountAtt=="")
				  {
					  $discountAtt="0";
					  $sellAmtAtt=$priceAtt;
				  }
				  else
				  {
					   $discountAtt=$discountAtt;
					   $disper=$discountAtt / 100;
					   $disamt = $priceAtt * $disper;
					   $sellAmtAtt=$priceAtt - $disamt;
					   $sellAmtAtt=round($sellAmtAtt);
				  }
				  
				  if($loggedInUserId==0)
				   {
						$colName="device_id";
						$colVal=$mobileDeviceId;
						  
						$whereArr=array($colName => $colVal,'user_id' => '0' ,'product_attribute' => $attributeid,'status' => 0);
						  
				   }
				   else
				   {
						$colName="user_id";
						$colVal=$loggedInUserId;
						  
						$whereArr=array($colName => $colVal,'product_attribute' => $attributeid,'status' => 0);
				   }
					
					$getCartDataAtt=$this->admin_model->getData('salo_temp_cart',$whereArr,'AND');
					if(count($getCartDataAtt)!=0)
					{
					  $prod_cart_qty_att=$getCartDataAtt[0]['quantity'];
					}
					else
					{
					  $prod_cart_qty_att="0";
					}
					
				
				  $unit = $this->admin_model->getData('salo_unit',array('id' => $unit_id),null);
				  $unitName=$unit[0]['unit'];
				  

				 $attributeName = $attribute_value." ".$unitName;
				 $arrAtt=array('attribute_id' => $attributeid,'attribute_name' => $attributeName,'listprice' => $priceAtt,'saleprice' => $sellAmtAtt,'discount' => $discountAtt,'attribute_cart_qty' => $prod_cart_qty_att);
				 array_push($attributePrice,$arrAtt);
			 }
			 
			$arr = array('id' => $id,'title' => $title,'image' => $imageUrl,'prod_cart_qty' => $prod_cart_qty,'attribute' => $attribute , 'listprice' => $price, 'saleprice' => $sellAmt, 'discount' => $discount,'stock_status' => $stock_status,'attributeList' => $attributePrice);
			
			array_push($productArray,$arr);
		}
       
       $data['products']=$productArray;
	   $data['msg']="Done";
	   echo json_encode($data);
	}
	
	
	public function discountedProductList($mobileDeviceId,$loggedInUserId,$page=1)
	{
	   $data['status']="true";
	   
	   $data['fetchLimit']=10;
	   if($page==1)
       {
         $limit=0;
       }
       else
       {
          $limit=($page - 1) * $data['fetchLimit'];
       }
	
       $fetchLimit=$data['fetchLimit'];	

	   $productCount=$this->admin_model->getBasic("SELECT DISTINCT product_id FROM salo_product_attribute WHERE `discount` != '' and status='1' AND product_id IN (SELECT `id` FROM salo_products WHERE status='0') group by product_id ORDER by product_id DESC");
	   $total_records=count($productCount);
	   $productData = $this->admin_model->getBasic("SELECT DISTINCT product_id FROM salo_product_attribute WHERE `discount` != '' and status='1' AND product_id IN (SELECT `id` FROM salo_products WHERE status='0') group by product_id ORDER by product_id DESC LIMIT $limit,$fetchLimit");
	   
	   
	   $fetchLimit=$data['fetchLimit'];
	   $total_pages = ceil($total_records / $fetchLimit);   
	   $data['total_pages']=$total_pages;
	   $data['current_page']=$page;
		
	    $productArray=array();
	    foreach($productData as $rt)
		{
		
		    $prodId=$rt['product_id'];
            
			$getProduct=$this->admin_model->getBasic("SELECT * FROM salo_products WHERE id='$prodId'");
			$lr=$getProduct[0];
			
			$id=$lr['id'];
			$title=$lr['title'];
			$image=$lr['image'];
			$stock_status=$lr['stock_status'];
			$description=$lr['description'];
			$catId=$lr['category_id'];
			$subcatId=$lr['sub_category_id'];
			$subsubcatId=$lr['sub_sub_category_id'];
			$brandId=$lr['brand_id'];
			
			if($image!="")
			{
			 $imageUrl=$this->config->item('base_image_url')."assets/uploads/products/".$image;
			}
			else
			{
			 $imageUrl="";
			}
			
			    $prodId=$lr['id'];
			    if($loggedInUserId==0)
    			{
    			  $colName="device_id";
    			  $colVal=$mobileDeviceId;
    			  
    			  $whereArr=array($colName => $colVal,'user_id' => '0' , 'pro_id' => $prodId,'status' => 0);
    			  
    			}
    			else
    			{
    			  $colName="user_id";
    			  $colVal=$loggedInUserId;
    			  
    			  $whereArr=array($colName => $colVal,'pro_id' => $prodId,'status' => 0);
    			}
			
			  $getCartData=$this->admin_model->getData('salo_temp_cart',$whereArr,'AND');
			  if(count($getCartData)!=0)
			  {
				$prod_cart_qty=$getCartData[0]['quantity'];
			  }
			  else
			  {
				$prod_cart_qty="0";
			  }
			
			
			$Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE product_id='$id' and `status`='1' ORDER BY price ASC LIMIT 1");
			$pr=$Productprice[0];
		 
		    $attributeid = $pr['id'];
			$attribute_value = $pr['attribute_value'];
			$unit_id = $pr['unit_id'];
			$price = $pr['price'];
			  
			if($price=="")
			{
			 $price="0.00";
			}
			else
			{
			  $price=$price;
			}
			  
			  $discount = $pr['discount'];
			  if($discount=="")
			  {
				  $discount="0";
				  $sellAmt=$price;
			  }
			  else
			  {
				   $discount=$discount;
				   $disper=$discount / 100;
				   $disamt = $price * $disper;
				   $sellAmt=$price - $disamt;
				   $sellAmt=round($sellAmt);
			  }
			  
			
			  $unit = $this->admin_model->getData('salo_unit',array('id' => $unit_id),null);
			  $unitName=$unit[0]['unit'];
			  
			 $attribute = $attribute_value." ".$unitName;
			 $attributePrice=array();
			 $Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE product_id='$id' and `status`='1' ORDER BY price ASC");
			 foreach($Productprice as $pr_pr)
			 {
			      $attributeid = $pr_pr['id'];
				  $attribute_value = $pr_pr['attribute_value'];
				  $unit_id = $pr_pr['unit_id'];
				  $priceAtt = $pr_pr['price'];
				  
				  if($priceAtt=="")
				  {
					$priceAtt="0.00";
				  }
				  else
				  {
					$priceAtt=$priceAtt;
				  }
				  
				  $discountAtt = $pr_pr['discount'];
				  if($discountAtt=="")
				  {
					  $discountAtt="0";
					  $sellAmtAtt=$priceAtt;
				  }
				  else
				  {
					   $discountAtt=$discountAtt;
					   $disper=$discountAtt / 100;
					   $disamt = $priceAtt * $disper;
					   $sellAmtAtt=$priceAtt - $disamt;
					   $sellAmtAtt=round($sellAmtAtt);
				  }
				  
				  
				   if($loggedInUserId==0)
				   {
						$colName="device_id";
						$colVal=$mobileDeviceId;
						  
						$whereArr=array($colName => $colVal,'user_id' => '0' ,'product_attribute' => $attributeid,'status' => 0);
						  
				   }
				   else
				   {
						$colName="user_id";
						$colVal=$loggedInUserId;
						  
						$whereArr=array($colName => $colVal,'product_attribute' => $attributeid,'status' => 0);
				   }
					
					$getCartDataAtt=$this->admin_model->getData('salo_temp_cart',$whereArr,'AND');
					if(count($getCartDataAtt)!=0)
					{
					  $prod_cart_qty_att=$getCartDataAtt[0]['quantity'];
					}
					else
					{
					  $prod_cart_qty_att="0";
					}
				
				  $unit = $this->admin_model->getData('salo_unit',array('id' => $unit_id),null);
				  $unitName=$unit[0]['unit'];
				  

				 $attributeName = $attribute_value." ".$unitName;
				 $arrAtt=array('attribute_id' => $attributeid,'attribute_name' => $attributeName,'listprice' => $priceAtt,'saleprice' => $sellAmtAtt,'discount' => $discountAtt,'attribute_cart_qty' => $prod_cart_qty_att);
				 array_push($attributePrice,$arrAtt);
			 }
			 
			$arr = array('id' => $id,'title' => $title,'image' => $imageUrl,'prod_cart_qty' => $prod_cart_qty,'attribute' => $attribute , 'listprice' => $price, 'saleprice' => $sellAmt, 'discount' => $discount,'stock_status' => $stock_status,'attributeList' => $attributePrice);
			
			array_push($productArray,$arr);
		}
       
       $data['products']=$productArray;
	   $data['msg']="Done";
	   echo json_encode($data);
	}
	
	
	public function mostPopularProductList($mobileDeviceId,$loggedInUserId,$page=1)
	{
	   $data['status']="true";
	   
	   $data['fetchLimit']=10;
	   if($page==1)
       {
         $limit=0;
       }
       else
       {
          $limit=($page - 1) * $data['fetchLimit'];
       }
	
       $fetchLimit=$data['fetchLimit'];	

	   $productCount=$this->admin_model->getBasic("SELECT pro_id,COUNT(pro_id) as cnt FROM `salo_temp_cart` WHERE payment_status='1' and status='1' AND pro_id IN (SELECT `id` FROM salo_products WHERE status='0') GROUP BY pro_id ORDER BY cnt DESC");
	   $total_records=count($productCount);
	   $productData = $this->admin_model->getBasic("SELECT pro_id,COUNT(pro_id) as cnt FROM `salo_temp_cart` WHERE payment_status='1' and status='1' AND pro_id IN (SELECT `id` FROM salo_products WHERE status='0') GROUP BY pro_id ORDER BY cnt DESC LIMIT $limit,$fetchLimit");
	   
	   
	   $fetchLimit=$data['fetchLimit'];
	   $total_pages = ceil($total_records / $fetchLimit);   
	   $data['total_pages']=$total_pages;
	   $data['current_page']=$page;
		
	    $productArray=array();
	    foreach($productData as $rt)
		{
		
		    $prodId=$rt['pro_id'];
            
			$getProduct=$this->admin_model->getBasic("SELECT * FROM salo_products WHERE id='$prodId'");
			$lr=$getProduct[0];
			
			$id=$lr['id'];
			$title=$lr['title'];
			$image=$lr['image'];
			$stock_status=$lr['stock_status'];
			$description=$lr['description'];
			$catId=$lr['category_id'];
			$subcatId=$lr['sub_category_id'];
			$subsubcatId=$lr['sub_sub_category_id'];
			$brandId=$lr['brand_id'];
			
			if($image!="")
			{
			 $imageUrl=$this->config->item('base_image_url')."assets/uploads/products/".$image;
			}
			else
			{
			 $imageUrl="";
			}
			
			    $prodId=$lr['id'];
			    if($loggedInUserId==0)
    			{
    			  $colName="device_id";
    			  $colVal=$mobileDeviceId;
    			  
    			  $whereArr=array($colName => $colVal,'user_id' => '0' , 'pro_id' => $prodId,'status' => 0);
    			  
    			}
    			else
    			{
    			  $colName="user_id";
    			  $colVal=$loggedInUserId;
    			  
    			  $whereArr=array($colName => $colVal,'pro_id' => $prodId,'status' => 0);
    			}
			
			  $getCartData=$this->admin_model->getData('salo_temp_cart',$whereArr,'AND');
			  if(count($getCartData)!=0)
			  {
				$prod_cart_qty=$getCartData[0]['quantity'];
			  }
			  else
			  {
				$prod_cart_qty="0";
			  }
			
			
			$Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE product_id='$id' and `status`='1' ORDER BY price ASC LIMIT 1");
			$pr=$Productprice[0];
		 
		    $attributeid = $pr['id'];
			$attribute_value = $pr['attribute_value'];
			$unit_id = $pr['unit_id'];
			$price = $pr['price'];
			  
			if($price=="")
			{
			 $price="0.00";
			}
			else
			{
			  $price=$price;
			}
			  
			  $discount = $pr['discount'];
			  if($discount=="")
			  {
				  $discount="0";
				  $sellAmt=$price;
			  }
			  else
			  {
				   $discount=$discount;
				   $disper=$discount / 100;
				   $disamt = $price * $disper;
				   $sellAmt=$price - $disamt;
				   $sellAmt=round($sellAmt);
			  }
			  
			
			  $unit = $this->admin_model->getData('salo_unit',array('id' => $unit_id),null);
			  $unitName=$unit[0]['unit'];
			  
			 $attribute = $attribute_value." ".$unitName;
			 $attributePrice=array();
			 $Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE product_id='$id' and `status`='1' ORDER BY price ASC");
			 foreach($Productprice as $pr_pr)
			 {
			      $attributeid = $pr_pr['id'];
				  $attribute_value = $pr_pr['attribute_value'];
				  $unit_id = $pr_pr['unit_id'];
				  $priceAtt = $pr_pr['price'];
				  
				  if($priceAtt=="")
				  {
					$priceAtt="0.00";
				  }
				  else
				  {
					$priceAtt=$priceAtt;
				  }
				  
				  $discountAtt = $pr_pr['discount'];
				  if($discountAtt=="")
				  {
					  $discountAtt="0";
					  $sellAmtAtt=$priceAtt;
				  }
				  else
				  {
					   $discountAtt=$discountAtt;
					   $disper=$discountAtt / 100;
					   $disamt = $priceAtt * $disper;
					   $sellAmtAtt=$priceAtt - $disamt;
					   $sellAmtAtt=round($sellAmtAtt);
				  }
				  
				  
				   if($loggedInUserId==0)
				   {
						$colName="device_id";
						$colVal=$mobileDeviceId;
						  
						$whereArr=array($colName => $colVal,'user_id' => '0' ,'product_attribute' => $attributeid,'status' => 0);
						  
				   }
				   else
				   {
						$colName="user_id";
						$colVal=$loggedInUserId;
						  
						$whereArr=array($colName => $colVal,'product_attribute' => $attributeid,'status' => 0);
				   }
					
					$getCartDataAtt=$this->admin_model->getData('salo_temp_cart',$whereArr,'AND');
					if(count($getCartDataAtt)!=0)
					{
					  $prod_cart_qty_att=$getCartDataAtt[0]['quantity'];
					}
					else
					{
					  $prod_cart_qty_att="0";
					}
				
				  $unit = $this->admin_model->getData('salo_unit',array('id' => $unit_id),null);
				  $unitName=$unit[0]['unit'];
				  

				 $attributeName = $attribute_value." ".$unitName;
				 $arrAtt=array('attribute_id' => $attributeid,'attribute_name' => $attributeName,'listprice' => $priceAtt,'saleprice' => $sellAmtAtt,'discount' => $discountAtt,'attribute_cart_qty' => $prod_cart_qty_att);
				 array_push($attributePrice,$arrAtt);
			 }
			 
			$arr = array('id' => $id,'title' => $title,'image' => $imageUrl,'prod_cart_qty' => $prod_cart_qty,'attribute' => $attribute , 'listprice' => $price, 'saleprice' => $sellAmt, 'discount' => $discount,'stock_status' => $stock_status,'attributeList' => $attributePrice);
			
			array_push($productArray,$arr);
		}
       
       $data['products']=$productArray;
	   $data['msg']="Done";
	   echo json_encode($data);
	}
	
	
	public function getsingleProd($proid)
	{
        $data['status']="true";
		$Products=$this->admin_model->getData('salo_products',array('status' => 0,'id' => $proid),'AND');
		$productArray=array();
		$lr=$Products[0];
			$id=$lr['id'];
			$title=$lr['title'];
			$stock_status=$lr['stock_status'];
			$image=$lr['image'];
			$description=$lr['description'];
			$catId=$lr['category_id'];
			$subcatId=$lr['sub_category_id'];
			$subsubcatId=$lr['sub_sub_category_id'];
			$brandId=$lr['brand_id'];
			$coupanstatus=$lr['coupon_code_status'];
			

			
			$Category=$this->admin_model->getData('salo_category',array('category_id' => $catId),null);
			$CategoryName=$Category[0]['category_name'];
	
			$SubCategory=$this->admin_model->getData('salo_sub_category',array('sub_category_id' => $subcatId),null);
			$SubCategoryName=$SubCategory[0]['sub_category_name'];
			
			$SubSubCategory=$this->admin_model->getData('salo_sub_sub_category',array('id' => $subsubcatId),null);
			$SubSubCategoryName=$SubSubCategory[0]['name'];
			
			$added_on=$lr['added_on'];
			
			if($image!="")
			{
			  $imageUrl=$this->config->item('base_image_url')."assets/uploads/products/".$image;
			}
			else
			{
				$imageUrl="";
			}
			
			if($coupanstatus == '1'){
			    $active_coupon_id = $lr['active_coupon_id'];
				$cdate=date('Y-m-d');
				$cupon = $this->db->query("SELECT * FROM salo_coupon WHERE `id`='$active_coupon_id' and `expiry_date` >= '$cdate'")->result_array();
				if(count($cupon)!=0)
				{												
					$coupon_code = $cupon[0]['coupon_code'];
					$couponDiscount = $cupon[0]['discount'];
					$expiry_date = $cupon[0]['expiry_date'];
					$status = $cupon[0]['status'];
					if($status == 0)
					{
					   $couponDiscount=$couponDiscount;
					   $couponCode=$coupon_code;
					}
					else
					{
					   $couponDiscount="0";
					   $couponCode="0";
					}
				}
				else
				{
				  $couponDiscount="0";
				  $couponCode="0";
				}
												
			}
			else
			{
				  $couponDiscount="0";
				  $couponCode="0";
			}
											  
			$Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE product_id='$id' and `status`='1' ORDER BY price ASC LIMIT 1");
			$pr=$Productprice[0];
		    $attributeid = $pr['id'];
			$attribute_value = $pr['attribute_value'];
			$unit_id = $pr['unit_id'];
			$price = $pr['price'];
			$discount = $pr['discount'];
			
			 if($price=="")
			  {
			    $price="0.00";
			  }
			  else
			  {
			    $price=$price;
			  }
			  
			  
			  if($discount=="")
			  {
				  $discount="0";
				  $sellAmt=$price;
			  }
			  else
			  {
				   $discount=$discount;
				   $disper=$discount / 100;
				   $disamt = $price * $disper;
				   $sellAmt=$price - $disamt;
				   $sellAmt=round($sellAmt);
			  }
			  
			
			  $unit = $this->admin_model->getData('salo_unit',array('id' => $unit_id),null);
			  $unitName=$unit[0]['unit'];
			  
			$attribute = $attribute_value." ".$unitName;
           
			
			$arr = array('product_id' => $id,'title' => $title,'image' => $imageUrl,'description' => $description,'catId'=>$catId,'catName'=>$CategoryName,'subcatId'=>$subcatId,'SubCategoryName'=>$SubCategoryName,'subsubcatId'=>$subsubcatId,'SubSubCategoryName'=>$SubSubCategoryName,'brandid'=>$brandId,'attribute' => $attribute , 'listprice' => $price, 'saleprice' => $sellAmt,'discount' => $discount,'coupon_discount' => $couponDiscount,'coupon_code' => $couponCode,'stock_status' => $stock_status,'added_on' => $added_on);
			array_push($productArray,$arr);
			
	
		
		$Productprice = $this->admin_model->getData('salo_product_attribute',array('product_id' => $proid,'status' =>'1'),'AND');
		$priceArray = array();
		foreach($Productprice as $pr)
		{
		    $attributeid = $pr['id'];
			$attribute_value = $pr['attribute_value'];
			$unit_id = $pr['unit_id'];
			$price = $pr['price'];
			$discount = $pr['discount'];
			
			 if($price=="")
			  {
			    $price="0.00";
			  }
			  else
			  {
			    $price=$price;
			  }
			  
			  if($discount=="")
			  {
				  $discount="0";
				  $sellAmt=$price;
			  }
			  else
			  {
				   $discount=$discount;
				   $disper=$discount / 100;
				   $disamt = $price * $disper;
				   $sellAmt=$price - $disamt;
				   $sellAmt=round($sellAmt);
			  }
			  
			  if($loggedInUserId==0)
				   {
						$colName="device_id";
						$colVal=$mobileDeviceId;
						  
						$whereArr=array($colName => $colVal,'user_id' => '0' ,'product_attribute' => $attributeid,'status' => 0);
						  
				   }
				   else
				   {
						$colName="user_id";
						$colVal=$loggedInUserId;
						  
						$whereArr=array($colName => $colVal,'product_attribute' => $attributeid,'status' => 0);
				   }
					
					$getCartDataAtt=$this->admin_model->getData('salo_temp_cart',$whereArr,'AND');
					if(count($getCartDataAtt)!=0)
					{
					  $prod_cart_qty_att=$getCartDataAtt[0]['quantity'];
					}
					else
					{
					  $prod_cart_qty_att="0";
					}
					
			$unit = $this->admin_model->getData('salo_unit',array('id' => $unit_id),null);
			$unitName=$unit[0]['unit'];
			 
            $arr = array('attributeid' => $attributeid,'attribute_value' => $attribute_value,'unit_id' => $unit_id,'unitName' => $unitName,'discount'=>$discount,'price' => $price,'discounted_price' => $sellAmt,'attribute_cart_qty' => $prod_cart_qty_att);
			array_push($priceArray,$arr);

		  
		}
		
		$data['productList']=$productArray;
		$data['attributeList']=$priceArray;
		
		if($subcatId==0)
		{
		  $relatedPro=$this->admin_model->getQueryOrderByLimit('salo_products',array('category_id' => array('=',$catId),'status' => array('=',0),'product_type' => array('=',1)),'DESC','id',0,10);
		  $relatedArray=array();
		   foreach($relatedPro as $lrr)
			{
				$id=$lrr['id'];
				$title=$lrr['title'];
				$stock_status=$lrr['stock_status'];
				$image=$lrr['image'];
				$description=$lrr['description'];
				$catId=$lrr['category_id'];
				$subcatId=$lrr['sub_category_id'];
				$subsubcatId=$lrr['sub_sub_category_id'];
				$brandId=$lrr['brand_id'];
				$coupanstatusSub=$lrr['coupon_code_status'];
				
				if($image!="")
				{
				 $imageUrl=$this->config->item('base_image_url')."assets/uploads/products/".$image;
				}
				else
				{
				 $imageUrl="";
				}
				
				if($coupanstatusSub == '1'){
			    $active_coupon_idSub = $lrr['active_coupon_id'];
				$cdate=date('Y-m-d');
				$cuponSub = $this->db->query("SELECT * FROM salo_coupon WHERE `id`='$active_coupon_idSub' and `expiry_date` >= '$cdate'")->result_array();
				if(count($cuponSub)!=0)
				{												
					$coupon_codeSub = $cuponSub[0]['coupon_code'];
					$coupondiscountSub = $cuponSub[0]['discount'];
					$expiry_dateSub = $cuponSub[0]['expiry_date'];
					$statusSub = $cuponSub[0]['status'];
					if($statusSub == 0)
					{
					   $coupondiscountSub=$coupondiscountSub;
					   $couponCodeSub=$coupon_codeSub;
					}
					else
					{
					   $coupondiscountSub="0";
					   $couponCodeSub="0";
					}
				}
				else
				{
				  $coupondiscountSub="0";
				  $couponCodeSub="0";
				}
												
			}
			else
		    {
				  $coupondiscountSub="0";
				  $couponCodeSub="0";
			}
			
				$Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE product_id='$id' and `status`='1' ORDER BY price ASC LIMIT 1");
				$prr=$Productprice[0];
			 
				  $attributeid = $prr['id'];
				  $attribute_value = $prr['attribute_value'];
				  $unit_id = $prr['unit_id'];
				  $price = $prr['price'];
				  $discountSub = $prr['discount'];
				  
				   if($price=="")
				  {
					$price="0.00";
				  }
				  else
				  {
					$price=$price;
				  }
			  
			  
				  if($discountSub=="")
				  {
					  $discountSub="0";
					  $sellAmt=$price;
				  }
				  else
				  {
					   $discountSub=$discountSub;
					   $disper=$discountSub / 100;
					   $disamt = $price * $disper;
					   $sellAmt=$price - $disamt;
					   $sellAmt=round($sellAmt);
				  }
				  
				
				  $unit = $this->admin_model->getData('salo_unit',array('id' => $unit_id),null);
				  $unitName=$unit[0]['unit'];
				  
				 $attribute = $attribute_value." ".$unitName;
				
				$attributePrice=array();
			    $Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE product_id='$id' and `status`='1' ORDER BY price ASC");
				 foreach($Productprice as $pr_pr)
				 {
					  $attributeid = $pr_pr['id'];
					  $attribute_value = $pr_pr['attribute_value'];
					  $unit_id = $pr_pr['unit_id'];
					  $priceAtt = $pr_pr['price'];
					  $discountSubAtt = $pr_pr['discount'];
					  
					  if($priceAtt=="")
					  {
						$priceAtt="0.00";
					  }
					  else
					  {
						$priceAtt=$priceAtt;
					  }
					  
					  $discountAtt = $discountSubAtt;
					  if($discountAtt=="")
					  {
						  $discountAtt="0";
						  $sellAmtAtt=$priceAtt;
					  }
					  else
					  {
						   $discountAtt=$discountAtt;
						   $disper=$discountAtt / 100;
						   $disamt = $priceAtt * $disper;
						   $sellAmtAtt=$priceAtt - $disamt;
						   $sellAmtAtt=round($sellAmtAtt);
					  }
					  
					  if($loggedInUserId==0)
					   {
							$colName="device_id";
							$colVal=$mobileDeviceId;
							  
							$whereArr=array($colName => $colVal,'user_id' => '0' ,'product_attribute' => $attributeid,'status' => 0);
							  
					   }
					   else
					   {
							$colName="user_id";
							$colVal=$loggedInUserId;
							  
							$whereArr=array($colName => $colVal,'product_attribute' => $attributeid,'status' => 0);
					   }
						
						$getCartDataAtt=$this->admin_model->getData('salo_temp_cart',$whereArr,'AND');
						if(count($getCartDataAtt)!=0)
						{
						  $prod_cart_qty_att=$getCartDataAtt[0]['quantity'];
						}
						else
						{
						  $prod_cart_qty_att="0";
						}
					
					  $unit = $this->admin_model->getData('salo_unit',array('id' => $unit_id),null);
					  $unitName=$unit[0]['unit'];
					  

					 $attributeName = $attribute_value." ".$unitName;
					 $arrAtt=array('attribute_id' => $attributeid,'attribute_name' => $attributeName,'listprice' => $priceAtt,'saleprice' => $sellAmtAtt,'discount' => $discountAtt,'attribute_cart_qty' => $prod_cart_qty_att);
					 array_push($attributePrice,$arrAtt);
				 }
			 
				 $arr = array('id' => $id,'title' => $title,'image' => $imageUrl,'attribute' => $attribute , 'listprice' => $price, 'saleprice' => $sellAmt, 'discount' => $discountSub,'coupon_discount' => $coupondiscountSub,'coupon_code' => $couponCodeSub,'stock_status' => $stock_status,'attributeList' => $attributePrice);
				
				array_push($relatedArray,$arr);

			}
		}
		else if($subsubcatId==0)
		{
		   $relatedPro=$this->admin_model->getQueryOrderByLimit('salo_products',array('sub_category_id' => array('=',$subcatId),'status' => array('=',0),'product_type' => array('=',1)),'DESC','id',0,10);
		   $relatedArray=array();
		   foreach($relatedPro as $lrr)
			{
				$id=$lrr['id'];
				$title=$lrr['title'];
				$stock_status=$lrr['stock_status'];
				$image=$lrr['image'];
				$description=$lrr['description'];
				$catId=$lrr['category_id'];
				$subcatId=$lrr['sub_category_id'];
				$subsubcatId=$lrr['sub_sub_category_id'];
				$brandId=$lrr['brand_id'];
				$coupanstatusSub=$lrr['coupon_code_status'];
				
				if($image!="")
				{
				 $imageUrl=$this->config->item('base_image_url')."assets/uploads/products/".$image;
				}
				else
				{
				 $imageUrl="";
				}
				
				if($coupanstatusSub == '1'){
					$active_coupon_idSub = $lrr['active_coupon_id'];
					$cdate=date('Y-m-d');
					$cuponSub = $this->db->query("SELECT * FROM salo_coupon WHERE `id`='$active_coupon_idSub' and `expiry_date` >= '$cdate'")->result_array();
					if(count($cuponSub)!=0)
					{												
						$coupon_codeSub = $cuponSub[0]['coupon_code'];
						$coupondiscountSub = $cuponSub[0]['discount'];
						$expiry_dateSub = $cuponSub[0]['expiry_date'];
						$statusSub = $cuponSub[0]['status'];
						if($statusSub == 0)
						{
						   $coupondiscountSub=$coupondiscountSub;
						   $couponCodeSub=$coupon_codeSub;
						}
						else
						{
						   $coupondiscountSub="0";
						   $couponCodeSub="0";
						}
					}
					else
					{
					  $coupondiscountSub="0";
					  $couponCodeSub="0";
					}
												
				}
				else
				{
					  $coupondiscountSub="0";
					  $couponCodeSub="0";
				}
			
				$Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE product_id='$id' and `status`='1' ORDER BY price ASC LIMIT 1");
				$prr=$Productprice[0];
			 
				  $attributeid = $prr['id'];
				  $attribute_value = $prr['attribute_value'];
				  $unit_id = $prr['unit_id'];
				  $price = $prr['price'];
				  $discountSub = $prr['discount'];
				  
				   if($price=="")
				  {
					$price="0.00";
				  }
				  else
				  {
					$price=$price;
				  }
			  
				  if($discountSub=="")
				  {
					  $discountSub="0";
					  $sellAmt=$price;
				  }
				  else
				  {
					   $discountSub=$discountSub;
					   $disper=$discountSub / 100;
					   $disamt = $price * $disper;
					   $sellAmt=$price - $disamt;
					   $sellAmt=round($sellAmt);
				  }
				  
				
				  $unit = $this->admin_model->getData('salo_unit',array('id' => $unit_id),null);
				  $unitName=$unit[0]['unit'];
				  
				 $attribute = $attribute_value." ".$unitName;
				
				$attributePrice=array();
			  $Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE product_id='$id' and `status`='1' ORDER BY price ASC");
			 foreach($Productprice as $pr_pr)
			 {
			      $attributeid = $pr_pr['id'];
				  $attribute_value = $pr_pr['attribute_value'];
				  $unit_id = $pr_pr['unit_id'];
				  $priceAtt = $pr_pr['price'];
				  $discountSubAtt = $pr_pr['discount'];
				  
				  if($priceAtt=="")
				  {
					$priceAtt="0.00";
				  }
				  else
				  {
					$priceAtt=$priceAtt;
				  }
				  
				  $discountAtt = $discountSubAtt;
				  if($discountAtt=="0")
				  {
					  $discountAtt="0";
					  $sellAmtAtt=$priceAtt;
				  }
				  else
				  {
					   $discountAtt=$discountAtt;
					   $disper=$discountAtt / 100;
					   $disamt = $priceAtt * $disper;
					   $sellAmtAtt=$priceAtt - $disamt;
					   $sellAmtAtt=round($sellAmtAtt);
				  }
				  
				   if($loggedInUserId==0)
					   {
							$colName="device_id";
							$colVal=$mobileDeviceId;
							  
							$whereArr=array($colName => $colVal,'user_id' => '0' ,'product_attribute' => $attributeid,'status' => 0);
							  
					   }
					   else
					   {
							$colName="user_id";
							$colVal=$loggedInUserId;
							  
							$whereArr=array($colName => $colVal,'product_attribute' => $attributeid,'status' => 0);
					   }
						
						$getCartDataAtt=$this->admin_model->getData('salo_temp_cart',$whereArr,'AND');
						if(count($getCartDataAtt)!=0)
						{
						  $prod_cart_qty_att=$getCartDataAtt[0]['quantity'];
						}
						else
						{
						  $prod_cart_qty_att="0";
						}
						
				  $unit = $this->admin_model->getData('salo_unit',array('id' => $unit_id),null);
				  $unitName=$unit[0]['unit'];
				  

				 $attributeName = $attribute_value." ".$unitName;
				 $arrAtt=array('attribute_id' => $attributeid,'attribute_name' => $attributeName,'listprice' => $priceAtt,'saleprice' => $sellAmtAtt,'discount' => $discountAtt,'attribute_cart_qty' => $prod_cart_qty_att);
				 array_push($attributePrice,$arrAtt);
			 }
			 
				 $arr = array('id' => $id,'title' => $title,'image' => $imageUrl,'attribute' => $attribute , 'listprice' => $price, 'saleprice' => $sellAmt, 'discount' => $discountSub,'coupon_discount' => $coupondiscountSub,'coupon_code' => $couponCodeSub,'stock_status' => $stock_status,'attributeList' => $attributePrice);
				
				array_push($relatedArray,$arr);

			}
		}
		else
		{
			$relatedPro=$this->admin_model->getQueryOrderByLimit('salo_products',array('sub_sub_category_id' => array('=',$subsubcatId),'status' => array('=',0),'product_type' => array('=',1)),'DESC','id',0,10);
		    $relatedArray=array();
		    foreach($relatedPro as $lrr)
			{
				$id=$lrr['id'];
				$title=$lrr['title'];
				$stock_status=$lrr['stock_status'];
				$image=$lrr['image'];
				$description=$lrr['description'];
				$catId=$lrr['category_id'];
				$subcatId=$lrr['sub_category_id'];
				$subsubcatId=$lrr['sub_sub_category_id'];
				$brandId=$lrr['brand_id'];
				$coupanstatusSub=$lrr['coupon_code_status'];
				
				if($image!="")
				{
				 $imageUrl=$this->config->item('base_image_url')."assets/uploads/products/".$image;
				}
				else
				{
				 $imageUrl="";
				}
				
				if($coupanstatusSub == '1'){
					$active_coupon_idSub = $lrr['active_coupon_id'];
					$cdate=date('Y-m-d');
					$cuponSub = $this->db->query("SELECT * FROM salo_coupon WHERE `id`='$active_coupon_idSub' and `expiry_date` >= '$cdate'")->result_array();
					if(count($cuponSub)!=0)
					{												
						$coupon_codeSub = $cuponSub[0]['coupon_code'];
						$coupondiscountSub = $cuponSub[0]['discount'];
						$expiry_dateSub = $cuponSub[0]['expiry_date'];
						$statusSub = $cuponSub[0]['status'];
						if($statusSub == 0)
						{
						   $coupondiscountSub=$coupondiscountSub;
						   $couponCodeSub=$coupon_codeSub;
						}
						else
						{
						   $coupondiscountSub="0";
						   $couponCodeSub="0";
						}
					}
					else
					{
					  $coupondiscountSub="0";
					  $couponCodeSub="0";
					}
												
				}
				else
				{
					  $coupondiscountSub="0";
					  $couponCodeSub="0";
				}
			
				$Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE product_id='$id' and `status`='1' ORDER BY price ASC LIMIT 1");
				$prr=$Productprice[0];
			 
				  $attributeid = $prr['id'];
				  $attribute_value = $prr['attribute_value'];
				  $unit_id = $prr['unit_id'];
				  $price = $prr['price'];
				  $discountSub = $prr['discount'];
				  
				   if($price=="")
				  {
					$price="0.00";
				  }
				  else
				  {
					$price=$price;
				  }
			  
			  
				  if($discountSub=="")
				  {
					  $discountSub="0";
					  $sellAmt=$price;
				  }
				  else
				  {
					   $discountSub=$discountSub;
					   $disper=$discountSub / 100;
					   $disamt = $price * $disper;
					   $sellAmt=$price - $disamt;
					   $sellAmt=round($sellAmt);
				  }
				  
				
				  $unit = $this->admin_model->getData('salo_unit',array('id' => $unit_id),null);
				  $unitName=$unit[0]['unit'];
				  
				 $attribute = $attribute_value." ".$unitName;
				
				$attributePrice=array();
			  $Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE product_id='$id' and `status`='1' ORDER BY price ASC");
			 foreach($Productprice as $pr_pr)
			 {
			      $attributeid = $pr_pr['id'];
				  $attribute_value = $pr_pr['attribute_value'];
				  $unit_id = $pr_pr['unit_id'];
				  $priceAtt = $pr_pr['price'];
				  $discountSubAtt = $pr_pr['discount'];
				  
				  if($priceAtt=="")
				  {
					$priceAtt="0.00";
				  }
				  else
				  {
					$priceAtt=$priceAtt;
				  }
				  
				  $discountAtt = $discountSubAtt;
				  if($discountAtt=="0")
				  {
					  $discountAtt="0";
					  $sellAmtAtt=$priceAtt;
				  }
				  else
				  {
					   $discountAtt=$discountAtt;
					   $disper=$discountAtt / 100;
					   $disamt = $priceAtt * $disper;
					   $sellAmtAtt=$priceAtt - $disamt;
					   $sellAmtAtt=round($sellAmtAtt);
				  }
				  
				  if($loggedInUserId==0)
					   {
							$colName="device_id";
							$colVal=$mobileDeviceId;
							  
							$whereArr=array($colName => $colVal,'user_id' => '0' ,'product_attribute' => $attributeid,'status' => 0);
							  
					   }
					   else
					   {
							$colName="user_id";
							$colVal=$loggedInUserId;
							  
							$whereArr=array($colName => $colVal,'product_attribute' => $attributeid,'status' => 0);
					   }
						
						$getCartDataAtt=$this->admin_model->getData('salo_temp_cart',$whereArr,'AND');
						if(count($getCartDataAtt)!=0)
						{
						  $prod_cart_qty_att=$getCartDataAtt[0]['quantity'];
						}
						else
						{
						  $prod_cart_qty_att="0";
						}
						
				  $unit = $this->admin_model->getData('salo_unit',array('id' => $unit_id),null);
				  $unitName=$unit[0]['unit'];
				  

				 $attributeName = $attribute_value." ".$unitName;
				 $arrAtt=array('attribute_id' => $attributeid,'attribute_name' => $attributeName,'listprice' => $priceAtt,'saleprice' => $sellAmtAtt,'discount' => $discountAtt,'attribute_cart_qty' => $prod_cart_qty_att);
				 array_push($attributePrice,$arrAtt);
			 }
			 
				 $arr = array('id' => $id,'title' => $title,'image' => $imageUrl,'attribute' => $attribute , 'listprice' => $price, 'saleprice' => $sellAmt, 'discount' => $discountSub,'coupon_discount' => $coupondiscountSub,'coupon_code' => $couponCodeSub,'stock_status' => $stock_status,'attributeList' => $attributePrice);
				
				array_push($relatedArray,$arr);

			}
			
		}
		
        $data['related_product']=$relatedArray;
        $data['msg']="Done";
		echo json_encode($data);
	}
	
	
	 public function categoryItem($catType,$catId)
	{
		    $data['status']="True";
		    
			switch($catType)
			{
				case "cat_lvl1":
				
				    $Category=$this->admin_model->getData('salo_category',array('category_id' => $catId),null);
					$lr=$Category[0];
				
					$category_id=$lr['category_id'];
					$category_name=$lr['category_name'];
					$image=$lr['image'];
					$description=$lr['description'];
					
					if($image!="")
					{
					  $imageUrl=$this->config->item('base_image_url')."assets/uploads/category/".$image;
					}
					else
					{
						$imageUrl="";
					}
					
					$secondCat = $this->admin_model->getData('salo_sub_category',array('status' => 0,'category_id' => $category_id),'AND');
					if(count($secondCat)!=0)
					{
					  $islast="false";
					}
					else
					{
					  $islast="true";
					}
					
					
			
			      
				  $catpro = $this->db->query("SELECT * FROM salo_products WHERE category_id ='$category_id'")->result_array();	
				   
				  $Products = $this->admin_model->getQueryOrderBy('salo_products',array('status' => array('=','0'),'product_type' => array('=','1'), 'category_id' => array('=',$category_id)),'DESC','id');
				  
				break;
				
				case "cat_lvl2":
				    
					$Category=$this->admin_model->getData('salo_sub_category',array('sub_category_id' => $catId),null);
					$lr=$Category[0];
				
					$category_id=$lr['sub_category_id'];
					$category_name=$lr['sub_category_name'];
					$image=$lr['image'];
					$description=$lr['description'];
					
					if($image!="")
					{
					  $imageUrl=$this->config->item('base_image_url')."assets/uploads/category/".$image;
					}
					else
					{
						$imageUrl="";
					}
					
					$secondCat = $this->admin_model->getData('salo_sub_sub_category',array('status' => 0,'sub_category_id' => $category_id),'AND');
					if(count($secondCat)!=0)
					{
					  $islast="false";
					}
					else
					{
					  $islast="true";
					}
					
					$catpro = $this->db->query("SELECT * FROM salo_products WHERE sub_category_id ='$category_id'")->result_array();	
					
					$Products = $this->admin_model->getQueryOrderBy('salo_products',array('status' => array('=','0'),'product_type' => array('=','1'), 'sub_category_id' => array('=',$category_id)),'DESC','id'); 
		
				break;
				
				case "cat_lvl3":
				
				    $Category=$this->admin_model->getData('salo_sub_sub_category',array('id' => $catId),null);
					$lr=$Category[0];
				
					$category_id=$lr['id'];
					$category_name=$lr['name'];
					$image=$lr['image'];
					$description=$lr['description'];
					
					if($image!="")
					{
					  $imageUrl=$this->config->item('base_image_url')."assets/uploads/category/".$image;
					}
					else
					{
						$imageUrl="";
					}
					
					$islast="true";
					
					$catpro = $this->db->query("SELECT * FROM salo_products WHERE sub_sub_category_id ='$category_id'")->result_array();
					
					$Products = $this->admin_model->getQueryOrderBy('salo_products',array('status' => array('=','0'),'product_type' => array('=','1'), 'sub_sub_category_id' => array('=',$category_id)),'DESC','id'); 
		
				break;
			}
			
			
			
			$arr=array('id' => $category_id,'name' => $category_name,'image' => $imageUrl,'description' => $description,'islast' => $islast);
		
		if(count($catpro) > 0){
			
			$proarray = array();
	        foreach($catpro as $pprow){
		       $proarray[] = $pprow['id'];
		    }
			$prodmp = implode(',',$proarray);
			$packsize =  $this->db->query("SELECT * FROM salo_product_attribute WHERE  status ='1' and product_id IN ($prodmp) group by attribute_value , unit_id ")->result_array();
			if(count($packsize)!=0)
			{
				$filterArr=array();
				 foreach($packsize as $pRow)
					{			 
						$pcid = $pRow['id'];
						$atv = $pRow['attribute_value'];
						$unitid = $pRow['unit_id'];
								 
								 
						$unitname = $this->db->query("SELECT * FROM salo_unit WHERE id ='$unitid'")->result_array();
						$prunitname = $unitname[0]['unit'];
                        $val=$atv.' '.$prunitname;
						
                        $farr=array('id' => $pcid,'name' => $val);	
                        array_push($filterArr,$farr);						
					}
			}
		}
		
		
		$filterArray=array('id' => 1,'name' => 'Package Size','data' => $filterArr);
		
		$data['feature']=$arr;
		$data['filter']=$filterArray;
		
		 
							 
	    $productArray=array();
	    foreach($Products as $lr)
		{
			$id=$lr['id'];
			$title=$lr['title'];
			$image=$lr['image'];
			$stock_status=$lr['stock_status'];
			$description=$lr['description'];
			$catId=$lr['category_id'];
			$subcatId=$lr['sub_category_id'];
			$subsubcatId=$lr['sub_sub_category_id'];
			$brandId=$lr['brand_id'];
			
			if($image!="")
			{
			 $imageUrl=$this->config->item('base_image_url')."assets/uploads/products/".$image;
			}
			else
			{
			 $imageUrl="";
			}
			
		    $Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE product_id='$id' and `status`='1' ORDER BY price ASC LIMIT 1");
			$pr=$Productprice[0];
		 
		      $attributeid = $pr['id'];
			  $attribute_value = $pr['attribute_value'];
			  $unit_id = $pr['unit_id'];
			  $price = $pr['price'];
			  
			   if($price=="")
			  {
			    $price="0.00";
			  }
			  else
			  {
			    $price=$price;
			  }
			  
			  $discount = $pr['discount'];
			  if($discount=="")
			  {
				  $discount="0";
				  $sellAmt=$price;
			  }
			  else
			  {
				   $discount=$discount;
				   $disper=$discount / 100;
				   $disamt = $price * $disper;
				   $sellAmt=$price - $disamt;
				   $sellAmt=round($sellAmt);
			  }
			  
			
			  $unit = $this->admin_model->getData('salo_unit',array('id' => $unit_id),null);
			  $unitName=$unit[0]['unit'];
			  
			$attribute = $attribute_value." ".$unitName;
            
			$attributePrice=array();
			  $Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE product_id='$id' and `status`='1' ORDER BY price ASC");
			 foreach($Productprice as $pr_pr)
			 {
			      $attributeid = $pr_pr['id'];
				  $attribute_value = $pr_pr['attribute_value'];
				  $unit_id = $pr_pr['unit_id'];
				  $priceAtt = $pr_pr['price'];
				  
				  if($priceAtt=="")
				  {
					$priceAtt="0.00";
				  }
				  else
				  {
					$priceAtt=$priceAtt;
				  }
				  
				  $discountAtt = $pr_pr['discount'];
				  if($discountAtt=="")
				  {
					  $discountAtt="0";
					  $sellAmtAtt=$priceAtt;
				  }
				  else
				  {
					   $discountAtt=$discountAtt;
					   $disper=$discountAtt / 100;
					   $disamt = $priceAtt * $disper;
					   $sellAmtAtt=$priceAtt - $disamt;
					   $sellAmtAtt=round($sellAmtAtt);
				  }
				  
				
				  $unit = $this->admin_model->getData('salo_unit',array('id' => $unit_id),null);
				  $unitName=$unit[0]['unit'];
				  

				 $attributeName = $attribute_value." ".$unitName;
				 $arrAtt=array('attribute_id' => $attributeid,'attribute_name' => $attributeName,'listprice' => $priceAtt,'saleprice' => $sellAmtAtt,'discount' => $discountAtt);
				 array_push($attributePrice,$arrAtt);
			 }
			 
			$arr = array('id' => $id,'title' => $title,'image' => $imageUrl,'attribute' => $attribute , 'listprice' => $price, 'saleprice' => $sellAmt, 'discount' => $discount,'stock_status' => $stock_status,'attributeList' => $attributePrice);
			
			array_push($productArray,$arr);

		}
		
		$data['products']=$productArray;
		$data['msg']="Done";
		echo json_encode($data);
	}
	
	
	public function categoryList($catType,$catId)
	{
		    $data['status']="True";
		    
			switch($catType)
			{
				case "cat_lvl1":
				
				    $Category=$this->admin_model->getData('salo_category',array('category_id' => $catId),null);
					$lr=$Category[0];
				
					$category_id=$lr['category_id'];
					$category_name=$lr['category_name'];
					$image=$lr['image'];
					$description=$lr['description'];
					
					if($image!="")
					{
					  $imageUrl=$this->config->item('base_image_url')."assets/uploads/category/".$image;
					}
					else
					{
						$imageUrl="";
					}
					
					
					$secondCat = $this->admin_model->getData('salo_sub_category',array('status' => 0,'category_id' => $category_id),'AND');
					if(count($secondCat)!=0)
					{
						$secondcatArray=array();
						$c=1;
						$count=count($secondCat);
						foreach($secondCat as $rt)
						{
						  $sid=$rt['sub_category_id'];
						  $sub_category_name=$rt['sub_category_name'];
						  $image=$rt['image'];
						  if($image!="")
							{
							  $subimageUrl=$this->config->item('base_image_url')."/assets/uploads/category/".$rt['image'];
							}
							else
							{
							  $subimageUrl=$this->config->item('base_image_url')."/assets/lupras/assets/img/extra/breadcrumb-1.jpg";
							}
							
							$proCount = $this->admin_model->getData('salo_products',array('status' => 0,'sub_category_id' => $sid),'AND');
					
								if(count($proCount)!=0)
								{
									$islast="true";
								}
								else
								{
									$islast="false";
								}
							
							 $thirdCat = $this->admin_model->getData('salo_sub_sub_category',array('status' => 0,'sub_category_id' => $sid),'AND');
							 if(count($thirdCat)!=0)
							 {
								$thirdcatArray=array();
								$ct=1;
								$thirdcount=count($thirdCat);
								foreach($thirdCat as $rtt)
								{
									  $tid=$rtt['id'];
									  $subsub_category_name=$rtt['name'];
									  $image=$rtt['image'];
									  if($image!="")
										{
										  $subsubimageUrl=$this->config->item('base_image_url')."/assets/uploads/category/".$rtt['image'];
										}
										else
										{
										  $subsubimageUrl=$this->config->item('base_image_url')."/assets/lupras/assets/img/extra/breadcrumb-1.jpg";
										}
										
										$proCount = $this->admin_model->getData('salo_products',array('status' => 0,'sub_sub_category_id' => $tid),'AND');
					
										if(count($proCount)!=0)
										{
											$islastsub="true";
										}
										else
										{
											$islastsub="false";
										}
										
										$arrsubsub=array('id' => $tid,'name' => $subsub_category_name,'img' => $subsubimageUrl,'islast' => $islastsub);
										array_push($thirdcatArray,$arrsubsub);
										$ct++;
								}
							 }
							 else
							 {
									$thirdcatArray="";
							 }
							
							$arrsub=array('id' => $sid,'name' => $sub_category_name,'img' => $subimageUrl,'islast' => $islast,'sub_sub_cat' => $thirdcatArray);
							array_push($secondcatArray,$arrsub);
							$c++;
						}
					}
					else
					{
						$secondcatArray="";
					}
			
			     
				break;
				
				case "cat_lvl2":
				    
					$Category=$this->admin_model->getData('salo_sub_category',array('sub_category_id' => $catId),null);
					$lr=$Category[0];
				
					$category_id=$lr['sub_category_id'];
					$category_name=$lr['sub_category_name'];
					$image=$lr['image'];
					$description=$lr['description'];
					
					if($image!="")
					{
					  $imageUrl=$this->config->item('base_image_url')."assets/uploads/category/".$image;
					}
					else
					{
						$imageUrl="";
					}
					
					
					$secondCat = $this->admin_model->getData('salo_sub_sub_category',array('status' => 0,'sub_category_id' => $category_id),'AND');
					if(count($secondCat)!=0)
					{
						$secondcatArray=array();
						$c=1;
						$count=count($secondCat);
						foreach($secondCat as $rt)
						{
						  $sid=$rt['id'];
						  $sub_category_name=$rt['name'];
						  $image=$rt['image'];
						  if($image!="")
							{
							  $subimageUrl=$this->config->item('base_image_url')."/assets/uploads/category/".$rt['image'];
							}
							else
							{
							  $subimageUrl="";
							}
							
							$proCount = $this->admin_model->getData('salo_products',array('status' => 0,'sub_category_id' => $sid),'AND');
					
										if(count($proCount)!=0)
										{
											$islast="true";
										}
										else
										{
											$islast="false";
										}
							
							
							$arrsub=array('id' => $sid,'name' => $sub_category_name,'img' => $subimageUrl,'islast' => $islast);
							array_push($secondcatArray,$arrsub);
							$c++;
						}
					}
					else
					{
						$secondcatArray="";
					}
					
		
				break;
				
				case "cat_lvl3":
				
				    $Category=$this->admin_model->getData('salo_sub_sub_category',array('id' => $catId),null);
					$lr=$Category[0];
				
					$category_id=$lr['id'];
					$category_name=$lr['name'];
					$image=$lr['image'];
					$description=$lr['description'];
					
					if($image!="")
					{
					  $imageUrl=$this->config->item('base_image_url')."assets/uploads/category/".$image;
					}
					else
					{
						$imageUrl="";
					}
					
					$secondcatArray="";
		
				break;
			}
			
			
			
			$arr=array('id' => $category_id,'name' => $category_name,'image' => $imageUrl,'description' => $description,'sub_cat' => $secondcatArray);
		
		
		
		$data['feature']=$arr;

		$data['msg']="Done";
		echo json_encode($data);
	}
	
	
	public function registerUser()
	{
	    $postData=$_POST;
	    $device_id=$postData['device_id'];
		$name=$postData['name'];
	    $email=$postData['email'];
	    $contact=$postData['mobile'];
	    $password=$postData['password'];
	    $referred_by=$postData['referred_by'];
		
	    if(($name!="") && ($email!="") && ($contact!="") && ($password!=""))
	    {
			  $dateTime=date('Y-m-d H:i:s');
			  $getReferredBy=$this->admin_model->getData('salo_users',array('referral_code' => $referred_by),null);
			  if((count($getReferredBy)==0) && ($referred_by!="") && (!empty($referred_by)))
			  {
			    $data['status'] = "False";
				$data['type'] = '400';
				$data['error'] = 'Invalid referral code';
				$data['msg']    = "Done";
			  }
			  else
			  {
				  $getUser=$this->admin_model->getData('salo_users',array('email' => $email),null);
				  if(count($getUser)==0)
				  {
					$size=6;
					$referralcode =strtoupper(substr(md5(time().rand(10000,99999)), 0,$size)); 
					
					$referral_code="GGBU".$referralcode;

					$up=$this->admin_model->insertData('salo_users',array('referral_code' => $referral_code,'user_type' => 1,'name' => $name,'email' => $email,'contact' => $contact,'password' => $password,'added_on' => $dateTime,'status' => 1,'referred_by' => $referred_by));
					
					$getUser=$this->admin_model->getData('salo_users',array('id' => $up),null);
					$first_login_status=$getUser[0]['first_login_status'];
					
					
					if((count($getReferredBy)!=0) && ($referred_by!="") && (!empty($referred_by)))
					{
						$getAdmin=$this->admin_model->getData('salo_admin_login',array('role' => 0),null);
						$referralPoint=$getAdmin[0]['referral_point'];
					
						$getReferredBy=$this->admin_model->getData('salo_users',array('referral_code' => $referred_by),null);
						$userId=$getReferredBy[0]['id'];
						$walletAmount=$getReferredBy[0]['wallet_amount'];
						$finalwalletAmount=$walletAmount + $referralPoint;
						  
						$updt=$this->admin_model->updateData('salo_users',array('id' => $userId),array('wallet_amount' => $finalwalletAmount));
					}
					
				   $admindt=$this->admin_model->getData('salo_admin_login',array('role' => 0),null);
				   $referral_point=$admindt[0]['referral_point'];
				   
					
					$chk=$this->admin_model->updateData('salo_temp_cart',array('user_id' => $up),array('status' => 0,'device_id' => $device_id,'user_id' => 0));
					$upDt=$this->admin_model->updateData('salo_users',array('id' => $up),array('first_login_status' => 1));

					$data['status'] = "True";
					$data['data']=array('id' => $up,'referral_code' => $referral_code,'referral_point' => $referral_point,'user_type' => '1','name' => $name,'email' => $email,'password' => $password,'contact' => $contact,'first_login_status' => $first_login_status);
					$data['type'] = '200';
					$data['success'] = 'true';
					$data['msg']    = "Done";
					
				  }else{ 
				  
					$data['status'] = "False";
					$data['type'] = '400';
					$data['error'] = 'Email already registered';
					$data['msg']    = "Done";
					
				  }
			  }
			    
		}
		else
		{
			$data['status'] = "False";
			$data['type']   = '400';
	        $data['error']  = 'All Fields Are Required';
			$data['msg']    = "Done";
		}

		echo json_encode($data);
	}
	
	public function login()
	{
	    $device_id=$_REQUEST['device_id'];
	    $email=$_REQUEST['email'];
	    $pwd=$_REQUEST['password'];
		
		if(($email!="") && ($pwd!=""))
	    {
			$dateTime=date('Y-m-d H:i:s');
            $getUser=$this->admin_model->getData('salo_users',array('email' => $email,'password' => $pwd,'status' => 1),'AND');
			 if(count($getUser)!=0)
			  {
				   $uid=$getUser[0]['id'];
				   $user_type=$getUser[0]['user_type'];
				   $referral_code=$getUser[0]['referral_code'];
				   $uname=$getUser[0]['name'];
				   $uemail=$getUser[0]['email'];
				   $ucontact=$getUser[0]['contact'];
				   $password=$getUser[0]['password'];
				   $first_login_status=$getUser[0]['first_login_status'];
				  
				   $admindt=$this->admin_model->getData('salo_admin_login',array('role' => 0),null);
				   $referral_point=$admindt[0]['referral_point'];
				   
				   $arr=array(array('id' => $uid,'user_type' => $user_type,'referral_code' => $referral_code,'referral_point' => $referral_point,'name' => $uname,'email' => $uemail,'password' => $password,'contact' => $ucontact,'first_login_status' => $first_login_status));
                  
				   $up=$this->admin_model->updateData('salo_temp_cart',array('user_id' => $uid),array('status' => 0,'device_id' => $device_id,'user_id' => 0));
				   
				   $upDt=$this->admin_model->updateData('salo_users',array('id' => $uid),array('first_login_status' => 1));
				  
				  $data['status'] = "True";
				  $data['data']=$arr;
				  $data['type'] = '200';
				  $data['success'] = 'Login Done';
				  $data['msg']    = "Done";
				
			  }else{ 
			  
			    $data['status'] = "False";
				$data['type'] = '400';
				$data['error'] = 'Invalid Credentials';
				$data['msg']    = "Done";
				
			  }  
		}
		else
		{
			$data['status'] = "False";
			$data['type']   = '400';
	        $data['error']  = 'All Fields Are Required';
			$data['msg']    = "Done";
		}
		
		echo json_encode($data);
	}
	
	public function getInfo($uid)
	{
		$data['status']="True";
		$getUser=$this->admin_model->getData('salo_users',array('id' => $uid),null);
		$uid=$getUser[0]['id'];
		$uname=$getUser[0]['name'];
		$uemail=$getUser[0]['email'];
		$ucontact=$getUser[0]['contact'];
		$password=$getUser[0]['password'];
		
		$data['data']=array('id' => $uid,'name' => $uname,'email' => $uemail,'password' => $password,'contact' => $ucontact);
		
		$data['msg']    = "Done";
		
		echo json_encode($data);
	}
	
	public function updateProfile()
	{
		 $uid=$_REQUEST['userid'];
		 $name=$_REQUEST['name'];
		 $email=$_REQUEST['email'];
		 $contact=$_REQUEST['mobile'];
	     $pwd=$_REQUEST['password'];
		
		if((($uid!="") && ($pwd!="") && ($email!="") && ($name!="")) || ($contact!=""))
	    {
			$this->admin_model->updateData('salo_users',array('name' => $name,'email' => $email,'contact' => $contact,'password' => $pwd),array('id' => $uid));
			$data['status']="True";
			$data['type'] = '200';
			$data['success'] = 'Details Updated';
			$data['msg']    = "Details Updated";
		}
		else
		{
			$data['status'] = "False";
			$data['type']   = '400';
	        $data['error']  = 'All Fields Are Required';
			$data['msg']    = "All Fields Are Required";
		}
		
		echo json_encode($data);
	}
	
	public function searchData($query)
	{
	    $data['status']="True";
		$proName=strtolower($query);
		
		$Products = $this->admin_model->getBasic("SELECT 'product' as tbl_type,id,title FROM `salo_products` WHERE ((lower(title) LIKE '%$proName%') AND `status`='0') UNION SELECT 'cat_lvl1' as tbl_type,category_id,category_name FROM `salo_category` WHERE ((lower(category_name) LIKE '%$proName%') AND `status`='0' ) UNION SELECT 'cat_lvl2' as tbl_type,sub_category_id,sub_category_name FROM `salo_sub_category` WHERE ((lower(sub_category_name) LIKE '%$proName%') AND (category_id IN (SELECT category_id FROM salo_category WHERE status='0')) AND `status`='0') UNION  SELECT 'cat_lvl3' as tbl_type,id,name FROM `salo_sub_sub_category` WHERE ((lower(name) LIKE '%$proName%') AND (category_id IN (SELECT category_id FROM salo_category WHERE status='0')) AND (sub_category_id IN (SELECT sub_category_id FROM salo_sub_category WHERE status='0')) AND `status`='0') UNION SELECT 'brand' as tbl_type,id,name FROM `salo_brands` WHERE ((lower(name) LIKE '%$proName%') AND `status`='1' )");
	    $productArray=array();
		foreach($Products as $lr)
		{
		  array_push($productArray,$lr);
		}
		
		$catpro = $this->db->query("SELECT * FROM salo_products WHERE `status`='0' and lower(title) LIKE '%$proName%' ORDER BY id")->result_array();
		
		if(count($catpro) > 0){
			
			$proarray = array();
	        foreach($catpro as $pprow){
		       $proarray[] = $pprow['id'];
		    }
			$prodmp = implode(',',$proarray);
			$packsize =  $this->db->query("SELECT * FROM salo_product_attribute WHERE  status ='1' and product_id IN ($prodmp) group by attribute_value , unit_id ")->result_array();
			if(count($packsize)!=0)
			{
				$filterArr=array();
				 foreach($packsize as $pRow)
					{			 
						$pcid = $pRow['id'];
						$atv = $pRow['attribute_value'];
						$unitid = $pRow['unit_id'];
								 
								 
						$unitname = $this->db->query("SELECT * FROM salo_unit WHERE id ='$unitid'")->result_array();
						$prunitname = $unitname[0]['unit'];
                        $val=$atv.' '.$prunitname;
						
                        $farr=array('id' => $pcid,'name' => $val);	
                        array_push($filterArr,$farr);						
					}
			}
		}
		
		
		$filterArray=array('id' => 1,'name' => 'Package Size','data' => $filterArr);
		
		$data['data']=$productArray;
		$data['filter']=$filterArray;
		$data['msg']="Done";
		
		echo json_encode($data);
	}
	
	
	public function addtoCart()
	{
		
		$userid=$_REQUEST['userid'];
		$device_id=$_REQUEST['device_id'];
		$pid=$_REQUEST['pid'];
		$qty=$_REQUEST['qty'];
		$attribute_id=$_REQUEST['attribute_id'];
		
		$getProAtt = $this->admin_model->getData('salo_product_attribute',array('id' => $attribute_id),null);
	    $attributid = $getProAtt[0]['id'];
	    $unitPrice = $getProAtt[0]['price'];
	    $discount = $getProAtt[0]['discount'];
		
		if(($userid==0) || ($userid==""))
		{
		 $uid=0;
		 $getCart=$this->admin_model->getBasic("SELECT * FROM `salo_temp_cart` WHERE device_id='$device_id' and user_id='$uid' and `status`='0' and `pro_id`='$pid' and 'product_attribute'='$attributid' and product_type='1'");
		 $conditionVariable="device_id='$device_id' and user_id='$uid'";  	
		 $conditionCol="device_id"; 	
		 $conditionColVar=$device_id; 	
		}
		else
		{
		  $uid=$userid;
		  $getCart=$this->admin_model->getBasic("SELECT * FROM `salo_temp_cart` WHERE user_id='$userid' and `status`='0' and `pro_id`='$pid' and 'product_attribute'='$attributid' and product_type='1'");	
		  $conditionVariable="user_id='$userid'"; 
		  $conditionCol="user_id"; 	
		  $conditionColVar=$userid; 
		}
		
		$cartCount=count($getCart);
		$dateTime=date('Y-m-d H:i:s');
	    if($cartCount!=0)
	    {
			 $rowId=$getCart[0]['id'];
		     $prevQty=$getCart[0]['quantity'];
		 
		      $finalQty = $prevQty + $qty;
		      if($discount !=''){
			  $disamt = $unitPrice * ($discount / 100);												 
			  $discountedprice = $unitPrice - $disamt ;	
			  $discountedprice =   round($discountedprice);
			  
			   $totalPrice=$finalQty * $discountedprice;
			  }else{
			   $totalPrice=$finalQty * $unitPrice;  
			  }
		     
	          $total_duration="";
			 
			  $up = $this->admin_model->updateData('salo_temp_cart',array('quantity' => $finalQty,'unit_price' => $unitPrice,'discount' =>$discount,'total_price' => $totalPrice,'updated_on' => $dateTime,'total_duration' => $total_duration,'device_id' => $device_id,'product_attribute'=> $attributid,'product_type' => 1),array('id' => $rowId));
			  
			  $getSum=$this->db->query("SELECT SUM(total_price) as tp FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0'")->result_array();
			  
			  $cDate=date('Y-m-d');
			  $getpro = $this->admin_model->getBasic("SELECT * FROM  salo_temp_cart WHERE $conditionVariable and `status`='0' and promo_code_id!='0' and promo_code_id IN (SELECT id FROM salo_coupon WHERE expiry_date>='$cDate' and `status`='0')");
			  if(count($getpro)!=0)
			  {
                 $discount=$getpro[0]['promo_discount'];
				 $totalPrice=$getSum[0]['tp'];
				 $dis=$discount / 100;
				 $disAmt = $totalPrice * $dis;
				 $finalAmt=$totalPrice - $disAmt;
				 $finalAmt=round($finalAmt,2);
				 
				 if(($uid==0))
				 {
				  	$up=$this->admin_model->updateData('salo_temp_cart',array('grand_total' => $totalPrice,'final_amount' => $finalAmt),array('device_id' => $device_id,'user_id' => $uid,'status' => 0));	
				 }
				 else
				 {
					$up=$this->admin_model->updateData('salo_temp_cart',array('grand_total' => $totalPrice,'final_amount' => $finalAmt),array('user_id' => $uid,'status' => 0));	 
				 }
				
				 
				 $getsData=$this->db->query("SELECT * FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0'")->result_array();
				 $orderid=$getsData[0]['order_id'];
				 if($orderid!="")
				 {
					$up=$this->admin_model->updateData('salo_cart_shipping_details',array('grand_total' => $totalPrice,'final_amount' => $finalAmt),array('order_id' => $orderid));
				 }
							
			  }
              else
			  {
					$totalPrice=$getSum[0]['tp'];
					
					 if(($uid==0))
					 {
						$up=$this->admin_model->updateData('salo_temp_cart',array('grand_total' => $totalPrice,'promo_code' => '','promo_code_id' => '0','promo_discount' => '','final_amount' => $totalPrice),array('device_id' => $device_id,'user_id' => $uid,'status' => 0));	
					 }
					 else
					 {
						$up=$this->admin_model->updateData('salo_temp_cart',array('grand_total' => $totalPrice,'promo_code' => '','promo_code_id' => '0','promo_discount' => '','final_amount' => $totalPrice),array('user_id' => $uid,'status' => 0));	 
					 }
								
					$getsData=$this->db->query("SELECT * FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0'")->result_array();
					$orderid=$getsData[0]['order_id'];
					if($orderid!="")
					{
					 $up=$this->admin_model->updateData('salo_cart_shipping_details',array('grand_total' => $totalPrice,'promo_code' => '','promo_code_id' => '0','promo_discount' => '','final_amount' => $totalPrice),array('order_id' => $orderid));
					}
			   }			  
						   
			 
			 $getCart=$this->admin_model->getBasic("SELECT SUM(quantity) as qty FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0'"); 
			 $cartCount=$getCart[0]['qty'];
			 
			   if((is_nan($up)==false) || ($up == "")){
				   
				$data['status']="True";
				$data['type'] = '200';
				$data['data'] = $cartCount;
	            $data['msg'] = 'Cart Updated successfully';	
			  }else{
				$data['status']="False";
				$data['type'] = '400';
				$data['data'] = $cartCount;
	            $data['msg'] = 'Sorry,something went wrong.Please try again'; 
			  }
	    }
		else
		{
			if($discount !=''){
			  $disamt = 	$unitPrice * ($discount / 100);												 
			  $discountedprice = $unitPrice - $disamt ;	
			  $discountedprice =   round($discountedprice);
			  
			  $totalPrice=$qty * $discountedprice;
			}else{
			  $totalPrice=$qty * $unitPrice;  
			}
		   
		    $total_duration="";
		   
		    $up=$this->admin_model->insertData('salo_temp_cart',array('user_id' => $uid,'pro_id' => $pid,'quantity' => $qty,'unit_price' => $unitPrice,'discount' =>$discount,'total_price' => $totalPrice,'added_on' => $dateTime,'total_duration' => $total_duration,'product_attribute'=> $attributid,'device_id' => $device_id,'product_type' => 1));
			
			$getSum=$this->db->query("SELECT SUM(total_price) as tp FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0'")->result_array();
			  
			  $cDate=date('Y-m-d');
			  $getpro = $this->admin_model->getBasic("SELECT * FROM  salo_temp_cart WHERE $conditionVariable and `status`='0' and promo_code_id!='0' and promo_code_id IN (SELECT id FROM salo_coupon WHERE expiry_date>='$cDate' and `status`='0')");
			  if(count($getpro)!=0)
			  {
                 $discount=$getpro[0]['promo_discount'];
				 $totalPrice=$getSum[0]['tp'];
				 $dis=$discount / 100;
				 $disAmt = $totalPrice * $dis;
				 $finalAmt=$totalPrice - $disAmt;
				 $finalAmt=round($finalAmt,2);
				 
				 if($uid==0)
				 {
					$up=$this->admin_model->updateData('salo_temp_cart',array('grand_total' => $totalPrice,'final_amount' => $finalAmt),array('device_id' => $device_id,'user_id' => $uid,'status' => 0)); 
				 }
				 else
				 {
					$up=$this->admin_model->updateData('salo_temp_cart',array('grand_total' => $totalPrice,'final_amount' => $finalAmt),array('user_id' => $uid,'status' => 0));  
				 }
				 
				 
				 $getsData=$this->db->query("SELECT * FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0'")->result_array();
				 $orderid=$getsData[0]['order_id'];
				 if($orderid!="")
				 {
					$up=$this->admin_model->updateData('salo_cart_shipping_details',array('grand_total' => $totalPrice,'final_amount' => $finalAmt),array('order_id' => $orderid));
				 }
							
			  }
              else
			  {
					$totalPrice=$getSum[0]['tp'];
					
					 if($uid==0)
					 {
                        $up=$this->admin_model->updateData('salo_temp_cart',array('grand_total' => $totalPrice,'promo_code' => '','promo_code_id' => '0','promo_discount' => '','final_amount' => $totalPrice),array('device_id' => $device_id,'user_id' => $uid,'status' => 0));
					 }
					 else
					 {
						 $up=$this->admin_model->updateData('salo_temp_cart',array('grand_total' => $totalPrice,'promo_code' => '','promo_code_id' => '0','promo_discount' => '','final_amount' => $totalPrice),array('user_id' => $uid,'status' => 0));
					 }
				 
				 
					
								
					$getsData=$this->db->query("SELECT * FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0'")->result_array();
					$orderid=$getsData[0]['order_id'];
					if($orderid!="")
					{
					 $up=$this->admin_model->updateData('salo_cart_shipping_details',array('grand_total' => $totalPrice,'promo_code' => '','promo_code_id' => '0','promo_discount' => '','final_amount' => $totalPrice),array('order_id' => $orderid));
					}
			   }
			   
			 
             $getCart=$this->admin_model->getBasic("SELECT SUM(quantity) as qty FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0'"); 
			 $cartCount=$getCart[0]['qty'];
			 

	         if((is_nan($up)==false) || ($up == "")){
				$data['status']="True";
				$data['type'] = '200';
				$data['data'] = $cartCount;
	            $data['msg'] = 'Cart Updated successfully';	
			 }else{
				$data['status']="False";
				$data['type'] = '400';
				$data['data'] = $cartCount;
	            $data['msg'] = 'Sorry,something went wrong.Please try again'; 
			 }
		}
		
		echo json_encode($data);
	}
	
	
	
	public function getCart($uid,$device_id)
	{
		if(($uid==0) || ($uid==""))
		{
		  $conditionVariable="device_id='$device_id' "; 
          $conditionCol="device_id"; 	
		  $conditionColVar=$device_id; 	
		  
		  $whereArr=array('user_id' => '0','device_id' => $device_id,'status' => 0);
		}
		else
		{
		  $conditionVariable="user_id='$uid'";  
          $conditionCol="user_id"; 	
		  $conditionColVar=$uid;
		  
		  $whereArr=array('user_id' => $uid,'status' => 0);
		}
		
		$data['status']="True";
		$getCart=$this->admin_model->getData('salo_temp_cart',$whereArr,'AND');
		$productArray=array();
		foreach($getCart as $row)
		{
			$id=$row['id'];
			$pid=$row['pro_id'];
			$unit_price=$row['unit_price'];
			$discount=$row['discount'];
			$quantity=$row['quantity'];
			$total_price=$row['total_price'];
			$final_amount=$row['final_amount'];
			$attId=$row['product_attribute'];
			$product_type=$row['product_type'];
			
			
			 if($discount=="0")
			  {
				  $discount="0";
				  $unitPrice=$unit_price;
			  }
			  else
			  {
				   $discount=$discount;
				   $disper=$discount / 100;
				   $disamt = $unit_price * $disper;
				   $sellAmt=$unit_price - $disamt;
				   $unitPrice=round($sellAmt);
			  }
			  
			switch($product_type)
			{
			  case "1": //*** Normal Product *****//
			    
				$proDetail = $this->admin_model->getData('salo_products',array('id' => $pid),null);
				$lr=$proDetail[0];
				$title=$lr['title'];
				$image=$lr['image'];
				
				$ProAtt = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE id='$attId'");
				$pr=$ProAtt[0];
				$attribute_value = $pr['attribute_value'];
				$unit_id = $pr['unit_id'];
				
				$unit = $this->admin_model->getData('salo_unit',array('id' => $unit_id),null);
				$unitName=$unit[0]['unit'];
				  
				$attribute = $attribute_value." ".$unitName;
				
				$imgFolder="products";
				
			  break;
			  
			  case "2":  //*** Offer Product *****//
			     
				$proDetail = $this->admin_model->getData('salo_combo_packs',array('id' => $pid),null);
				$lr=$proDetail[0];
				$title=$lr['title'];
				$image=$lr['image'];
				$unit_price=$lr['price'];
				$attribute=$lr['pack_attribute'];
				$attId="0";
				$imgFolder="combo-packs";
				
			  break;
			}	
			
			
			if($image!="")
			{
			 $imageUrl=$this->config->item('base_image_url')."assets/uploads/".$imgFolder."/".$image;
			}
			else
			{
			 $imageUrl="";
			}
			
			$arr = array('id' => $id,'product_id' => $pid,'title' => $title,'image' => $imageUrl,'attribute_id' => $attId,'attribute' => $attribute ,'listprice' => $unit_price, 'discount' => $discount, 'saleprice' => $unitPrice,'quantity' => $quantity,'total_price' => $total_price,'product_type' => $product_type);
			
			array_push($productArray,$arr);
		}
		
		$getSetting = $this->admin_model->getData('salo_admin_login',array('role' => '0'),null);
		$sr=$getSetting[0];
		$order_amount=$sr['order_amount'];
		$order_charge=$sr['order_charge'];
		
		if($final_amount>=$order_amount)
		{
		  $shipping_charge="0";
		}
		else
		{
		  $shipping_charge=$order_charge;
		}
		
		$grandTotal=$final_amount + $shipping_charge;
		
		$this->admin_model->updateData('salo_temp_cart',array('shipping_charge' => $shipping_charge),array('user_id' => $uid,'device_id' => $device_id,'status' => 0));
		
		$getCart=$this->admin_model->getData('salo_temp_cart',array('user_id' => $uid,'device_id' => $device_id,'status' => 0),'AND');
		$orderId=$getCart[0]['order_id'];
		if($orderId!="")
		{
		   $this->admin_model->updateData('salo_cart_shipping_details',array('shipping_charge' => $shipping_charge),array('order_id' => $orderId));
		}
		
		$data['products']=$productArray;
		$data['finalAmount']=$final_amount;
		$data['shipping_charge']=$shipping_charge;
		$data['grand_total']=$grandTotal;
		$data['msg']="Done";
		echo json_encode($data);
		
	}
	
	
	public function deleteCart($id)
	{
		$data['status']="True";
		
		$getCart=$this->db->query("SELECT * FROM `salo_temp_cart` WHERE id='$id'")->result_array();
		$userId=$getCart[0]['user_id']; 
		$deviceId=$getCart[0]['device_id']; 
		
		$this->admin_model->deleteData('salo_temp_cart',array('id' => $id));
       
		if(($userId==0) || ($userId==""))
		{
		  $getSum=$this->db->query("SELECT SUM(total_price) as tp FROM `salo_temp_cart` WHERE device_id='$deviceId' and `status`='0'")->result_array();	
		  $conditionVariable="device_id='$deviceId' and user_id='$userId'"; 
		}
		else
		{
		  $getSum=$this->db->query("SELECT SUM(total_price) as tp FROM `salo_temp_cart` WHERE user_id='$userId' and `status`='0'")->result_array();
		  $conditionVariable="user_id='$userId'";
		}
		  
		 $cDate=date('Y-m-d');
		 $getpro = $this->admin_model->getBasic("SELECT * FROM  salo_temp_cart WHERE $conditionVariable and `status`='0' and promo_code_id!='0' and promo_code_id IN (SELECT id FROM salo_coupon WHERE expiry_date>='$cDate' and `status`='0')");
			  if(count($getpro)!=0)
			  {
                 $discount=$getpro[0]['promo_discount'];
				 $totalPrice=$getSum[0]['tp'];
				 $dis=$discount / 100;
				 $disAmt = $totalPrice * $dis;
				 $finalAmt=$totalPrice - $disAmt;
				 $finalAmt=round($finalAmt,2);
				 
				 if($userId==0)
				 {
					$up=$this->admin_model->updateData('salo_temp_cart',array('grand_total' => $totalPrice,'final_amount' => $finalAmt),array('device_id' => $deviceId,'user_id' => $userId,'status' => 0)); 
				 }
				 else
				 {
					$up=$this->admin_model->updateData('salo_temp_cart',array('grand_total' => $totalPrice,'final_amount' => $finalAmt),array('user_id' => $userId,'status' => 0));  
				 }
				 
				 
				 $getsData=$this->db->query("SELECT * FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0'")->result_array();
				 $orderid=$getsData[0]['order_id'];
				 if($orderid!="")
				 {
					$up=$this->admin_model->updateData('salo_cart_shipping_details',array('grand_total' => $totalPrice,'final_amount' => $finalAmt),array('order_id' => $orderid));
				 }
							
			  }
              else
			  {
					$totalPrice=$getSum[0]['tp'];
					 if($userId==0)
					 {
						$up=$this->admin_model->updateData('salo_temp_cart',array('grand_total' => $totalPrice,'promo_code' => '','promo_code_id' => '0','promo_discount' => '','final_amount' => $totalPrice),array('device_id' => $device_id,'user_id' => $userId,'status' => 0));
						
						
					 }
					 else
					 {
						$up=$this->admin_model->updateData('salo_temp_cart',array('grand_total' => $totalPrice,'promo_code' => '','promo_code_id' => '0','promo_discount' => '','final_amount' => $totalPrice),array('user_id' => $userId,'status' => 0));
					 }
				 
					
								
					$getsData=$this->db->query("SELECT * FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0'")->result_array();
					$orderid=$getsData[0]['order_id'];
					if($orderid!="")
					{
					 $up=$this->admin_model->updateData('salo_cart_shipping_details',array('grand_total' => $totalPrice,'promo_code' => '','promo_code_id' => '0','promo_discount' => '','final_amount' => $totalPrice),array('order_id' => $orderid));
					}
			   }
			   
			   
		$data['msg']="Item Deleted";
		echo json_encode($data);
		
	}
	
	
	
	public function updateQty()
	{
		$cartid=$_REQUEST['cartid'];
		$qty=$_REQUEST['qty'];
		
		
		$getCart=$this->admin_model->getBasic("SELECT * FROM `salo_temp_cart` WHERE id='$cartid'");
		$cartCount=count($getCart);
		$dateTime=date('Y-m-d H:i:s');
	    if($cartCount!=0)
	    {
			  $rowId=$getCart[0]['id'];
			  $userid=$getCart[0]['user_id'];
			  $device_id=$getCart[0]['device_id'];
			  $unitPrice=$getCart[0]['unit_price'];
			  $discount=$getCart[0]['discount'];
			 
			 if(($userid==0) || ($userid==""))
			 {
				$conditionVariable="device_id='$device_id' and user_id='0'";  	
		        $conditionCol="device_id"; 	
		        $conditionColVar=$device_id; 		
			 }
			 else
			 {
				$conditionVariable="user_id='$userid'";  	
		        $conditionCol="user_id"; 	
		        $conditionColVar=$userid;  
			 }
			 
			  $finalQty = $qty;
		      if($discount !=''){
			  $disamt = $unitPrice * ($discount / 100);												 
			  $discountedprice = $unitPrice - $disamt ;	
			  $discountedprice =   round($discountedprice);
			  
			   $totalPrice=$finalQty * $discountedprice;
			  }else{
			   $totalPrice=$finalQty * $unitPrice;  
			  }
		     
	          $total_duration="";
			  
			  
			  $up = $this->admin_model->updateData('salo_temp_cart',array('quantity' => $finalQty,'total_price' => $totalPrice,'updated_on' => $dateTime),array('id' => $rowId));
			  
			  $getSum=$this->db->query("SELECT SUM(total_price) as tp FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0'")->result_array();
			  
			  $cDate=date('Y-m-d');
			  $getpro = $this->admin_model->getBasic("SELECT * FROM  salo_temp_cart WHERE $conditionVariable and `status`='0' and promo_code_id!='0' and promo_code_id IN (SELECT id FROM salo_coupon WHERE expiry_date>='$cDate' and `status`='0')");
			  if(count($getpro)!=0)
			  {
                 $discount=$getpro[0]['promo_discount'];
				 $totalPrice=$getSum[0]['tp'];
				 $dis=$discount / 100;
				 $disAmt = $totalPrice * $dis;
				 $finalAmt=$totalPrice - $disAmt;
				 $finalAmt=round($finalAmt,2);
				 
				 if($userid==0)
				 {
					$up=$this->admin_model->updateData('salo_temp_cart',array('grand_total' => $totalPrice,'final_amount' => $finalAmt),array('device_id' => $device_id,'user_id' => $userid,'status' => 0)); 
				 }
				 else
				 {
					$up=$this->admin_model->updateData('salo_temp_cart',array('grand_total' => $totalPrice,'final_amount' => $finalAmt),array('user_id' => $userid,'status' => 0));  
				 }
				 
				 
				 $getsData=$this->db->query("SELECT * FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0'")->result_array();
				 $orderid=$getsData[0]['order_id'];
				 if($orderid!="")
				 {
					$up=$this->admin_model->updateData('salo_cart_shipping_details',array('grand_total' => $totalPrice,'final_amount' => $finalAmt),array('order_id' => $orderid));
				 }
							
			  }
              else
			  {
					$totalPrice=$getSum[0]['tp'];
					 if($userid==0)
					 {
						$up=$this->admin_model->updateData('salo_temp_cart',array('grand_total' => $totalPrice,'promo_code' => '','promo_code_id' => '0','promo_discount' => '','final_amount' => $totalPrice),array('device_id' => $device_id,'user_id' => $userid,'status' => 0));
						
						
					 }
					 else
					 {
						$up=$this->admin_model->updateData('salo_temp_cart',array('grand_total' => $totalPrice,'promo_code' => '','promo_code_id' => '0','promo_discount' => '','final_amount' => $totalPrice),array('user_id' => $userid,'status' => 0));
					 }
				 
					
								
					$getsData=$this->db->query("SELECT * FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0'")->result_array();
					$orderid=$getsData[0]['order_id'];
					if($orderid!="")
					{
					 $up=$this->admin_model->updateData('salo_cart_shipping_details',array('grand_total' => $totalPrice,'promo_code' => '','promo_code_id' => '0','promo_discount' => '','final_amount' => $totalPrice),array('order_id' => $orderid));
					}
			   }			  
						   
			
             $getCart=$this->admin_model->getBasic("SELECT SUM(quantity) as qty FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0'"); 
			 $cartCount=$getCart[0]['qty'];
			 
			   if((is_nan($up)==false) || ($up == "")){
				   
		        $data['status']="True";
				$data['type'] = '200';
				$data['data'] = $cartCount;
	            $data['msg'] = 'Cart Updated successfully';	
				
			  }else{
				  
				$data['status']="False";
				$data['type'] = '400';
				$data['data'] = $cartCount;
	            $data['msg'] = 'Sorry,something went wrong.Please try again'; 
			  }
		}
		else
		{
			$userid=$getCart[0]['user_id'];
			$device_id=$getCart[0]['device_id'];
			
			 if(($userid==0) || ($userid==""))
			 {
				$conditionVariable="device_id='$device_id' and user_id='0'";  	
		        $conditionCol="device_id"; 	
		        $conditionColVar=$device_id; 		
			 }
			 else
			 {
				$conditionVariable="user_id='$userid'";  	
		        $conditionCol="user_id"; 	
		        $conditionColVar=$userid;  
			 }
			 
			$getCart=$this->admin_model->getBasic("SELECT SUM(quantity) as qty FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0'"); 
			 $cartCount=$getCart[0]['qty'];
			 
			$data['status']="False";
			$data['type'] = '400';
			$data['data'] = $cartCount;
	        $data['msg'] = 'Sorry,Item Not Available'; 
		}
		
		echo json_encode($data);
		
	}
	
	
	
	public function checkout()
	{
	      $postData=$_POST;
		  $userId=$postData['user_id'];
		  $subd['user_id'] = $userId;

	      $subd['delivery_time'] = $postData['delivery_time'];
	      $subd['delivery_date'] = $postData['delivery_date'];
		  $subd['name'] = $postData['name'];
          $subd['contact'] = $postData['contact'];	
          $subd['email'] = $postData['email'];	
          $subd['address'] = $postData['address'];	
          $subd['state'] = $postData['state'];	
          $subd['city'] = $postData['city'];
          $subd['country'] = $postData['country'];	
          $subd['pincode'] = $postData['pincode'];		  
		  $subd['added_on'] = date('Y-m-d H:i:s');	
		  
		  $subd['billing_name'] = $postData['billing_name'];
          $subd['billing_contact'] =$postData['billing_contact'];	
          $subd['billing_email'] =$postData['billing_email'];	
          $subd['billing_address'] = $postData['billing_address'];	
          $subd['billing_state'] = $postData['billing_state'];	
          $subd['billing_city'] = $postData['billing_city'];
          $subd['billing_country'] = $postData['billing_country'];	
          $subd['billing_pincode'] = $postData['billing_pincode'];  
		  $subd['before_time'] = $postData['beforetime'];
		  
		  $checkData = $this->admin_model->getBasic("SELECT * FROM  salo_temp_cart WHERE `user_id`='$userId' and `status`='0'");
		  $order_id = $checkData[0]['order_id'];
		  if($order_id=="")
		  {
			  $nordr=$this->admin_model->getBasic("SELECT * FROM  salo_cart_shipping_details order by id desc limit 0, 1");
				if(count($nordr)==0)
				{
				  $lastnum="001";
				}
				else
				{
				  $idd = $nordr[0]['id'];
				  $order_id = $nordr[0]['order_id'];
				  $ex_inv=explode('-',$order_id);
				  $lastExinv=$ex_inv[1];
				  $lastnum=$lastExinv + 1;
				  $lastnum="00".$lastnum;
                }
			  //$newid = $idd + 1 ;
			  $orderid = 'LUP'.date('Y').date('m').date('d').'-'.$lastnum ;
		  }
		  else
		  {
		      $orderid=$order_id;
		  }	
		  
		  $subd['order_id'] = $orderid;
		  
		  $checktype = $this->admin_model->getBasic("SELECT * FROM  salo_temp_cart WHERE `user_id`='$userId' and `status`='0'");
		  $grandTotal = $checktype[0]['grand_total'];
		  $promoCode = $checktype[0]['promo_code'];
		  $promoCodeId = $checktype[0]['promo_code_id'];
		  $discount = $checktype[0]['promo_discount'];
		  $finalAmount = $checktype[0]['final_amount'];
		  $device_id = $checktype[0]['device_id'];
		  
		  $subd['grand_total'] = $grandTotal; 
		  $subd['promo_code'] = $promoCode; 
		  $subd['promo_code_id'] = $promoCodeId; 
		  $subd['promo_discount'] = $discount; 
		  $subd['final_amount'] = $finalAmount; 
		  $subd['device_id'] = $device_id; 
		  
		    $getSetting = $this->admin_model->getData('salo_admin_login',array('role' => '0'),null);
			$sr=$getSetting[0];
			$order_amount=$sr['order_amount'];
			$order_charge=$sr['order_charge'];
			
			if($finalAmount>=$order_amount)
			{
			  $shipping_charge="0";
			}
			else
			{
			  $shipping_charge=$order_charge;
			}
		
			
		   $checkData = $this->admin_model->getBasic("SELECT * FROM  salo_temp_cart WHERE `user_id`='$userId' and `status`='0'");
		 
		  $order_id = $checkData[0]['order_id'];
		  
		  $dataUpdate['order_id'] = $orderid;
		  
		  
		  if($order_id=="")
		  {
		    $uid=$this->admin_model->insertData('salo_cart_shipping_details',$subd);
		  }
		  else
		  {
			  
		    $uid=$this->admin_model->updateData('salo_cart_shipping_details',$subd,array('order_id' => $orderid));
			$uid=$orderid;
		  }

		  if($uid !=''){
			 
			 $data['status']="True";
			 $dataUpdate['shipping_charge']=$shipping_charge;
			 $this->db->update('salo_temp_cart', $dataUpdate, array('user_id' => $userId,'status' => '0'));
			 $data['orderid']=$orderid;
			 $data['msg']="Done";
			 
		    }
			else
			{
				$data['status']="False";
				$data['msg']="Error";
			}
			
			echo json_encode($data);
	}
	

	
	public function Applypromocode($prcode,$orderid)
	{
		
		  $conditionVariable="order_id='$orderid'"; 
		  $conditionCol="order_id"; 	
		  $conditionColVar=$orderid;
		
		
		$cdate=date('Y-m-d');
		$getPromo=$this->admin_model->getBasic("SELECT * FROM salo_coupon WHERE coupon_code='$prcode' and `status`='0'");
		if(count($getPromo)!=0)
		{
			$expiry_date=$getPromo[0]['expiry_date'];
			if($expiry_date>=$cdate)
		    {
			 $couponId=$getPromo[0]['id'];
			 $discount=$getPromo[0]['discount'];
			 $discount_type=$getPromo[0]['discount_type'];
			 $isCashback=$getPromo[0]['is_cashback'];
			 
			 $chkPro=$this->db->query("SELECT * FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0' and (`pro_id` IN (SELECT id FROM salo_products WHERE `active_coupon_id`='$couponId' and `coupon_code_status`='1'))")->result_array();
			
			 if(count($chkPro)!=0)
			 {
				$getSum=$this->db->query("SELECT SUM(total_price) as tp FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0'")->result_array();
				$totalPrice=$getSum[0]['tp'];
				
				switch($isCashback)
				{
				  case "0":  //******** Discount *******//
				    
					switch($discount_type)
					{
					  case "1":  //******** in percent *******//
					  					
						$dis=$discount / 100;
						$disAmt = $totalPrice * $dis;
						$finalAmt=$totalPrice - $disAmt;
						$finalAmt=round($finalAmt,2);
						$cashbackAmt="0";
						
					  break;
					  
					  case "2":  //******** in rs *******//

						$finalAmt=$totalPrice - $discount;
						$finalAmt=round($finalAmt,2);
						$cashbackAmt="0";
					
					  break;
					}
					
				  break;
				  
				  case "1":  //******** Cashback *******//
				  
				    switch($discount_type)
					{
					  case "1":  //******** in percent *******//
					  					
						$dis=$discount / 100;
						$disAmt = $totalPrice * $dis;
						$finalAmt=$totalPrice;
						
						$finalAmt=round($finalAmt,2);
						$cashbackAmt=round($disAmt,2);
						
					  break;
					  
					  case "2":  //******** in rs *******//

						$finalAmt=$totalPrice;
						$finalAmt=round($finalAmt,2);
						$cashbackAmt=round($discount,2);
					
					  break;
					}
					
				  break;
				}
				
				
				$getSetting = $this->admin_model->getData('salo_admin_login',array('role' => '0'),null);
				$sr=$getSetting[0];
				$order_amount=$sr['order_amount'];
				$order_charge=$sr['order_charge'];
				
				if($finalAmt>=$order_amount)
				{
				  $shipping_charge="0";
				}
				else
				{
				  $shipping_charge=$order_charge;
				}
				
				
				$up=$this->admin_model->updateData('salo_temp_cart',array('grand_total' => $totalPrice,'shipping_charge' => $shipping_charge,'promo_code' => $prcode,'promo_code_id' => $couponId,'promo_discount' => $discount,'promo_discount_type' => $discount_type,'is_cashback' => $isCashback,'cashback_amount' => $cashbackAmt,'final_amount' => $finalAmt),array('order_id' => $orderid,'status' => 0));
			
			    $up=$this->admin_model->updateData('salo_cart_shipping_details',array('grand_total' => $totalPrice,'shipping_charge' => $shipping_charge,'promo_code' => $prcode,'promo_code_id' => $couponId,'promo_discount' => $discount,'promo_discount_type' => $discount_type,'is_cashback' => $isCashback,'cashback_amount' => $cashbackAmt,'final_amount' => $finalAmt),array('order_id' => $orderid));
				
				$grandTotal=$finalAmt + $shipping_charge;
				
				$data['status']="True";
				$data['finalAmount']=$finalAmt;
				$data['shipping_charge']=$shipping_charge;
				$data['grand_total']=$grandTotal;
		        $data['msg']="Done";
				
		        echo json_encode($data);
			 }
			 else
				{
					 $data['status']="False";
				     $data['msg']="Invalid Promo Code";
					 echo json_encode($data);
				}
			}
			else
			{
				 $data['status']="False";
				 $data['msg']="Promo code has expired";
				 echo json_encode($data);
			}
		}
		else
		{
			  $data['status']="False";
			  $data['msg']="Invalid Promo Code";
			  echo json_encode($data);
		}
	}
	
	
	public function cashonDeliveryNew()
	{
		$postData=$_POST;
	    $orderId=$postData['order_id'];
		$used_wallet_amount=$postData['used_wallet_amount'];
		$final_amount=$postData['final_amount'];

		$siteDetails=$this->adminDetails();
		
		$this->db->update('salo_temp_cart', array('used_wallet_amount' => $used_wallet_amount,'final_amount' => $final_amount,'status' => 1) , array('order_id' => $orderId)); 
					 
		$this->db->update('salo_cart_shipping_details', array('used_wallet_amount' => $used_wallet_amount,'final_amount' => $final_amount,'payment_mode' => 'Cash On Delivery (COD)') , array('order_id' => $orderId)); 
					 
		$prot = $this->admin_model->getBasic("SELECT * FROM  salo_temp_cart WHERE order_id='$orderId'");
	    $protype = $prot[0]['product_type'];
		$bookHead="Shipping Address";
					 
		$orderData=$this->admin_model->getData('salo_temp_cart',array('order_id' => $orderId),null);
					 
        $ordershippingData=$this->admin_model->getData('salo_cart_shipping_details',array('order_id' => $orderId),null);
					 
		$userId=$ordershippingData[0]['user_id'];
					 
					  
		if($used_wallet_amount!=0)
		{
			$userData=$this->admin_model->getData('salo_users',array('id' => $userId),null);
			$old_wallet_amount=$userData[0]['wallet_amount'];
			$rem_wallet_amount=$old_wallet_amount - $used_wallet_amount;
					  
			$this->db->update('salo_users', array('wallet_amount' => $rem_wallet_amount) , array('id' => $userId)); 
		}
					
	    $userData=$this->admin_model->getData('salo_users',array('id' => $userId),null);
					 
		$name=$userData[0]['name'];
		$booking_name=$ordershippingData[0]['name'];
		$booking_phone=$ordershippingData[0]['contact'];
		$booking_email=$ordershippingData[0]['email'];
		$delivery_date=$ordershippingData[0]['delivery_date'];
		$delivery_time=$ordershippingData[0]['delivery_time'];
					
		$delivery_date=date('F j,Y',strtotime($delivery_date));
					
					
		$timeSlot=$this->admin_model->getData('salo_time_slot',array('id' => $delivery_time),null);
		$start = date("g:i a", strtotime($timeSlot[0]['start']));
		$end = date("g:i a", strtotime($timeSlot[0]['end']));
					
					
		$booking_address=$ordershippingData[0]['address'].",".$ordershippingData[0]['city'].",".$ordershippingData[0]['state'].",".$ordershippingData[0]['country'].",".$ordershippingData[0]['pincode'];
					
					
		
/***************************** User Mail Starts *************************/		
		
		
$html='<table cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" style="background:#ffffff;border:1px solid #e3e3e3;border-radius:3px;max-width:640px">
<tbody><tr>
  <td width="620" align="center"><table border="0" cellpadding="0" cellspacing="0" align="center" style="max-width:620px">
    <tbody><tr>
      <td align="center" style="border-bottom:1px dotted #e3e3e3"><a href="'.base_url().'" target="_blank" data-saferedirecturl="'.base_url().'">
        <div style="width:100%"><img src="'.base_url().'assets/uploads/logo/'.$siteDetails[0]['company_logo'].'" border="0" alt="" style="margin:0;display:block;max-width:200px;width:inherit" vspace="0" hspace="0" align="center" class="CToWUd"></div>
        </a></td>
    </tr>

    </tbody></table></td>
</tr>
<tr>
  <td align="center" style="padding:0px"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-family:arial;font-size:12px;text-align:left;color:#333333">
    <tbody>
      <tr>
        <td style="padding:0px 0px"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" style="max-width:600px;font-family:arial;font-size:14px;text-align:left;color:#333333">
          <tbody>
            <tr>
              <td style="padding:10px">Dear '.$name.' ,</td>
              </tr>
            <tr>
              <td style="padding:0px 10px 20px 10px">We have received your order. We will be calling you shortly to confirm your order. In case you are not able to pick up our call, do not worry. We will be calling multiple times before going ahead with the cancellation. </td>
              </tr>
            
            </tbody>
          </table></td>
        </tr>
      <tr>
        <td style="padding:0px 10px"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tbody>
            <tr>
              <td bgcolor="#84a743" style="border-bottom:1px solid #4f7605;padding:10px;font-size:14px"><strong style="color:white">Shipping Address</strong><strong style="float:right;text-align:right;color:white">Order Id :  '.$orderid.'</strong></td>
              </tr>
            <tr>
              <td style="font-size:0;padding:0 10px 10px 10px" align="left" bgcolor="#dff2e0">
                <table width="200" cellpadding="0" cellspacing="0" border="0" align="left" style="font-family:arial;font-size:12px;text-align:left;color:#333333">
                  <tbody><tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top" width="60"><strong>Name</strong></td>
                    <td valign="top">'.$booking_name.' </td>
                    </tr>
                  <tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top"><strong>Email</strong></td>
                    <td valign="top"><a href="#m_2299037813007463152_" style="display:block;color:#333333;word-break:break-all;text-decoration:none">'.$booking_email.'</a></td>
                    </tr>
					 <tr>
                    <td valign="top"><strong>Delivery Time</strong></td>
                    <td valign="top"><a href="#m_2299037813007463152_" style="display:block;color:#333333;word-break:break-all;text-decoration:none"> '.$delivery_date.' between '.$start .' - '.$end.'</a></td>
                    </tr>
					 <tr>
                    <td valign="top"><strong>Before Time</strong></td>
                    <td valign="top"><a href="#m_2299037813007463152_" style="display:block;color:#333333;word-break:break-all;text-decoration:none">'.$btime.'</a></td>
                    </tr>
                  </tbody></table>
                
                <table width="200" cellpadding="0" cellspacing="0" border="0" align="left" style="font-family:arial;font-size:12px;text-align:left;color:#333333">
                  <tbody><tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top" width="60"><strong>Mobile</strong></td>
                    <td valign="top">'.$booking_phone.'</td>
                    </tr>
                  <tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top"><strong>Address</strong></td>
                    <td valign="top">'.$booking_address.'</td>
                    </tr>
                  </tbody></table>';
				  
				$html.='</td>
              </tr>
            </tbody>
          </table></td>
        </tr>
      <tr>
        <td height="20"></td>
        </tr>
      <tr>
        <td width="600" align="center" style="padding:0px 10px">
          
          
          
          <table width="98%" border="0" bgcolor="#dff2e0" align="center" cellpadding="8" cellspacing="0" style="font-family:arial;font-size:12px;text-align:left;color:#333333;max-width:600px">
            <tbody>
			
			    <tr>
					 <th style="padding:3px;border:1px solid black">Category</th>
					 <th style="padding:3px;border:1px solid black">Product</th>
					 <th style="padding:3px;border:1px solid black">Price</th>
					 <th style="padding:3px;border:1px solid black">Discount(%)</th>';
					
					 $html.='<th style="padding:3px;border:1px solid black">Qty</th>
					 <th style="padding:3px;border:1px solid black">Attribute</th>
					 <th style="padding:3px;border:1px solid black">Total</th>
			    </tr>';
		
         $totQty=0;		
         $totPrc=0;		
         $totDur=0;		
		foreach($orderData as $rw)
		{
		    $pid=$rw['pro_id'];
			
			    $product_attribute = $rw['product_attribute'] ;
			    $getatt=$this->db->query("SELECT * FROM salo_product_attribute WHERE `id`='$product_attribute'")->result_array();
				$unitid = $getatt[0]['unit_id'];
				$attvalue = $getatt[0]['attribute_value'];
				$unitatt=$this->db->query("SELECT * FROM salo_unit WHERE `id`='$unitid'")->result_array();
				$unitname = $unitatt[0]['unit'];
			
			
			
			
			$proData=$this->admin_model->getData('salo_products',array('id' => $pid),null);
			$catid=$proData[0]['sub_category_id'];
			$proTitle=$proData[0]['title'];
			
			
		    $subCatData=$this->admin_model->getData('salo_sub_category',array('sub_category_id' => $catid),null);
			$sub_category_name=$subCatData[0]['sub_category_name'];
			
			
				$html.=' <tr>
				 
						   <td style="padding:3px;border:1px solid black">'.$sub_category_name.'</td>
						   <td style="padding:3px 10px 3px 3px;border:1px solid black">'.$proTitle.'</td>
						   <td style="text-align:center;padding:3px;border:1px solid black">'.$rw['unit_price'].'</td>
						   <td style="text-align:center;padding:3px;border:1px solid black">'.$rw['discount'].'</td>';
						    $html.='<td style="padding:3px;text-align:center;border:1px solid black">'.$rw['quantity'].'</td>
						    <td style="text-align:center;padding:3px;border:1px solid black">'.$attvalue .' '.$unitname.'</td>
						   <td style="text-align:center;padding:3px;border:1px solid black">'.$rw['total_price'].'</td>
						   
						</tr>';
				
				$totQty= $totQty + $rw['quantity'];
				$totPrc= $totPrc + $rw['total_price'];
				$totDur= $totDur + $proData[0]['duration'];
			}
				$html.='<tr>
					<td colspan="4" style="text-align:right;padding:3px;border:1px solid black">Total</td>';
					
					$html.='<td style="padding:3px;text-align:right;border:1px solid black">'.$totQty.'</td><td style="padding:3px;text-align:right;border:1px solid black"></td>
					<td style="text-align:right;padding:3px;border:1px solid black">Rs. '.$totPrc.'</td>
				</tr>';
				
				if($ordershippingData[0]['promo_code_id']!=0)
		        {		
                   $shipping_charge=$ordershippingData[0]['shipping_charge'];
                   $dis=$ordershippingData[0]['promo_discount'];
                   $final_amount=$ordershippingData[0]['final_amount'];			  
				    $html.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Promo Discount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$dis.'</td>
				 
				 </tr>';
				 
				 if($shipping_charge!=0)
				 {
				   $html.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Shipping Charge</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$shipping_charge.'</td>
				 
				 </tr>';
				 
				 $finalAmt = $final_amount + $shipping_charge;
				 }
				 else
				 {
				   $finalAmt = $final_amount;
				 }
				$html.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Final Amount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black"><i class="fa fa-inr"></i> '.$finalAmt.'</td>
				 
				 </tr>';
		        }
				else
				{
				  $shipping_charge=$ordershippingData[0]['shipping_charge'];
				  $final_amount=$ordershippingData[0]['final_amount'];
				   if($shipping_charge!=0)
					 {
					   $finalAmt = $final_amount + $shipping_charge;
					   $html.='<tr>
					
					   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Shipping Charge</td>
					   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$shipping_charge.'</td>
					 
					 </tr>
					 <tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Final Amount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black"><i class="fa fa-inr"></i> '.$finalAmt.'</td>
				 
				 </tr>';
					 
					
					 }
				}
			$html.='<tr>
				<td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Payment mode</td>
				<td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$payment_mode.'</td>
			</tr>';
            $html.='</tbody></table></td>
      </tr>
 
      </tbody>
    </table></td>
</tr>
<tr>
  <td align="center" style="padding-top:0px"><table align="center" cellpadding="0" cellspacing="0" class="m_2299037813007463152container" style="width:100%">
    <tbody><tr>
      <td align="center" style="text-align:center;vertical-align:top;font-size:0;padding:10px">
        <div style="width:240px;display:inline-block;vertical-align:top;text-align:center;font-size:0" align="center">
          <table width="220" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px">
            <tbody><tr>
              <td width="30" align="center" style="padding:5px"><a href="'.base_url().'" target="_blank" data-saferedirecturl="'.base_url().'"><img src="'.base_url().'assets/template/images/icon/www.png" alt="" width="30" height="30" style="margin:0" border="0" vspace="0" hspace="0" align="absmiddle" class="CToWUd"></a></td>
              <td align="left" style="padding:0px 0px;font-size:16px;font-family: Open Sans,Gill Sans,Arial,Helvetica,sans-serif"><a href="'.base_url().'" style="color:#444444;text-decoration:none" target="_blank" data-saferedirecturl="'.base_url().'">Ghar Ghar Bazaar</a></td>
              </tr>
            </tbody></table>
          </div>
        
        <div style="width:200px;display:inline-block;vertical-align:top;text-align:center;font-size:0" align="center">
          <table width="180" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px">
            <tbody><tr>
              <td width="30" align="center" style="padding:5px"><a href="'.base_url().'" target="_blank" data-saferedirecturl="'.base_url().'"><img src="'.base_url().'assets/template/images/icon/call.png" alt="" width="30" height="30" style="margin:0" border="0" vspace="0" hspace="0" align="absmiddle" class="CToWUd"></a></td>
              <td align="left" style="padding:0px 0px;font-size:16px;font-family:Open Sans,Gill Sans,Arial,Helvetica,sans-serif"><a style="color:#444444;text-decoration:none" href="tel:'.$siteDetails[0]['company_phone'].'" target="_blank">'.$siteDetails[0]['company_phone'].'</a></td>
              </tr>
            </tbody></table>
          </div>
        </td>
      </tr>
    </tbody></table></td>
</tr>
</tbody></table>';

                        $from="no-reply@ghargharbazaar.com";
						$subject="Thank You for shopping with ".$siteDetails[0]['company_name'];
						$to=$userData[0]['email'];			
						$headers  = 'MIME-Version: 1.0' . "\r\n";
						$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
						$headers .= 'From: '.$from . "\r\n";
						mail($to,$subject,$html,$headers);


						
/***************************** User Mail Ends *************************/

						
						
/***************************** Admin Mail Starts *************************/						
						
						
$htmlAdmin='<table cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" style="background:#ffffff;border:1px solid #e3e3e3;border-radius:3px;max-width:640px">
<tbody><tr>
  <td width="620" align="center"><table border="0" cellpadding="0" cellspacing="0" align="center" style="max-width:620px">
    <tbody><tr>
      <td align="center" style="border-bottom:1px dotted #e3e3e3"><a href="'.base_url().'" target="_blank" data-saferedirecturl="'.base_url().'">
        <div style="width:100%"><img src="'.base_url().'assets/uploads/logo/'.$siteDetails[0]['company_logo'].'" border="0" alt="" style="margin:0;display:block;max-width:200px;width:inherit" vspace="0" hspace="0" align="center" class="CToWUd"></div>
        </a></td>
    </tr>

    </tbody></table></td>
</tr>
<tr>
  <td align="center" style="padding:0px"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-family:arial;font-size:12px;text-align:left;color:#333333">
    <tbody>
      <tr>
        <td style="padding:0px 0px"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" style="max-width:600px;font-family:arial;font-size:14px;text-align:left;color:#333333">
          <tbody>
            <tr>
              <td style="padding:10px">Hello ,</td>
              </tr>
            <tr>
              <td style="padding:0px 10px 20px 10px">You have recieved a new order from '.$name.'.Here are the details.</td>
              </tr>
            </tbody>
          </table></td>
        </tr>
      <tr>
        <td style="padding:0px 10px"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tbody>
            <tr>
              <td bgcolor="#84a743" style="border-bottom:1px solid #4f7605;padding:10px;font-size:14px"><strong style="color:white">Shipping Address</strong><strong style="float:right;text-align:right;color:white">Order Id :  '.$orderid.'</strong></td>
              </tr>
            <tr>
              <td style="font-size:0;padding:0 10px 10px 10px" align="left" bgcolor="#dff2e0">
                <table width="200" cellpadding="0" cellspacing="0" border="0" align="left" style="font-family:arial;font-size:12px;text-align:left;color:#333333">
                  <tbody><tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top" width="60"><strong>Name</strong></td>
                    <td valign="top">'.$booking_name.' </td>
                    </tr>
                  <tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top"><strong>Email</strong></td>
                    <td valign="top"><a href="#m_2299037813007463152_" style="display:block;color:#333333;word-break:break-all;text-decoration:none">'.$booking_email.'</a></td>
                    </tr>
					 <tr>
                    <td valign="top"><strong>Delivery Time</strong></td>
                    <td valign="top"><a href="#m_2299037813007463152_" style="display:block;color:#333333;word-break:break-all;text-decoration:none"> '.$delivery_date.' between '.$start .' - '.$end.'</a></td>
                    </tr>
					 <tr>
                    <td valign="top"><strong>Before Time</strong></td>
                    <td valign="top"><a href="#m_2299037813007463152_" style="display:block;color:#333333;word-break:break-all;text-decoration:none">'.$btime.'</a></td>
                    </tr>
                  </tbody></table>
                
                <table width="200" cellpadding="0" cellspacing="0" border="0" align="left" style="font-family:arial;font-size:12px;text-align:left;color:#333333">
                  <tbody><tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top" width="60"><strong>Mobile</strong></td>
                    <td valign="top">'.$booking_phone.'</td>
                    </tr>
                  <tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top"><strong>Address</strong></td>
                    <td valign="top">'.$booking_address.'</td>
                    </tr>
                  </tbody></table>';
				  
				
				
                $htmlAdmin.='</td>
              </tr>
            </tbody>
          </table></td>
        </tr>
      <tr>
        <td height="20"></td>
        </tr>
      <tr>
        <td width="600" align="center" style="padding:0px 10px">
          
           <table width="98%" border="0" bgcolor="#dff2e0" align="center" cellpadding="8" cellspacing="0" style="font-family:arial;font-size:12px;text-align:left;color:#333333;max-width:600px">
            <tbody>
			
			    <tr>
					 <th style="padding:3px;border:1px solid black">Category</th>
					 <th style="padding:3px;border:1px solid black">Product</th>
					 <th style="padding:3px;border:1px solid black">Price</th>
					 <th style="padding:3px;border:1px solid black">Discount(%)</th>';
					 $htmlAdmin.='<th style="padding:3px;border:1px solid black">Qty</th>
					 <th style="padding:3px;border:1px solid black">Attribute</th>
					 <th style="padding:3px;border:1px solid black">Total</th>
			    </tr>';
		
        $totQty=0;		
        $totPrc=0;		
        $totDur=0;		
		foreach($orderData as $rw)
		{
		        $pid=$rw['pro_id'];
			    $product_attribute = $rw['product_attribute'] ;
			    $getatt=$this->db->query("SELECT * FROM salo_product_attribute WHERE `id`='$product_attribute'")->result_array();
				$unitid = $getatt[0]['unit_id'];
				$attvalue = $getatt[0]['attribute_value'];
				$unitatt=$this->db->query("SELECT * FROM salo_unit WHERE `id`='$unitid'")->result_array();
				$unitname = $unitatt[0]['unit'];
			
			$proData=$this->admin_model->getData('salo_products',array('id' => $pid),null);
			$catid=$proData[0]['sub_category_id'];
			$proTitle=$proData[0]['title'];
			
			
		    $subCatData=$this->admin_model->getData('salo_sub_category',array('sub_category_id' => $catid),null);
			$sub_category_name=$subCatData[0]['sub_category_name'];
			
			
				$htmlAdmin.=' <tr>
				           <td style="padding:3px;border:1px solid black">'.$sub_category_name.'</td>
						   <td style="padding:3px 10px 3px 3px;border:1px solid black">'.$proTitle.'</td>
						   <td style="text-align:center;padding:3px;border:1px solid black">'.$rw['unit_price'].'</td>
						   <td style="text-align:center;padding:3px;border:1px solid black">'.$rw['discount'].'</td>';
						   $htmlAdmin.='<td style="padding:3px;text-align:center;border:1px solid black">'.$rw['quantity'].'</td>
						   <td style="text-align:center;padding:3px;border:1px solid black">'.$attvalue .' '.$unitname.'</td>
						   <td style="text-align:center;padding:3px;border:1px solid black">'.$rw['total_price'].'</td>
						   
						</tr>';
				
				$totQty= $totQty + $rw['quantity'];
				$totPrc= $totPrc + $rw['total_price'];
				$totDur= $totDur + $proData[0]['duration'];
			}
				$htmlAdmin.='<tr>
					<td colspan="4" style="text-align:right;padding:3px;border:1px solid black">Total</td>';
					$htmlAdmin.='<td style="padding:3px;text-align:right;border:1px solid black">'.$totQty.'</td><td style="padding:3px;text-align:right;border:1px solid black"></td>
					<td style="text-align:right;padding:3px;border:1px solid black">Rs. '.$totPrc.'</td>
				</tr>';
				
			 if($ordershippingData[0]['promo_code_id']!=0)
		        {		
                   $shipping_charge=$ordershippingData[0]['shipping_charge'];
                   $dis=$ordershippingData[0]['promo_discount'];
                   $final_amount=$ordershippingData[0]['final_amount'];			  
				    $htmlAdmin.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Promo Discount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$dis.'</td>
				 
				 </tr>';
				 
				 if($shipping_charge!=0)
				 {
				   $htmlAdmin.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Shipping Charge</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$shipping_charge.'</td>
				 
				 </tr>';
				 
				 $finalAmt = $final_amount + $shipping_charge;
				 }
				 else
				 {
				   $finalAmt = $final_amount;
				 }
				$htmlAdmin.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Final Amount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black"><i class="fa fa-inr"></i> '.$finalAmt.'</td>
				 
				 </tr>';
		        }
				else
				{
				  $shipping_charge=$ordershippingData[0]['shipping_charge'];
				  $final_amount=$ordershippingData[0]['final_amount'];
				   if($shipping_charge!=0)
					 {
					   $finalAmt = $final_amount + $shipping_charge;
					   $htmlAdmin.='<tr>
					
					   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Shipping Charge</td>
					   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$shipping_charge.'</td>
					 
					 </tr>
					 <tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Final Amount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black"><i class="fa fa-inr"></i> '.$finalAmt.'</td>
				 
				 </tr>';
					 
					
					 }
				}
             $htmlAdmin.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Payment mode</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$payment_mode.'</td>
				 
				 </tr>';

			
           $htmlAdmin.=' </tbody></table></td>
      </tr>
 
      </tbody>
    </table></td>
</tr>
<tr>
  <td align="center" style="padding-top:0px"><table align="center" cellpadding="0" cellspacing="0" class="m_2299037813007463152container" style="width:100%">
    <tbody><tr>
      <td align="center" style="text-align:center;vertical-align:top;font-size:0;padding:10px">
        <div style="width:240px;display:inline-block;vertical-align:top;text-align:center;font-size:0" align="center">
          <table width="220" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px">
            <tbody><tr>
              <td width="30" align="center" style="padding:5px"><a href="'.base_url().'" target="_blank" data-saferedirecturl="'.base_url().'"><img src="'.base_url().'assets/template/images/icon/www.png" alt="" width="30" height="30" style="margin:0" border="0" vspace="0" hspace="0" align="absmiddle" class="CToWUd"></a></td>
              <td align="left" style="padding:0px 0px;font-size:16px;font-family: Open Sans,Gill Sans,Arial,Helvetica,sans-serif"><a href="'.base_url().'" style="color:#444444;text-decoration:none" target="_blank" data-saferedirecturl="'.base_url().'">Ghar Ghar Bazaar</a></td>
              </tr>
            </tbody></table>
          </div>
        
        <div style="width:200px;display:inline-block;vertical-align:top;text-align:center;font-size:0" align="center">
          <table width="180" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px">
            <tbody><tr>
              <td width="30" align="center" style="padding:5px"><a href="'.base_url().'" target="_blank" data-saferedirecturl="'.base_url().'"><img src="'.base_url().'assets/template/images/icon/call.png" alt="" width="30" height="30" style="margin:0" border="0" vspace="0" hspace="0" align="absmiddle" class="CToWUd"></a></td>
              <td align="left" style="padding:0px 0px;font-size:16px;font-family:Open Sans,Gill Sans,Arial,Helvetica,sans-serif"><a style="color:#444444;text-decoration:none" href="tel:'.$siteDetails[0]['company_phone'].'" target="_blank">'.$siteDetails[0]['company_phone'].'</a></td>
              </tr>
            </tbody></table>
          </div>
        </td>
      </tr>
    </tbody></table></td>
</tr>
</tbody></table>';
            $webDetails=$this->adminDetails();
            $fromAdmin="no-reply@ghargharbazaar.com";
			$subjectAdmin="New Order Recieved on Ghar Ghar Bazaar";
			$toAdmin= $webDetails[0]['company_email'];			
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'From: '.$fromAdmin . "\r\n";
			mail($toAdmin,$subjectAdmin,$htmlAdmin,$headers);
						
			/***************************** Admin Mail Ends *************************/
		
		$data['status']="True";
		$data['msg']="Done";
		echo json_encode($data);
	}
	
	
	public function cashonDelivery($orderId)
	{
		
		$siteDetails=$this->adminDetails();
		
		            $this->db->update('salo_temp_cart', array('status' => 1) , array('order_id' => $orderId)); 
					 
					 $this->db->update('salo_cart_shipping_details', array('payment_mode' => 'Cash On Delivery (COD)') , array('order_id' => $orderId)); 
					 
					 $prot = $this->admin_model->getBasic("SELECT * FROM  salo_temp_cart WHERE order_id='$orderId'");
			         $protype = $prot[0]['product_type'];
					 $bookHead="Shipping Address";
					 
					 $orderData=$this->admin_model->getData('salo_temp_cart',array('order_id' => $orderId),null);
					 
                     $ordershippingData=$this->admin_model->getData('salo_cart_shipping_details',array('order_id' => $orderId),null);
					 
					 $userId=$ordershippingData[0]['user_id'];
					 
	                 $userData=$this->admin_model->getData('salo_users',array('id' => $userId),null);
					 
					$name=$userData[0]['name'];
					$booking_name=$ordershippingData[0]['name'];
					$booking_phone=$ordershippingData[0]['contact'];
					$booking_email=$ordershippingData[0]['email'];
					$delivery_date=$ordershippingData[0]['delivery_date'];
					$delivery_time=$ordershippingData[0]['delivery_time'];
					
				    $delivery_date=date('F j,Y',strtotime($delivery_date));
					
					
					$timeSlot=$this->admin_model->getData('salo_time_slot',array('id' => $delivery_time),null);
					$start = date("g:i a", strtotime($timeSlot[0]['start']));
					$end = date("g:i a", strtotime($timeSlot[0]['end']));
					
					
					$booking_address=$ordershippingData[0]['address'].",".$ordershippingData[0]['city'].",".$ordershippingData[0]['state'].",".$ordershippingData[0]['country'].",".$ordershippingData[0]['pincode'];
					
					
		
/***************************** User Mail Starts *************************/		
		
		
$html='<table cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" style="background:#ffffff;border:1px solid #e3e3e3;border-radius:3px;max-width:640px">
<tbody><tr>
  <td width="620" align="center"><table border="0" cellpadding="0" cellspacing="0" align="center" style="max-width:620px">
    <tbody><tr>
      <td align="center" style="border-bottom:1px dotted #e3e3e3"><a href="'.base_url().'" target="_blank" data-saferedirecturl="'.base_url().'">
        <div style="width:100%"><img src="'.base_url().'assets/uploads/logo/'.$siteDetails[0]['company_logo'].'" border="0" alt="" style="margin:0;display:block;max-width:200px;width:inherit" vspace="0" hspace="0" align="center" class="CToWUd"></div>
        </a></td>
    </tr>

    </tbody></table></td>
</tr>
<tr>
  <td align="center" style="padding:0px"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-family:arial;font-size:12px;text-align:left;color:#333333">
    <tbody>
      <tr>
        <td style="padding:0px 0px"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" style="max-width:600px;font-family:arial;font-size:14px;text-align:left;color:#333333">
          <tbody>
            <tr>
              <td style="padding:10px">Dear '.$name.' ,</td>
              </tr>
            <tr>
              <td style="padding:0px 10px 20px 10px">We have received your order. We will be calling you shortly to confirm your order. In case you are not able to pick up our call, do not worry. We will be calling multiple times before going ahead with the cancellation. </td>
              </tr>
            
            </tbody>
          </table></td>
        </tr>
      <tr>
        <td style="padding:0px 10px"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tbody>
            <tr>
              <td bgcolor="#84a743" style="border-bottom:1px solid #4f7605;padding:10px;font-size:14px"><strong style="color:white">Shipping Address</strong><strong style="float:right;text-align:right;color:white">Order Id :  '.$orderid.'</strong></td>
              </tr>
            <tr>
              <td style="font-size:0;padding:0 10px 10px 10px" align="left" bgcolor="#dff2e0">
                <table width="200" cellpadding="0" cellspacing="0" border="0" align="left" style="font-family:arial;font-size:12px;text-align:left;color:#333333">
                  <tbody><tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top" width="60"><strong>Name</strong></td>
                    <td valign="top">'.$booking_name.' </td>
                    </tr>
                  <tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top"><strong>Email</strong></td>
                    <td valign="top"><a href="#m_2299037813007463152_" style="display:block;color:#333333;word-break:break-all;text-decoration:none">'.$booking_email.'</a></td>
                    </tr>
					 <tr>
                    <td valign="top"><strong>Delivery Time</strong></td>
                    <td valign="top"><a href="#m_2299037813007463152_" style="display:block;color:#333333;word-break:break-all;text-decoration:none"> '.$delivery_date.' between '.$start .' - '.$end.'</a></td>
                    </tr>
					 <tr>
                    <td valign="top"><strong>Before Time</strong></td>
                    <td valign="top"><a href="#m_2299037813007463152_" style="display:block;color:#333333;word-break:break-all;text-decoration:none">'.$btime.'</a></td>
                    </tr>
                  </tbody></table>
                
                <table width="200" cellpadding="0" cellspacing="0" border="0" align="left" style="font-family:arial;font-size:12px;text-align:left;color:#333333">
                  <tbody><tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top" width="60"><strong>Mobile</strong></td>
                    <td valign="top">'.$booking_phone.'</td>
                    </tr>
                  <tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top"><strong>Address</strong></td>
                    <td valign="top">'.$booking_address.'</td>
                    </tr>
                  </tbody></table>';
				  
				$html.='</td>
              </tr>
            </tbody>
          </table></td>
        </tr>
      <tr>
        <td height="20"></td>
        </tr>
      <tr>
        <td width="600" align="center" style="padding:0px 10px">
          
          
          
          <table width="98%" border="0" bgcolor="#dff2e0" align="center" cellpadding="8" cellspacing="0" style="font-family:arial;font-size:12px;text-align:left;color:#333333;max-width:600px">
            <tbody>
			
			    <tr>
					 <th style="padding:3px;border:1px solid black">Category</th>
					 <th style="padding:3px;border:1px solid black">Product</th>
					 <th style="padding:3px;border:1px solid black">Price</th>
					 <th style="padding:3px;border:1px solid black">Discount(%)</th>';
					
					 $html.='<th style="padding:3px;border:1px solid black">Qty</th>
					 <th style="padding:3px;border:1px solid black">Attribute</th>
					 <th style="padding:3px;border:1px solid black">Total</th>
			    </tr>';
		
         $totQty=0;		
         $totPrc=0;		
         $totDur=0;		
		foreach($orderData as $rw)
		{
		    $pid=$rw['pro_id'];
			
			    $product_attribute = $rw['product_attribute'] ;
			    $getatt=$this->db->query("SELECT * FROM salo_product_attribute WHERE `id`='$product_attribute'")->result_array();
				$unitid = $getatt[0]['unit_id'];
				$attvalue = $getatt[0]['attribute_value'];
				$unitatt=$this->db->query("SELECT * FROM salo_unit WHERE `id`='$unitid'")->result_array();
				$unitname = $unitatt[0]['unit'];
			
			
			
			
			$proData=$this->admin_model->getData('salo_products',array('id' => $pid),null);
			$catid=$proData[0]['sub_category_id'];
			$proTitle=$proData[0]['title'];
			
			
		    $subCatData=$this->admin_model->getData('salo_sub_category',array('sub_category_id' => $catid),null);
			$sub_category_name=$subCatData[0]['sub_category_name'];
			
			
				$html.=' <tr>
				 
						   <td style="padding:3px;border:1px solid black">'.$sub_category_name.'</td>
						   <td style="padding:3px 10px 3px 3px;border:1px solid black">'.$proTitle.'</td>
						   <td style="text-align:center;padding:3px;border:1px solid black">'.$rw['unit_price'].'</td>
						   <td style="text-align:center;padding:3px;border:1px solid black">'.$rw['discount'].'</td>';
						    $html.='<td style="padding:3px;text-align:center;border:1px solid black">'.$rw['quantity'].'</td>
						    <td style="text-align:center;padding:3px;border:1px solid black">'.$attvalue .' '.$unitname.'</td>
						   <td style="text-align:center;padding:3px;border:1px solid black">'.$rw['total_price'].'</td>
						   
						</tr>';
				
				$totQty= $totQty + $rw['quantity'];
				$totPrc= $totPrc + $rw['total_price'];
				$totDur= $totDur + $proData[0]['duration'];
			}
				$html.='<tr>
					<td colspan="4" style="text-align:right;padding:3px;border:1px solid black">Total</td>';
					
					$html.='<td style="padding:3px;text-align:right;border:1px solid black">'.$totQty.'</td><td style="padding:3px;text-align:right;border:1px solid black"></td>
					<td style="text-align:right;padding:3px;border:1px solid black">Rs. '.$totPrc.'</td>
				</tr>';
				
				if($ordershippingData[0]['promo_code_id']!=0)
		        {		
                   $shipping_charge=$ordershippingData[0]['shipping_charge'];
                   $dis=$ordershippingData[0]['promo_discount'];
                   $final_amount=$ordershippingData[0]['final_amount'];			  
				    $html.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Promo Discount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$dis.'</td>
				 
				 </tr>';
				 
				 if($shipping_charge!=0)
				 {
				   $html.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Shipping Charge</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$shipping_charge.'</td>
				 
				 </tr>';
				 
				 $finalAmt = $final_amount + $shipping_charge;
				 }
				 else
				 {
				   $finalAmt = $final_amount;
				 }
				$html.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Final Amount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black"><i class="fa fa-inr"></i> '.$finalAmt.'</td>
				 
				 </tr>';
		        }
				else
				{
				  $shipping_charge=$ordershippingData[0]['shipping_charge'];
				  $final_amount=$ordershippingData[0]['final_amount'];
				   if($shipping_charge!=0)
					 {
					   $finalAmt = $final_amount + $shipping_charge;
					   $html.='<tr>
					
					   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Shipping Charge</td>
					   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$shipping_charge.'</td>
					 
					 </tr>
					 <tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Final Amount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black"><i class="fa fa-inr"></i> '.$finalAmt.'</td>
				 
				 </tr>';
					 
					
					 }
				}
			$html.='<tr>
				<td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Payment mode</td>
				<td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$payment_mode.'</td>
			</tr>';
            $html.='</tbody></table></td>
      </tr>
 
      </tbody>
    </table></td>
</tr>
<tr>
  <td align="center" style="padding-top:0px"><table align="center" cellpadding="0" cellspacing="0" class="m_2299037813007463152container" style="width:100%">
    <tbody><tr>
      <td align="center" style="text-align:center;vertical-align:top;font-size:0;padding:10px">
        <div style="width:240px;display:inline-block;vertical-align:top;text-align:center;font-size:0" align="center">
          <table width="220" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px">
            <tbody><tr>
              <td width="30" align="center" style="padding:5px"><a href="'.base_url().'" target="_blank" data-saferedirecturl="'.base_url().'"><img src="'.base_url().'assets/template/images/icon/www.png" alt="" width="30" height="30" style="margin:0" border="0" vspace="0" hspace="0" align="absmiddle" class="CToWUd"></a></td>
              <td align="left" style="padding:0px 0px;font-size:16px;font-family: Open Sans,Gill Sans,Arial,Helvetica,sans-serif"><a href="'.base_url().'" style="color:#444444;text-decoration:none" target="_blank" data-saferedirecturl="'.base_url().'">Ghar Ghar Bazaar</a></td>
              </tr>
            </tbody></table>
          </div>
        
        <div style="width:200px;display:inline-block;vertical-align:top;text-align:center;font-size:0" align="center">
          <table width="180" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px">
            <tbody><tr>
              <td width="30" align="center" style="padding:5px"><a href="'.base_url().'" target="_blank" data-saferedirecturl="'.base_url().'"><img src="'.base_url().'assets/template/images/icon/call.png" alt="" width="30" height="30" style="margin:0" border="0" vspace="0" hspace="0" align="absmiddle" class="CToWUd"></a></td>
              <td align="left" style="padding:0px 0px;font-size:16px;font-family:Open Sans,Gill Sans,Arial,Helvetica,sans-serif"><a style="color:#444444;text-decoration:none" href="tel:'.$siteDetails[0]['company_phone'].'" target="_blank">'.$siteDetails[0]['company_phone'].'</a></td>
              </tr>
            </tbody></table>
          </div>
        </td>
      </tr>
    </tbody></table></td>
</tr>
</tbody></table>';

                        $from="no-reply@ghargharbazaar.com";
						$subject="Thank You for shopping with ".$siteDetails[0]['company_name'];
						$to=$userData[0]['email'];			
						$headers  = 'MIME-Version: 1.0' . "\r\n";
						$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
						$headers .= 'From: '.$from . "\r\n";
						mail($to,$subject,$html,$headers);


						
/***************************** User Mail Ends *************************/

						
						
/***************************** Admin Mail Starts *************************/						
						
						
$htmlAdmin='<table cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" style="background:#ffffff;border:1px solid #e3e3e3;border-radius:3px;max-width:640px">
<tbody><tr>
  <td width="620" align="center"><table border="0" cellpadding="0" cellspacing="0" align="center" style="max-width:620px">
    <tbody><tr>
      <td align="center" style="border-bottom:1px dotted #e3e3e3"><a href="'.base_url().'" target="_blank" data-saferedirecturl="'.base_url().'">
        <div style="width:100%"><img src="'.base_url().'assets/uploads/logo/'.$siteDetails[0]['company_logo'].'" border="0" alt="" style="margin:0;display:block;max-width:200px;width:inherit" vspace="0" hspace="0" align="center" class="CToWUd"></div>
        </a></td>
    </tr>

    </tbody></table></td>
</tr>
<tr>
  <td align="center" style="padding:0px"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-family:arial;font-size:12px;text-align:left;color:#333333">
    <tbody>
      <tr>
        <td style="padding:0px 0px"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" style="max-width:600px;font-family:arial;font-size:14px;text-align:left;color:#333333">
          <tbody>
            <tr>
              <td style="padding:10px">Hello ,</td>
              </tr>
            <tr>
              <td style="padding:0px 10px 20px 10px">You have recieved a new order from '.$name.'.Here are the details.</td>
              </tr>
            </tbody>
          </table></td>
        </tr>
      <tr>
        <td style="padding:0px 10px"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tbody>
            <tr>
              <td bgcolor="#84a743" style="border-bottom:1px solid #4f7605;padding:10px;font-size:14px"><strong style="color:white">Shipping Address</strong><strong style="float:right;text-align:right;color:white">Order Id :  '.$orderid.'</strong></td>
              </tr>
            <tr>
              <td style="font-size:0;padding:0 10px 10px 10px" align="left" bgcolor="#dff2e0">
                <table width="200" cellpadding="0" cellspacing="0" border="0" align="left" style="font-family:arial;font-size:12px;text-align:left;color:#333333">
                  <tbody><tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top" width="60"><strong>Name</strong></td>
                    <td valign="top">'.$booking_name.' </td>
                    </tr>
                  <tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top"><strong>Email</strong></td>
                    <td valign="top"><a href="#m_2299037813007463152_" style="display:block;color:#333333;word-break:break-all;text-decoration:none">'.$booking_email.'</a></td>
                    </tr>
					 <tr>
                    <td valign="top"><strong>Delivery Time</strong></td>
                    <td valign="top"><a href="#m_2299037813007463152_" style="display:block;color:#333333;word-break:break-all;text-decoration:none"> '.$delivery_date.' between '.$start .' - '.$end.'</a></td>
                    </tr>
					 <tr>
                    <td valign="top"><strong>Before Time</strong></td>
                    <td valign="top"><a href="#m_2299037813007463152_" style="display:block;color:#333333;word-break:break-all;text-decoration:none">'.$btime.'</a></td>
                    </tr>
                  </tbody></table>
                
                <table width="200" cellpadding="0" cellspacing="0" border="0" align="left" style="font-family:arial;font-size:12px;text-align:left;color:#333333">
                  <tbody><tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top" width="60"><strong>Mobile</strong></td>
                    <td valign="top">'.$booking_phone.'</td>
                    </tr>
                  <tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top"><strong>Address</strong></td>
                    <td valign="top">'.$booking_address.'</td>
                    </tr>
                  </tbody></table>';
				  
				
				
                $htmlAdmin.='</td>
              </tr>
            </tbody>
          </table></td>
        </tr>
      <tr>
        <td height="20"></td>
        </tr>
      <tr>
        <td width="600" align="center" style="padding:0px 10px">
          
           <table width="98%" border="0" bgcolor="#dff2e0" align="center" cellpadding="8" cellspacing="0" style="font-family:arial;font-size:12px;text-align:left;color:#333333;max-width:600px">
            <tbody>
			
			    <tr>
					 <th style="padding:3px;border:1px solid black">Category</th>
					 <th style="padding:3px;border:1px solid black">Product</th>
					 <th style="padding:3px;border:1px solid black">Price</th>
					 <th style="padding:3px;border:1px solid black">Discount(%)</th>';
					 $htmlAdmin.='<th style="padding:3px;border:1px solid black">Qty</th>
					 <th style="padding:3px;border:1px solid black">Attribute</th>
					 <th style="padding:3px;border:1px solid black">Total</th>
			    </tr>';
		
        $totQty=0;		
        $totPrc=0;		
        $totDur=0;		
		foreach($orderData as $rw)
		{
		        $pid=$rw['pro_id'];
			    $product_attribute = $rw['product_attribute'] ;
			    $getatt=$this->db->query("SELECT * FROM salo_product_attribute WHERE `id`='$product_attribute'")->result_array();
				$unitid = $getatt[0]['unit_id'];
				$attvalue = $getatt[0]['attribute_value'];
				$unitatt=$this->db->query("SELECT * FROM salo_unit WHERE `id`='$unitid'")->result_array();
				$unitname = $unitatt[0]['unit'];
			
			$proData=$this->admin_model->getData('salo_products',array('id' => $pid),null);
			$catid=$proData[0]['sub_category_id'];
			$proTitle=$proData[0]['title'];
			
			
		    $subCatData=$this->admin_model->getData('salo_sub_category',array('sub_category_id' => $catid),null);
			$sub_category_name=$subCatData[0]['sub_category_name'];
			
			
				$htmlAdmin.=' <tr>
				           <td style="padding:3px;border:1px solid black">'.$sub_category_name.'</td>
						   <td style="padding:3px 10px 3px 3px;border:1px solid black">'.$proTitle.'</td>
						   <td style="text-align:center;padding:3px;border:1px solid black">'.$rw['unit_price'].'</td>
						   <td style="text-align:center;padding:3px;border:1px solid black">'.$rw['discount'].'</td>';
						   $htmlAdmin.='<td style="padding:3px;text-align:center;border:1px solid black">'.$rw['quantity'].'</td>
						   <td style="text-align:center;padding:3px;border:1px solid black">'.$attvalue .' '.$unitname.'</td>
						   <td style="text-align:center;padding:3px;border:1px solid black">'.$rw['total_price'].'</td>
						   
						</tr>';
				
				$totQty= $totQty + $rw['quantity'];
				$totPrc= $totPrc + $rw['total_price'];
				$totDur= $totDur + $proData[0]['duration'];
			}
				$htmlAdmin.='<tr>
					<td colspan="4" style="text-align:right;padding:3px;border:1px solid black">Total</td>';
					$htmlAdmin.='<td style="padding:3px;text-align:right;border:1px solid black">'.$totQty.'</td><td style="padding:3px;text-align:right;border:1px solid black"></td>
					<td style="text-align:right;padding:3px;border:1px solid black">Rs. '.$totPrc.'</td>
				</tr>';
				
			 if($ordershippingData[0]['promo_code_id']!=0)
		        {		
                   $shipping_charge=$ordershippingData[0]['shipping_charge'];
                   $dis=$ordershippingData[0]['promo_discount'];
                   $final_amount=$ordershippingData[0]['final_amount'];			  
				    $htmlAdmin.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Promo Discount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$dis.'</td>
				 
				 </tr>';
				 
				 if($shipping_charge!=0)
				 {
				   $htmlAdmin.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Shipping Charge</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$shipping_charge.'</td>
				 
				 </tr>';
				 
				 $finalAmt = $final_amount + $shipping_charge;
				 }
				 else
				 {
				   $finalAmt = $final_amount;
				 }
				$htmlAdmin.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Final Amount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black"><i class="fa fa-inr"></i> '.$finalAmt.'</td>
				 
				 </tr>';
		        }
				else
				{
				  $shipping_charge=$ordershippingData[0]['shipping_charge'];
				  $final_amount=$ordershippingData[0]['final_amount'];
				   if($shipping_charge!=0)
					 {
					   $finalAmt = $final_amount + $shipping_charge;
					   $htmlAdmin.='<tr>
					
					   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Shipping Charge</td>
					   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$shipping_charge.'</td>
					 
					 </tr>
					 <tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Final Amount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black"><i class="fa fa-inr"></i> '.$finalAmt.'</td>
				 
				 </tr>';
					 
					
					 }
				}
             $htmlAdmin.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Payment mode</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$payment_mode.'</td>
				 
				 </tr>';

			
           $htmlAdmin.=' </tbody></table></td>
      </tr>
 
      </tbody>
    </table></td>
</tr>
<tr>
  <td align="center" style="padding-top:0px"><table align="center" cellpadding="0" cellspacing="0" class="m_2299037813007463152container" style="width:100%">
    <tbody><tr>
      <td align="center" style="text-align:center;vertical-align:top;font-size:0;padding:10px">
        <div style="width:240px;display:inline-block;vertical-align:top;text-align:center;font-size:0" align="center">
          <table width="220" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px">
            <tbody><tr>
              <td width="30" align="center" style="padding:5px"><a href="'.base_url().'" target="_blank" data-saferedirecturl="'.base_url().'"><img src="'.base_url().'assets/template/images/icon/www.png" alt="" width="30" height="30" style="margin:0" border="0" vspace="0" hspace="0" align="absmiddle" class="CToWUd"></a></td>
              <td align="left" style="padding:0px 0px;font-size:16px;font-family: Open Sans,Gill Sans,Arial,Helvetica,sans-serif"><a href="'.base_url().'" style="color:#444444;text-decoration:none" target="_blank" data-saferedirecturl="'.base_url().'">Ghar Ghar Bazaar</a></td>
              </tr>
            </tbody></table>
          </div>
        
        <div style="width:200px;display:inline-block;vertical-align:top;text-align:center;font-size:0" align="center">
          <table width="180" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px">
            <tbody><tr>
              <td width="30" align="center" style="padding:5px"><a href="'.base_url().'" target="_blank" data-saferedirecturl="'.base_url().'"><img src="'.base_url().'assets/template/images/icon/call.png" alt="" width="30" height="30" style="margin:0" border="0" vspace="0" hspace="0" align="absmiddle" class="CToWUd"></a></td>
              <td align="left" style="padding:0px 0px;font-size:16px;font-family:Open Sans,Gill Sans,Arial,Helvetica,sans-serif"><a style="color:#444444;text-decoration:none" href="tel:'.$siteDetails[0]['company_phone'].'" target="_blank">'.$siteDetails[0]['company_phone'].'</a></td>
              </tr>
            </tbody></table>
          </div>
        </td>
      </tr>
    </tbody></table></td>
</tr>
</tbody></table>';
            $webDetails=$this->adminDetails();
            $fromAdmin="no-reply@ghargharbazaar.com";
			$subjectAdmin="New Order Recieved on Ghar Ghar Bazaar";
			$toAdmin= $webDetails[0]['company_email'];			
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'From: '.$fromAdmin . "\r\n";
			mail($toAdmin,$subjectAdmin,$htmlAdmin,$headers);
						
			/***************************** Admin Mail Ends *************************/
		
		$data['status']="True";
		$data['msg']="Done";
		echo json_encode($data);
	}
	
	public function getTimeSlot()
	{
		$data['status']="True";
		$data['data']= $this->admin_model->getBasic("SELECT * FROM salo_time_slot order by id asc");
		$data['msg']="Done";
		echo json_encode($data);
	}
	
	
	
	public function thankyou($oid)
	{
	  $orderData=$this->admin_model->getData('salo_temp_cart',array('order_id' => $oid),null);
	  $orderDataArr=array();
	  foreach($orderData as $rw)
	  {
		   
		$pid=$rw['pro_id'];
		$attid=$rw['product_attribute'];
		
	    $quantity=$rw['quantity'];
	    $total_price=$rw['total_price'];
	    $unit_price=$rw['unit_price'];
	    $discount=$rw['discount'];
		
	    $proData=$this->admin_model->getData('salo_products',array('id' => $pid),null);
		
		$id=$lrr['id'];
		$title=$lrr['title'];
		$image=$lrr['image'];
	
		if($image!="")
		{
		  $imageUrl=$this->config->item('base_image_url')."assets/uploads/products/".$image;
		}
		else
		{
		  $imageUrl="";
		}
				
		$Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE id='$attid'");
		$prr=$Productprice[0];

		$attribute_value = $prr['attribute_value'];
		$unit_id = $prr['unit_id'];
				
		$unit = $this->admin_model->getData('salo_unit',array('id' => $unit_id),null);
		$unitName=$unit[0]['unit'];
				  
		$attribute = $attribute_value." ".$unitName;
				 
				 
		$arrDetail=array('product_name' => $title,'image' => $imageUrl,'attribute' => $attribute,'quantity' => $quantity,'unit_price' => $unit_price,'discount' => $discount,'total_price' => $total_price);
		array_push($orderDataArr,$arrDetail);
	    $a++;
	  }
	  
	  $shippingDetails=$this->admin_model->getData('salo_cart_shipping_details',array('order_id' => $oid),null);
	  $user_id=$shippingDetails[0]['user_id'];
	  $order_status=$shippingDetails[0]['order_status'];
		   
		   switch($order_status)
		   {
			   case "0":
			     $orderStatus="Pending";
				 
				 $orderArr=array('order_status' => $orderStatus);
			   break;
			   
			   case "1":
			     $orderStatus="Accepted";
				 
				 $orderArr=array('order_status' => $orderStatus);
			   break;
			   
			   case "2":
			     $orderStatus="Ready To Dispatch";
				 
				 $orderArr=array('order_status' => $orderStatus);
			   break;
			   
			    case "3":
			     $orderStatus="Dispatched";
				 
				 $orderArr=array('order_status' => $orderStatus);
			   break;
			   
			    case "4":
			     $orderStatus="Declined";
				 
				 $orderArr=array('order_status' => $orderStatus);
			   break;
			   
			   case "5":
			     $orderStatus="Cancelled";
				 $reason=$shippingDetails[$a][0]['remark'];
				 $orderArr=array('order_status' => $orderStatus,'cancellation_reason' => $reason);
			   break;
			   
			   case "6":
			     $orderStatus="Completed";
				 $orderArr=array('order_status' => $orderStatus);
			   break;
		   }
		   
	  $qtyList=$this->admin_model->getBasic("SELECT SUM(quantity) as qty FROM salo_temp_cart WHERE order_id='$oid'");
      $qty=$qtyList[0]['qty'];
	  

	  $shipping_charge=$orderData[0]['shipping_charge'];
	  $grand_total=$orderData[0]['grand_total'];
	  $final_amount=$orderData[0]['final_amount'];
	  $promo_code_id=$orderData[0]['promo_code_id'];
	  
	  $finalAmt = $final_amount + $shipping_charge;
	   
	  if($promo_code_id!=0)
	  {
		  $promo_code=$orderData[0]['promo_code'];
		  $grand_total=$orderData[0]['grand_total'];
		  $promo_discount=$orderData[0]['promo_discount'];
		  
		  $arr=array('order_id' => $oid,'quantity' => $qty,'promo_code' => $promo_code,'grand_total' => $grand_total,'promo_discount' => $promo_discount,'shipping_charge' => $shipping_charge,'final_amount' => $finalAmt);
		  $prArry=array();
		 
	  }
	  else
	  {
		$arr=array('order_id' => $oid,'grand_total' => $grand_total,'shipping_charge' => $shipping_charge,'quantity' => $qty,'final_amount' => $finalAmt);
	  }

	  $arr=$arr + $orderArr;
	  
	  $data['status']="True";
	  $data['orderDetail']=$arr;
	  $data['itemList']=$orderDataArr;
	  $data['shippingAddress']=$shippingDetails;
	  $data['msg']="Thank you for shopping with us";
	  echo json_encode($data);
	}
	
	
	
	public function orderHistory($uid)
	{
		$a=0;
		$orderArray=array();
		$orderDetail=$this->admin_model->getBasic("SELECT DISTINCT order_id FROM salo_temp_cart WHERE user_id='$uid' and status='1'");
		foreach($orderDetail as $row_or)
		{
		   $orderId=$row_or['order_id'];
		   
		   $qtyList=$this->admin_model->getBasic("SELECT SUM(quantity) as qty FROM salo_temp_cart WHERE order_id='$orderId'");
		
		   $shippingDetails=$this->admin_model->getBasic("SELECT * FROM salo_cart_shipping_details WHERE order_id='$orderId'");
		   
		   $ordersList=$this->admin_model->getBasic("SELECT * FROM salo_temp_cart WHERE order_id='$orderId'");
		   $grand_total=$ordersList[0]['grand_total'];
		   $promo_code_id=$ordersList[0]['promo_code_id'];
		   $shipping_charge=$ordersList[0]['shipping_charge'];
		   $final_amount=$ordersList[0]['final_amount'];
		   
		   $added_on=$shippingDetails[0]['added_on'];
		   $order_status=$shippingDetails[0]['order_status'];
		   
		   $finalAmt = $final_amount + $shipping_charge;
		   
		   switch($order_status)
		   {
			   case "0":
			     $orderStatus="Pending";
				 
				 $orderArr=array('order_status' => $orderStatus);
			   break;
			   
			   case "1":
			     $orderStatus="Accepted";
				 
				 $orderArr=array('order_status' => $orderStatus);
			   break;
			   
			   case "2":
			     $orderStatus="Ready To Dispatch";
				 
				 $orderArr=array('order_status' => $orderStatus);
			   break;
			   
			    case "3":
			     $orderStatus="Dispatched";
				 
				 $orderArr=array('order_status' => $orderStatus);
			   break;
			   
			    case "4":
			     $orderStatus="Declined";
				 
				 $orderArr=array('order_status' => $orderStatus);
			   break;
			   
			   case "5":
			     $orderStatus="Cancelled";
				 $reason=$shippingDetails[$a][0]['remark'];
				 $orderArr=array('order_status' => $orderStatus,'cancellation_reason' => $reason);
			   break;
			   
			   case "6":
			     $orderStatus="Completed";
				 $orderArr=array('order_status' => $orderStatus);
			   break;
		   }
		   
		   if($promo_code_id==0)
		   {
		     $disAmt="0";
		   }
		   else
		   {
		    $disAmt=$grand_total - $final_amount;
		   }
		   
		   $qty=$qtyList[0]['qty'];
		   $arr=array('order_id' => $orderId,'total_quantity' => $qty,'grand_total' => $grand_total,'discount_amount' => $disAmt,'shipping_charge' => $shipping_charge,'final_amount' => $finalAmt,'order_date' => $added_on,'shipping_address' => $shippingDetails);
		   $arr=$arr + $orderArr;
		   array_push($orderArray,$arr);
		   $a++;
		}
		
		if(count($orderArray)!=0)
		{
		  $data['status']="True";
		  $data['data']=$orderArray;
		  $data['msg']="Done";
		}
		else
		{
		  $data['status']="False";
		  $data['msg']="No Result Found";			
		}

		echo json_encode($data);
	}
	
	public function getorderDetail($oid)
	{
	  $orderData=$this->admin_model->getData('salo_temp_cart',array('order_id' => $oid),null);
	  $orderDataArr=array();
	  foreach($orderData as $rw)
	  {
		   
		$pid=$rw['pro_id'];
		$attid=$rw['product_attribute'];
		
	    $quantity=$rw['quantity'];
	    $total_price=$rw['total_price'];
	    $unit_price=$rw['unit_price'];
	    $discount=$rw['discount'];
		
	    $proData=$this->admin_model->getData('salo_products',array('id' => $pid),null);
		$lrr=$proData[0];
		
		$id=$lrr['id'];
		$title=$lrr['title'];
		$image=$lrr['image'];
	
		if($image!="")
		{
		  $imageUrl=$this->config->item('base_image_url')."assets/uploads/products/".$image;
		}
		else
		{
		  $imageUrl="";
		}
				
		$Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE id='$attid'");
		$prr=$Productprice[0];

		$attribute_value = $prr['attribute_value'];
		$unit_id = $prr['unit_id'];
				
		$unit = $this->admin_model->getData('salo_unit',array('id' => $unit_id),null);
		$unitName=$unit[0]['unit'];
				  
		$attribute = $attribute_value." ".$unitName;
				 
				 
		$arrDetail=array('product_name' => $title,'image' => $imageUrl,'attribute' => $attribute,'quantity' => $quantity,'unit_price' => $unit_price,'discount' => $discount,'total_price' => $total_price);
		array_push($orderDataArr,$arrDetail);
	    $a++;
	  }
	  
	  $shippingDetails=$this->admin_model->getData('salo_cart_shipping_details',array('order_id' => $oid),null);
	  $user_id=$shippingDetails[0]['user_id'];
	  $delivery_time_id=$shippingDetails[0]['delivery_time'];
	  $order_status=$shippingDetails[0]['order_status'];
			   
	  $timeSlot=$this->admin_model->getBasic("SELECT * FROM salo_time_slot WHERE id='$delivery_time_id'");
      $start=$timeSlot[0]['start'];   
      $end=$timeSlot[0]['end'];   
	  $start=date("g:i a", strtotime($start));
	  $end=date("g:i a", strtotime($end));
	  $deliveryTime=$start." - ".$end;
	  
		   switch($order_status)
		   {
			   case "0":
			     $orderStatus="Pending";
				 
				 $orderArr=array('order_status' => $orderStatus);
			   break;
			   
			   case "1":
			     $orderStatus="Accepted";
				 
				 $orderArr=array('order_status' => $orderStatus);
			   break;
			   
			   case "2":
			     $orderStatus="Ready To Dispatch";
				 
				 $orderArr=array('order_status' => $orderStatus);
			   break;
			   
			    case "3":
			     $orderStatus="Dispatched";
				 
				 $orderArr=array('order_status' => $orderStatus);
			   break;
			   
			    case "4":
			     $orderStatus="Declined";
				 
				 $orderArr=array('order_status' => $orderStatus);
			   break;
			   
			   case "5":
			     $orderStatus="Cancelled";
				 $reason=$shippingDetails[$a][0]['remark'];
				 $orderArr=array('order_status' => $orderStatus,'cancellation_reason' => $reason);
			   break;
			   
			   case "6":
			     $orderStatus="Completed";
				 $orderArr=array('order_status' => $orderStatus);
			   break;
		   }
		   
	  $qtyList=$this->admin_model->getBasic("SELECT SUM(quantity) as qty FROM salo_temp_cart WHERE order_id='$oid'");
      $qty=$qtyList[0]['qty'];
	  
	  $shipping_charge=$orderData[0]['shipping_charge'];
	  $grand_total=$orderData[0]['grand_total'];
	  $final_amount=$orderData[0]['final_amount'];
	  $promo_code_id=$orderData[0]['promo_code_id'];
	  
	  $finalAmt=$final_amount + $shipping_charge;
	   
	  if($promo_code_id!=0)
	  {
		  $promo_code=$orderData[0]['promo_code'];
		  $grand_total=$orderData[0]['grand_total'];
		  $promo_discount=$orderData[0]['promo_discount'];
		  
		  $disAmt = $grand_total - $final_amount;
		  
		  $arr=array('order_id' => $oid,'quantity' => $qty,'promo_code' => $promo_code,'grand_total' => $grand_total,'promo_discount' => $promo_discount,'discount_amount' => $disAmt,'amount_after_discount' => $final_amount,'shipping_charge' => $shipping_charge,'final_amount' => $finalAmt);
		  $prArry=array();
		 
	  }
	  else
	  {
		$arr=array('order_id' => $oid,'quantity' => $qty,'promo_code' => '','grand_total' => $grand_total,'promo_discount' => '0','discount_amount' => '0','amount_after_discount' => $final_amount,'shipping_charge' => $shipping_charge,'final_amount' => $finalAmt);
	  }

	  $arr=$arr + $orderArr;
	  
      $art=array('deliverytime' => $deliveryTime);
	  array_push($shippingDetails,$art);
	  $data['status']="True";
	  $data['orderDetail']=$arr;
	  $data['itemList']=$orderDataArr;
	  $data['shippingAddress']=$shippingDetails;
	  $data['msg']="Done";
	  echo json_encode($data);
	  
	}
	
	public function trackOrder($oid)
	{
		
	  $orderData=$this->admin_model->getData('salo_temp_cart',array('order_id' => $oid),null);
	  $grand_total=$orderData[0]['grand_total'];
	  $promo_code_id=$orderData[0]['promo_code_id'];
	  $shipping_charge=$orderData[0]['shipping_charge'];
	  $final_amount=$orderData[0]['final_amount'];
	  
	  if($promo_code_id==0)
	  {
	    $disAmt="0";
	  }
	  else
	  {
	   $disAmt=$grand_total - $final_amount;
	  }
	  
	  $finalAmt=$final_amount + $shipping_charge;
	  
	  $qtyList=$this->admin_model->getBasic("SELECT SUM(quantity) as qty FROM salo_temp_cart WHERE order_id='$oid'");
      $qty=$qtyList[0]['qty'];
	  
	   $shippingDetails=$this->admin_model->getData('salo_cart_shipping_details',array('order_id' => $oid),null);
	   $user_id=$shippingDetails[0]['user_id'];
	   $order_status=$shippingDetails[0]['order_status'];
		   
		   switch($order_status)
		   {
			   case "0":
			     $orderStatus="Pending";
				 
				 $orderArr=array('order_status' => $orderStatus);
			   break;
			   
			   case "1":
			     $orderStatus="Accepted";
				 
				 $orderArr=array('order_status' => $orderStatus);
			   break;
			   
			   case "2":
			     $orderStatus="Ready To Dispatch";
				 
				 $orderArr=array('order_status' => $orderStatus);
			   break;
			   
			    case "3":
			     $orderStatus="Dispatched";
				 
				 $orderArr=array('order_status' => $orderStatus);
			   break;
			   
			    case "4":
			     $orderStatus="Declined";
				 
				 $orderArr=array('order_status' => $orderStatus);
			   break;
			   
			   case "5":
			     $orderStatus="Cancelled";
				 $reason=$shippingDetails[$a][0]['remark'];
				 $orderArr=array('order_status' => $orderStatus,'cancellation_reason' => $reason);
			   break;
			   
			   case "6":
			     $orderStatus="Completed";
				 $orderArr=array('order_status' => $orderStatus);
			   break;
		   }
		   
	  $arr=array('order_id' => $oid,'quantity' => $qty,'grand_total' => $grand_total,'discount_amount' => $disAmt,'amount_after_discount' => $final_amount,'shipping_charge' => $shipping_charge,'final_amount' => $finalAmt);
	  $arr=$arr + $orderArr;
	  
	  $data['status']="True";
	  $data['orderDetail']=$arr;
	  $data['msg']="Done";
	  echo json_encode($data);

	}
	
	public function cancelOrder()
	{
		$oid=$_REQUEST['orderid'];
		$cancellation_reason=$_REQUEST['cancellation_reason'];
		
		$up=$this->admin_model->updateData('salo_cart_shipping_details',array('order_status' => 5,'remark' => $cancellation_reason),array('order_id' => $oid));
		
        $data['status']="True";
        $data['msg']="Done";
		
		echo json_encode($data);
		
	}
	
	public function forgotPass()
	{
		$email=$_REQUEST['email'];
		if($email!="")
		{
			$logoUrl=$this->config->item('base_image_url');
			
		$checkpass = $this->admin_model->getBasic("SELECT * FROM salo_users WHERE email='$email'");
		if(count($checkpass) != 0){
			 $password=$checkpass[0]['password'];
			 $userMail='<div style="margin:5px;color:#333333;line-height:150%;font-family:Verdana,Arial,Helvetica"><div class="adM">
				</div><a href="'.$logoUrl.'" target="_blank" data-saferedirecturl="'.$logoUrl.'"><img alt="Ghar Ghar Bazaar" hspace="5" vspace="15" border="0" src="'.$logoUrl.'assets/uploads/logo/logo.png" class="CToWUd" style="width:200px;"></a><br>
				<br>
				<br>Hello,
				<br><br>
				Someone requested for your password of Ghar Ghar Bazaar Account.Here is your password.
				<br><br><br>
				<table cellspacing="1" cellpadding="10" style="margin:5px 15px;padding:5px;background-color:#eeeeee;color:#333333;font-family:Verdana,Arial,Helvetica">
					<tbody><tr style="background-color:#ffffff">
						<td width="150">Password    : </td><td>'.$password.' &nbsp;</td>
					</tr>
				</tbody></table>
				<br><br><br>
				Best Regards,
				<br>
				Ghar Ghar Bazaar</div>';
				 
                $email = $checkpass[0]['email'];
				
                $subject="Ghar Ghar Bazaar Account Password";
	
		        $from="no-reply@ghargharbazaar.com";
                $emailid = $email;			
                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers .= 'From: '.$from . "\r\n";
                mail($emailid,$subject,$userMail,$headers);
		
        $data['status']="True";
        $data['msg']="Done";
		
		 }
		 else
		 {
			$data['status']="False";
            $data['msg']="Email not registered";
		 }
	   }
	   else
		 {
			$data['status']="False";
            $data['msg']="Email required";
		 }
			 
			
		echo json_encode($data);
		
	}
	
	
	
	public function getpacksizeother(){
		
		$limit = count($_REQUEST['packsize']);
		for($i= 0; $i<$limit; $i++)
		{
		 $getselected[] = $_REQUEST['packsize'][$i]	;
		}
		
		
		$catId = $_REQUEST['cat_id'];
		$page_name = $_REQUEST['page_name'];
		
		if($page_name == 'cat_lvl1'){
		 $allProList = $this->admin_model->getData('salo_products',array('category_id' =>$catId,'status' => 0,'product_type' => 1),'AND');
	   	}
		else if($page_name == 'cat_lvl2'){
		 $allProList = $this->admin_model->getData('salo_products',array('sub_category_id' =>$catId,'status' => 0,'product_type' => 1),'AND');
	   	}
		else if($page_name == 'cat_lvl3'){
		 $allProList = $this->admin_model->getData('salo_products',array('sub_sub_category_id' =>$catId,'status' => 0,'product_type' => 1),'AND');
	   	}
		else if($page_name == 'brands'){
		 $allProList = $this->admin_model->getData('salo_products',array('brand_id' =>$catId,'status' => 0,'product_type' => 1),'AND');
	   	}
		else if($page_name == 'search'){
		 $allProList = $this->db->query("SELECT * FROM salo_products WHERE `status`='0' and lower(title) LIKE '%$catId%' ORDER BY id DESC")->result_array();
	   	}


	   $proarray = array();
	    foreach($allProList as $prow){
		  $proarray[] = $prow['id'];
		}
		
		
		$prodmp = implode(',',$proarray);
		
		if(count($getselected) > 0){
		   $attimp = implode(',',$getselected);
		   $produ = $this->db->query("SELECT * from salo_product_attribute where `id` IN ($attimp)")->result_array();
		   $allatt = array();
		   $allunits = array();
	       foreach($produ as $rrow){
		    $allatt[] = $rrow['attribute_value'];
			$allunits[] = $rrow['unit_id'];
		   }
		   
		    $limitone = count($allatt);
			 $prda = array();
		    for($i= 0; $i<$limitone; $i++)
		    {
			  $satt = $allatt[$i];
			  $sunit = $allunits[$i];
		      $checkl =  $this->db->query("SELECT * from salo_product_attribute where `attribute_value` = '$satt'  and `unit_id` = '$sunit' and product_id IN ($prodmp) group by product_id")->result_array();
		     
			  foreach($checkl as $asrow){
				 if (!in_array($asrow['product_id'], $prda))
                  {
                   $prda[] = $asrow['product_id']; 
				   $dsjkfkj[] = $asrow['product_id'].'_'. $asrow['id'] ; 
                  }
			  }
			}
		    $hjkhfd =  $dsjkfkj ;
		   
		  
		}else{
			
		    $checkl =  $this->admin_model->getBasic("SELECT * from salo_product_attribute where product_id IN ($prodmp) group by product_id");
		    foreach($checkl as $asrow){
			$dsjkfkj[] = $asrow['product_id'].'_'. $asrow['id'] ; 
			}
			$hjkhfd =  $dsjkfkj ;
		}
			 $limit = count($hjkhfd);
	  
			if($limit > 0){
			
			  $productArray=array();
			
			  for($i= 0; $i<$limit; $i++)
			  {
			     $asd = $hjkhfd[$i];		
			     $assd =	explode('_',$asd);

				 $pid = $assd[0];
				 $attid = $assd[1];
			   
			     $prodetails =  $this->admin_model->getBasic("SELECT * FROM salo_products WHERE id='$pid'");
			     $lr=$prodetails[0];
			   
			     $id=$lr['id'];
				 $title=$lr['title'];
				 $stock_status=$lr['stock_status'];
				 $image=$lr['image'];
				 $description=$lr['description'];
				 $catId=$lr['category_id'];
				 $subcatId=$lr['sub_category_id'];
				 $subsubcatId=$lr['sub_sub_category_id'];
				 $brandId=$lr['brand_id'];
				
					if($image!="")
					{
					 $imageUrl=$this->config->item('base_image_url')."assets/uploads/products/".$image;
					}
					else
					{
					 $imageUrl="";
					}
	
			   $Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE id='$attid' and `status`='1' ORDER BY price ASC LIMIT 1");
			   $pr=$Productprice[0];
		 
		      $attributeid = $pr['id'];
			  $attribute_value = $pr['attribute_value'];
			  $unit_id = $pr['unit_id'];
			  $price = $pr['price'];
			   if($price=="")
			  {
			    $price="0.00";
			  }
			  else
			  {
			    $price=$price;
			  }
			  $discount = $pr['discount'];
			  if($discount=="")
			  {
				  $discount="0";
				  $sellAmt=$price;
			  }
			  else
			  {
				   $discount=$discount;
				   $disper=$discount / 100;
				   $disamt = $price * $disper;
				   $sellAmt=$price - $disamt;
				   $sellAmt=round($sellAmt);
			  }
			  
			
			  $unit = $this->admin_model->getData('salo_unit',array('id' => $unit_id),null);
			  $unitName=$unit[0]['unit'];
			  
			 $attribute = $attribute_value." ".$unitName;
            
			 $arr = array('id' => $id,'title' => $title,'image' => $imageUrl,'attribute' => $attribute , 'listprice' => $price, 'saleprice' => $sellAmt, 'discount' => $discount,'stock_status' => $stock_status);
			
			 array_push($productArray,$arr);
			
			}
			
			$data['status']="True";
			$data['data']=$productArray;
			$data['msg']="Done";
			
			}
			else
			{
				$data['status']="False";
				$data['msg']="No Result Found";
			}
			
		   echo json_encode($data);
		
	}


	public function updateCart()
	{
		$cartid=$_REQUEST['cartid'];
		$pid=$_REQUEST['pid'];
		$qty=$_REQUEST['qty'];
		$attribute_id=$_REQUEST['attribute_id'];
		
		$getProAtt = $this->admin_model->getData('salo_product_attribute',array('id' => $attribute_id),null);
	    $attributid = $getProAtt[0]['id'];
	    $unitPrice = $getProAtt[0]['price'];
	    $discount = $getProAtt[0]['discount'];
		
		
		$getCart=$this->admin_model->getBasic("SELECT * FROM `salo_temp_cart` WHERE id='$cartid' and `status`='0'");
		$cartCount=count($getCart);
		$dateTime=date('Y-m-d H:i:s');
	    if($cartCount!=0)
	    {
			  $rowId=$getCart[0]['id'];
			  $userid=$getCart[0]['user_id'];
			  $device_id=$getCart[0]['device_id'];
			 
			 if(($userid==0) || ($userid==""))
			 {
				$conditionVariable="device_id='$device_id' and user_id='0'";  	
		        $conditionCol="device_id"; 	
		        $conditionColVar=$device_id; 		
			 }
			 else
			 {
				$conditionVariable="user_id='$userid'";  	
		        $conditionCol="user_id"; 	
		        $conditionColVar=$userid;  
			 }
			 
			  $finalQty = $qty;
		      if($discount !=''){
			  $disamt = $unitPrice * ($discount / 100);												 
			  $discountedprice = $unitPrice - $disamt ;	
			  $discountedprice =   round($discountedprice);
			  
			   $totalPrice=$finalQty * $discountedprice;
			  }else{
			   $totalPrice=$finalQty * $unitPrice;  
			  }
		     
	          $total_duration="";
			  
			  $up = $this->admin_model->updateData('salo_temp_cart',array('quantity' => $finalQty,'unit_price' => $unitPrice,'discount' =>$discount,'total_price' => $totalPrice,'updated_on' => $dateTime,'total_duration' => $total_duration,'product_attribute'=> $attributid),array('id' => $rowId));
			  
			  $getSum=$this->db->query("SELECT SUM(total_price) as tp FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0'")->result_array();
			  
			  $cDate=date('Y-m-d');
			  $getpro = $this->admin_model->getBasic("SELECT * FROM  salo_temp_cart WHERE $conditionVariable and `status`='0' and promo_code_id!='0' and promo_code_id IN (SELECT id FROM salo_coupon WHERE expiry_date>='$cDate' and `status`='0')");
			  if(count($getpro)!=0)
			  {
                 $discount=$getpro[0]['promo_discount'];
				 $totalPrice=$getSum[0]['tp'];
				 $dis=$discount / 100;
				 $disAmt = $totalPrice * $dis;
				 $finalAmt=$totalPrice - $disAmt;
				 $finalAmt=round($finalAmt,2);
				 
				 if($userid==0)
				 {
					$up=$this->admin_model->updateData('salo_temp_cart',array('grand_total' => $totalPrice,'final_amount' => $finalAmt),array('device_id' => $device_id,'user_id' => $userid,'status' => 0)); 
				 }
				 else
				 {
					$up=$this->admin_model->updateData('salo_temp_cart',array('grand_total' => $totalPrice,'final_amount' => $finalAmt),array('user_id' => $userid,'status' => 0));  
				 }
				 
				 
				 $getsData=$this->db->query("SELECT * FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0'")->result_array();
				 $orderid=$getsData[0]['order_id'];
				 if($orderid!="")
				 {
					$up=$this->admin_model->updateData('salo_cart_shipping_details',array('grand_total' => $totalPrice,'final_amount' => $finalAmt),array('order_id' => $orderid));
				 }
							
			  }
              else
			  {
					$totalPrice=$getSum[0]['tp'];
					
					 
					 if($userid==0)
					 {
						$up=$this->admin_model->updateData('salo_temp_cart',array('grand_total' => $totalPrice,'promo_code' => '','promo_code_id' => '0','promo_discount' => '','final_amount' => $totalPrice),array('device_id' => $device_id,'user_id' => $userid,'status' => 0)); 
					 }
					 else
					 {
						$up=$this->admin_model->updateData('salo_temp_cart',array('grand_total' => $totalPrice,'promo_code' => '','promo_code_id' => '0','promo_discount' => '','final_amount' => $totalPrice),array('user_id' => $userid,'status' => 0)); 
					 }
			
					$getsData=$this->db->query("SELECT * FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0'")->result_array();
					$orderid=$getsData[0]['order_id'];
					if($orderid!="")
					{
					 $up=$this->admin_model->updateData('salo_cart_shipping_details',array('grand_total' => $totalPrice,'promo_code' => '','promo_code_id' => '0','promo_discount' => '','final_amount' => $totalPrice),array('order_id' => $orderid));
					}
			   }			  
			
             $getCart=$this->admin_model->getBasic("SELECT SUM(quantity) as qty FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0'"); 
			 $cartCount=$getCart[0]['qty'];			
			  
			   if((is_nan($up)==false) || ($up == "")){
				   
		        $data['status']="True";
				$data['type'] = '200';
				$data['data'] = $cartCount;
	            $data['msg'] = 'Cart Updated successfully';	
				
			  }else{
				  
				$data['status']="False";
				$data['type'] = '400';
				$data['data'] = $cartCount;
	            $data['msg'] = 'Sorry,something went wrong.Please try again'; 
			  }
		}
		else
		{
			 
			$data['status']="False";
			$data['type'] = '400';
	        $data['msg'] = 'Sorry,Item Not Available'; 
		}
		
		echo json_encode($data);
		
	}
	
	
	public function getAddress($uid,$device_id)
	{
	  $getCart=$this->admin_model->getBasic("SELECT * FROM salo_cart_shipping_details WHERE user_id='$uid' ORDER BY id DESC LIMIT 1");
	  if(count($getCart)!=0)
	  {
		  $name=$getCart[0]['name'];
		  $email=$getCart[0]['email'];
		  $contact=$getCart[0]['contact'];
		  $address=$getCart[0]['address'];
		  $state=$getCart[0]['state'];
		  $city=$getCart[0]['city'];
		  $country=$getCart[0]['country'];
		  $pincode=$getCart[0]['pincode'];
		  $billing_name=$getCart[0]['billing_name'];
		  $billing_email=$getCart[0]['billing_email'];
		  $billing_contact=$getCart[0]['billing_contact'];
		  $billing_address=$getCart[0]['billing_address'];
		  $billing_state=$getCart[0]['billing_state'];
		  $billing_city=$getCart[0]['billing_city'];
		  $billing_country=$getCart[0]['billing_country'];
		  $billing_pincode=$getCart[0]['billing_pincode'];
		  
		  $arr=array('name' => $name,'email' => $email,'contact' => $contact,'address' => $address,'state' => $state,'city' => $city,'country' => $country,'pincode' => $pincode,'billing_name' => $billing_name,'billing_email' => $billing_email,'billing_contact' => $billing_contact,'billing_address' => $billing_address,'billing_state' => $billing_state,'billing_city' => $billing_city,'billing_country' => $billing_country,'billing_pincode' => $billing_pincode);
		  
		  $data['status']="True";
		  $data['data'] = $arr;
		  $data['msg'] = 'Done'; 
	  }
	  else
	  {
		 $data['status']="False";
		 $data['msg'] = 'Done'; 
	  }
           echo json_encode($data);
	}
	
	public function getAllCat()
	{
		$menuList=array();
		$a=0;
		$firstCat = $this->admin_model->getData('salo_category',array('status' => 0),null);
		foreach($firstCat as $row)
		{
			$cid=$row['category_id'];
			$category_name=$row['category_name'];
			$image=$row['image'];
			
			if($image!="")
			{
			  $imageUrl=$this->config->item('base_image_url')."/assets/uploads/category/".$row['image'];
			}
			else
			{
				$imageUrl=$this->config->item('base_image_url')."/assets/lupras/assets/img/extra/breadcrumb-1.jpg";
			}
			
			$secondCat = $this->admin_model->getData('salo_sub_category',array('status' => 0,'category_id' => $cid),'AND');
			if(count($secondCat)!=0)
			{
				$islastFirst="false";
			}
			else
			{
				$islastFirst="true";
			}
			
			$arr=array('id' => $cid,'name' => $category_name,'img' => $imageUrl,'islast' => $islastFirst);
			array_push($menuList,$arr);
			$a++;
		}
		
	  if(count($firstCat)!=0)
	   {
		$data['status']="True";
		$data['catList']=$menuList;
		$data['msg']="Done";
	   }
	   else
	   {
		  $data['status']="False"; 
		  $data['msg']="Done";
	   }
	   
	   echo json_encode($data);
	}
	
		public function getAllSubCat($catId)
	{
		$menuList=array();
		$a=0;
		$firstCat = $this->admin_model->getData('salo_sub_category',array('category_id' => $catId,'status' => 0),null);
		foreach($firstCat as $rt)
		{
            $cid=$rt['category_id'];
            $sid=$rt['sub_category_id'];
			$sub_category_name=$rt['sub_category_name'];
			$image=$rt['image'];
			if($image!="")
			{
              $subimageUrl=$this->config->item('base_image_url')."/assets/uploads/category/".$rt['image'];
			}
			else
			{
		       $subimageUrl=$this->config->item('base_image_url')."/assets/lupras/assets/img/extra/breadcrumb-1.jpg";
			}
			
			$thirdCat = $this->admin_model->getData('salo_sub_sub_category',array('status' => 0,'sub_category_id' => $sid),'AND');
			if(count($thirdCat)!=0)
			{
			  $islastSecond="false";
			}
			else
			{
			  $islastSecond="true";
			}
			
			$arr=array('id' => $sid,'main_category_id' => $cid,'name' => $sub_category_name,'img' => $subimageUrl,'islast' => $islastSecond);
			array_push($menuList,$arr);
			$a++;
		}
		
	  if(count($firstCat)!=0)
	   {
		$data['status']="True";
		$data['catList']=$menuList;
		$data['msg']="Done";
	   }
	   else
	   {
		  $data['status']="False"; 
		  $data['msg']="Done";
	   }
	   
	   echo json_encode($data);
	}
	
	
	public function getAllSubSubCat($subcatId)
	{
		$menuList=array();
		$a=0;
		$firstCat = $this->admin_model->getData('salo_sub_sub_category',array('sub_category_id' => $subcatId,'status' => 0),'AND');
		foreach($firstCat as $rt)
		{
            $id=$rt['id'];
            $cid=$rt['category_id'];
            $sid=$rt['sub_category_id'];
			$name=$rt['name'];
			$image=$rt['image'];
			if($image!="")
			{
              $subimageUrl=$this->config->item('base_image_url')."/assets/uploads/category/".$rt['image'];
			}
			else
			{
		       $subimageUrl=$this->config->item('base_image_url')."/assets/lupras/assets/img/extra/breadcrumb-1.jpg";
			}
			
			$islastSecond="true";
			
			$arr=array('id' => $id,'main_category_id' => $cid,'sub_category_id' => $sid,'name' => $name,'img' => $subimageUrl,'islast' => $islastSecond);
			array_push($menuList,$arr);
			$a++;
		}
		
	  if(count($firstCat)!=0)
	   {
		$data['status']="True";
		$data['catList']=$menuList;
		$data['msg']="Done";
	   }
	   else
	   {
		  $data['status']="False"; 
		  $data['msg']="Done";
	   }
	   
	   echo json_encode($data);
	}
	
	public function getPromoCode($oid)
	{
	  $cdate=date('Y-m-d');
	  $getData=$this->admin_model->getBasic("SELECT DISTINCT pro_id FROM `salo_temp_cart` WHERE `order_id`='$oid' and pro_id IN (SELECT id FROM salo_products WHERE `coupon_code_status`='1' and active_coupon_id IN (SELECT id FROM salo_coupon WHERE expiry_date >= '$cdate' and status='0'))");
	  if(count($getData)!=0)
	  {
		  $arrPromo=array();
		  foreach($getData as $rt)
		  {
			 $pid=$rt['pro_id']; 
			 $getpro=$this->admin_model->getBasic("SELECT * FROM salo_products WHERE id='$pid'");
			 $couponId=$getpro[0]['active_coupon_id'];
			 
			 $getCoupon=$this->admin_model->getBasic("SELECT * FROM salo_coupon WHERE id='$couponId'");
			 $row=$getCoupon[0];
			 $id=$row['id']; 
			 $title=$row['title']; 
			 $coupon_code=$row['coupon_code']; 
			 $discount=$row['discount']; 
			 $expiry_date=$row['expiry_date']; 
			 
			 $arr=array('id' => $id,'title' => $title,'coupon_code' => $coupon_code,'discount_percent' => $discount,'expiry_date' => $expiry_date);
			 array_push($arrPromo,$arr);
		  }
		  
		 $data['status']="True";
		 $data['promoCodeList']=$arrPromo;
		 $data['msg']="Done";
	  }
	  else
	  {
		  $data['status']="False";
		  $data['msg']="Done";
	  }
	  
	   echo json_encode($data);
	}
	
	public function orderReview($orderid)
	{
		$getData=$this->admin_model->getBasic("SELECT SUM(quantity) as qty FROM salo_temp_cart WHERE order_id='$orderid'");
		$qty=$getData[0]['qty'];
		
		$getAdmin=$this->admin_model->getBasic("SELECT * FROM salo_admin_login WHERE role='0'");
		$max_wallet_pay=$getAdmin[0]['max_wallet_pay'];
		
		$getBasic=$this->admin_model->getBasic("SELECT * FROM salo_temp_cart WHERE order_id='$orderid'");
		$grandTotal=$getBasic[0]['grand_total'];
		$shipping_charge=$getBasic[0]['shipping_charge'];
		$totalPrice=$getBasic[0]['final_amount'];
		$user_id=$getBasic[0]['user_id'];
		/*$promo_discount=$getBasic[0]['promo_discount'];
		if($promo_discount=="")
		{
			$promo_discount=0;
		}
		*/
		$discountAmount=$grandTotal - $totalPrice;
		
		$totalPrice=$totalPrice + $shipping_charge;
		
		$getUser=$this->admin_model->getBasic("SELECT * FROM salo_users WHERE id='$user_id'");
		$wallet_amount=$getUser[0]['wallet_amount'];
		
		if(($wallet_amount==0) && ($wallet_amount==""))
		{
		  $used_wallet_amount="0";
		}
		else if($wallet_amount>=$max_wallet_pay)
		{
		  $used_wallet_amount=$max_wallet_pay;
		}
		else
		{
		  $used_wallet_amount=$wallet_amount;
		}
		
		$afterWalletUse=$totalPrice-$used_wallet_amount;
		
		$arr=array('order_id' => $orderid,'total_quantity' => $qty,'payable_price' => $grandTotal,'discount' => $discountAmount,'shipping_charge' => $shipping_charge,'total_price' => $totalPrice,'used_wallet_amount' => $used_wallet_amount,'amount_after_wallet_use' => $afterWalletUse);
		
		$data['status']="True";
		$data['reviewList']=$arr;
		$data['msg']="Done";
		echo json_encode($data);
		
	}
	
	public function payonlineSuccessNew()
	{
	
	    $postData=$_POST;
	    $orderId=$postData['order_id'];
		$used_wallet_amount=$postData['used_wallet_amount'];
		$final_amount=$postData['final_amount'];
		
		
		$prot = $this->admin_model->getBasic("SELECT * FROM  salo_temp_cart WHERE order_id='$orderId'");
		if(count($prot)!=0)
		{
		            $siteDetails=$this->adminDetails();
		
		            $this->db->update('salo_temp_cart', array('used_wallet_amount' => $used_wallet_amount,'final_amount' => $final_amount,'status' => 1,'payment_status' => 1) , array('order_id' => $orderId)); 
					 
					 $this->db->update('salo_cart_shipping_details', array('used_wallet_amount' => $used_wallet_amount,'final_amount' => $final_amount,'payment_mode' => 'Online Payment','payment_status' => 1) , array('order_id' => $orderId)); 
					 
					 $prot = $this->admin_model->getBasic("SELECT * FROM  salo_temp_cart WHERE order_id='$orderId'");
			         $protype = $prot[0]['product_type'];
					 $bookHead="Shipping Address";
					 
					 $orderData=$this->admin_model->getData('salo_temp_cart',array('order_id' => $orderId),null);
					 
                     $ordershippingData=$this->admin_model->getData('salo_cart_shipping_details',array('order_id' => $orderId),null);
					 
					 $userId=$ordershippingData[0]['user_id'];
					 $isCashback=$ordershippingData[0]['is_cashback'];
					 $cashbackAmount=$ordershippingData[0]['cashback_amount'];
					 
					 
					if($used_wallet_amount!=0)
					{
					  $userData=$this->admin_model->getData('salo_users',array('id' => $userId),null);
					  $old_wallet_amount=$userData[0]['wallet_amount'];
					  $rem_wallet_amount=$old_wallet_amount - $used_wallet_amount;
					  
					  $this->db->update('salo_users', array('wallet_amount' => $rem_wallet_amount) , array('id' => $userId)); 
					}
					
	                 $userData=$this->admin_model->getData('salo_users',array('id' => $userId),null);

					$name=$userData[0]['name'];
					
					if($isCashback==1)
					{
					  $old_wallet_amount=$userData[0]['wallet_amount'];
					  $final_wallet_amount=$old_wallet_amount + $cashbackAmount;
					  
					  $this->db->update('salo_users', array('wallet_amount' => $final_wallet_amount) , array('id' => $userId)); 
					}
					
					
					$booking_name=$ordershippingData[0]['name'];
					$booking_phone=$ordershippingData[0]['contact'];
					$booking_email=$ordershippingData[0]['email'];
					$delivery_date=$ordershippingData[0]['delivery_date'];
					$delivery_time=$ordershippingData[0]['delivery_time'];
					
				    $delivery_date=date('F j,Y',strtotime($delivery_date));
					
					
					$timeSlot=$this->admin_model->getData('salo_time_slot',array('id' => $delivery_time),null);
					$start = date("g:i a", strtotime($timeSlot[0]['start']));
					$end = date("g:i a", strtotime($timeSlot[0]['end']));
					
					
					$booking_address=$ordershippingData[0]['address'].",".$ordershippingData[0]['city'].",".$ordershippingData[0]['state'].",".$ordershippingData[0]['country'].",".$ordershippingData[0]['pincode'];
					
					
		
/***************************** User Mail Starts *************************/		
		
		
$html='<table cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" style="background:#ffffff;border:1px solid #e3e3e3;border-radius:3px;max-width:640px">
<tbody><tr>
  <td width="620" align="center"><table border="0" cellpadding="0" cellspacing="0" align="center" style="max-width:620px">
    <tbody><tr>
      <td align="center" style="border-bottom:1px dotted #e3e3e3"><a href="'.base_url().'" target="_blank" data-saferedirecturl="'.base_url().'">
        <div style="width:100%"><img src="'.base_url().'assets/uploads/logo/'.$siteDetails[0]['company_logo'].'" border="0" alt="" style="margin:0;display:block;max-width:200px;width:inherit" vspace="0" hspace="0" align="center" class="CToWUd"></div>
        </a></td>
    </tr>

    </tbody></table></td>
</tr>
<tr>
  <td align="center" style="padding:0px"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-family:arial;font-size:12px;text-align:left;color:#333333">
    <tbody>
      <tr>
        <td style="padding:0px 0px"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" style="max-width:600px;font-family:arial;font-size:14px;text-align:left;color:#333333">
          <tbody>
            <tr>
              <td style="padding:10px">Dear '.$name.' ,</td>
              </tr>
            <tr>
              <td style="padding:0px 10px 20px 10px">We have received your order. We will be calling you shortly to confirm your order. In case you are not able to pick up our call, do not worry. We will be calling multiple times before going ahead with the cancellation. </td>
              </tr>
            
            </tbody>
          </table></td>
        </tr>
      <tr>
        <td style="padding:0px 10px"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tbody>
            <tr>
              <td bgcolor="#84a743" style="border-bottom:1px solid #4f7605;padding:10px;font-size:14px"><strong style="color:white">Shipping Address</strong><strong style="float:right;text-align:right;color:white">Order Id :  '.$orderid.'</strong></td>
              </tr>
            <tr>
              <td style="font-size:0;padding:0 10px 10px 10px" align="left" bgcolor="#dff2e0">
                <table width="200" cellpadding="0" cellspacing="0" border="0" align="left" style="font-family:arial;font-size:12px;text-align:left;color:#333333">
                  <tbody><tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top" width="60"><strong>Name</strong></td>
                    <td valign="top">'.$booking_name.' </td>
                    </tr>
                  <tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top"><strong>Email</strong></td>
                    <td valign="top"><a href="#m_2299037813007463152_" style="display:block;color:#333333;word-break:break-all;text-decoration:none">'.$booking_email.'</a></td>
                    </tr>
					 <tr>
                    <td valign="top"><strong>Delivery Time</strong></td>
                    <td valign="top"><a href="#m_2299037813007463152_" style="display:block;color:#333333;word-break:break-all;text-decoration:none"> '.$delivery_date.' between '.$start .' - '.$end.'</a></td>
                    </tr>
					 <tr>
                    <td valign="top"><strong>Before Time</strong></td>
                    <td valign="top"><a href="#m_2299037813007463152_" style="display:block;color:#333333;word-break:break-all;text-decoration:none">'.$btime.'</a></td>
                    </tr>
                  </tbody></table>
                
                <table width="200" cellpadding="0" cellspacing="0" border="0" align="left" style="font-family:arial;font-size:12px;text-align:left;color:#333333">
                  <tbody><tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top" width="60"><strong>Mobile</strong></td>
                    <td valign="top">'.$booking_phone.'</td>
                    </tr>
                  <tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top"><strong>Address</strong></td>
                    <td valign="top">'.$booking_address.'</td>
                    </tr>
                  </tbody></table>';
				  
				$html.='</td>
              </tr>
            </tbody>
          </table></td>
        </tr>
      <tr>
        <td height="20"></td>
        </tr>
      <tr>
        <td width="600" align="center" style="padding:0px 10px">
          
          
          
          <table width="98%" border="0" bgcolor="#dff2e0" align="center" cellpadding="8" cellspacing="0" style="font-family:arial;font-size:12px;text-align:left;color:#333333;max-width:600px">
            <tbody>
			
			    <tr>
					 <th style="padding:3px;border:1px solid black">Category</th>
					 <th style="padding:3px;border:1px solid black">Product</th>
					 <th style="padding:3px;border:1px solid black">Price</th>
					 <th style="padding:3px;border:1px solid black">Discount(%)</th>';
					
					 $html.='<th style="padding:3px;border:1px solid black">Qty</th>
					 <th style="padding:3px;border:1px solid black">Attribute</th>
					 <th style="padding:3px;border:1px solid black">Total</th>
			    </tr>';
		
         $totQty=0;		
         $totPrc=0;		
         $totDur=0;		
		foreach($orderData as $rw)
		{
		    $pid=$rw['pro_id'];
			
			    $product_attribute = $rw['product_attribute'] ;
			    $getatt=$this->db->query("SELECT * FROM salo_product_attribute WHERE `id`='$product_attribute'")->result_array();
				$unitid = $getatt[0]['unit_id'];
				$attvalue = $getatt[0]['attribute_value'];
				$unitatt=$this->db->query("SELECT * FROM salo_unit WHERE `id`='$unitid'")->result_array();
				$unitname = $unitatt[0]['unit'];
			
			
			
			
			$proData=$this->admin_model->getData('salo_products',array('id' => $pid),null);
			$catid=$proData[0]['sub_category_id'];
			$proTitle=$proData[0]['title'];
			
			
		    $subCatData=$this->admin_model->getData('salo_sub_category',array('sub_category_id' => $catid),null);
			$sub_category_name=$subCatData[0]['sub_category_name'];
			
			
				$html.=' <tr>
				 
						   <td style="padding:3px;border:1px solid black">'.$sub_category_name.'</td>
						   <td style="padding:3px 10px 3px 3px;border:1px solid black">'.$proTitle.'</td>
						   <td style="text-align:center;padding:3px;border:1px solid black">'.$rw['unit_price'].'</td>
						   <td style="text-align:center;padding:3px;border:1px solid black">'.$rw['discount'].'</td>';
						    $html.='<td style="padding:3px;text-align:center;border:1px solid black">'.$rw['quantity'].'</td>
						    <td style="text-align:center;padding:3px;border:1px solid black">'.$attvalue .' '.$unitname.'</td>
						   <td style="text-align:center;padding:3px;border:1px solid black">'.$rw['total_price'].'</td>
						   
						</tr>';
				
				$totQty= $totQty + $rw['quantity'];
				$totPrc= $totPrc + $rw['total_price'];
				$totDur= $totDur + $proData[0]['duration'];
			}
				$html.='<tr>
					<td colspan="4" style="text-align:right;padding:3px;border:1px solid black">Total</td>';
					
					$html.='<td style="padding:3px;text-align:right;border:1px solid black">'.$totQty.'</td><td style="padding:3px;text-align:right;border:1px solid black"></td>
					<td style="text-align:right;padding:3px;border:1px solid black">Rs. '.$totPrc.'</td>
				</tr>';
				
				if($ordershippingData[0]['promo_code_id']!=0)
		        {		
                   $shipping_charge=$ordershippingData[0]['shipping_charge'];
                   $dis=$ordershippingData[0]['promo_discount'];
                   $final_amount=$ordershippingData[0]['final_amount'];			  
				    $html.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Promo Discount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$dis.'</td>
				 
				 </tr>';
				 
				 if($shipping_charge!=0)
				 {
				   $html.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Shipping Charge</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$shipping_charge.'</td>
				 
				 </tr>';
				 
				 $finalAmt = $final_amount + $shipping_charge;
				 }
				 else
				 {
				   $finalAmt = $final_amount;
				 }
				$html.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Final Amount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black"><i class="fa fa-inr"></i> '.$finalAmt.'</td>
				 
				 </tr>';
		        }
				else
				{
				  $shipping_charge=$ordershippingData[0]['shipping_charge'];
				  $final_amount=$ordershippingData[0]['final_amount'];
				   if($shipping_charge!=0)
					 {
					   $finalAmt = $final_amount + $shipping_charge;
					   $html.='<tr>
					
					   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Shipping Charge</td>
					   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$shipping_charge.'</td>
					 
					 </tr>
					 <tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Final Amount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black"><i class="fa fa-inr"></i> '.$finalAmt.'</td>
				 
				 </tr>';
					 
					
					 }
				}
			$html.='<tr>
				<td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Payment mode</td>
				<td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$payment_mode.'</td>
			</tr>';
            $html.='</tbody></table></td>
      </tr>
 
      </tbody>
    </table></td>
</tr>
<tr>
  <td align="center" style="padding-top:0px"><table align="center" cellpadding="0" cellspacing="0" class="m_2299037813007463152container" style="width:100%">
    <tbody><tr>
      <td align="center" style="text-align:center;vertical-align:top;font-size:0;padding:10px">
        <div style="width:240px;display:inline-block;vertical-align:top;text-align:center;font-size:0" align="center">
          <table width="220" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px">
            <tbody><tr>
              <td width="30" align="center" style="padding:5px"><a href="'.base_url().'" target="_blank" data-saferedirecturl="'.base_url().'"><img src="'.base_url().'assets/template/images/icon/www.png" alt="" width="30" height="30" style="margin:0" border="0" vspace="0" hspace="0" align="absmiddle" class="CToWUd"></a></td>
              <td align="left" style="padding:0px 0px;font-size:16px;font-family: Open Sans,Gill Sans,Arial,Helvetica,sans-serif"><a href="'.base_url().'" style="color:#444444;text-decoration:none" target="_blank" data-saferedirecturl="'.base_url().'">Ghar Ghar Bazaar</a></td>
              </tr>
            </tbody></table>
          </div>
        
        <div style="width:200px;display:inline-block;vertical-align:top;text-align:center;font-size:0" align="center">
          <table width="180" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px">
            <tbody><tr>
              <td width="30" align="center" style="padding:5px"><a href="'.base_url().'" target="_blank" data-saferedirecturl="'.base_url().'"><img src="'.base_url().'assets/template/images/icon/call.png" alt="" width="30" height="30" style="margin:0" border="0" vspace="0" hspace="0" align="absmiddle" class="CToWUd"></a></td>
              <td align="left" style="padding:0px 0px;font-size:16px;font-family:Open Sans,Gill Sans,Arial,Helvetica,sans-serif"><a style="color:#444444;text-decoration:none" href="tel:'.$siteDetails[0]['company_phone'].'" target="_blank">'.$siteDetails[0]['company_phone'].'</a></td>
              </tr>
            </tbody></table>
          </div>
        </td>
      </tr>
    </tbody></table></td>
</tr>
</tbody></table>';

                        $from="no-reply@ghargharbazaar.com";
						$subject="Thank You for shopping with ".$siteDetails[0]['company_name'];
						$to=$userData[0]['email'];			
						$headers  = 'MIME-Version: 1.0' . "\r\n";
						$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
						$headers .= 'From: '.$from . "\r\n";
						mail($to,$subject,$html,$headers);


						
/***************************** User Mail Ends *************************/

						
						
/***************************** Admin Mail Starts *************************/						
						
						
$htmlAdmin='<table cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" style="background:#ffffff;border:1px solid #e3e3e3;border-radius:3px;max-width:640px">
<tbody><tr>
  <td width="620" align="center"><table border="0" cellpadding="0" cellspacing="0" align="center" style="max-width:620px">
    <tbody><tr>
      <td align="center" style="border-bottom:1px dotted #e3e3e3"><a href="'.base_url().'" target="_blank" data-saferedirecturl="'.base_url().'">
        <div style="width:100%"><img src="'.base_url().'assets/uploads/logo/'.$siteDetails[0]['company_logo'].'" border="0" alt="" style="margin:0;display:block;max-width:200px;width:inherit" vspace="0" hspace="0" align="center" class="CToWUd"></div>
        </a></td>
    </tr>

    </tbody></table></td>
</tr>
<tr>
  <td align="center" style="padding:0px"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-family:arial;font-size:12px;text-align:left;color:#333333">
    <tbody>
      <tr>
        <td style="padding:0px 0px"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" style="max-width:600px;font-family:arial;font-size:14px;text-align:left;color:#333333">
          <tbody>
            <tr>
              <td style="padding:10px">Hello ,</td>
              </tr>
            <tr>
              <td style="padding:0px 10px 20px 10px">You have recieved a new order from '.$name.'.Here are the details.</td>
              </tr>
            </tbody>
          </table></td>
        </tr>
      <tr>
        <td style="padding:0px 10px"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tbody>
            <tr>
              <td bgcolor="#84a743" style="border-bottom:1px solid #4f7605;padding:10px;font-size:14px"><strong style="color:white">Shipping Address</strong><strong style="float:right;text-align:right;color:white">Order Id :  '.$orderid.'</strong></td>
              </tr>
            <tr>
              <td style="font-size:0;padding:0 10px 10px 10px" align="left" bgcolor="#dff2e0">
                <table width="200" cellpadding="0" cellspacing="0" border="0" align="left" style="font-family:arial;font-size:12px;text-align:left;color:#333333">
                  <tbody><tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top" width="60"><strong>Name</strong></td>
                    <td valign="top">'.$booking_name.' </td>
                    </tr>
                  <tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top"><strong>Email</strong></td>
                    <td valign="top"><a href="#m_2299037813007463152_" style="display:block;color:#333333;word-break:break-all;text-decoration:none">'.$booking_email.'</a></td>
                    </tr>
					 <tr>
                    <td valign="top"><strong>Delivery Time</strong></td>
                    <td valign="top"><a href="#m_2299037813007463152_" style="display:block;color:#333333;word-break:break-all;text-decoration:none"> '.$delivery_date.' between '.$start .' - '.$end.'</a></td>
                    </tr>
					 <tr>
                    <td valign="top"><strong>Before Time</strong></td>
                    <td valign="top"><a href="#m_2299037813007463152_" style="display:block;color:#333333;word-break:break-all;text-decoration:none">'.$btime.'</a></td>
                    </tr>
                  </tbody></table>
                
                <table width="200" cellpadding="0" cellspacing="0" border="0" align="left" style="font-family:arial;font-size:12px;text-align:left;color:#333333">
                  <tbody><tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top" width="60"><strong>Mobile</strong></td>
                    <td valign="top">'.$booking_phone.'</td>
                    </tr>
                  <tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top"><strong>Address</strong></td>
                    <td valign="top">'.$booking_address.'</td>
                    </tr>
                  </tbody></table>';
				  
				
				
                $htmlAdmin.='</td>
              </tr>
            </tbody>
          </table></td>
        </tr>
      <tr>
        <td height="20"></td>
        </tr>
      <tr>
        <td width="600" align="center" style="padding:0px 10px">
          
           <table width="98%" border="0" bgcolor="#dff2e0" align="center" cellpadding="8" cellspacing="0" style="font-family:arial;font-size:12px;text-align:left;color:#333333;max-width:600px">
            <tbody>
			
			    <tr>
					 <th style="padding:3px;border:1px solid black">Category</th>
					 <th style="padding:3px;border:1px solid black">Product</th>
					 <th style="padding:3px;border:1px solid black">Price</th>
					 <th style="padding:3px;border:1px solid black">Discount(%)</th>';
					 $htmlAdmin.='<th style="padding:3px;border:1px solid black">Qty</th>
					 <th style="padding:3px;border:1px solid black">Attribute</th>
					 <th style="padding:3px;border:1px solid black">Total</th>
			    </tr>';
		
        $totQty=0;		
        $totPrc=0;		
        $totDur=0;		
		foreach($orderData as $rw)
		{
		        $pid=$rw['pro_id'];
			    $product_attribute = $rw['product_attribute'] ;
			    $getatt=$this->db->query("SELECT * FROM salo_product_attribute WHERE `id`='$product_attribute'")->result_array();
				$unitid = $getatt[0]['unit_id'];
				$attvalue = $getatt[0]['attribute_value'];
				$unitatt=$this->db->query("SELECT * FROM salo_unit WHERE `id`='$unitid'")->result_array();
				$unitname = $unitatt[0]['unit'];
			
			$proData=$this->admin_model->getData('salo_products',array('id' => $pid),null);
			$catid=$proData[0]['sub_category_id'];
			$proTitle=$proData[0]['title'];
			
			
		    $subCatData=$this->admin_model->getData('salo_sub_category',array('sub_category_id' => $catid),null);
			$sub_category_name=$subCatData[0]['sub_category_name'];
			
			
				$htmlAdmin.=' <tr>
				           <td style="padding:3px;border:1px solid black">'.$sub_category_name.'</td>
						   <td style="padding:3px 10px 3px 3px;border:1px solid black">'.$proTitle.'</td>
						   <td style="text-align:center;padding:3px;border:1px solid black">'.$rw['unit_price'].'</td>
						   <td style="text-align:center;padding:3px;border:1px solid black">'.$rw['discount'].'</td>';
						   $htmlAdmin.='<td style="padding:3px;text-align:center;border:1px solid black">'.$rw['quantity'].'</td>
						   <td style="text-align:center;padding:3px;border:1px solid black">'.$attvalue .' '.$unitname.'</td>
						   <td style="text-align:center;padding:3px;border:1px solid black">'.$rw['total_price'].'</td>
						   
						</tr>';
				
				$totQty= $totQty + $rw['quantity'];
				$totPrc= $totPrc + $rw['total_price'];
				$totDur= $totDur + $proData[0]['duration'];
			}
				$htmlAdmin.='<tr>
					<td colspan="4" style="text-align:right;padding:3px;border:1px solid black">Total</td>';
					$htmlAdmin.='<td style="padding:3px;text-align:right;border:1px solid black">'.$totQty.'</td><td style="padding:3px;text-align:right;border:1px solid black"></td>
					<td style="text-align:right;padding:3px;border:1px solid black">Rs. '.$totPrc.'</td>
				</tr>';
				
			if($ordershippingData[0]['promo_code_id']!=0)
		        {		
                   $shipping_charge=$ordershippingData[0]['shipping_charge'];
                   $dis=$ordershippingData[0]['promo_discount'];
                   $final_amount=$ordershippingData[0]['final_amount'];			  
				    $htmlAdmin.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Promo Discount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$dis.'</td>
				 
				 </tr>';
				 
				 if($shipping_charge!=0)
				 {
				   $htmlAdmin.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Shipping Charge</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$shipping_charge.'</td>
				 
				 </tr>';
				 
				 $finalAmt = $final_amount + $shipping_charge;
				 }
				 else
				 {
				   $finalAmt = $final_amount;
				 }
				$htmlAdmin.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Final Amount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black"><i class="fa fa-inr"></i> '.$finalAmt.'</td>
				 
				 </tr>';
		        }
				else
				{
				  $shipping_charge=$ordershippingData[0]['shipping_charge'];
				  $final_amount=$ordershippingData[0]['final_amount'];
				   if($shipping_charge!=0)
					 {
					   $finalAmt = $final_amount + $shipping_charge;
					   $htmlAdmin.='<tr>
					
					   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Shipping Charge</td>
					   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$shipping_charge.'</td>
					 
					 </tr>
					 <tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Final Amount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black"><i class="fa fa-inr"></i> '.$finalAmt.'</td>
				 
				 </tr>';
					 
					
					 }
				}
             $htmlAdmin.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Payment mode</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$payment_mode.'</td>
				 
				 </tr>';

			
           $htmlAdmin.=' </tbody></table></td>
      </tr>
 
      </tbody>
    </table></td>
</tr>
<tr>
  <td align="center" style="padding-top:0px"><table align="center" cellpadding="0" cellspacing="0" class="m_2299037813007463152container" style="width:100%">
    <tbody><tr>
      <td align="center" style="text-align:center;vertical-align:top;font-size:0;padding:10px">
        <div style="width:240px;display:inline-block;vertical-align:top;text-align:center;font-size:0" align="center">
          <table width="220" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px">
            <tbody><tr>
              <td width="30" align="center" style="padding:5px"><a href="'.base_url().'" target="_blank" data-saferedirecturl="'.base_url().'"><img src="'.base_url().'assets/template/images/icon/www.png" alt="" width="30" height="30" style="margin:0" border="0" vspace="0" hspace="0" align="absmiddle" class="CToWUd"></a></td>
              <td align="left" style="padding:0px 0px;font-size:16px;font-family: Open Sans,Gill Sans,Arial,Helvetica,sans-serif"><a href="'.base_url().'" style="color:#444444;text-decoration:none" target="_blank" data-saferedirecturl="'.base_url().'">Ghar Ghar Bazaar</a></td>
              </tr>
            </tbody></table>
          </div>
        
        <div style="width:200px;display:inline-block;vertical-align:top;text-align:center;font-size:0" align="center">
          <table width="180" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px">
            <tbody><tr>
              <td width="30" align="center" style="padding:5px"><a href="'.base_url().'" target="_blank" data-saferedirecturl="'.base_url().'"><img src="'.base_url().'assets/template/images/icon/call.png" alt="" width="30" height="30" style="margin:0" border="0" vspace="0" hspace="0" align="absmiddle" class="CToWUd"></a></td>
              <td align="left" style="padding:0px 0px;font-size:16px;font-family:Open Sans,Gill Sans,Arial,Helvetica,sans-serif"><a style="color:#444444;text-decoration:none" href="tel:'.$siteDetails[0]['company_phone'].'" target="_blank">'.$siteDetails[0]['company_phone'].'</a></td>
              </tr>
            </tbody></table>
          </div>
        </td>
      </tr>
    </tbody></table></td>
</tr>
</tbody></table>';
            $webDetails=$this->adminDetails();
            $fromAdmin="no-reply@ghargharbazaar.com";
			$subjectAdmin="New Order Recieved on Ghar Ghar Bazaar";
			$toAdmin= $webDetails[0]['company_email'];			
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'From: '.$fromAdmin . "\r\n";
			mail($toAdmin,$subjectAdmin,$htmlAdmin,$headers);
						
			/***************************** Admin Mail Ends *************************/
		
		$data['status']="True";
		$data['msg']="Done";
	  }
	  else
	  {
	    $data['status']="False";
		$data['msg']="Order Id Not Found";
	  }
		echo json_encode($data);
	}
	
	
	public function payonlineSuccess($orderId)
	{
		
		$prot = $this->admin_model->getBasic("SELECT * FROM  salo_temp_cart WHERE order_id='$orderId'");
		if(count($prot)!=0)
		{
		            $siteDetails=$this->adminDetails();
		
		            $this->db->update('salo_temp_cart', array('status' => 1,'payment_status' => 1) , array('order_id' => $orderId)); 
					 
					 $this->db->update('salo_cart_shipping_details', array('payment_mode' => 'Online Payment','payment_status' => 1) , array('order_id' => $orderId)); 
					 
					 $prot = $this->admin_model->getBasic("SELECT * FROM  salo_temp_cart WHERE order_id='$orderId'");
			         $protype = $prot[0]['product_type'];
					 $bookHead="Shipping Address";
					 
					 $orderData=$this->admin_model->getData('salo_temp_cart',array('order_id' => $orderId),null);
					 
                     $ordershippingData=$this->admin_model->getData('salo_cart_shipping_details',array('order_id' => $orderId),null);
					 
					 $userId=$ordershippingData[0]['user_id'];
					 
	                 $userData=$this->admin_model->getData('salo_users',array('id' => $userId),null);
					 
					$name=$userData[0]['name'];
					$booking_name=$ordershippingData[0]['name'];
					$booking_phone=$ordershippingData[0]['contact'];
					$booking_email=$ordershippingData[0]['email'];
					$delivery_date=$ordershippingData[0]['delivery_date'];
					$delivery_time=$ordershippingData[0]['delivery_time'];
					
				    $delivery_date=date('F j,Y',strtotime($delivery_date));
					
					
					$timeSlot=$this->admin_model->getData('salo_time_slot',array('id' => $delivery_time),null);
					$start = date("g:i a", strtotime($timeSlot[0]['start']));
					$end = date("g:i a", strtotime($timeSlot[0]['end']));
					
					
					$booking_address=$ordershippingData[0]['address'].",".$ordershippingData[0]['city'].",".$ordershippingData[0]['state'].",".$ordershippingData[0]['country'].",".$ordershippingData[0]['pincode'];
					
					
		
/***************************** User Mail Starts *************************/		
		
		
$html='<table cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" style="background:#ffffff;border:1px solid #e3e3e3;border-radius:3px;max-width:640px">
<tbody><tr>
  <td width="620" align="center"><table border="0" cellpadding="0" cellspacing="0" align="center" style="max-width:620px">
    <tbody><tr>
      <td align="center" style="border-bottom:1px dotted #e3e3e3"><a href="'.base_url().'" target="_blank" data-saferedirecturl="'.base_url().'">
        <div style="width:100%"><img src="'.base_url().'assets/uploads/logo/'.$siteDetails[0]['company_logo'].'" border="0" alt="" style="margin:0;display:block;max-width:200px;width:inherit" vspace="0" hspace="0" align="center" class="CToWUd"></div>
        </a></td>
    </tr>

    </tbody></table></td>
</tr>
<tr>
  <td align="center" style="padding:0px"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-family:arial;font-size:12px;text-align:left;color:#333333">
    <tbody>
      <tr>
        <td style="padding:0px 0px"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" style="max-width:600px;font-family:arial;font-size:14px;text-align:left;color:#333333">
          <tbody>
            <tr>
              <td style="padding:10px">Dear '.$name.' ,</td>
              </tr>
            <tr>
              <td style="padding:0px 10px 20px 10px">We have received your order. We will be calling you shortly to confirm your order. In case you are not able to pick up our call, do not worry. We will be calling multiple times before going ahead with the cancellation. </td>
              </tr>
            
            </tbody>
          </table></td>
        </tr>
      <tr>
        <td style="padding:0px 10px"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tbody>
            <tr>
              <td bgcolor="#84a743" style="border-bottom:1px solid #4f7605;padding:10px;font-size:14px"><strong style="color:white">Shipping Address</strong><strong style="float:right;text-align:right;color:white">Order Id :  '.$orderid.'</strong></td>
              </tr>
            <tr>
              <td style="font-size:0;padding:0 10px 10px 10px" align="left" bgcolor="#dff2e0">
                <table width="200" cellpadding="0" cellspacing="0" border="0" align="left" style="font-family:arial;font-size:12px;text-align:left;color:#333333">
                  <tbody><tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top" width="60"><strong>Name</strong></td>
                    <td valign="top">'.$booking_name.' </td>
                    </tr>
                  <tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top"><strong>Email</strong></td>
                    <td valign="top"><a href="#m_2299037813007463152_" style="display:block;color:#333333;word-break:break-all;text-decoration:none">'.$booking_email.'</a></td>
                    </tr>
					 <tr>
                    <td valign="top"><strong>Delivery Time</strong></td>
                    <td valign="top"><a href="#m_2299037813007463152_" style="display:block;color:#333333;word-break:break-all;text-decoration:none"> '.$delivery_date.' between '.$start .' - '.$end.'</a></td>
                    </tr>
					 <tr>
                    <td valign="top"><strong>Before Time</strong></td>
                    <td valign="top"><a href="#m_2299037813007463152_" style="display:block;color:#333333;word-break:break-all;text-decoration:none">'.$btime.'</a></td>
                    </tr>
                  </tbody></table>
                
                <table width="200" cellpadding="0" cellspacing="0" border="0" align="left" style="font-family:arial;font-size:12px;text-align:left;color:#333333">
                  <tbody><tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top" width="60"><strong>Mobile</strong></td>
                    <td valign="top">'.$booking_phone.'</td>
                    </tr>
                  <tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top"><strong>Address</strong></td>
                    <td valign="top">'.$booking_address.'</td>
                    </tr>
                  </tbody></table>';
				  
				$html.='</td>
              </tr>
            </tbody>
          </table></td>
        </tr>
      <tr>
        <td height="20"></td>
        </tr>
      <tr>
        <td width="600" align="center" style="padding:0px 10px">
          
          
          
          <table width="98%" border="0" bgcolor="#dff2e0" align="center" cellpadding="8" cellspacing="0" style="font-family:arial;font-size:12px;text-align:left;color:#333333;max-width:600px">
            <tbody>
			
			    <tr>
					 <th style="padding:3px;border:1px solid black">Category</th>
					 <th style="padding:3px;border:1px solid black">Product</th>
					 <th style="padding:3px;border:1px solid black">Price</th>
					 <th style="padding:3px;border:1px solid black">Discount(%)</th>';
					
					 $html.='<th style="padding:3px;border:1px solid black">Qty</th>
					 <th style="padding:3px;border:1px solid black">Attribute</th>
					 <th style="padding:3px;border:1px solid black">Total</th>
			    </tr>';
		
         $totQty=0;		
         $totPrc=0;		
         $totDur=0;		
		foreach($orderData as $rw)
		{
		    $pid=$rw['pro_id'];
			
			    $product_attribute = $rw['product_attribute'] ;
			    $getatt=$this->db->query("SELECT * FROM salo_product_attribute WHERE `id`='$product_attribute'")->result_array();
				$unitid = $getatt[0]['unit_id'];
				$attvalue = $getatt[0]['attribute_value'];
				$unitatt=$this->db->query("SELECT * FROM salo_unit WHERE `id`='$unitid'")->result_array();
				$unitname = $unitatt[0]['unit'];
			
			
			
			
			$proData=$this->admin_model->getData('salo_products',array('id' => $pid),null);
			$catid=$proData[0]['sub_category_id'];
			$proTitle=$proData[0]['title'];
			
			
		    $subCatData=$this->admin_model->getData('salo_sub_category',array('sub_category_id' => $catid),null);
			$sub_category_name=$subCatData[0]['sub_category_name'];
			
			
				$html.=' <tr>
				 
						   <td style="padding:3px;border:1px solid black">'.$sub_category_name.'</td>
						   <td style="padding:3px 10px 3px 3px;border:1px solid black">'.$proTitle.'</td>
						   <td style="text-align:center;padding:3px;border:1px solid black">'.$rw['unit_price'].'</td>
						   <td style="text-align:center;padding:3px;border:1px solid black">'.$rw['discount'].'</td>';
						    $html.='<td style="padding:3px;text-align:center;border:1px solid black">'.$rw['quantity'].'</td>
						    <td style="text-align:center;padding:3px;border:1px solid black">'.$attvalue .' '.$unitname.'</td>
						   <td style="text-align:center;padding:3px;border:1px solid black">'.$rw['total_price'].'</td>
						   
						</tr>';
				
				$totQty= $totQty + $rw['quantity'];
				$totPrc= $totPrc + $rw['total_price'];
				$totDur= $totDur + $proData[0]['duration'];
			}
				$html.='<tr>
					<td colspan="4" style="text-align:right;padding:3px;border:1px solid black">Total</td>';
					
					$html.='<td style="padding:3px;text-align:right;border:1px solid black">'.$totQty.'</td><td style="padding:3px;text-align:right;border:1px solid black"></td>
					<td style="text-align:right;padding:3px;border:1px solid black">Rs. '.$totPrc.'</td>
				</tr>';
				
				if($ordershippingData[0]['promo_code_id']!=0)
		        {		
                   $shipping_charge=$ordershippingData[0]['shipping_charge'];
                   $dis=$ordershippingData[0]['promo_discount'];
                   $final_amount=$ordershippingData[0]['final_amount'];			  
				    $html.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Promo Discount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$dis.'</td>
				 
				 </tr>';
				 
				 if($shipping_charge!=0)
				 {
				   $html.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Shipping Charge</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$shipping_charge.'</td>
				 
				 </tr>';
				 
				 $finalAmt = $final_amount + $shipping_charge;
				 }
				 else
				 {
				   $finalAmt = $final_amount;
				 }
				$html.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Final Amount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black"><i class="fa fa-inr"></i> '.$finalAmt.'</td>
				 
				 </tr>';
		        }
				else
				{
				  $shipping_charge=$ordershippingData[0]['shipping_charge'];
				  $final_amount=$ordershippingData[0]['final_amount'];
				   if($shipping_charge!=0)
					 {
					   $finalAmt = $final_amount + $shipping_charge;
					   $html.='<tr>
					
					   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Shipping Charge</td>
					   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$shipping_charge.'</td>
					 
					 </tr>
					 <tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Final Amount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black"><i class="fa fa-inr"></i> '.$finalAmt.'</td>
				 
				 </tr>';
					 
					
					 }
				}
			$html.='<tr>
				<td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Payment mode</td>
				<td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$payment_mode.'</td>
			</tr>';
            $html.='</tbody></table></td>
      </tr>
 
      </tbody>
    </table></td>
</tr>
<tr>
  <td align="center" style="padding-top:0px"><table align="center" cellpadding="0" cellspacing="0" class="m_2299037813007463152container" style="width:100%">
    <tbody><tr>
      <td align="center" style="text-align:center;vertical-align:top;font-size:0;padding:10px">
        <div style="width:240px;display:inline-block;vertical-align:top;text-align:center;font-size:0" align="center">
          <table width="220" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px">
            <tbody><tr>
              <td width="30" align="center" style="padding:5px"><a href="'.base_url().'" target="_blank" data-saferedirecturl="'.base_url().'"><img src="'.base_url().'assets/template/images/icon/www.png" alt="" width="30" height="30" style="margin:0" border="0" vspace="0" hspace="0" align="absmiddle" class="CToWUd"></a></td>
              <td align="left" style="padding:0px 0px;font-size:16px;font-family: Open Sans,Gill Sans,Arial,Helvetica,sans-serif"><a href="'.base_url().'" style="color:#444444;text-decoration:none" target="_blank" data-saferedirecturl="'.base_url().'">Ghar Ghar Bazaar</a></td>
              </tr>
            </tbody></table>
          </div>
        
        <div style="width:200px;display:inline-block;vertical-align:top;text-align:center;font-size:0" align="center">
          <table width="180" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px">
            <tbody><tr>
              <td width="30" align="center" style="padding:5px"><a href="'.base_url().'" target="_blank" data-saferedirecturl="'.base_url().'"><img src="'.base_url().'assets/template/images/icon/call.png" alt="" width="30" height="30" style="margin:0" border="0" vspace="0" hspace="0" align="absmiddle" class="CToWUd"></a></td>
              <td align="left" style="padding:0px 0px;font-size:16px;font-family:Open Sans,Gill Sans,Arial,Helvetica,sans-serif"><a style="color:#444444;text-decoration:none" href="tel:'.$siteDetails[0]['company_phone'].'" target="_blank">'.$siteDetails[0]['company_phone'].'</a></td>
              </tr>
            </tbody></table>
          </div>
        </td>
      </tr>
    </tbody></table></td>
</tr>
</tbody></table>';

                        $from="no-reply@ghargharbazaar.com";
						$subject="Thank You for shopping with ".$siteDetails[0]['company_name'];
						$to=$userData[0]['email'];			
						$headers  = 'MIME-Version: 1.0' . "\r\n";
						$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
						$headers .= 'From: '.$from . "\r\n";
						mail($to,$subject,$html,$headers);


						
/***************************** User Mail Ends *************************/

						
						
/***************************** Admin Mail Starts *************************/						
						
						
$htmlAdmin='<table cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" style="background:#ffffff;border:1px solid #e3e3e3;border-radius:3px;max-width:640px">
<tbody><tr>
  <td width="620" align="center"><table border="0" cellpadding="0" cellspacing="0" align="center" style="max-width:620px">
    <tbody><tr>
      <td align="center" style="border-bottom:1px dotted #e3e3e3"><a href="'.base_url().'" target="_blank" data-saferedirecturl="'.base_url().'">
        <div style="width:100%"><img src="'.base_url().'assets/uploads/logo/'.$siteDetails[0]['company_logo'].'" border="0" alt="" style="margin:0;display:block;max-width:200px;width:inherit" vspace="0" hspace="0" align="center" class="CToWUd"></div>
        </a></td>
    </tr>

    </tbody></table></td>
</tr>
<tr>
  <td align="center" style="padding:0px"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-family:arial;font-size:12px;text-align:left;color:#333333">
    <tbody>
      <tr>
        <td style="padding:0px 0px"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" style="max-width:600px;font-family:arial;font-size:14px;text-align:left;color:#333333">
          <tbody>
            <tr>
              <td style="padding:10px">Hello ,</td>
              </tr>
            <tr>
              <td style="padding:0px 10px 20px 10px">You have recieved a new order from '.$name.'.Here are the details.</td>
              </tr>
            </tbody>
          </table></td>
        </tr>
      <tr>
        <td style="padding:0px 10px"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tbody>
            <tr>
              <td bgcolor="#84a743" style="border-bottom:1px solid #4f7605;padding:10px;font-size:14px"><strong style="color:white">Shipping Address</strong><strong style="float:right;text-align:right;color:white">Order Id :  '.$orderid.'</strong></td>
              </tr>
            <tr>
              <td style="font-size:0;padding:0 10px 10px 10px" align="left" bgcolor="#dff2e0">
                <table width="200" cellpadding="0" cellspacing="0" border="0" align="left" style="font-family:arial;font-size:12px;text-align:left;color:#333333">
                  <tbody><tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top" width="60"><strong>Name</strong></td>
                    <td valign="top">'.$booking_name.' </td>
                    </tr>
                  <tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top"><strong>Email</strong></td>
                    <td valign="top"><a href="#m_2299037813007463152_" style="display:block;color:#333333;word-break:break-all;text-decoration:none">'.$booking_email.'</a></td>
                    </tr>
					 <tr>
                    <td valign="top"><strong>Delivery Time</strong></td>
                    <td valign="top"><a href="#m_2299037813007463152_" style="display:block;color:#333333;word-break:break-all;text-decoration:none"> '.$delivery_date.' between '.$start .' - '.$end.'</a></td>
                    </tr>
					 <tr>
                    <td valign="top"><strong>Before Time</strong></td>
                    <td valign="top"><a href="#m_2299037813007463152_" style="display:block;color:#333333;word-break:break-all;text-decoration:none">'.$btime.'</a></td>
                    </tr>
                  </tbody></table>
                
                <table width="200" cellpadding="0" cellspacing="0" border="0" align="left" style="font-family:arial;font-size:12px;text-align:left;color:#333333">
                  <tbody><tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top" width="60"><strong>Mobile</strong></td>
                    <td valign="top">'.$booking_phone.'</td>
                    </tr>
                  <tr>
                    <td height="10" colspan="2"></td>
                    </tr>
                  <tr>
                    <td valign="top"><strong>Address</strong></td>
                    <td valign="top">'.$booking_address.'</td>
                    </tr>
                  </tbody></table>';
				  
				
				
                $htmlAdmin.='</td>
              </tr>
            </tbody>
          </table></td>
        </tr>
      <tr>
        <td height="20"></td>
        </tr>
      <tr>
        <td width="600" align="center" style="padding:0px 10px">
          
           <table width="98%" border="0" bgcolor="#dff2e0" align="center" cellpadding="8" cellspacing="0" style="font-family:arial;font-size:12px;text-align:left;color:#333333;max-width:600px">
            <tbody>
			
			    <tr>
					 <th style="padding:3px;border:1px solid black">Category</th>
					 <th style="padding:3px;border:1px solid black">Product</th>
					 <th style="padding:3px;border:1px solid black">Price</th>
					 <th style="padding:3px;border:1px solid black">Discount(%)</th>';
					 $htmlAdmin.='<th style="padding:3px;border:1px solid black">Qty</th>
					 <th style="padding:3px;border:1px solid black">Attribute</th>
					 <th style="padding:3px;border:1px solid black">Total</th>
			    </tr>';
		
        $totQty=0;		
        $totPrc=0;		
        $totDur=0;		
		foreach($orderData as $rw)
		{
		        $pid=$rw['pro_id'];
			    $product_attribute = $rw['product_attribute'] ;
			    $getatt=$this->db->query("SELECT * FROM salo_product_attribute WHERE `id`='$product_attribute'")->result_array();
				$unitid = $getatt[0]['unit_id'];
				$attvalue = $getatt[0]['attribute_value'];
				$unitatt=$this->db->query("SELECT * FROM salo_unit WHERE `id`='$unitid'")->result_array();
				$unitname = $unitatt[0]['unit'];
			
			$proData=$this->admin_model->getData('salo_products',array('id' => $pid),null);
			$catid=$proData[0]['sub_category_id'];
			$proTitle=$proData[0]['title'];
			
			
		    $subCatData=$this->admin_model->getData('salo_sub_category',array('sub_category_id' => $catid),null);
			$sub_category_name=$subCatData[0]['sub_category_name'];
			
			
				$htmlAdmin.=' <tr>
				           <td style="padding:3px;border:1px solid black">'.$sub_category_name.'</td>
						   <td style="padding:3px 10px 3px 3px;border:1px solid black">'.$proTitle.'</td>
						   <td style="text-align:center;padding:3px;border:1px solid black">'.$rw['unit_price'].'</td>
						   <td style="text-align:center;padding:3px;border:1px solid black">'.$rw['discount'].'</td>';
						   $htmlAdmin.='<td style="padding:3px;text-align:center;border:1px solid black">'.$rw['quantity'].'</td>
						   <td style="text-align:center;padding:3px;border:1px solid black">'.$attvalue .' '.$unitname.'</td>
						   <td style="text-align:center;padding:3px;border:1px solid black">'.$rw['total_price'].'</td>
						   
						</tr>';
				
				$totQty= $totQty + $rw['quantity'];
				$totPrc= $totPrc + $rw['total_price'];
				$totDur= $totDur + $proData[0]['duration'];
			}
				$htmlAdmin.='<tr>
					<td colspan="4" style="text-align:right;padding:3px;border:1px solid black">Total</td>';
					$htmlAdmin.='<td style="padding:3px;text-align:right;border:1px solid black">'.$totQty.'</td><td style="padding:3px;text-align:right;border:1px solid black"></td>
					<td style="text-align:right;padding:3px;border:1px solid black">Rs. '.$totPrc.'</td>
				</tr>';
				
			if($ordershippingData[0]['promo_code_id']!=0)
		        {		
                   $shipping_charge=$ordershippingData[0]['shipping_charge'];
                   $dis=$ordershippingData[0]['promo_discount'];
                   $final_amount=$ordershippingData[0]['final_amount'];			  
				    $htmlAdmin.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Promo Discount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$dis.'</td>
				 
				 </tr>';
				 
				 if($shipping_charge!=0)
				 {
				   $htmlAdmin.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Shipping Charge</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$shipping_charge.'</td>
				 
				 </tr>';
				 
				 $finalAmt = $final_amount + $shipping_charge;
				 }
				 else
				 {
				   $finalAmt = $final_amount;
				 }
				$htmlAdmin.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Final Amount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black"><i class="fa fa-inr"></i> '.$finalAmt.'</td>
				 
				 </tr>';
		        }
				else
				{
				  $shipping_charge=$ordershippingData[0]['shipping_charge'];
				  $final_amount=$ordershippingData[0]['final_amount'];
				   if($shipping_charge!=0)
					 {
					   $finalAmt = $final_amount + $shipping_charge;
					   $htmlAdmin.='<tr>
					
					   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Shipping Charge</td>
					   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$shipping_charge.'</td>
					 
					 </tr>
					 <tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Final Amount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black"><i class="fa fa-inr"></i> '.$finalAmt.'</td>
				 
				 </tr>';
					 
					
					 }
				}
             $htmlAdmin.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Payment mode</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$payment_mode.'</td>
				 
				 </tr>';

			
           $htmlAdmin.=' </tbody></table></td>
      </tr>
 
      </tbody>
    </table></td>
</tr>
<tr>
  <td align="center" style="padding-top:0px"><table align="center" cellpadding="0" cellspacing="0" class="m_2299037813007463152container" style="width:100%">
    <tbody><tr>
      <td align="center" style="text-align:center;vertical-align:top;font-size:0;padding:10px">
        <div style="width:240px;display:inline-block;vertical-align:top;text-align:center;font-size:0" align="center">
          <table width="220" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px">
            <tbody><tr>
              <td width="30" align="center" style="padding:5px"><a href="'.base_url().'" target="_blank" data-saferedirecturl="'.base_url().'"><img src="'.base_url().'assets/template/images/icon/www.png" alt="" width="30" height="30" style="margin:0" border="0" vspace="0" hspace="0" align="absmiddle" class="CToWUd"></a></td>
              <td align="left" style="padding:0px 0px;font-size:16px;font-family: Open Sans,Gill Sans,Arial,Helvetica,sans-serif"><a href="'.base_url().'" style="color:#444444;text-decoration:none" target="_blank" data-saferedirecturl="'.base_url().'">Ghar Ghar Bazaar</a></td>
              </tr>
            </tbody></table>
          </div>
        
        <div style="width:200px;display:inline-block;vertical-align:top;text-align:center;font-size:0" align="center">
          <table width="180" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px">
            <tbody><tr>
              <td width="30" align="center" style="padding:5px"><a href="'.base_url().'" target="_blank" data-saferedirecturl="'.base_url().'"><img src="'.base_url().'assets/template/images/icon/call.png" alt="" width="30" height="30" style="margin:0" border="0" vspace="0" hspace="0" align="absmiddle" class="CToWUd"></a></td>
              <td align="left" style="padding:0px 0px;font-size:16px;font-family:Open Sans,Gill Sans,Arial,Helvetica,sans-serif"><a style="color:#444444;text-decoration:none" href="tel:'.$siteDetails[0]['company_phone'].'" target="_blank">'.$siteDetails[0]['company_phone'].'</a></td>
              </tr>
            </tbody></table>
          </div>
        </td>
      </tr>
    </tbody></table></td>
</tr>
</tbody></table>';
            $webDetails=$this->adminDetails();
            $fromAdmin="no-reply@ghargharbazaar.com";
			$subjectAdmin="New Order Recieved on Ghar Ghar Bazaar";
			$toAdmin= $webDetails[0]['company_email'];			
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'From: '.$fromAdmin . "\r\n";
			mail($toAdmin,$subjectAdmin,$htmlAdmin,$headers);
						
			/***************************** Admin Mail Ends *************************/
		
		$data['status']="True";
		$data['msg']="Done";
	  }
	  else
	  {
	    $data['status']="False";
		$data['msg']="Order Id Not Found";
	  }
		echo json_encode($data);
	}
	
	public function staticPage($type)
	{
	    switch($type)
	    {
	        case "success":
	            
	             $this->load->view('success');
	             
	        break;
	            
	        case "failure":
	            
	            $this->load->view('failure');
	            
	        break;
	    }
	}
	
	
	public function registerVendor()
	{
	    $postData=$_POST;
	    $device_id=$postData['device_id'];
		$name=$postData['name'];
	    $email=$postData['email'];
	    $contact=$postData['mobile'];
	    $password=$postData['password'];
		$referred_by=$postData['referred_by'];
		
	    if(($name!="") && ($email!="") && ($contact!="") && ($password!=""))
	    {
			$dateTime=date('Y-m-d H:i:s');
			$getReferredBy=$this->admin_model->getData('salo_users',array('referral_code' => $referred_by),null);
			if((count($getReferredBy)==0) && ($referred_by!="") && (!empty($referred_by)))
			{
			    $data['status'] = "False";
				$data['type'] = '400';
				$data['error'] = 'Invalid referral code';
				$data['msg']    = "Done";
			}
			else
			{
              $getUser=$this->admin_model->getData('salo_users',array('email' => $email),null);
			  if(count($getUser)==0)
			  {
			  
			    $size=6;
                $referralcode =strtoupper(substr(md5(time().rand(10000,99999)), 0,$size)); 
				
				$referral_code="GGBV".$referralcode;
			    
				
				$up=$this->admin_model->insertData('salo_users',array('referral_code' => $referral_code,'user_type' => 2,'name' => $name,'email' => $email,'contact' => $contact,'password' => $password,'added_on' => $dateTime,'status' => 1,'referred_by' => $referred_by));
				

				if((count($getReferredBy)!=0) && ($referred_by!="") && (!empty($referred_by)))
				{
					$getAdmin=$this->admin_model->getData('salo_admin_login',array('role' => 0),null);
				    $referralPoint=$getAdmin[0]['referral_point'];
				
				    $getReferredBy=$this->admin_model->getData('salo_users',array('referral_code' => $referred_by),null);
					$userId=$getReferredBy[0]['id'];
					$walletAmount=$getReferredBy[0]['wallet_amount'];
					$finalwalletAmount=$walletAmount + $referralPoint;
					  
					$updt=$this->admin_model->updateData('salo_users',array('id' => $userId),array('wallet_amount' => $finalwalletAmount));
				}
					
				$getUser=$this->admin_model->getData('salo_users',array('id' => $up),null);
				$first_login_status=$getUser[0]['first_login_status'];
				
				$upDt=$this->admin_model->updateData('salo_users',array('id' => $up),array('first_login_status' => 1));
				
				$admindt=$this->admin_model->getData('salo_admin_login',array('role' => 0),null);
				$referral_point=$admindt[0]['referral_point'];
				   
				   
				$data['status'] = "True";
				$data['data']=array('id' => $up,'referral_code' => $referral_code,'referral_point' => $referral_point,'user_type' => '2','name' => $name,'email' => $email,'password' => $password,'contact' => $contact,'first_login_status' => $first_login_status);
				$data['type'] = '200';
				$data['success'] = 'true';
				$data['msg']    = "Done";
				
			  }else{ 
			  
			    $data['status'] = "False";
				$data['type'] = '400';
				$data['error'] = 'Email already registered';
				$data['msg']    = "Done";
				
			  }  			  
			}
		}
		else
		{
			$data['status'] = "False";
			$data['type']   = '400';
	        $data['error']  = 'All Fields Are Required';
			$data['msg']    = "Done";		  
		}		
		echo json_encode($data);
	}
	
	public function referredByUser()
	{
	    $postData=$_POST;
	    $device_id=$postData['device_id'];
		$user_id=$postData['user_id'];
		$referred_by=$postData['referred_by'];
		
		$getReferredBy=$this->admin_model->getData('salo_users',array('referral_code' => $referred_by),null);
	    if((count($getReferredBy)!=0) && ($referred_by!="") && (!empty($referred_by)))
		{
			$getAdmin=$this->admin_model->getData('salo_admin_login',array('role' => 0),null);
			$referralPoint=$getAdmin[0]['referral_point'];
			
			$userId=$getReferredBy[0]['id'];
			$walletAmount=$getReferredBy[0]['wallet_amount'];
			$finalwalletAmount=$walletAmount + $referralPoint;
			
			$updt1=$this->admin_model->updateData('salo_users',array('id' => $userId),array('wallet_amount' => $finalwalletAmount));
			
			$getUser=$this->admin_model->getData('salo_users',array('id' => $userId),null);
			
			$upDt=$this->admin_model->updateData('salo_users',array('id' => $userId),array('first_login_status' => 1));
			
			$uid=$getUser[0]['id'];
		    $user_type=$getUser[0]['user_type'];
			$referral_code=$getUser[0]['referral_code'];
			$uname=$getUser[0]['name'];
			$uemail=$getUser[0]['email'];
			$ucontact=$getUser[0]['contact'];
			$password=$getUser[0]['password'];
			$first_login_status=$getUser[0]['first_login_status'];
				  
				  
		    $arr=array(array('id' => $uid,'user_type' => $user_type,'referral_code' => $referral_code,'name' => $uname,'email' => $uemail,'password' => $password,'contact' => $ucontact,'first_login_status' => $first_login_status));
				   
		    $data['status'] = "True";
			$data['data']=$arr;
			$data['type'] = '200';
			$data['success'] = 'true';
			$data['msg']    = "Done";
		}
		else
		{
			$data['status'] = "False";
			$data['type'] = '400';
			$data['error'] = 'Invalid referral code';
			$data['msg']    = "Done";
		}
		echo json_encode($data);
	}
	
	
	public function offerProducts($mobileDeviceId,$loggedInUserId,$offType)
	{
		    $data['status']="True";
		    
			switch($offType)
			{
				case "combo_pack":
				
				    $Products = $this->admin_model->getQueryOrderBy('salo_combo_packs',array('offer_type' => array('=','1'),'status' => array('=','1')),'DESC','id');
				  
				    $proImgFolder="combo-packs";
				    
				    $productType="2";
				  
				break;
				
				case "buy_get_free":

				    $Products = $this->admin_model->getQueryOrderBy('salo_combo_packs',array('offer_type' => array('=','2'),'status' => array('=','1')),'DESC','id');
				    
					$proImgFolder="combo-packs";
					
					$productType="2";
					
				break;
				
				default:
				  
				  $exoffType=explode('_',$offType);
				  $matchVal=$exoffType[1];
			
				   $Products = $this->admin_model->getBasic("SELECT * FROM salo_products WHERE status='1' AND (id IN (SELECT product_id FROM  salo_product_attribute WHERE `discount` <= '$matchVal')) ORDER BY id DESC");
				   
				   $proImgFolder="products";
				   
				   $productType="1";
				   
				break;
			
			}

	    $productArray=array();
	    foreach($Products as $lr)
		{
			$id=$lr['id'];
			$title=$lr['title'];
			$image=$lr['image'];
			$stock_status=$lr['stock_status'];
			$description=$lr['description'];
			$price = $lr['price'];
			$discount_type = $lr['discount_type'];
			$discount = $lr['discount'];
            $attribute = $lr['pack_attribute'];
			
			if($image!="")
			{
			 $imageUrl=$this->config->item('base_image_url')."assets/uploads/".$proImgFolder."/".$image;
			}
			else
			{
			 $imageUrl="";
			}
		
			  
			  
			   if($price=="")
			  {
			    $price="0.00";
			  }
			  else
			  {
			    $price=$price;
			  }
			
			  if($discount=="")
			  {
				  $discount="0";
				  $sellAmt=$price;
			  }
			  else
			  {
				   $discount=$discount;
				   $disper=$discount / 100;
				   $disamt = $price * $disper;
				   $sellAmt=$price - $disamt;
				   $sellAmt=round($sellAmt);
			  }
			  
              
			  if(($offType!='combo_pack') && ($offType!='buy_get_free'))
			  {
			      $attributePrice=array();
				  $Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE product_id='$id' and `status`='1' ORDER BY price ASC");
					 foreach($Productprice as $pr_pr)
					 {
						  $attributeid = $pr_pr['id'];
						  $attribute_value = $pr_pr['attribute_value'];
						  $unit_id = $pr_pr['unit_id'];
						  $priceAtt = $pr_pr['price'];
						  
						  if($priceAtt=="")
						  {
							$priceAtt="0.00";
						  }
						  else
						  {
							$priceAtt=$priceAtt;
						  }
						  
						  $discountAtt = $pr_pr['discount'];
						  if($discountAtt=="")
						  {
							  $discountAtt="0";
							  $sellAmtAtt=$priceAtt;
						  }
						  else
						  {
							   $discountAtt=$discountAtt;
							   $disper=$discountAtt / 100;
							   $disamt = $priceAtt * $disper;
							   $sellAmtAtt=$priceAtt - $disamt;
							   $sellAmtAtt=round($sellAmtAtt);
						  }
						  
						
						  $unit = $this->admin_model->getData('salo_unit',array('id' => $unit_id),null);
						  $unitName=$unit[0]['unit'];
						  

                       if($loggedInUserId==0)
					   {
							$colName="device_id";
							$colVal=$mobileDeviceId;
							  
							$whereArr=array($colName => $colVal,'user_id' => '0' ,'product_attribute' => $attributeid,'status' => 0);
							  
					   }
					   else
					   {
							$colName="user_id";
							$colVal=$loggedInUserId;
							  
							$whereArr=array($colName => $colVal,'product_attribute' => $attributeid,'status' => 0);
					   }
						
						$getCartDataAtt=$this->admin_model->getData('salo_temp_cart',$whereArr,'AND');
						if(count($getCartDataAtt)!=0)
						{
						  $prod_cart_qty_att=$getCartDataAtt[0]['quantity'];
						}
						else
						{
						  $prod_cart_qty_att="0";
						}
						
						 $attributeName = $attribute_value." ".$unitName;
						 $arrAtt=array('attribute_id' => $attributeid,'attribute_name' => $attributeName,'listprice' => $priceAtt,'saleprice' => $sellAmtAtt,'discount' => $discountAtt,'attribute_cart_qty' => $prod_cart_qty_att);
						 array_push($attributePrice,$arrAtt);
					 }
			 
			  }
			  else
			  {
			  
			         if($loggedInUserId==0)
					   {
							$colName="device_id";
							$colVal=$mobileDeviceId;
							  
							$whereArr=array($colName => $colVal,'user_id' => '0' ,'pro_id' => $id,'product_type' => 2,'status' => 0);
							  
					   }
					   else
					   {
							$colName="user_id";
							$colVal=$loggedInUserId;
							  
							$whereArr=array($colName => $colVal,'pro_id' => $id,'product_type' => 2,'status' => 0);
					   }
						
						$getCartDataAtt=$this->admin_model->getData('salo_temp_cart',$whereArr,'AND');
						if(count($getCartDataAtt)!=0)
						{
						  $prod_cart_qty_att=$getCartDataAtt[0]['quantity'];
						}
						else
						{
						  $prod_cart_qty_att="0";
						}
						
			    $attributePrice=array('attribute_id' => 0,'attribute_name' => $attribute,'listprice' => $price,'saleprice' => $sellAmt,'discount' => $discount,'attribute_cart_qty' => $prod_cart_qty_att);
			  }
			  
			 $prodId=$id;
			 if($loggedInUserId==0)
    		 {
    			$colName="device_id";
    			$colVal=$mobileDeviceId;
    			  
    			$whereArr=array($colName => $colVal,'user_id' => '0' , 'pro_id' => $prodId,'product_type' => 2,'status' => 0);
    			  
    		}
    		else
    		{
    			$colName="user_id";
    			$colVal=$loggedInUserId;
    			  
    			$whereArr=array($colName => $colVal,'pro_id' => $prodId,'product_type' => 2,'status' => 0);
    		}
			
			$getCartData=$this->admin_model->getData('salo_temp_cart',$whereArr,'AND');
			if(count($getCartData)!=0)
			{
			  $prod_cart_qty=$getCartData[0]['quantity'];
			}
			else
			{
			  $prod_cart_qty="0";
			}
			 
			$arr = array('id' => $id,'title' => $title,'image' => $imageUrl,'product_type' => $productType,'attribute' => $attribute , 'listprice' => $price, 'saleprice' => $sellAmt, 'discount' => $discount,'stock_status' => $stock_status,'prod_cart_qty' => $prod_cart_qty,'attributeList' => $attributePrice);
			
			array_push($productArray,$arr);

		}
		
		$data['products']=$productArray;
		$data['msg']="Done";
		echo json_encode($data);
	}
	
	
	
public function getsingleOfferProduct($mobileDeviceId,$loggedInUserId,$proid)
	{
        $data['status']="true";
		$Products=$this->admin_model->getData('salo_combo_packs',array('status' => 1,'id' => $proid),'AND');
		$productArray=array();
		$lr=$Products[0];
	    $id=$lr['id'];
		$title=$lr['title'];
		$stock_status=$lr['stock_status'];
		$image=$lr['image'];
		$description=$lr['description'];
		$price=$lr['price'];
		$discount=$lr['discount'];
		$added_on=$lr['added_on'];
			
			if($image!="")
			{
			    $imageUrl=$this->config->item('base_image_url')."assets/uploads/combo-packs/".$image;
			}
			else
			{
				$imageUrl="";
			}
			
	
											  
		
		    $attributeid = "0";
			$attribute = $lr['pack_attribute'];
			
			 if($price=="")
			  {
			    $price="0.00";
			  }
			  else
			  {
			    $price=$price;
			  }
			  
			  
			  if($discount=="")
			  {
				  $discount="0";
				  $sellAmt=$price;
			  }
			  else
			  {
				   $discount=$discount;
				   $disper=$discount / 100;
				   $disamt = $price * $disper;
				   $sellAmt=$price - $disamt;
				   $sellAmt=round($sellAmt);
			  }
			 
			
			$arr = array('product_id' => $id,'title' => $title,'product_type' => 2,'image' => $imageUrl,'description' => $description,'attribute' => $attribute , 'listprice' => $price, 'saleprice' => $sellAmt,'discount' => $discount,'stock_status' => $stock_status,'added_on' => $added_on);
			array_push($productArray,$arr);

			
			   if($loggedInUserId==0)
				   {
						$colName="device_id";
						$colVal=$mobileDeviceId;
						  
						$whereArr=array($colName => $colVal,'user_id' => '0' ,'product_type' => '2','pro_id' => $id,'status' => 0);
						  
				   }
				   else
				   {
						$colName="user_id";
						$colVal=$loggedInUserId;
						  
						$whereArr=array($colName => $colVal,'product_type' => '2','pro_id' => $id,'status' => 0);
				   }
					
					$getCartDataAtt=$this->admin_model->getData('salo_temp_cart',$whereArr,'AND');
					if(count($getCartDataAtt)!=0)
					{
					  $prod_cart_qty_att=$getCartDataAtt[0]['quantity'];
					}
					else
					{
					  $prod_cart_qty_att="0";
					}

             $priceArray=array();
            $arr = array('attributeid' => $attributeid,'attribute_value' => $attribute,'unit_id' => '0','unitName' => '','discount'=>$discount,'price' => $price,'discounted_price' => $sellAmt,'attribute_cart_qty' => $prod_cart_qty_att);
			array_push($priceArray,$arr);

		  $data['productList']=$productArray;
		  $data['attributeList']=$priceArray;
		
	
        $data['msg']="Done";
		echo json_encode($data);
	}
	
	
	public function addtoCartOffer()
	{
		
		$userid=$_REQUEST['userid'];
		$device_id=$_REQUEST['device_id'];
		$pid=$_REQUEST['pid'];
		$qty=$_REQUEST['qty'];

		
		$getProAtt = $this->admin_model->getData('salo_combo_packs',array('id' => $pid),null);
	    $attributid = $getProAtt[0]['id'];
	    $unitPrice = $getProAtt[0]['price'];
	    $discount = $getProAtt[0]['discount'];
		
		if(($userid==0) || ($userid==""))
		{
		 $uid=0;
		 $getCart=$this->admin_model->getBasic("SELECT * FROM `salo_temp_cart` WHERE device_id='$device_id' and user_id='$uid' and `status`='0' and `pro_id`='$pid' and `product_type`='2'");
		 $conditionVariable="device_id='$device_id' and user_id='$uid'";  	
		 $conditionCol="device_id"; 	
		 $conditionColVar=$device_id; 	
		}
		else
		{
		  $uid=$userid;
		  $getCart=$this->admin_model->getBasic("SELECT * FROM `salo_temp_cart` WHERE user_id='$userid' and `status`='0' and `pro_id`='$pid' and `product_type`='2'");	
		  $conditionVariable="user_id='$userid'"; 
		  $conditionCol="user_id"; 	
		  $conditionColVar=$userid; 
		}
		
		$cartCount=count($getCart);
		$dateTime=date('Y-m-d H:i:s');
	    if($cartCount!=0)
	    {
			 $rowId=$getCart[0]['id'];
		     $prevQty=$getCart[0]['quantity'];
		 
		      $finalQty = $prevQty + $qty;
		      if($discount !=''){
			  $disamt = $unitPrice * ($discount / 100);												 
			  $discountedprice = $unitPrice - $disamt ;	
			  $discountedprice =   round($discountedprice);
			  
			   $totalPrice=$finalQty * $discountedprice;
			  }else{
			   $totalPrice=$finalQty * $unitPrice;  
			  }
		     
	          $total_duration="";
			 
			  $up = $this->admin_model->updateData('salo_temp_cart',array('quantity' => $finalQty,'unit_price' => $unitPrice,'discount' =>$discount,'total_price' => $totalPrice,'updated_on' => $dateTime,'total_duration' => $total_duration,'device_id' => $device_id,'product_type'=> 2),array('id' => $rowId));
			  
			  $getSum=$this->db->query("SELECT SUM(total_price) as tp FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0'")->result_array();
			  
			  $cDate=date('Y-m-d');
			  $getpro = $this->admin_model->getBasic("SELECT * FROM  salo_temp_cart WHERE $conditionVariable and `status`='0' and promo_code_id!='0' and promo_code_id IN (SELECT id FROM salo_coupon WHERE expiry_date>='$cDate' and `status`='0')");
			  if(count($getpro)!=0)
			  {
                 $discount=$getpro[0]['promo_discount'];
				 $totalPrice=$getSum[0]['tp'];
				 $dis=$discount / 100;
				 $disAmt = $totalPrice * $dis;
				 $finalAmt=$totalPrice - $disAmt;
				 $finalAmt=round($finalAmt,2);
				 
				 if(($uid==0))
				 {
				  	$up=$this->admin_model->updateData('salo_temp_cart',array('grand_total' => $totalPrice,'final_amount' => $finalAmt),array('device_id' => $device_id,'user_id' => $uid,'status' => 0));	
				 }
				 else
				 {
					$up=$this->admin_model->updateData('salo_temp_cart',array('grand_total' => $totalPrice,'final_amount' => $finalAmt),array('user_id' => $uid,'status' => 0));	 
				 }
				
				 
				 $getsData=$this->db->query("SELECT * FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0'")->result_array();
				 $orderid=$getsData[0]['order_id'];
				 if($orderid!="")
				 {
					$up=$this->admin_model->updateData('salo_cart_shipping_details',array('grand_total' => $totalPrice,'final_amount' => $finalAmt),array('order_id' => $orderid));
				 }
							
			  }
              else
			  {
					$totalPrice=$getSum[0]['tp'];
					
					 if(($uid==0))
					 {
						$up=$this->admin_model->updateData('salo_temp_cart',array('grand_total' => $totalPrice,'promo_code' => '','promo_code_id' => '0','promo_discount' => '','final_amount' => $totalPrice),array('device_id' => $device_id,'user_id' => $uid,'status' => 0));	
					 }
					 else
					 {
						$up=$this->admin_model->updateData('salo_temp_cart',array('grand_total' => $totalPrice,'promo_code' => '','promo_code_id' => '0','promo_discount' => '','final_amount' => $totalPrice),array('user_id' => $uid,'status' => 0));	 
					 }
								
					$getsData=$this->db->query("SELECT * FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0'")->result_array();
					$orderid=$getsData[0]['order_id'];
					if($orderid!="")
					{
					 $up=$this->admin_model->updateData('salo_cart_shipping_details',array('grand_total' => $totalPrice,'promo_code' => '','promo_code_id' => '0','promo_discount' => '','final_amount' => $totalPrice),array('order_id' => $orderid));
					}
			   }			  
						   
			 
			 $getCart=$this->admin_model->getBasic("SELECT SUM(quantity) as qty FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0'"); 
			 $cartCount=$getCart[0]['qty'];
			 
			   if((is_nan($up)==false) || ($up == "")){
				   
				$data['status']="True";
				$data['type'] = '200';
				$data['data'] = $cartCount;
	            $data['msg'] = 'Cart Updated successfully';	
			  }else{
				$data['status']="False";
				$data['type'] = '400';
				$data['data'] = $cartCount;
	            $data['msg'] = 'Sorry,something went wrong.Please try again'; 
			  }
	    }
		else
		{
			if($discount !=''){
			  $disamt = 	$unitPrice * ($discount / 100);												 
			  $discountedprice = $unitPrice - $disamt ;	
			  $discountedprice =   round($discountedprice);
			  
			  $totalPrice=$qty * $discountedprice;
			}else{
			  $totalPrice=$qty * $unitPrice;  
			}
		   
		    $total_duration="";
		   
		    $up=$this->admin_model->insertData('salo_temp_cart',array('user_id' => $uid,'pro_id' => $pid,'quantity' => $qty,'unit_price' => $unitPrice,'discount' =>$discount,'total_price' => $totalPrice,'added_on' => $dateTime,'total_duration' => $total_duration,'product_type'=> 2,'device_id' => $device_id));
			
			$getSum=$this->db->query("SELECT SUM(total_price) as tp FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0'")->result_array();
			  
			  $cDate=date('Y-m-d');
			  $getpro = $this->admin_model->getBasic("SELECT * FROM  salo_temp_cart WHERE $conditionVariable and `status`='0' and promo_code_id!='0' and promo_code_id IN (SELECT id FROM salo_coupon WHERE expiry_date>='$cDate' and `status`='0')");
			  if(count($getpro)!=0)
			  {
                 $discount=$getpro[0]['promo_discount'];
				 $totalPrice=$getSum[0]['tp'];
				 $dis=$discount / 100;
				 $disAmt = $totalPrice * $dis;
				 $finalAmt=$totalPrice - $disAmt;
				 $finalAmt=round($finalAmt,2);
				 
				 if($uid==0)
				 {
					$up=$this->admin_model->updateData('salo_temp_cart',array('grand_total' => $totalPrice,'final_amount' => $finalAmt),array('device_id' => $device_id,'user_id' => $uid,'status' => 0)); 
				 }
				 else
				 {
					$up=$this->admin_model->updateData('salo_temp_cart',array('grand_total' => $totalPrice,'final_amount' => $finalAmt),array('user_id' => $uid,'status' => 0));  
				 }
				 
				 
				 $getsData=$this->db->query("SELECT * FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0'")->result_array();
				 $orderid=$getsData[0]['order_id'];
				 if($orderid!="")
				 {
					$up=$this->admin_model->updateData('salo_cart_shipping_details',array('grand_total' => $totalPrice,'final_amount' => $finalAmt),array('order_id' => $orderid));
				 }
							
			  }
              else
			  {
					$totalPrice=$getSum[0]['tp'];
					
					 if($uid==0)
					 {
                        $up=$this->admin_model->updateData('salo_temp_cart',array('grand_total' => $totalPrice,'promo_code' => '','promo_code_id' => '0','promo_discount' => '','final_amount' => $totalPrice),array('device_id' => $device_id,'user_id' => $uid,'status' => 0));
					 }
					 else
					 {
						 $up=$this->admin_model->updateData('salo_temp_cart',array('grand_total' => $totalPrice,'promo_code' => '','promo_code_id' => '0','promo_discount' => '','final_amount' => $totalPrice),array('user_id' => $uid,'status' => 0));
					 }
				 
				 
					
								
					$getsData=$this->db->query("SELECT * FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0'")->result_array();
					$orderid=$getsData[0]['order_id'];
					if($orderid!="")
					{
					 $up=$this->admin_model->updateData('salo_cart_shipping_details',array('grand_total' => $totalPrice,'promo_code' => '','promo_code_id' => '0','promo_discount' => '','final_amount' => $totalPrice),array('order_id' => $orderid));
					}
			   }
			   
			 
             $getCart=$this->admin_model->getBasic("SELECT SUM(quantity) as qty FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0'"); 
			 $cartCount=$getCart[0]['qty'];
			 

	         if((is_nan($up)==false) || ($up == "")){
				$data['status']="True";
				$data['type'] = '200';
				$data['data'] = $cartCount;
	            $data['msg'] = 'Cart Updated successfully';	
			 }else{
				$data['status']="False";
				$data['type'] = '400';
				$data['data'] = $cartCount;
	            $data['msg'] = 'Sorry,something went wrong.Please try again'; 
			 }
		}
		
		echo json_encode($data);
	}	
}

?>