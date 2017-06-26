<?php
class ControllerExtensionModificationEditor extends Controller {
    public function index() {
        if (!$this->user->hasPermission('modify', 'extension/modification_editor')) {
            $this->response->redirect($this->url->link('extension/modification', 'token=' . $this->session->data['token'], true));
        }

        $this->load->language('extension/modification_editor');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->document->addScript('view/javascript/ace/ace.js');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_modifications'),
            'href' => $this->url->link('extension/modification', 'token=' . $this->session->data['token'], true)
        );

        if (isset($this->request->get['modification_id'])) {
            $modification_id = $this->request->get['modification_id'];

            $this->load->model('extension/modification_editor');
            $modification_info = $this->model_extension_modification_editor->getModification($modification_id);

            if ($modification_info) {
                $data['modification_id'] = $modification_id;
                $data['name'] = $modification_info['name'];
                $data['xml'] = ltrim($modification_info['xml'], "﻿");

                $data['breadcrumbs'][] = array(
                    'text' => $this->language->get('heading_title'),
                    'href' => $this->url->link('extension/modification_editor', 'token=' . $this->session->data['token'] . '&modification_id=' . $modification_id, true)
                );
            } else {
                $this->response->redirect($this->url->link('extension/modification', 'token=' . $this->session->data['token'], true));
            }
        } else {
            $data['modification_id'] = 0;
            $data['name'] = $this->language->get('text_new');
            $data['xml'] = '<?xml version="1.0" encoding="UTF-8"?>
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
</modification>';

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/modification_editor', 'token=' . $this->session->data['token'], true)
            );
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_help_ocmod'] = $this->language->get('text_help_ocmod');
        $data['text_loading'] = $this->language->get('text_loading');
        $data['text_erasing'] = $this->language->get('text_erasing');
        $data['text_xml_code'] = $this->language->get('text_xml_code');

        $data['button_clear_data']  = $this->language->get('button_clear_data');
        $data['button_clear_image'] = $this->language->get('button_clear_image');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_return'] = $this->language->get('button_return');

        $data['return'] = $this->url->link('extension/modification', 'token=' . $this->session->data['token'], true);

        $data['token'] = $this->session->data['token'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/modification_editor.tpl', $data));
    }

    public function save() {
        $json = array();

        $this->load->language('extension/modification_editor');

        if (!$this->user->hasPermission('modify', 'extension/modification_editor')) {
            $json['error'] = $this->language->get('error_permission');
        } else {
            $xml = html_entity_decode($this->request->post['xml']);

            if ($xml) {
                if ($this->validateXML($xml)) {
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

                                $this->load->model('extension/modification');
                                $this->load->model('extension/modification_editor');

                                if ($modification_id > 0) {
                                    $modification_info = $this->model_extension_modification_editor->getModification($modification_id);

                                    if ($modification_info) {
                                        if ($modification_info['code'] == $modification_data['code']) {
                                            $this->model_extension_modification_editor->editModification($modification_id, $modification_data);
                                            $json['success'] = $this->language->get('text_success_edit');
                                        } else {
                                            $modification_info = $this->model_extension_modification->getModificationByCode($modification_data['code']);

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
                                    $modification_info = $this->model_extension_modification->getModificationByCode($modification_data['code']);

                                    if ($modification_info) {
                                        $json['error'] = sprintf($this->language->get('error_code_used'), $modification_info['name']);
                                    } else {
                                        $this->model_extension_modification_editor->addModification($modification_data);
                                        $json['success'] = $this->language->get('text_success_add');
                                    }
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

    public function download() {
        if (!$this->user->hasPermission('modify', 'extension/modification_editor')) {
            $this->response->redirect($this->url->link('extension/modification', 'token=' . $this->session->data['token'], true));
        }

        if (isset($this->request->get['modification_id'])) {
            $modification_id = $this->request->get['modification_id'];
        } else {
            $modification_id = 0;
        }

        $this->load->model('extension/modification_editor');
        $modification = $this->model_extension_modification_editor->getModification($modification_id);

        if ($modification) {
            $filename = $modification['code'] . '.ocmod.xml';
            $file = $modification['xml'];

            ob_start();
            echo $file;
            $download = ob_get_contents();
            $size = ob_get_length();
            ob_end_clean();

            if (!headers_sent()) {
                if (!empty($modification['xml'])) {
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
            $this->response->redirect($this->url->link('extension/modification', 'token=' . $this->session->data['token'], true));
        }
    }

    private function delTree($dir) {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
             (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : @unlink("$dir/$file");
        }

        return @rmdir($dir);
    }

    public function clearCacheData() {
        if (!$this->user->hasPermission('modify', 'extension/modification_editor')) {
            $this->response->redirect($this->url->link('extension/modification', 'token=' . $this->session->data['token'], true));
        }

        $files = glob(DIR_CACHE . 'cache.*');
        if ($files) {
            foreach ($files as $file) {
                if (file_exists($file)) {
                    @unlink($file);
                }
            }
        }

        $this->load->language('extension/modification_editor');
        $json['success'] = $this->language->get('text_clear_data');

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function clearCacheImage() {
        if (!$this->user->hasPermission('modify', 'extension/modification_editor')) {
            $this->response->redirect($this->url->link('extension/modification', 'token=' . $this->session->data['token'], true));
        }

        $this->delTree(DIR_IMAGE . 'cache/');
        @mkdir(DIR_IMAGE . 'cache/');

        $this->load->language('extension/modification_editor');
        $json['success'] = $this->language->get('text_clear_image');

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function validateXML($xml) {
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
}
