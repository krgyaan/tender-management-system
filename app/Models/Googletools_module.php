<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Googletools_module extends CI_Model {
    
public function getRowArray($tbl,$where){
 $this->db->select('*');  
 $this->db->where($where);
 $query = $this->db->get($tbl)->row_array();
 return $query;
}

/////////////Insert Query Simple
public function insertData($table,$data){
    $this->db->insert($table,$data);
    return $this->db->insert_id();
}

 public function datatablesupdate($tablename,$data,$where){
$this->db->where($where);
$this->db->update($tablename, $data);
return true;

}  

    ////////////////////Fetch Data
public function GetAllDataarray($table,$where){
     $this->db->select('*');  
 $this->db->where($where);
 $this->db->order_by('id','DESC');
 $query = $this->db->get($table)->result_array();
 return $query;
}

public function GetAllDataarrayAsc($table,$where){
     $this->db->select('*');  
 $this->db->where($where);
 $this->db->order_by('id','ASC');
 $query = $this->db->get($table)->result_array();
 return $query;
}

  /////////////Insert Query Condition
public function addDataCondition($table,$data,$condition){
    $this->db->select('*');  
    $this->db->where($condition);
    $query = $this->db->get($table)->num_rows();
    if($query > 0){
    return false;    
    }else{
    $this->db->insert($table,$data);
    return true;
    }
 } 
 
 /// Insert update 
 
 public function addDataConditionupdate($table,$data,$condition){
    $this->db->select('*');  
    $this->db->where($condition);
    $query = $this->db->get($table)->num_rows();
    if($query > 0){
    $this->db->where($condition);
    $this->db->update($table,$data);    
    return true;    
    }else{
    $this->db->insert($table,$data);
    return true;
    }
 } 
 
   ///////////// Update Query Condition
public function editDataCondition($table,$data,$condition,$where){
    $this->db->select('*');  
    $this->db->where($condition);
    $query = $this->db->get($table)->num_rows();
  ///  echo $this->db->last_query();
  ///  die;
    if($query==0){
        $this->db->where($where);
        $this->db->update($table,$data);
        return true;
    }else{
        return false;
    }
}

public function updateData($table,$data,$where){
   
        $this->db->where($where);
        $this->db->update($table,$data);
        return true;
  
}

///////////////delete data
public function deleteData($table,$where){
    $this->db->where($where);
    if($this->db->delete($table)){
        return true;
    }
}

  /////////////Insert Query Simple
public function addDataConditionlastid($table,$data,$condition){
    $this->db->select('*');  
 $this->db->where($condition);
 $query = $this->db->get($table)->num_rows();
 if($query==0){
    $this->db->insert($table,$data);
    return $this->db->insert_id();
 }else{
      return false;
 }
 
} 

  
  public function usercheckregister($table,$data,$condition){
    $this->db->select('*');  
 $this->db->or_where($condition);
 $query = $this->db->get($table)->num_rows();
 if($query==0){
    $this->db->insert($table,$data);
    return $this->db->insert_id();
 }else{
      return false;
 }
 
} 

public function getNumRows($table,$where){
     $this->db->select('*');  
 $this->db->where($where);
 $query = $this->db->get($table)->num_rows();
 return $query;
}
    

}