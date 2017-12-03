<?php
class ModelExtensionModificationEditor extends Model {
    public function getModifications($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "modification ORDER BY name";

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getModification($modification_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "modification WHERE modification_id = '" . (int)$modification_id . "'");

        return $query->row;
    }

    public function getModificationByCode($code) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "modification WHERE code = '" . $this->db->escape($code) . "'");

        return $query->row;
    }

    public function addModification($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "modification SET code = '" . $this->db->escape($data['code']) . "', name = '" . $this->db->escape($data['name']) . "', author = '" . $this->db->escape($data['author']) . "', version = '" . $this->db->escape($data['version']) . "', link = '" . $this->db->escape($data['link']) . "', xml = '" . $this->db->escape($data['xml']) . "', status = '" . (int)$data['status'] . "', date_added = NOW()");
    }

    public function editModification($modification_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "modification SET code = '" . $this->db->escape($data['code']) . "', name = '" . $this->db->escape($data['name']) . "', author = '" . $this->db->escape($data['author']) . "', version = '" . $this->db->escape($data['version']) . "', link = '" . $this->db->escape($data['link']) . "', xml = '" . $this->db->escape($data['xml']) . "' WHERE modification_id = '" . (int)$modification_id . "'");
    }
}