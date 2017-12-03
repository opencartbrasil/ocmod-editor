<?php
class ControllerExtensionModificationFiles extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/modification/files');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['heading_title'] = $this->language->get('heading_title');

        if (isset($this->session->data['error'])) {
            $data['error_warning'] = $this->session->data['error'];

            unset($this->session->data['error']);
        } elseif (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['empty'])) {
            $data['error_empty'] = $this->session->data['empty'];

            unset($this->session->data['empty']);
        } else {
            $data['error_empty'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_modifications'),
            'href' => $this->url->link('marketplace/modification', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/modification/files', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['modified_files'] = $this->getModifiedFiles();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/modification/files', $data));
    }

    private function getCacheFiles($directory, $length = 0) {
        $result = array();

        if (!$length) { $length = strlen($directory); }

        $files = glob(rtrim($directory, '/') . '/*');
        if (is_array($files)) {
            foreach($files as $file) {
                if ($file == $directory . 'index.html') {
                    continue;
                } else if (is_file($file)) {
                    $result[] = substr($file, $length);
                } else if (is_dir($file)) {
                    $result = array_merge($result, $this->getCacheFiles($file, $length));
                }
            }
        }

        return $result;
    }

    private function getXmlFiles() {
        $result = array();

        $xml = array();
        $xml[] = file_get_contents(DIR_SYSTEM . 'modification.xml');

        $files = glob(DIR_SYSTEM . '*.ocmod.xml');
        if ($files) {
            foreach ($files as $file) {
                $xml[] = file_get_contents($file);
            }
        }

        $this->load->model('extension/modification/editor');
        $results = $this->model_extension_modification_editor->getModifications();

        foreach ($results as $result) {
            $xml[] = $result['xml'];
        }

        foreach ($xml as $xml) {
            if (empty($xml)){ continue; }

            $dom = new DOMDocument('1.0', 'UTF-8');
            $dom->preserveWhiteSpace = false;
            $dom->loadXml($xml);

            $files = $dom->getElementsByTagName('modification')->item(0)->getElementsByTagName('file');
            foreach ($files as $file) {

                $files = explode('|', $file->getAttribute('path'));

                foreach ($files as $file) {
                    $path = '';

                    if (substr($file, 0, 7) == 'catalog') {
                        $path = DIR_CATALOG . str_replace('../', '', substr($file, 8));
                    }
                    if (substr($file, 0, 5) == 'admin') {
                        $path = DIR_APPLICATION . str_replace('../', '', substr($file, 6));
                    }
                    if (substr($file, 0, 6) == 'system') {
                        $path = DIR_SYSTEM . str_replace('../', '', substr($file, 7));
                    }

                    if ($path) {
                        $files = glob($path, GLOB_BRACE);
                        if ($files) {
                            foreach ($files as $file) {
                                if (substr($file, 0, strlen(DIR_CATALOG)) == DIR_CATALOG) {
                                    $file = 'catalog/' . substr($file, strlen(DIR_CATALOG));
                                }
                                if (substr($file, 0, strlen(DIR_APPLICATION)) == DIR_APPLICATION) {
                                    $file = 'admin/' . substr($file, strlen(DIR_APPLICATION));
                                }
                                if (substr($file, 0, strlen(DIR_SYSTEM)) == DIR_SYSTEM) {
                                    $file = 'system/' . substr($file, strlen(DIR_SYSTEM));
                                }

                                if (!isset($result[$file])) { $result[$file] = array(); }

                                if ($dom->getElementsByTagName('version')->length) {
                                    $version = $dom->getElementsByTagName('version')->item(0)->textContent;
                                } else {
                                    $version = '';
                                }

                                if ($dom->getElementsByTagName('author')->length) {
                                    $author = $dom->getElementsByTagName('author')->item(0)->textContent;
                                } else {
                                    $author = '';
                                }

                                $result[$file][] = array(
                                    'code' => $dom->getElementsByTagName('code')->item(0)->textContent,
                                    'name' => $dom->getElementsByTagName('name')->item(0)->textContent,
                                    'version' => $version,
                                    'author' => $author
                                );
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

    private function getModifiedFiles() {
        $result = array();

        $cache_files = $this->getCacheFiles(DIR_MODIFICATION);
        $xml_files = $this->getXmlFiles();

        foreach($cache_files as $cache_file) {
            if (isset($xml_files[$cache_file])) {
                $modifications = $xml_files[$cache_file];
            } else {
                $modifications = array();
            }

            $result[] = array(
                'file' => $cache_file,
                'modifications' => $modifications
            );
        }

        return $result;
    }
}