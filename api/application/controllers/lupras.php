<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Lupras extends CI_Controller{

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
	
	public function splash()
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
			
			
			//****************** New Arrivals Starts ********************************//
			
		$latestProducts = $this->admin_model->getQueryOrderByLimit('salo_products',array('status' => array('=','0'),'product_type' => array('=','1')),'DESC','id',0,10); 
		
	    $productArray=array();
	    foreach($latestProducts as $lr)
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
              

			$arr = array('id' => $id,'title' => $title,'image' => $imageUrl,'attribute' => $attribute , 'listprice' => $price, 'saleprice' => $sellAmt, 'discount' => $discount,'stock_status' => $stock_status);
			
			array_push($productArray,$arr);
			
		   
		}
       
        $data['newArrivals']=$productArray;
		
			//******************  New Arrivals Ends ********************************//
		
		
			//******************  Most Popular Starts ********************************//
		
		
		$mostPopular = $this->admin_model->getBasic("SELECT pro_id , count(pro_id) cnt FROM salo_temp_cart WHERE `product_type`='1' group by pro_id order by cnt desc limit 0,10"); 
		
	    $productsecArray=array();
	    foreach($mostPopular as $ss)
		{
			$proId=$ss['pro_id'];
			$getPro=$this->db->query("SELECT * FROM salo_products WHERE `id`='$proId'")->result_array();
			$lr=$getPro[0];
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
			
		    $Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE product_id='$proId' and `status`='1' ORDER BY price ASC LIMIT 1");
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
            
			$arr = array('id' => $proId,'title' => $title,'image' => $imageUrl,'attribute' => $attribute , 'listprice' => $price, 'saleprice' => $sellAmt, 'discount' => $discount,'stock_status' => $stock_status);
			array_push($productsecArray,$arr);
			
		   
		}
       
        $data['mostPopular']=$productsecArray;
		
		//******************  Most Popular Ends ********************************//
		
		
			//******************  Most Popular Starts ********************************//
		
		
		$mostPopular = $this->admin_model->getBasic("SELECT pro_id , count(pro_id) cnt FROM salo_temp_cart WHERE `product_type`='1' group by pro_id order by cnt desc limit 0,10"); 
		
	    $productsecArray=array();
	    foreach($mostPopular as $ss)
		{
			$proId=$ss['pro_id'];
			$getPro=$this->db->query("SELECT * FROM salo_products WHERE `id`='$proId'")->result_array();
			$lr=$getPro[0];
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
			
		     $Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE product_id='$proId' and `status`='1' ORDER BY price ASC LIMIT 1");
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
            
			$arr = array('id' => $proId,'title' => $title,'image' => $imageUrl,'attribute' => $attribute , 'listprice' => $price, 'saleprice' => $sellAmt, 'discount' => $discount,'stock_status' => $stock_status);
			array_push($productsecArray,$arr);
			
		   
		}
       
        $data['mostPopular']=$productsecArray;
		
		//******************  Most Popular Ends ********************************//
		
		
		//******************  Recent Sold Starts ********************************//
		
		
		$recentSold = $this->admin_model->getQueryOrderByLimit('salo_temp_cart',array('status' => array('=','1'),'product_type' => array('=','1')),'DESC','id',0,10); 
		
	    $productthArray=array();
	    foreach($recentSold as $ss)
		{
			$proId=$ss['pro_id'];
			$getPro=$this->db->query("SELECT * FROM salo_products WHERE `id`='$proId'")->result_array();
			$lr=$getPro[0];
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
			
		    $Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE product_id='$proId' and `status`='1' ORDER BY price ASC LIMIT 1");
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
            
			$arr = array('id' => $proId,'title' => $title,'image' => $imageUrl,'attribute' => $attribute , 'listprice' => $price, 'saleprice' => $sellAmt, 'discount' => $discount,'stock_status' => $stock_status);
			array_push($productthArray,$arr);
			
		   
		}
       
        $data['recentSold']=$productthArray;
		
		//******************   Recent Sold Ends ********************************//
		
		
		//******************  Menu Starts ********************************//
		
		$a=0;
		$menuList=array();
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
					
					 $thirdCat = $this->admin_model->getData('salo_sub_sub_category',array('status' => 0,'sub_category_id' => $sid),'AND');
					 if(count($thirdCat)!=0)
					 {
					    $islastSecond="false";
					 }
					 else
					 {
					    $islastSecond="true";
					 }
					
					$arrsub=array('id' => $sid,'name' => $sub_category_name,'img' => $subimageUrl,'islast' => $islast,'islast' => $islastSecond);
					array_push($secondcatArray,$arrsub);
					$c++;
				}
			}
			else
			{
			    $islastFirst="true";
				$secondcatArray="";
			}
			$arr=array('id' => $cid,'name' => $category_name,'img' => $imageUrl,'islast' => $islastFirst,'sub_cat' => $secondcatArray);
			array_push($menuList,$arr);
			$a++;
		}
		
		$data['menuList']=$menuList;
		
			//******************  Menu Ends ********************************//
			
			
			
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
			$coupon_code_status=$lr['coupon_code_status'];
			
			if($coupon_code_status==1)
			{
			  $codeId=$lr['active_coupon_id'];
			  $cdate=date('Y-m-d');
			  $cupon = $this->db->query("SELECT * FROM salo_coupon WHERE `id`='$codeId' and `expiry_date` >= '$cdate' and status='0'")->result_array();
			  if(count($cupon)!=0)
			  {
			    $couponCode=$cupon[0]['coupon_code'];
			    $couponDiscount=$cupon[0]['discount'];
			  }
			  else
			  {
			     $couponCode="No Coupon";
			     $couponDiscount="0";
			  }
			}
			else
			{
			   $couponCode="No Coupon";
			   $couponDiscount="0";
			}
			
			
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
           
			
			$arr = array('product_id' => $id,'title' => $title,'image' => $imageUrl,'description' => $description,'catId'=>$catId,'catName'=>$CategoryName,'subcatId'=>$subcatId,'SubCategoryName'=>$SubCategoryName,'subsubcatId'=>$subsubcatId,'SubSubCategoryName'=>$SubSubCategoryName,'brandid'=>$brandId,'attribute' => $attribute , 'listprice' => $price, 'saleprice' => $sellAmt, 'discount' => $discount,'coupon_code' => $couponCode,'coupon_discount' => $couponDiscount,'stock_status' => $stock_status,'added_on' => $added_on);
			array_push($productArray,$arr);
			
	
		
		$Productprice = $this->admin_model->getData('salo_product_attribute',array('product_id' => $proid,'status' =>'1'),'AND');
		$priceArray = array();
		foreach($Productprice as $pr)
		{
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
			
			$unit = $this->admin_model->getData('salo_unit',array('id' => $unit_id),null);
			$unitName=$unit[0]['unit'];
			 
            $arr = array('attributeid' => $attributeid,'attribute_value' => $attribute_value,'unit_id' => $unit_id,'unitName' => $unitName,'price' => $price,'discount'=>$discount);
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
				
				if($image!="")
				{
				 $imageUrl=$this->config->item('base_image_url')."assets/uploads/products/".$image;
				}
				else
				{
				 $imageUrl="";
				}
				
				$Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE product_id='$id' and `status`='1' ORDER BY price ASC LIMIT 1");
				$prr=$Productprice[0];
			 
				  $attributeid = $prr['id'];
				  $attribute_value = $prr['attribute_value'];
				  $unit_id = $prr['unit_id'];
				  $price = $prr['price'];
				  
				   if($price=="")
				  {
					$price="0.00";
				  }
				  else
				  {
					$price=$price;
				  }
			  
			  
				  $discount = $prr['discount'];
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
				
				if($image!="")
				{
				 $imageUrl=$this->config->item('base_image_url')."assets/uploads/products/".$image;
				}
				else
				{
				 $imageUrl="";
				}
				
				$Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE product_id='$id' and `status`='1' ORDER BY price ASC LIMIT 1");
				$prr=$Productprice[0];
			 
				  $attributeid = $prr['id'];
				  $attribute_value = $prr['attribute_value'];
				  $unit_id = $prr['unit_id'];
				  $price = $prr['price'];
				  
				   if($price=="")
				  {
					$price="0.00";
				  }
				  else
				  {
					$price=$price;
				  }
			  
			  
				  $discount = $prr['discount'];
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
				
				if($image!="")
				{
				 $imageUrl=$this->config->item('base_image_url')."assets/uploads/products/".$image;
				}
				else
				{
				 $imageUrl="";
				}
				
				$Productprice = $this->admin_model->getBasic("select * FROM salo_product_attribute WHERE product_id='$id' and `status`='1' ORDER BY price ASC LIMIT 1");
				$prr=$Productprice[0];
			 
				  $attributeid = $prr['id'];
				  $attribute_value = $prr['attribute_value'];
				  $unit_id = $prr['unit_id'];
				  $price = $prr['price'];
				  
				   if($price=="")
				  {
					$price="0.00";
				  }
				  else
				  {
					$price=$price;
				  }
			  
			  
				  $discount = $prr['discount'];
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
				
				array_push($relatedArray,$arr);

			}
			
		}
		
        $data['related_product']=$relatedArray;
        $data['msg']="Done";
		echo json_encode($data);
	}
	
	
	 public function categoryItem($catType,$catId,$order_by,$pageNo,$offset,$packsize,$subCatList)
	{
		if(($packsize!=0) || ($subCatList!=0))
		{
		
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
					
				
		          $refineCat = $this->admin_model->getData('salo_sub_sub_category',array('category_id' => $category_id,'status' => 0),'AND');
		
				  $catpro = $this->db->query("SELECT * FROM salo_products WHERE category_id ='$category_id'")->result_array();	
				   
				  $Products = $this->admin_model->getQueryOrderBy('salo_products',array('status' => array('=','0'),'product_type' => array('=','1'), 'category_id' => array('=',$category_id)),'DESC','id');
				  $conditionCol="category_id";
		          $conditionVal=$category_id;
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
					
					$refineCat = $this->admin_model->getData('salo_sub_sub_category',array('sub_category_id' => $category_id,'status' => 0),'AND');
					
					$catpro = $this->db->query("SELECT * FROM salo_products WHERE sub_category_id ='$category_id'")->result_array();	
					
					$Products = $this->admin_model->getQueryOrderBy('salo_products',array('status' => array('=','0'),'product_type' => array('=','1'), 'sub_category_id' => array('=',$category_id)),'DESC','id'); 
		            $conditionCol="sub_category_id";
		            $conditionVal=$category_id;
				break;
				
				case "cat_lvl3":
				
				    $Category=$this->admin_model->getData('salo_sub_sub_category',array('id' => $catId),null);
					$lr=$Category[0];
				
					$category_id=$lr['id'];
					$sub_category_id=$lr['sub_category_id'];
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
					
					$refineCat = $this->admin_model->getData('salo_sub_sub_category',array('sub_category_id' => $sub_category_id,'status' => 0),'AND');
					
					$catpro = $this->db->query("SELECT * FROM salo_products WHERE sub_sub_category_id ='$category_id'")->result_array();
					
					$Products = $this->admin_model->getQueryOrderBy('salo_products',array('status' => array('=','0'),'product_type' => array('=','1'), 'sub_sub_category_id' => array('=',$category_id)),'DESC','id'); 
		            $conditionCol="sub_sub_category_id";
		            $conditionVal=$category_id;
				break;
			}
			
			
			
			$arr=array('id' => $category_id,'name' => $category_name,'image' => $imageUrl,'description' => $description,'islast' => $islast);
			
		$filterArray=array();
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
		  $filterSearch=array('id' => 1,'name' => 'Package Size','data' => $filterArr);
		  array_push($filterArray,$filterSearch);	
		}
		
