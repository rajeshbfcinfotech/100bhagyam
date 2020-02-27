<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Admin_model extends CI_Model {
    
    public function __construct(){
        parent::__construct();
        $this->load->helper('string');
		$this->load->library('image_lib');

    }
	
	
	// select from database
public function getData($tbl,$col,$operator)
{
 $set = "";
	   $x= 1;
   foreach($col as $name => $value)
	   {
	     
	      $set .= " $name = '$value' ";
		  if($x < count($col))
		  {
		     $set .= $operator ;
		  }
		  
		  $x++;
		
	   }
			
			
$sql= $this->db->query("SELECT * FROM $tbl WHERE $set");
$data=$sql->result_array();
return $data;
}

// get all data from database
public function getAll($tbl)
{
$sql = $this->db->query("SELECT * FROM $tbl");
$data=$sql->result_array();
return $data;
}


// insert data in database
public function insertData($tbl,$data)
{
$data = $this->db->insert("$tbl",$data);
$lastId = $this->db->insert_id();
return $lastId;
}

// Updating Data
public function updateData($tbl,$data,$where){
	$this->db->where($where)->update("$tbl",$data);
	$affected_rows = $this->db->affected_rows();
	return $affected_rows ;
}

// deleting Data
public function deleteData($tbl,$where)
{
$data = $this->db->delete("$tbl",$where);
return $data;
}


// select from database
public function getQuery($tbl,$col)
{
 $set = "";
	   $x= 1;
   foreach($col as $name => $value)
	   {
	      $operator=$value[0];
	      $value=$value[1];
		  if($operator=='like')
		  {
		     $set .= " lower($name) LIKE '%$value%' ";
		  }
		  else
		  {
		     $set .= " $name $operator '$value' ";
		  }
	     
		  if($x < count($col))
		  {
		     $set .=' and ';
		  }
		  
		  $x++;
		
	   }
$sql = $this->db->query("SELECT * FROM $tbl WHERE $set");
$data=$sql->result_array();
return $data;
}

// get all data from database
public function getBasic($sql)
{
$sql = $this->db->query($sql);
$data=$sql->result_array();
return $data;
}

// get all data from database
public function getBasicOther($sql)
{
$sql = $this->db->query($sql);
$data=$sql->result_array();
return $data;
}

// select from database
public function getQueryLimit($tbl,$col,$start,$end)
{
 $set = "";
	   $x= 1;
   foreach($col as $name => $value)
	   {
	      $operator=$value[0];
	      $value=$value[1];
		  if($operator=='like')
		  {
		     $set .= " $name LIKE '%$value%' ";
		  }
		  else
		  {
		     $set .= " $name $operator '$value' ";
		  }
	     
		  if($x < count($col))
		  {
		     $set .=' and ';
		  }
		  
		  $x++;
		
	   }
$sql = $this->db->query("SELECT * FROM $tbl WHERE $set LIMIT $start,$end");
$data=$sql->result_array();
return $data;
}

// select from database
public function getQueryOrderBy($tbl,$col,$order,$ordered_col)
{
 $set = "";
	   $x= 1;
   foreach($col as $name => $value)
	   {
	      $operator=$value[0];
	      $value=$value[1];
		  if($operator=='like')
		  {
		     $set .= " $name LIKE '%$value%' ";
		  }
		  else
		  {
		     $set .= " $name $operator '$value' ";
		  }
	     
		  if($x < count($col))
		  {
		     $set .=' and ';
		  }
		  
		  $x++;
		
	   }
$sql = $this->db->query("SELECT * FROM $tbl WHERE $set ORDER By $ordered_col $order");
$data=$sql->result_array();
return $data;
}


public function getwithLimitOrderBy($tbl,$where,$end_limit,$start_limit,$col,$order)
{
    $tbl=$this->db->dbprefix.$tbl;
	$this->db->order_by($col, $order);
	$query = $this->db->get_where($tbl,$where,$end_limit,$start_limit); 
    return $query->result();
}
		

// select from database
public function getQueryOrderByLimit($tbl,$col,$order,$ordered_col,$start,$end)
{
 $set = "";
	   $x= 1;
   foreach($col as $name => $value)
	   {
	      $operator=$value[0];
	      $value=$value[1];
		  if($operator=='like')
		  {
		     $set .= " $name LIKE '%$value%' ";
		  }
		  else
		  {
		     $set .= " $name $operator '$value' ";
		  }
	     
		  if($x < count($col))
		  {
		     $set .=' and ';
		  }
		  
		  $x++;
		
	   }
$sql = $this->db->query("SELECT * FROM $tbl WHERE $set ORDER By $ordered_col $order LIMIT $start,$end");
$data=$sql->result_array();
return $data;
}


// select from database
public function updateQuery($tbl,$dat,$col)
{
    
     $setdat = "";
	   $y= 1;
   foreach($dat as $name => $value)
	   {
	     
	      $setdat .= " $name = '$value' ";
		  if($y < count($dat))
		  {
		     $setdat .= ',' ;
		  }
		  
		  $y++;
		
	   }
	   
	   
 $set = "";
	   $x= 1;
   foreach($col as $name => $value)
	   {
	      $operator=$value[0];
	      $value=$value[1];
		  if($operator=='like')
		  {
		     $set .= " lower($name) LIKE '%$value%' ";
		  }
		  else
		  {
		     $set .= " $name $operator '$value' ";
		  }
	     
		  if($x < count($col))
		  {
		     $set .=' and ';
		  }
		  
		  $x++;
		
	   }
$sql = $this->db->query("UPDATE $tbl SET $setdat WHERE $set");
return $sql;
}

function get_category($category_name = false, $category_id = false) {
		$this->db->select("c.category_id, c.category_name,c.description,c.category_for,c.image, c.status");
		$this->db->from("salo_category c");
		if($category_name !== false && $category_name != ''){$this->db->where("c.category_name", $category_name);}
		if($category_id !== false && $category_id != ''){$this->db->where("c.category_id", $category_id);}
		$this->db->order_by("c.category_id", 'desc');
		$query = $this->db->get();
		return $query->result();
	}
	
	function save_add_category($category_id = false, $category_name,$description,$category_for) {
		$data['category_name'] = $category_name;
		$data['description'] = $description;
		$data['category_for'] = $category_for;
		if($category_id !== false && $category_id != '') {
			$addedby = $this->session->userdata('loggedInId') ;
			$data['updated_on'] = date('Y-m-d H:i:s');
			$data['updated_by'] = $addedby;
			$this->db->where("category_id", $category_id);
			return $this->db->update("salo_category", $data);
		} else {
			$addedby = $this->session->userdata('loggedInId') ;
			$data['added_on'] = date('Y-m-d H:i:s');
			$data['added_by'] = $addedby;
			$this->db->insert("salo_category", $data);
			return $this->db->insert_id();
		}	
	}
	
	function delete_category($category_id, $status) {
		$data['status'] = $status;
		$this->db->where("category_id", $category_id);
		return $this->db->update("salo_category", $data);
	}
	
	function get_sub_category($sub_category_name = false, $category_id = false, $sub_category_id = false) {
		$this->db->select("c.sub_category_id, c.category_id, c.sub_category_name, c.description,c.category_for, c.image, c.status, cat.category_name");
		$this->db->from("salo_sub_category c");
		$this->db->join("salo_category cat", "cat.category_id = c.category_id", "INNER");
		if($sub_category_name !== false && $sub_category_name != ''){$this->db->where("c.sub_category_name", $sub_category_name);}
		if($sub_category_id !== false && $sub_category_id != ''){$this->db->where("c.sub_category_id", $sub_category_id);}
		if($category_id !== false && $category_id != ''){$this->db->where("c.category_id", $category_id);}
		$this->db->order_by("c.sub_category_id", 'desc');
		$query = $this->db->get();
		return $query->result();
	}
	
	function save_add_sub_category($sub_category_id = false, $category_id, $sub_category_name,$description,$category_for) {
		$data['category_id'] = $category_id;
		$data['sub_category_name'] = $sub_category_name;
		$data['description'] = $description;
		$data['category_for'] = $category_for;
		if($sub_category_id !== false && $sub_category_id != '') {
			
			$addedby = $this->session->userdata('loggedInId') ;
			$data['updated_on'] = date('Y-m-d H:i:s');
			$data['updated_by'] = $addedby;
			
			$this->db->where("sub_category_id", $sub_category_id);
			return $this->db->update("salo_sub_category", $data);
		} else {
			$addedby = $this->session->userdata('loggedInId') ;
			$data['added_on'] = date('Y-m-d H:i:s');
			$data['added_by'] = $addedby;
			$this->db->insert("salo_sub_category", $data);
			return $this->db->insert_id();
		}	
	}

function parseString($string ) {
		$my_smilies = array(
       ':)' => '<img src="'.base_url().'assets/emotions/smile.png" alt="" style="width:20px;height:20px"/>',
		':-)' => '<img src="'.base_url().'assets/emotions/smile.png" alt="" style="width:20px;height:20px"/>',
		'(smile)' => '<img src="'.base_url().'assets/emotions/smile.png" alt="" style="width:20px;height:20px"/>',
		':(' => '<img src="'.base_url().'assets/emotions/sad.png" alt="" style="width:20px;height:20px"/>',
		':-(' => '<img src="'.base_url().'assets/emotions/sad.png" alt="" style="width:20px;height:20px"/>',
		'(sad)' => '<img src="'.base_url().'assets/emotions/sad.png" alt="" style="width:20px;height:20px"/>',
		':|' => '<img src="'.base_url().'assets/emotions/puzzled.png" alt="" style="width:20px;height:20px"/>',
		':-|' => '<img src="'.base_url().'assets/emotions/puzzled.png" alt="" style="width:20px;height:20px"/>',
		'(puzzled)' => '<img src="'.base_url().'assets/emotions/puzzled.png" alt="" style="width:20px;height:20px"/>',
		'(strange)' => '<img src="'.base_url().'assets/emotions/puzzled.png" alt="" style="width:20px;height:20px"/>',
		'<3' => '<img src="'.base_url().'assets/emotions/heart.png" alt="" style="width:20px;height:20px"/>',
		'(heart)' => '<img src="'.base_url().'assets/emotions/heart.png" alt="" style="width:20px;height:20px"/>',
		'<-3' => '<img src="'.base_url().'assets/emotions/big_heart.png" alt="" style="width:20px;height:20px"/>',
		'(heart-o)' => '<img src="'.base_url().'assets/emotions/big_heart.png" alt="" style="width:20px;height:20px"/>',
		'<|3' => '<img src="'.base_url().'assets/emotions/broken_heart.png" alt="" style="width:20px;height:20px"/>',
		'(heart-broken)' => '<img src="'.base_url().'assets/emotions/broken_heart.png" alt="" style="width:20px;height:20px"/>',
		';-)' => '<img src="'.base_url().'assets/emotions/wink.png" alt="" style="width:20px;height:20px"/>',
		';)' => '<img src="'.base_url().'assets/emotions/wink.png" alt="" style="width:20px;height:20px"/>',
		'(wink)' => '<img src="'.base_url().'assets/emotions/wink.png" alt="" style="width:20px;height:20px"/>',
		'B|' => '<img src="'.base_url().'assets/emotions/cool.png" alt="" style="width:20px;height:20px"/>',
		'(cool)' => '<img src="'.base_url().'assets/emotions/cool.png" alt="" style="width:20px;height:20px"/>',
		':-*' => '<img src="'.base_url().'assets/emotions/kiss.png" alt="" style="width:20px;height:20px"/>',
		':*' => '<img src="'.base_url().'assets/emotions/kiss.png" alt="" style="width:20px;height:20px"/>',
		'(kiss)' => '<img src="'.base_url().'assets/emotions/kiss.png" alt="" style="width:20px;height:20px"/>',
		':D' => '<img src="'.base_url().'assets/emotions/laugh.png" alt="" style="width:20px;height:20px"/>',
		':-D' => '<img src="'.base_url().'assets/emotions/laugh.png" alt="" style="width:20px;height:20px"/>',
		'(laugh)' => '<img src="'.base_url().'assets/emotions/laugh.png" alt="" style="width:20px;height:20px"/>',
		':-P' => '<img src="'.base_url().'assets/emotions/wink_smiley.png" alt="" style="width:20px;height:20px"/>',
		':P' => '<img src="'.base_url().'assets/emotions/wink_smiley.png" alt="" style="width:20px;height:20px"/>',
		'(tongue)' => '<img src="'.base_url().'assets/emotions/wink_smiley.png" alt="" style="width:20px;height:20px"/>',
		'(smail)' => '<img src="'.base_url().'assets/emotions/smail.png" alt="" style="width:20px;height:20px"/>',
		'(Y)' => '<img src="'.base_url().'assets/emotions/thumbs-up.png" alt="" style="width:20px;height:20px"/>',
		'(thumbs-up)' => '<img src="'.base_url().'assets/emotions/thumbs-up.png" alt="" style="width:20px;height:20px"/>',
		":'(" => '<img src="'.base_url().'assets/emotions/cry.png" alt="" style="width:20px;height:20px"/>',
		"(cry)" => '<img src="'.base_url().'assets/emotions/cry.png" alt="" style="width:20px;height:20px"/>',
		":-*>" => '<img src="'.base_url().'assets/emotions/blush.png" alt="" style="width:20px;height:20px"/>',
		"(blush)" => '<img src="'.base_url().'assets/emotions/blush.png" alt="" style="width:20px;height:20px"/>',
		":*0" => '<img src="'.base_url().'assets/emotions/kiss_eye.png" alt="" style="width:20px;height:20px"/>',
		"(kisseyeclosed)" => '<img src="'.base_url().'assets/emotions/kiss_eye.png" alt="" style="width:20px;height:20px"/>',
		"</3" => '<img src="'.base_url().'assets/emotions/heart_broken.png" alt="" style="width:20px;height:20px"/>',
		"(brokenheart)" => '<img src="'.base_url().'assets/emotions/heart_broken.png" alt="" style="width:20px;height:20px"/>',
    );
	
	return str_replace( array_keys($my_smilies), array_values($my_smilies), $string);
}


}
?>