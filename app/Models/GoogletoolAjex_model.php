<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class GoogletoolAjex_model extends CI_Model {



public function google_drivesAjex($rows, $searchValue, $rowcount,$limit) {
    $this->db->select('*, tblgoogle_drives.id as did,tblgoogle_drives.description as go_description');
    $this->db->from('tblgoogle_drives');
    $this->db->join('tbl_users', 'tbl_users.id = tblgoogle_drives.staffid', 'left');
    $this->db->where('tblgoogle_drives.type','doc');
    if ($searchValue) {
        $this->db->group_start();  // Start grouping for search conditions
        $this->db->or_like('tblgoogle_drives.driveid', $searchValue);
        $this->db->or_like('tbl_users.username', $searchValue);
        $this->db->or_like('tblgoogle_drives.title', $searchValue);
        $this->db->group_end();  // End grouping for search conditions
    }
    $this->db->order_by('tblgoogle_drives.id', 'DESC');
    if ($rowcount == 1) {
        if($limit!='-1'){
        $this->db->limit($limit, $rows);
        }
        $query = $this->db->get();
        return $query->result_array();
    } else {
        $query = $this->db->get();
        return $query->num_rows();
    }
}


public function google_drivesSheetsAjex($rows, $searchValue, $rowcount,$limit) {
    $this->db->select('*, tblgoogle_drives.id as did,tblgoogle_drives.description as go_description');
    $this->db->from('tblgoogle_drives');
    $this->db->join('tbl_users', 'tbl_users.id = tblgoogle_drives.staffid', 'left');
    $this->db->where('tblgoogle_drives.type','sheet');
    if ($searchValue) {
        $this->db->group_start();  // Start grouping for search conditions
        $this->db->or_like('tblgoogle_drives.driveid', $searchValue);
        $this->db->or_like('tbl_users.username', $searchValue);
        $this->db->or_like('tblgoogle_drives.title', $searchValue);
        $this->db->group_end();  // End grouping for search conditions
    }
    $this->db->order_by('tblgoogle_drives.id', 'DESC');
    if ($rowcount == 1) {
        if($limit!='-1'){
        $this->db->limit($limit, $rows);
        }
        $query = $this->db->get();
        return $query->result_array();
    } else {
        $query = $this->db->get();
        return $query->num_rows();
    }
}


}