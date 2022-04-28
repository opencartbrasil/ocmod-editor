<?php
class ModelExtensionModificationEditor extends Model {
    public function getModifications() {
        $sql = "SELECT * FROM `" . DB_PREFIX . "modification` ORDER BY name";

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getModification($modification_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "modification` WHERE modification_id = '" . (int) $modification_id . "'");

        return $query->row;
    }

    public function getModificationByCode($code) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "modification` WHERE code = '" . $this->db->escape($code) . "'");

        return $query->row;
    }

    public function addModification($data) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "modification` SET code = '" . $this->db->escape($data['code']) . "', name = '" . $this->db->escape($data['name']) . "', author = '" . $this->db->escape($data['author']) . "', version = '" . $this->db->escape($data['version']) . "', link = '" . $this->db->escape($data['link']) . "', xml = '" . $this->db->escape($data['xml']) . "', status = '" . (int) $data['status'] . "', date_added = NOW()");
    }

    public function editModification($modification_id, $data) {
        $this->db->query("UPDATE `" . DB_PREFIX . "modification` SET code = '" . $this->db->escape($data['code']) . "', name = '" . $this->db->escape($data['name']) . "', author = '" . $this->db->escape($data['author']) . "', version = '" . $this->db->escape($data['version']) . "', link = '" . $this->db->escape($data['link']) . "', xml = '" . $this->db->escape($data['xml']) . "' WHERE modification_id = '" . (int) $modification_id . "'");
    }

    public function getTotalSearchModificationElement($data = array()) {
        $sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "modification`";

        $implode = array();

        if (!empty($data['search_query'])) {
            $query_search = htmlspecialchars_decode($data['search_query']);

            $search_exploded = explode ( " ", $query_search );

            foreach( $search_exploded as $search_each ) {
                $implode[] = " xml LIKE '%" . $this->db->escape($search_each) . "%'";
            }
        }

        if ($implode) {
            $sql .= " WHERE " . implode(" AND ", $implode);
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function searchModificationElement($data = array()) {
        $sql = "SELECT * FROM `" . DB_PREFIX . "modification`";

        $implode = array();

        if (!empty($data['search_query'])) {
            $query_search = htmlspecialchars_decode($data['search_query']);

            $search_exploded = explode(" ", $query_search);

            foreach( $search_exploded as $search_each ) {
                $implode[] = " xml LIKE '%" . $this->db->escape($search_each) . "%'";
            }
        }

        if ($implode) {
            $sql .= " WHERE " . implode(" AND ", $implode);
        }

        $sort_data = array(
            'name',
            'code',
            'version',
            'author',
            'date_added'
        );

        if (
            isset($data['sort'])
            && in_array($data['sort'], $sort_data)
        ) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY name";
        }

        if (
            isset($data['order'])
            && ($data['order'] == 'DESC')
        ) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (
            isset($data['start'])
            || isset($data['limit'])
        ) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getExtensionInstallByExtensionInstallId($extension_install_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "extension_install` WHERE `extension_install_id` = '" . (int) $extension_install_id . "'");

        if ($query->num_rows) {
            return $query->row['filename'];
        }

        return '';
    }
}
