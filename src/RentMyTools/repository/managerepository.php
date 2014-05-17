<?php

namespace RentMyTools\Repository;

class ManageRepository extends \Knp\Repository {

	public function getTableName() {
            return 'items';
	}

    // data from items table
    public function find($id) {
        return $this->db->fetchAssoc('SELECT items.*, accounts.username from items INNER JOIN accounts ON items.userid = accounts.id WHERE items.id = ?', array($id));
    }

    public function findId($username) {
        return $this->db->fetchAssoc('SELECT accounts.id from accounts WHERE accounts.username = ?', array($username));
    }

    public function getToolsByUser($userId, $pageStart, $pageEnd){
        return $this->db->fetchAll('SELECT items.*, accounts.username from items INNER JOIN accounts ON items.userId = accounts.id WHERE accounts.id = ? AND items.id BETWEEN ? AND ?', array($userId, $pageStart,$pageEnd));
    }

    // insert, update, delete
    public function insertItem(array $data) {
        return $this->db->insert($this->getTableName(), $data);
    }

    public function updateItem(array $data, array $id) {
        return $this->db->update($this->getTableName(), $data, $id);
    }

    public function deleteItem(array $id) {
        return $this->db->delete($this->getTableName(), $id);
    }

    // user information
    public function findUserIdByItem($id){
        return $this->db->fetchAssoc('SELECT items.userid FROM items WHERE items.id = ?', array((int)$id));
    }

    public function getUser($id) {
    	return $this->db->fetchAssoc('SELECT accounts.username from accounts WHERE accounts.id = ?', array((int) $id));
    }

    // metadata
    public function getCountByUser($id){
        return $this->db->fetchAssoc('SELECT COUNT(*) AS total FROM items WHERE items.userid = ?', array((int)$id));
    }
}