if(count($refineCat) > 0){
			
			$menuList=array();
			$a=0;
			foreach($refineCat as $rt)
			{
				$id=$rt['id'];
				$cid=$rt['category_id'];
				$sid=$rt['sub_category_id'];
				$name=$rt['name'];
				
				$arr=array('id' => $id,'main_category_id' => $cid,'sub_category_id' => $sid,'name' => $name);
				array_push($menuList,$arr);
				$a++;
			}
			
			$filterRefine=array('id' => 2,'name' => 'Refine By Category','data' => $menuList);
			 array_push($filterArray,$filterRefine);	
		}
		
		
		//$filterArray=$filterSearch;
		
		  if($packsize!=0)
		  {
		    $exPackSize=explode('-',$packsize);
			$limit=count($exPackSize);
			for($i= 0; $i<$limit; $i++)
			{
			 $getselected[] = $exPackSize[$i];
			}
		  }
		  
			   if($order_by=="OTN")
				{
				  $orderBy="ASC";
				}
				else
				{
				  $orderBy="DESC";
				}
				
				
		  if($subCatList!=0)
		  {
		
		     $exsubCatList=explode('-',$subCatList);
			 $limitCat=count($exsubCatList);
			 $subCatList=0;
			for($i= 0; $i<$limitCat; $i++)
			{
			 $sbid = $exsubCatList[$i];
			 if($subCatList==0)
			 {
			   $subCatList=$sbid;
			 }
			 else
			 {
			  $subCatList=$subCatList.",".$sbid;
			 }
			}
			
		     $allProList = $this->admin_model->getBasic("SELECT * FROM salo_products WHERE `status`='0' AND `product_type`='1' and sub_sub_category_id IN ($subCatList) ORDER BY id $orderBy");
		  }
		  else
		  {
			  switch($catType)
			  {
				case "cat_lvl1":
				
				 $allProList = $this->admin_model->getQueryOrderBy('salo_products',array('category_id' => array('=',$catId),'status' => array('=',0),'product_type' => array('=',1)),$orderBy,'id');
				 
				break;
				
				case "cat_lvl2":
				
				 $allProList = $this->admin_model->getQueryOrderBy('salo_products',array('sub_category_id' => array('=',$catId),'status' => array('=',0),'product_type' => array('=',1)),$orderBy,'id');
				 
				break;
				
				case "cat_lvl3":
				
				 $allProList = $this->admin_model->getQueryOrderBy('salo_products',array('sub_sub_category_id' => array('=',$catId),'status' => array('=',0),'product_type' => array('=',1)),$orderBy,'id');
				 
				break;
			  }
		 }
		  
		    $proarray = array();
			foreach($allProList as $prow){
			  $proarray[] = $prow['id'];
			}
			
			$prodmp = implode(',',$proarray);
			$prodmpCount = explode(',',$prodmp);
			if((count($prodmpCount) > 0) && ($prodmp!="") && (!empty($prodmp)))
			{
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
		      $checkl =  $this->db->query("SELECT * from salo_product_attribute where `attribute_value` = '$satt'  and `unit_id` = '$sunit' and product_id IN ($prodmp) group by product_id ORDER by id $orderBy")->result_array();
		     
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
		    $checkl =  $this->admin_model->getBasic("SELECT * from salo_product_attribute where product_id IN ($prodmp) group by product_id ORDER by id $orderBy");
		    foreach($checkl as $asrow){
			$dsjkfkj[] = $asrow['product_id'].'_'. $asrow['id'] ; 
			}
			$hjkhfd =  $dsjkfkj ;
		}
		
		if(($offset==0) || ($offset==""))
        {
		  $per_page=10;
		}
		else
		{
			 $per_page=$offset;
		}
		
		$limitCount = count($hjkhfd);
		$total_pages = ceil($limitCount / $per_page);//total pages we going to have		
		
		 if($limitCount > 0){
		    $productArray=array();
			if(($pageNo==1) || ($pageNo==0))
				{
				  $start=0;
				  if($limitCount<=$offset)
				  {
				    $limit=$limitCount;
				  }
				  else
				  {
				   $limit=$offset;
				  }
				  
				}
				else
				{
				  $pp=$pageNo - 1;
				  $sta = $pp * $offset;
				  $start=$sta + 1;
				  $limit = $offset * $pageNo;
				  
				}
			
			if($pageNo <= $total_pages)
		{
			  for($i= $start; $i<$limit; $i++)
			  {
			     $asd = $hjkhfd[$i];		
			     $assd =	explode('_',$asd);
if(count($assd)!=0)
{
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
			}
			
			$data['status']="True";
			$data['feature']=$arr;
		    $data['filter']=$filterArray;
			$data['products']=$productArray;
		    $data['totalPagination']=$total_pages;
			$data['msg']="Done";
		  }
		   else
		 {
				$data['status']="False";
				$data['msg']="No Result Found";
	     }  
         }
         else
		 {
				$data['status']="False";
				$data['msg']="No Result Found";
	     }     		 
		}
      else
		 {
				$data['status']="False";
				$data['msg']="No Result Found";
	     }		
			
			 
		}
		else
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
					
				
		          $refineCat = $this->admin_model->getData('salo_sub_sub_category',array('category_id' => $category_id,'status' => 0),'AND');
		
				  $catpro = $this->db->query("SELECT * FROM salo_products WHERE category_id ='$category_id'")->result_array();	
				   
				  $Products = $this->admin_model->getQueryOrderBy('salo_products',array('status' => array('=','0'),'product_type' => array('=','1'), 'category_id' => array('=',$category_id)),'DESC','id');
				  $conditionCol="category_id";
		          $conditionVal=$category_id;
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
					
					$refineCat = $this->admin_model->getData('salo_sub_sub_category',array('sub_category_id' => $category_id,'status' => 0),'AND');
					
					$catpro = $this->db->query("SELECT * FROM salo_products WHERE sub_category_id ='$category_id'")->result_array();	
					
					$Products = $this->admin_model->getQueryOrderBy('salo_products',array('status' => array('=','0'),'product_type' => array('=','1'), 'sub_category_id' => array('=',$category_id)),'DESC','id'); 
		            $conditionCol="sub_category_id";
		            $conditionVal=$category_id;
				break;
				
				case "cat_lvl3":
				
				    $Category=$this->admin_model->getData('salo_sub_sub_category',array('id' => $catId),null);
					$lr=$Category[0];
				
					$category_id=$lr['id'];
					$sub_category_id=$lr['sub_category_id'];
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
					
					$refineCat = $this->admin_model->getData('salo_sub_sub_category',array('sub_category_id' => $sub_category_id,'status' => 0),'AND');
					
					$catpro = $this->db->query("SELECT * FROM salo_products WHERE sub_sub_category_id ='$category_id'")->result_array();
					
					$Products = $this->admin_model->getQueryOrderBy('salo_products',array('status' => array('=','0'),'product_type' => array('=','1'), 'sub_sub_category_id' => array('=',$category_id)),'DESC','id'); 
		            $conditionCol="sub_sub_category_id";
		            $conditionVal=$category_id;
				break;
			}
			
			
			
			$arr=array('id' => $category_id,'name' => $category_name,'image' => $imageUrl,'description' => $description,'islast' => $islast);
			
		$filterArray=array();
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
		  $filterSearch=array('id' => 1,'name' => 'Package Size','data' => $filterArr);
		  array_push($filterArray,$filterSearch);	
		}
		
