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

    public function getCount(){
        return $this->db->fetchAssoc('SELECT COUNT(*) AS total FROM items');
    }
}
