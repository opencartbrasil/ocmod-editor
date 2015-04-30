<?php
class ModelExtensionModificationEditor extends Model {
	public function getModification($modification_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "modification WHERE modification_id = '" . (int)$modification_id . "'");

		return $query->row;
	}

	public function editModification($data) {
		$this->db->query("UPDATE " . DB_PREFIX . "modification SET name = '" . $this->db->escape($data['name']) . "', author = '" . $this->db->escape($data['author']) . "', version = '" . $this->db->escape($data['version']) . "', link = '" . $this->db->escape($data['link']) . "', xml = '" . $this->db->escape($data['xml']) . "' WHERE modification_id = '" . (int)$data['modification_id'] . "'");
	}
}