if(count($refineCat) > 0){
			
			$menuList=array();
			$a=0;
			foreach($refineCat as $rt)
			{
				$id=$rt['id'];
				$cid=$rt['category_id'];
				$sid=$rt['sub_category_id'];
				$name=$rt['name'];
				
				$arr=array('id' => $id,'main_category_id' => $cid,'sub_category_id' => $sid,'name' => $name);
				array_push($menuList,$arr);
				$a++;
			}
			
			$filterRefine=array('id' => 2,'name' => 'Refine By Category','data' => $menuList);
			 array_push($filterArray,$filterRefine);	
		}
		
		
		//$filterArray=$filterSearch;
		$data['feature']=$arr;
		$data['filter']=$filterArray;
		
		if(($offset==0) || ($offset==""))
        {
		  $per_page=10;
		}
		else
		{
			 $per_page=$offset;
		}	
		$total_results=count($Products);
        $total_pages = ceil($total_results / $per_page);//total pages we going to have		
		
		
		//-------------if page is setcheck------------------//
		if (($pageNo!="") || ($pageNo!=0)) {
			$show_page = $pageNo;             //it will telles the current page
			if ($show_page > 0 && $show_page <= $total_pages) {
				$start = ($show_page - 1) * $per_page;
				$end = $start + $per_page;
			} else {
					// error - show first set of results
					$start = 0;              
					$end = $per_page;
				}
		} else {
					// if page isn't set, show first set of results
					$start = 0;
					$end = $per_page;
		}
		
        if($order_by=="OTN")
		{
		  $orderBy="ASC";
		}
		else
		{
          $orderBy="DESC";
		}	
		
		if($pageNo > $total_pages)
		{
			$productArray="No More Products To Show";
		}
		else
		{
		$ProductsList = $this->admin_model->getQueryOrderByLimit('salo_products',array('status' => array('=','0'),'product_type' => array('=','1'), $conditionCol => array('=',$conditionVal)),$orderBy,'id',$start,$per_page); 									
	    $productArray=array();
	    foreach($ProductsList as $lr)
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
            
			$arr = array('id' => $id,'title' => $title,'image' => $imageUrl,'attribute' => $attribute , 'listprice' => $price, 'saleprice' => $sellAmt, 'discount' => $discount,'stock_status' => $stock_status);
			
			array_push($productArray,$arr);

		}
	   }
		
		$data['products']=$productArray;
		$data['totalPagination']=$total_pages;
		$data['msg']="Done";
		}
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
	
	
	public function register()
	{
		$device_id=$_REQUEST['device_id'];
		$pageName=$_REQUEST['page_name'];
		$name=$_REQUEST['name'];
	    $email=$_REQUEST['email'];
	    $contact=$_REQUEST['mobile'];
	    $password=$_REQUEST['password'];
		
		if(($name!="") && ($email!="") && ($contact!="") && ($password!=""))
	    {
			$dateTime=date('Y-m-d H:i:s');
            $getUser=$this->admin_model->getData('salo_users',array('email' => $email),null);
			  if(count($getUser)==0)
			  {
				$up=$this->admin_model->insertData('salo_users',array('name' => $name,'email' => $email,'contact' => $contact,'password' => $password,'added_on' => $dateTime,'status' => 1));
				
				$getUser=$this->admin_model->getData('salo_users',array('id' => $up),null);
				
				
				$chk=$this->admin_model->updateData('salo_temp_cart',array('user_id' => $up),array('status' => 0,'device_id' => $device_id,'user_id' => 0));
				

				$data['status'] = "True";
				$data['data']=array('id' => $up,'name' => $name,'email' => $email,'password' => $password,'contact' => $contact);
				$data['type'] = '200';
				$data['success'] = 'Registered Successfully.Now you can login';
				$data['msg']    = "Done";
				
			  }else{ 
			  
			    $data['status'] = "False";
				$data['type'] = '400';
				$data['error'] = 'Email already registered';
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
				   $uname=$getUser[0]['name'];
				   $uemail=$getUser[0]['email'];
				   $ucontact=$getUser[0]['contact'];
				   $password=$getUser[0]['password'];
				  
				  
				  $arr=array(array('id' => $uid,'name' => $uname,'email' => $uemail,'password' => $password,'contact' => $ucontact));
                  
				  $up=$this->admin_model->updateData('salo_temp_cart',array('user_id' => $uid),array('status' => 0,'device_id' => $device_id,'user_id' => 0));
				  
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

		$catpro = $this->db->query("SELECT * FROM salo_products WHERE `status`='0' and lower(title) LIKE '%$proName%' ORDER BY id")->result_array();	
		 
		$Products = $this->admin_model->getBasic("SELECT * FROM salo_products WHERE `status`='0' and lower(title) LIKE '%$proName%' ORDER BY id");
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
            
			$arr = array('id' => $id,'title' => $title,'image' => $imageUrl,'attribute' => $attribute , 'listprice' => $price, 'saleprice' => $sellAmt, 'discount' => $discount,'stock_status' => $stock_status);
			
			array_push($productArray,$arr);

		}
		
		
		
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
		 $getCart=$this->admin_model->getBasic("SELECT * FROM `salo_temp_cart` WHERE device_id='$device_id' and user_id='$uid' and `status`='0' and `pro_id`='$pid' and 'product_attribute'='$attributid'");
		 $conditionVariable="device_id='$device_id' and user_id='$uid'";  	
		 $conditionCol="device_id"; 	
		 $conditionColVar=$device_id; 	
		}
		else
		{
		  $uid=$userid;
		  $getCart=$this->admin_model->getBasic("SELECT * FROM `salo_temp_cart` WHERE user_id='$userid' and `status`='0' and `pro_id`='$pid' and 'product_attribute'='$attributid'");	
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
			 
			  $up = $this->admin_model->updateData('salo_temp_cart',array('quantity' => $finalQty,'unit_price' => $unitPrice,'discount' =>$discount,'total_price' => $totalPrice,'updated_on' => $dateTime,'total_duration' => $total_duration,'device_id' => $device_id,'product_attribute'=> $attributid),array('id' => $rowId));
			  
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
		   
		    $up=$this->admin_model->insertData('salo_temp_cart',array('user_id' => $uid,'pro_id' => $pid,'quantity' => $qty,'unit_price' => $unitPrice,'discount' =>$discount,'total_price' => $totalPrice,'added_on' => $dateTime,'total_duration' => $total_duration,'product_attribute'=> $attributid,'device_id' => $device_id));
			
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
          $getCart=$this->admin_model->getData('salo_temp_cart',array('device_id' => $device_id,'user_id' => '0','status' => 0),'AND'); 		  
		}
		else
		{
		  $conditionVariable="user_id='$uid'";  
          $conditionCol="user_id"; 	
		  $conditionColVar=$uid;
          $getCart=$this->admin_model->getData('salo_temp_cart',array('user_id' => $uid,'status' => 0),'AND'); 		  
		}
		
		$data['status']="True";
		
		$grandTotal=$getCart[0]['grand_total'];
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
            
			
			
			if($image!="")
			{
			 $imageUrl=$this->config->item('base_image_url')."assets/uploads/products/".$image;
			}
			else
			{
			 $imageUrl="";
			}
			
			$arr = array('id' => $id,'product_id' => $pid,'title' => $title,'image' => $imageUrl,'attribute_id' => $attId,'attribute' => $attribute ,'listprice' => $unit_price, 'discount' => $discount, 'saleprice' => $unitPrice,'quantity' => $quantity,'total_price' => $total_price);
			
			array_push($productArray,$arr);
		}
		
		//$discountAmount=$grandTotal - $final_amount;
		$data['products']=$productArray;
		$data['payable_price']=$grandTotal;
		//$data['discount_amount']=$discountAmount;
		//$data['finalAmount']=$final_amount;
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
		  $userId=$_REQUEST['user_id'];
		  $subd['user_id'] = $userId;

	      $subd['delivery_time'] = $_REQUEST['delivery_time'];
	      $subd['delivery_date'] = $_REQUEST['delivery_date'];
		  $subd['name'] = $_REQUEST['name'];
          $subd['contact'] = $_REQUEST['contact'];	
          $subd['email'] = $_REQUEST['email'];	
          $subd['address'] = $_REQUEST['address'];	
          $subd['state'] = $_REQUEST['state'];	
          $subd['city'] = $_REQUEST['city'];
          $subd['country'] = $_REQUEST['country'];	
          $subd['pincode'] = $_REQUEST['pincode'];		  
		  $subd['added_on'] = date('Y-m-d H:i:s');	
		  
		  $subd['billing_name'] = $_REQUEST['billing_name'];
          $subd['billing_contact'] =$_REQUEST['billing_contact'];	
          $subd['billing_email'] =$_REQUEST['billing_email'];	
          $subd['billing_address'] = $_REQUEST['billing_address'];	
          $subd['billing_state'] = $_REQUEST['billing_state'];	
          $subd['billing_city'] = $_REQUEST['billing_city'];
          $subd['billing_country'] = $_REQUEST['billing_country'];	
          $subd['billing_pincode'] = $_REQUEST['billing_pincode'];  
		  $subd['before_time'] = $_REQUEST['beforetime'];
		  
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
		  
		  $subd['grand_total'] = $grandTotal; 
		  $subd['promo_code'] = $promoCode; 
		  $subd['promo_code_id'] = $promoCodeId; 
		  $subd['promo_discount'] = $discount; 
		  $subd['final_amount'] = $finalAmount; 
		  
		  
		  $checkData = $this->admin_model->getBasic("SELECT * FROM  salo_temp_cart WHERE `user_id`='$userId' and `status`='0'");
	
		  $order_id = $checkData[0]['order_id'];
		  
		  $dataUpdate['order_id'] = $orderid;
		 
		  if($order_id=="")
		  {
		    $uid=$this->admin_model->insertData('salo_cart_shipping_details',$subd);
			$this->db->update('salo_temp_cart', $dataUpdate, array('user_id' => $userId,'status' => '0'));
		  }
		  else
		  {
			  
		    $uid=$this->admin_model->updateData('salo_cart_shipping_details',$subd,array('order_id' => $orderid));
		  }
		
			 $data['status']="True";
			 
			 $data['orderid']=$orderid;
			 $data['msg']="Done";
			 
		   
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
			 
			 $chkPro=$this->db->query("SELECT * FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0' and (`pro_id` IN (SELECT id FROM salo_products WHERE `active_coupon_id`='$couponId' and `coupon_code_status`='1'))")->result_array();
			
			 if(count($chkPro)!=0)
			 {
				 
				$getSum=$this->db->query("SELECT SUM(total_price) as tp FROM `salo_temp_cart` WHERE $conditionVariable and `status`='0'")->result_array();
				$totalPrice=$getSum[0]['tp'];
				$dis=$discount / 100;
				$disAmt = $totalPrice * $dis;
				$finalAmt=$totalPrice - $disAmt;
				$finalAmt=round($finalAmt,2);
				
				$up=$this->admin_model->updateData('salo_temp_cart',array('grand_total' => $totalPrice,'promo_code' => $prcode,'promo_code_id' => $couponId,'promo_discount' => $discount,'final_amount' => $finalAmt),array('order_id' => $orderid,'status' => 0));
			
			    $up=$this->admin_model->updateData('salo_cart_shipping_details',array('grand_total' => $totalPrice,'promo_code' => $prcode,'promo_code_id' => $couponId,'promo_discount' => $discount,'final_amount' => $finalAmt),array('order_id' => $orderid));
				
				
				$data['status']="True";
				$data['finalAmount']=$finalAmt;
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
	
	public function cashonDelivery($orderId)
	{
		
		$prot = $this->admin_model->getBasic("SELECT * FROM  salo_temp_cart WHERE order_id='$orderId'");
		if(count($prot)!=0)
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
                   $dis=$ordershippingData[0]['promo_discount'];
                   $finalAmt=$ordershippingData[0]['final_amount'];			  
				    $html.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Promo Discount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$dis.'</td>
				 
				 </tr>
				 <tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Final Amount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black"><i class="fa fa-inr"></i> '.$finalAmt.'</td>
				 
				 </tr>';
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
              <td align="left" style="padding:0px 0px;font-size:16px;font-family: Open Sans,Gill Sans,Arial,Helvetica,sans-serif"><a href="'.base_url().'" style="color:#444444;text-decoration:none" target="_blank" data-saferedirecturl="'.base_url().'">Lupras</a></td>
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

                        $from="no-reply@lupras.com";
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
                   $dis=$ordershippingData[0]['promo_discount'];
                   $finalAmt=$ordershippingData[0]['final_amount'];			  
				   $htmlAdmin.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Promo Discount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$dis.'</td>
				 
				 </tr>
				 <tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Final Amount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black"><i class="fa fa-inr"></i> '.$finalAmt.'</td>
				 
				 </tr>';
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
              <td align="left" style="padding:0px 0px;font-size:16px;font-family: Open Sans,Gill Sans,Arial,Helvetica,sans-serif"><a href="'.base_url().'" style="color:#444444;text-decoration:none" target="_blank" data-saferedirecturl="'.base_url().'">Lupras</a></td>
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
            $fromAdmin="no-reply@lupras.com";
			$subjectAdmin="New Order Recieved on Lupras";
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
	  
	  $finalAmt=$orderData[0]['final_amount'];
	  $promo_code_id=$orderData[0]['promo_code_id'];
	  
	 
	   
	  if($promo_code_id!=0)
	  {
		  $promo_code=$orderData[0]['promo_code'];
		  $grand_total=$orderData[0]['grand_total'];
		  $promo_discount=$orderData[0]['promo_discount'];
		  
		  $arr=array('order_id' => $oid,'quantity' => $qty,'promo_code' => $promo_code,'grand_total' => $grand_total,'promo_discount' => $promo_discount,'final_amount' => $finalAmt);
		  $prArry=array();
		 
	  }
	  else
	  {
		$arr=array('order_id' => $oid,'quantity' => $qty,'final_amount' => $finalAmt);
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
		   $finalAmt=$ordersList[0]['final_amount'];
		   $grand_total=$ordersList[0]['grand_total'];
		   $added_on=$shippingDetails[0]['added_on'];
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
		   
		   $qty=$qtyList[0]['qty'];
		   $discounted_amount = $grand_total - $finalAmt;
		   $arr=array('order_id' => $orderId,'total_quantity' => $qty,'payable_price' => $grand_total,'discounted_amount' => $discounted_amount,'final_amount' => $finalAmt,'order_date' => $added_on,'shipping_address' => $shippingDetails);
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
	  if(count($orderData)!=0)
	  {
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
	  
	  $finalAmt=$orderData[0]['final_amount'];
	  $grand_total=$orderData[0]['grand_total'];
	  $promo_code_id=$orderData[0]['promo_code_id'];
	  
	 
	      $promo_code=$orderData[0]['promo_code'];
		  $grand_total=$orderData[0]['grand_total'];
		  $promo_discount=$orderData[0]['promo_discount'];
		  
		  $dis_amount=$grand_total - $finalAmt;
		  $arr=array('order_id' => $oid,'quantity' => $qty,'grand_total' => $grand_total,'discounted_amount' => $dis_amount,'final_amount' => $finalAmt);
		 // $prArry=array();
		 
	 
	  $arr=$arr + $orderArr;
	  
      $art=array('deliverytime' => $deliveryTime);
	  //array_push($shippingDetails,$art);
	  $ar=$shippingDetails[0];
	  $ar=array_merge($ar,$art);
      $shippingDetails[0]=$ar;
	  $data['status']="True";
	  $data['orderDetail']=$arr;
	  $data['itemList']=$orderDataArr;
	  $data['shippingAddress']=$shippingDetails;
	  $data['msg']="Done";
	  }
	  else
	  {
	   $data['status']="False";
	   $data['msg']="Order Id Not Found";
	  }
	  echo json_encode($data);
	  
	}
	
	public function trackOrder($oid)
	{
		
	  $orderData=$this->admin_model->getData('salo_temp_cart',array('order_id' => $oid),null);
	  $finalAmt=$orderData[0]['final_amount'];
	  
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
		   
	  $arr=array('order_id' => $oid,'quantity' => $qty,'final_amount' => $finalAmt);
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
				</div><a href="'.$logoUrl.'" target="_blank" data-saferedirecturl="'.$logoUrl.'"><img alt="Lupras" hspace="5" vspace="15" border="0" src="'.$logoUrl.'assets/uploads/logo/logo.png" class="CToWUd" style="width:200px;"></a><br>
				<br>
				<br>Hello,
				<br><br>
				Someone requested for your password of Lupras Account.Here is your password.
				<br><br><br>
				<table cellspacing="1" cellpadding="10" style="margin:5px 15px;padding:5px;background-color:#eeeeee;color:#333333;font-family:Verdana,Arial,Helvetica">
					<tbody><tr style="background-color:#ffffff">
						<td width="150">Password    : </td><td>'.$password.' &nbsp;</td>
					</tr>
				</tbody></table>
				<br><br><br>
				Best Regards,
				<br>
				Lupras</div>';
				 
                $email = $checkpass[0]['email'];
				
                $subject="Lupras Account Password";
	
		        $from="no-reply@lupras.com";
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
		
		$getBasic=$this->admin_model->getBasic("SELECT * FROM salo_temp_cart WHERE order_id='$orderid'");
		$grandTotal=$getBasic[0]['grand_total'];
		$totalPrice=$getBasic[0]['final_amount'];
		/*$promo_discount=$getBasic[0]['promo_discount'];
		if($promo_discount=="")
		{
			$promo_discount=0;
		}
		*/
		$discountAmount=$grandTotal - $totalPrice;
		
		$arr=array('order_id' => $orderid,'total_quantity' => $qty,'payable_price' => $grandTotal,'discount' => $discountAmount,'total_price' => $totalPrice);
		
		$data['status']="True";
		$data['reviewList']=$arr;
		$data['msg']="Done";
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
                   $dis=$ordershippingData[0]['promo_discount'];
                   $finalAmt=$ordershippingData[0]['final_amount'];			  
				    $html.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Promo Discount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$dis.'</td>
				 
				 </tr>
				 <tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Final Amount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black"><i class="fa fa-inr"></i> '.$finalAmt.'</td>
				 
				 </tr>';
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
              <td align="left" style="padding:0px 0px;font-size:16px;font-family: Open Sans,Gill Sans,Arial,Helvetica,sans-serif"><a href="'.base_url().'" style="color:#444444;text-decoration:none" target="_blank" data-saferedirecturl="'.base_url().'">Lupras</a></td>
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

                        $from="no-reply@lupras.com";
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
                   $dis=$ordershippingData[0]['promo_discount'];
                   $finalAmt=$ordershippingData[0]['final_amount'];			  
				   $htmlAdmin.='<tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Promo Discount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black">'.$dis.'</td>
				 
				 </tr>
				 <tr>
				
				   <td colspan="4" style="padding:3px;text-align:right;border:1px solid black">Final Amount</td>
				   <td colspan="4" style="text-align:right;padding:3px;border:1px solid black"><i class="fa fa-inr"></i> '.$finalAmt.'</td>
				 
				 </tr>';
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
              <td align="left" style="padding:0px 0px;font-size:16px;font-family: Open Sans,Gill Sans,Arial,Helvetica,sans-serif"><a href="'.base_url().'" style="color:#444444;text-decoration:none" target="_blank" data-saferedirecturl="'.base_url().'">Lupras</a></td>
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
            $fromAdmin="no-reply@lupras.com";
			$subjectAdmin="New Order Recieved on Lupras";
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
	

}

?>