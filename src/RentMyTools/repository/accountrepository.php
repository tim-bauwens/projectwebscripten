<?php

namespace RentMyTools\Repository;

class AccountRepository extends \Knp\Repository {

	public function getTableName() {
		return 'accounts';
	}

    public function insert(array $data) {
    	return $this->db->insert($this->getTableName(), $data);
    }

    public function findPasswByUser($username) {
        return $this->db->fetchAll('SELECT password from accounts WHERE username = ?', array($username));
	}
        
    public function findAll() {
        return $this->db->fetchAll('SELECT * from accounts ');
	}

	public function findEmail($id) {
        return $this->db->fetchAssoc('SELECT accounts.email from accounts WHERE accounts.id = ?', array($id));
	}

    public function getUserDataByUser($username){
        return $this->db->fetchAll('SELECT accounts.username, accounts.firstname, accounts.lastname, accounts.email, accounts.phonenumber, accounts.biography, accounts.address from accounts WHERE accounts.username = ?', array($username));
    }

    public function getNewestByUser($username){
        return $this->db->fetchAll('SELECT items.*, accounts.username from items inner join accounts ON items.userid = accounts.id WHERE accounts.username = ? ORDER BY dateadded DESC LIMIT 0, 5',array($username));
    }
}