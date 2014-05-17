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
}