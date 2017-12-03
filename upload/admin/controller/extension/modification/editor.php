<?php
class ControllerExtensionModificationEditor extends Controller {
    public function index() {
        $this->load->language('extension/modification/editor');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->document->addScript('view/javascript/ace/ace.js');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_modifications'),
            'href' => $this->url->link('marketplace/modification', 'user_token=' . $this->session->data['user_token'], true)
        );

        if (isset($this->request->get['modification_id'])) {
            $modification_id = $this->request->get['modification_id'];

            $this->load->model('extension/modification/editor');
            $modification_info = $this->model_extension_modification_editor->getModification($modification_id);

            if ($modification_info) {
                $data['modification_id'] = $modification_id;
                $data['name'] = $modification_info['name'];
                $data['xml'] = htmlentities(ltrim($modification_info['xml'], "﻿"));

                $data['breadcrumbs'][] = array(
                    'text' => $this->language->get('heading_title'),
                    'href' => $this->url->link('extension/modification/editor', 'user_token=' . $this->session->data['user_token'] . '&modification_id=' . $modification_id, true)
                );
            } else {
                $this->response->redirect($this->url->link('marketplace/modification', 'user_token=' . $this->session->data['user_token'], true));
            }
        } else {
            $data['modification_id'] = 0;
            $data['name'] = $this->language->get('text_new');
            $xml = <<<'EOT'
<?xml version="1.0" encoding="utf-8"?>
<modification>
  <name></name>
  <code></code>
  <version></version>
  <author></author>
  <link></link>
  <file path="">
    <operation>
      <search><![CDATA[]]></search>
      <add position=""><![CDATA[]]></add>
    </operation>
  </file>
</modification>
EOT;
            $data['xml'] = htmlentities($xml);

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/modification/editor', 'user_token=' . $this->session->data['user_token'], true)
            );
        }

        $data['button_return'] = $this->language->get('button_return');

        $data['return'] = $this->url->link('marketplace/modification', 'user_token=' . $this->session->data['user_token'], true);

        $data['user_token'] = $this->session->data['user_token'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/modification/editor', $data));
    }

    public function download() {
        $this->validate();

        if (isset($this->request->get['modification_id'])) {
            $modification_id = $this->request->get['modification_id'];
        } else {
            $modification_id = 0;
        }

        $this->load->model('extension/modification/editor');
        $modification_info = $this->model_extension_modification_editor->getModification($modification_id);

        if ($modification_info) {
            $filename = $modification_info['code'] . '.ocmod.xml';
            $file = $modification_info['xml'];

            ob_start();
            echo $file;
            $download = ob_get_contents();
            $size = ob_get_length();
            ob_end_clean();

            if (!headers_sent()) {
                if (!empty($modification_info['xml'])) {
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="' . $filename . '"');
                    header('Content-Transfer-Encoding: binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Pragma: public');
                    header('Content-Length: ' . $size);

                    if (ob_get_level()) {
                        ob_end_clean();
                    }

                    echo $download;

                    exit();
                } else {
                    exit($this->language->get('error_file'));
                }
            } else {
                exit($this->language->get('error_headers'));
            }
        } else {
            $this->response->redirect($this->url->link('marketplace/modification', 'user_token=' . $this->session->data['user_token'], true));
        }
    }

    public function save() {
        $json = array();

        $this->load->language('extension/modification/editor');

        if (!$this->user->hasPermission('modify', 'extension/modification/editor')) {
            $json['error'] = $this->language->get('error_permission');
        } else {
            $xml = html_entity_decode($this->request->post['xml']);

            if ($xml) {
                if ($this->validate_xml($xml)) {
                    try {
                        $dom = new DOMDocument('1.0', 'UTF-8');
                        $dom->loadXml($xml);

                        $modification_data = array();

                        $tags = array(
                            'name',
                            'code',
                            'version',
                            'author',
                            'link'
                        );

                        $this->load->model('extension/modification/editor');

                        foreach ($tags as $tag) {
                            $item = $dom->getElementsByTagName($tag)->item(0);

                            if ($item) {
                                if (!empty(trim($item->nodeValue))) {
                                    $modification_data[$tag] = $item->nodeValue;
                                } else {
                                    $json['error'] = $this->language->get('error_'.$tag.'_value');
                                    break;
                                }
                            } else {
                                $json['error'] = $this->language->get('error_'.$tag.'_tag');
                                break;
                            }
                        }

                        $file_tag = $dom->getElementsByTagName('file');
                        if ($file_tag->length == 0) { 
                            $json['error'] = $this->language->get('error_file_tag');
                        }

                        if (!isset($json['error'])) {
                            $modification_data['xml'] = $xml;
                            $modification_data['status'] = 1;

                            if (isset($this->request->post['modification_id'])) {
                                $modification_id = $this->request->post['modification_id'];

                                if ($modification_id > 0) {
                                    $modification_info = $this->model_extension_modification_editor->getModification($modification_id);

                                    if ($modification_info) {
                                        if ($modification_info['code'] == $modification_data['code']) {
                                            $this->model_extension_modification_editor->editModification($modification_id, $modification_data);
                                            $json['success'] = $this->language->get('text_success_edit');
                                        } else {
                                            $modification_info = $this->model_extension_modification_editor->getModificationByCode($modification_data['code']);

                                            if ($modification_info) {
                                                $json['error'] = sprintf($this->language->get('error_code_used'), $modification_info['name']);
                                            } else {
                                                $this->model_extension_modification_editor->editModification($modification_id, $modification_data);
                                                $json['success'] = $this->language->get('text_success_edit');
                                            }
                                        }
                                    } else {
                                        $json['error'] = $this->language->get('error_modification_id');
                                    }
                                } else {
                                    $modification_info = $this->model_extension_modification_editor->getModificationByCode($modification_data['code']);

                                    if ($modification_info) {
                                        $json['error'] = sprintf($this->language->get('error_code_used'), $modification_info['name']);
                                    } else {
                                        $this->model_extension_modification_editor->addModification($modification_data);
                                        $json['success'] = $this->language->get('text_success_add');
                                    }
                                }

                                if (!isset($json['error'])) {
                                    $this->refresh();
                                }
                            } else {
                                $json['error'] = $this->language->get('error_modification_id');
                            }
                        }
                    } catch(Exception $exception) {
                        $json['error'] = sprintf($this->language->get('error_exception'), $exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine());
                    }
                } else {
                    $json['error'] = $this->language->get('error_xml');
                }
            } else {
                $json['error'] = $this->language->get('error_file');
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function erase_cache_theme() {
        $json = array();

        $this->load->language('extension/modification/editor');

        if (!$this->user->hasPermission('modify', 'extension/modification/editor')) {
            $json['error'] = $this->language->get('error_permission');
        } else {
            $file = DIR_APPLICATION  . 'view/stylesheet/bootstrap.css';
            
            if (is_file($file) && is_file(DIR_APPLICATION . 'view/stylesheet/sass/_bootstrap.scss')) {
                unlink($file);
            }
             
            $files = glob(DIR_CATALOG  . 'view/theme/*/stylesheet/sass/_bootstrap.scss');
             
            foreach ($files as $file) {
                $file = substr($file, 0, -21) . '/bootstrap.css';
                
                if (is_file($file)) {
                    unlink($file);
                }
            }

            $directories = glob(DIR_CACHE . '*', GLOB_ONLYDIR);

            if ($directories) {
                foreach ($directories as $directory) {
                    $files = glob($directory . '/*');
                    
                    foreach ($files as $file) { 
                        if (is_file($file)) {
                            unlink($file);
                        }
                    }
                    
                    if (is_dir($directory)) {
                        rmdir($directory);
                    }
                }
            }

            $json['success'] = $this->language->get('text_erase_theme');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function erase_cache_data() {
        $json = array();

        $this->load->language('extension/modification/editor');

        if (!$this->user->hasPermission('modify', 'extension/modification/editor')) {
            $json['error'] = $this->language->get('error_permission');
        } else {
            $files = glob(DIR_CACHE . 'cache.*');
            if ($files) {
                foreach ($files as $file) {
                    if (file_exists($file)) {
                        @unlink($file);
                    }
                }
            }

            $json['success'] = $this->language->get('text_erase_data');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function erase_cache_image() {
        $json = array();

        $this->load->language('extension/modification/editor');

        if (!$this->user->hasPermission('modify', 'extension/modification/editor')) {
            $json['error'] = $this->language->get('error_permission');
        } else {
            $this->delete_tree(DIR_IMAGE . 'cache/');
            @mkdir(DIR_IMAGE . 'cache/');

            $json['success'] = $this->language->get('text_erase_image');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    private function delete_tree($dir) {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
             (is_dir("$dir/$file")) ? $this->delete_tree("$dir/$file") : @unlink("$dir/$file");
        }

        return @rmdir($dir);
    }

    public function refresh() {
        $this->validate();

        $maintenance = $this->config->get('config_maintenance');
        $this->load->model('setting/setting');
        $this->model_setting_setting->editSettingValue('config', 'config_maintenance', true);

        $log = array();
        $log_error = array();

        $files = array();

        $path = array(DIR_MODIFICATION . '*');

        while (count($path) != 0) {
            $next = array_shift($path);

            foreach (glob($next) as $file) {
                if (is_dir($file)) {
                    $path[] = $file . '/*';
                }

                $files[] = $file;
            }
        }

        rsort($files);

        foreach ($files as $file) {
            if ($file != DIR_MODIFICATION . 'index.html') {
                if (is_file($file)) {
                    @unlink($file);

                } elseif (is_dir($file)) {
                    @rmdir($file);
                }
            }
        }

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
            if ($result['status']) {
                $xml[] = $result['xml'];
            }
        }

        $modification = array();

        foreach ($xml as $xml) {
            if (empty($xml)){
                continue;
            }
            
            $dom = new DOMDocument('1.0', 'UTF-8');
            $dom->preserveWhiteSpace = false;
            $dom->loadXml($xml);

            $log[] = 'MOD: ' . $dom->getElementsByTagName('name')->item(0)->textContent;

            $log_mod = 'MOD: ' . $dom->getElementsByTagName('name')->item(0)->textContent;

            $recovery = array();

            if (isset($modification)) {
                $recovery = $modification;
            }

            $files = $dom->getElementsByTagName('modification')->item(0)->getElementsByTagName('file');
            foreach ($files as $file) {
                $operations = $file->getElementsByTagName('operation');

                $error_file = $file->getAttribute('error');

                $files = explode('|', $file->getAttribute('path'));
                foreach ($files as $file) {
                    $path = '';
                    if (substr($file, 0, 7) == 'catalog') {
                        $path = DIR_CATALOG . substr($file, 8);
                    }
                    if (substr($file, 0, 5) == 'admin') {
                        $path = DIR_APPLICATION . substr($file, 6);
                    }
                    if (substr($file, 0, 6) == 'system') {
                        $path = DIR_SYSTEM . substr($file, 7);
                    }
                    if ($path) {
                        $files = glob($path, GLOB_BRACE);
                        if ($files) {
                            foreach ($files as $file) {
                                if (substr($file, 0, strlen(DIR_CATALOG)) == DIR_CATALOG) {
                                    $key = 'catalog/' . substr($file, strlen(DIR_CATALOG));
                                }
                                if (substr($file, 0, strlen(DIR_APPLICATION)) == DIR_APPLICATION) {
                                    $key = 'admin/' . substr($file, strlen(DIR_APPLICATION));
                                }
                                if (substr($file, 0, strlen(DIR_SYSTEM)) == DIR_SYSTEM) {
                                    $key = 'system/' . substr($file, strlen(DIR_SYSTEM));
                                }
                                if (!isset($modification[$key])) {
                                    $content = file_get_contents($file);
                                    $modification[$key] = preg_replace('~\r?\n~', "\n", $content);
                                    $original[$key] = preg_replace('~\r?\n~', "\n", $content);

                                    $log[] = PHP_EOL . 'FILE: ' . $key;
                                }
                                foreach ($operations as $operation) {
                                    $error = $operation->getAttribute('error');

                                    $ignoreif = $operation->getElementsByTagName('ignoreif')->item(0);
                                    if ($ignoreif) {
                                        if ($ignoreif->getAttribute('regex') != 'true') {
                                            if (strpos($modification[$key], $ignoreif->textContent) !== false) {
                                                continue;
                                            }
                                        } else {
                                            if (preg_match($ignoreif->textContent, $modification[$key])) {
                                                continue;
                                            }
                                        }
                                    }

                                    $status = false;

                                    if ($operation->getElementsByTagName('search')->item(0)->getAttribute('regex') != 'true') {
                                        $search = $operation->getElementsByTagName('search')->item(0)->textContent;
                                        $trim = $operation->getElementsByTagName('search')->item(0)->getAttribute('trim');
                                        $index = $operation->getElementsByTagName('search')->item(0)->getAttribute('index');

                                        if (!$trim || $trim == 'true') {
                                            $search = trim($search);
                                        }

                                        $add = $operation->getElementsByTagName('add')->item(0)->textContent;
                                        $trim = $operation->getElementsByTagName('add')->item(0)->getAttribute('trim');
                                        $position = $operation->getElementsByTagName('add')->item(0)->getAttribute('position');
                                        $offset = $operation->getElementsByTagName('add')->item(0)->getAttribute('offset');

                                        if ($offset == '') {
                                            $offset = 0;
                                        }

                                        if ($trim == 'true') {
                                            $add = trim($add);
                                        }

                                        $log[] = 'CODE: ' . $search;

                                        if ($index !== '') {
                                            $indexes = explode(',', $index);
                                        } else {
                                            $indexes = array();
                                        }

                                        $i = 0;
                                        $lines = explode("\n", $modification[$key]);
                                        for ($line_id = 0; $line_id < count($lines); $line_id++) {
                                            $line = $lines[$line_id];
                                            $match = false;
                                            if (stripos($line, $search) !== false) {
                                                if (!$indexes) {
                                                    $match = true;
                                                } elseif (in_array($i, $indexes)) {
                                                    $match = true;
                                                }
                                                $i++;
                                            }

                                            if ($match) {
                                                switch ($position) {
                                                    default:
                                                    case 'replace':
                                                        $new_lines = explode("\n", $add);

                                                        if ($offset < 0) {
                                                            array_splice($lines, $line_id + $offset, abs($offset) + 1, array(str_replace($search, $add, $line)));
                                                            $line_id -= $offset;
                                                        } else {
                                                            array_splice($lines, $line_id, $offset + 1, array(str_replace($search, $add, $line)));
                                                        }
                                                        break;
                                                    case 'before':
                                                        $new_lines = explode("\n", $add);
                                                        array_splice($lines, $line_id - $offset, 0, $new_lines);
                                                        $line_id += count($new_lines);
                                                        break;
                                                    case 'after':
                                                        $new_lines = explode("\n", $add);
                                                        array_splice($lines, ($line_id + 1) + $offset, 0, $new_lines);
                                                        $line_id += count($new_lines);
                                                        break;
                                                }

                                                $log[] = 'LINE: ' . $line_id;
                                                $status = true;
                                            }
                                        }

                                        $modification[$key] = implode("\n", $lines);
                                    } else {
                                        $search = trim($operation->getElementsByTagName('search')->item(0)->textContent);
                                        $limit = $operation->getElementsByTagName('search')->item(0)->getAttribute('limit');
                                        $replace = trim($operation->getElementsByTagName('add')->item(0)->textContent);

                                        if (!$limit) {
                                            $limit = -1;
                                        }

                                        $match = array();
                                        preg_match_all($search, $modification[$key], $match, PREG_OFFSET_CAPTURE);

                                        if ($limit > 0) {
                                            $match[0] = array_slice($match[0], 0, $limit);
                                        }

                                        if ($match[0]) {
                                            $log[] = 'REGEX: ' . $search;
                                            for ($i = 0; $i < count($match[0]); $i++) {
                                                $log[] = 'LINE: ' . (substr_count(substr($modification[$key], 0, $match[0][$i][1]), "\n") + 1);
                                            }
                                            $status = true;
                                        }

                                        $modification[$key] = preg_replace($search, $replace, $modification[$key], $limit);
                                    }

                                    if (!$status) {
                                        if ($error != 'skip') {
                                            $log_error[] = PHP_EOL . 'ERROR: CODE NOT FOUND!';
                                            $log_error[] = 'FILE: ' . $key;
                                            $log_error[] = 'CODE: ' . $search;
                                            $log_error[] = $log_mod;
                                        }

                                        if ($error == 'abort') {
                                            $modification = $recovery;
                                            $log[] = 'NOT FOUND - ABORTING!';
                                            break 5;
                                        } elseif ($error == 'skip') {
                                            $log[] = 'NOT FOUND - OPERATION SKIPPED!';
                                            continue;
                                        } else {
                                            $log[] = 'NOT FOUND - OPERATIONS ABORTED!';
                                             break;
                                        }
                                    }
                                }
                            }
                        } else {
                            if ($error_file != 'skip') {
                                $log_error[] = PHP_EOL . 'ERROR: FILE NOT FOUND!';
                                $log_error[] = 'FILE: ' . $path;
                                $log_error[] = $log_mod;
                            }
                        }
                    }
                }
            }

            $log[] = '----------------------------------------------------------------';
        }

        $ocmod = new Log('ocmod.log');
        $ocmod->write(implode("\n", $log));

        $log_error_path = DIR_LOGS . 'error_ocmod.log';
        if (file_exists($log_error_path)) {
            @unlink($log_error_path);
        }

        if ($log_error) {
            $ocmod = new Log('error_ocmod.log');
            $ocmod->write(implode("\n", $log_error));
        }

        foreach ($modification as $key => $value) {
            if ($original[$key] != $value) {
                $path = '';
                $directories = explode('/', dirname($key));
                foreach ($directories as $directory) {
                    $path = $path . '/' . $directory;
                    if (!is_dir(DIR_MODIFICATION . $path)) {
                        @mkdir(DIR_MODIFICATION . $path, 0777);
                    }
                }
                $handle = fopen(DIR_MODIFICATION . $key, 'w');
                fwrite($handle, $value);
                fclose($handle);
            }
        }

        $this->model_setting_setting->editSettingValue('config', 'config_maintenance', $maintenance);
    }

    private function validate_xml($xml) {
        if (!empty($xml)) {
            libxml_use_internal_errors(true);

            $doc = new DOMDocument('1.0', 'utf-8');
            $doc->loadXML($xml);

            $errors = libxml_get_errors();

            libxml_clear_errors();

            return empty($errors);
        } else {
            return false;
        }
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'extension/modification/editor')) {
            $this->response->redirect($this->url->link('marketplace/modification', 'user_token=' . $this->session->data['user_token'], true));
        }
    }
}
