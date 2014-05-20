<?php

namespace RentMyTools\Repository;

class ToolsRepository extends \Knp\Repository {

	public function getTableName() {
            return 'items';
	}
        
    public function find($id) {
        return $this->db->fetchAssoc('SELECT items.*, accounts.username from items INNER JOIN accounts ON items.userid = accounts.id WHERE items.id = ?', array($id));
    }

    public function getTools($pageStart, $pageEnd){
        return $this->db->fetchAll('SELECT items.*, accounts.username from items INNER JOIN accounts ON items.userId = accounts.id WHERE items.id BETWEEN ? AND ?', array($pageStart,$pageEnd));
    }

    public function getToolsBySearch($keyword,$pageStart,$pageEnd){
        return $this->db->fetchAll('CALL getToolsBySearch(?,?,?)',array($keyword,$pageStart,$pageEnd));
    }

        public function getToolsBySearchCount($keyword){
        return $this->db->fetchAll('CALL getToolsBySearchCount(?)',array($keyword));
    }

    public function getCount(){
        return $this->db->fetchAssoc('SELECT COUNT(*) AS total FROM items');
    }


    public function getToolsFiltered($filter, $pageStart, $pageEnd){
        
        $extraWhere = '';
        //town
        if ($filter['town'] != '') {
            $extraWhere .= ' AND items.town = "' . $filter['town'] . '"';
        }
        //postalcode
        if ($filter['postalcode'] != 0) {
            $extraWhere .= ' AND items.postalcode= ' . $filter['postalcode'];
        }
        //province
        if ($filter['province'] != '') {
            $extraWhere .= ' AND items.province = "' . $filter['province'] . '"';
        }
        //startdate
        if ($filter['startdate'] != '2009/02/20') {
            $extraWhere .= ' AND items.startdate >= "' . $filter['startdate'] . '"';
        }
        //enddate
        if ($filter['enddate'] != '2009/02/20') {
            $extraWhere .= ' AND items.startdate <= "' . $filter['startdate'] . '"';
        }
        //price
        if ($filter['dayprice'] != -1) {
            $extraWhere .= ' AND items.dayprice = ' . $filter['dayprice'];
        }

        return $this->db->fetchAll('
            SELECT items.*, accounts.username from items INNER JOIN accounts
            ON items.userId = accounts.id WHERE items.id BETWEEN ? AND ? ' . $extraWhere
            , array($pageStart,$pageEnd));
    }

    public function getCountFiltered($filter){
        $extraWhere = '';
        //town
        if ($filter['town'] != '') {
            $extraWhere .= ' AND items.town = "' . $filter['town'] . '"';
        }
        //postalcode
        if ($filter['postalcode'] != 0) {
            $extraWhere .= ' AND items.postalcode= ' . $filter['postalcode'];
        }
        //province
        if ($filter['province'] != '') {
            $extraWhere .= ' AND items.province = "' . $filter['province'] . '"';
        }
        //startdate
        if ($filter['startdate'] != '2009/02/20') {
            $extraWhere .= ' AND items.startdate >= "' . $filter['startdate'] . '"';
        }
        //enddate
        if ($filter['enddate'] != '2009/02/20') {
            $extraWhere .= ' AND items.startdate <= "' . $filter['startdate'] . '"';
        }
        //price
        if ($filter['dayprice'] != -1) {
            $extraWhere .= ' AND items.dayprice = ' . $filter['dayprice'];
        }
        return $this->db->fetchAssoc('SELECT COUNT(*) AS total FROM items WHERE id NOT in (null)' . $extraWhere);
    }
}
