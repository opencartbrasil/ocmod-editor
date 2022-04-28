<?php
class ControllerExtensionModificationDiff extends Controller {
    public function index() {
        $this->validate();

        $data = $this->load->language('extension/modification/diff');

        $this->document->setTitle($this->language->get('heading_title'));

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
            'text' => $this->language->get('text_modified_files'),
            'href' => $this->url->link('extension/modification/files', 'user_token=' . $this->session->data['user_token'], true)
        );

        $this->document->addStyle('view/javascript/ace/diff.min.css');
        $this->document->addScript('view/javascript/ace/diff-patch.min.js');

        if (isset($this->request->get['file_patch'])) {
            $file_patch = $this->request->get['file_patch'];

            $patch = DIR_MODIFICATION . $file_patch;
            if (is_file($patch)) {
                $data['file_patch'] = $file_patch;

                ob_start();

                readfile($patch);

                $data['code_cache'] = htmlentities(ob_get_contents());

                ob_end_clean();

                $dir = str_replace('\\', '/', realpath(DIR_APPLICATION . '../')) . '/';

                ob_start();

                readfile($dir . $file_patch);

                $data['code_original'] = htmlentities(ob_get_contents());

                ob_end_clean();

                $data['breadcrumbs'][] = array(
                    'text' => $this->language->get('heading_title'),
                    'href' => $this->url->link('extension/modification/diff', 'user_token=' . $this->session->data['user_token'] . '&file_patch=' . $file_patch, true)
                );
            } else {
                $this->response->redirect($this->url->link('marketplace/modification', 'user_token=' . $this->session->data['user_token'], true));
            }
        } else {
            $this->response->redirect($this->url->link('marketplace/modification', 'user_token=' . $this->session->data['user_token'], true));
        }

        $data['return'] = $this->url->link('extension/modification/files', 'user_token=' . $this->session->data['user_token'], true);

        $data['user_token'] = $this->session->data['user_token'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/modification/diff', $data));
    }

    public function save() {
        $json = array();

        $this->load->language('extension/modification/diff');

        if (!$this->user->hasPermission('modify', 'extension/modification/editor')) {
            $json['error'] = $this->language->get('error_permission');
        } else {
            $file_patch = '';

            if (isset($this->request->get['file_patch'])) {
                $file_patch = $this->request->get['file_patch'];

                $patch = DIR_MODIFICATION . $file_patch;
            }

            if ($file_patch && is_file($patch)) {
                if (isset($this->request->post['code_cache'])) {
                    $code_cache = html_entity_decode($this->request->post['code_cache']);

                    $result = file_put_contents($patch, $code_cache);

                    if ($result === false) {
                        $json['error'] = $this->language->get('error_filewrite');
                    } else {
                        $json['success'] = sprintf($this->language->get('text_success_edit'), DIR_MODIFICATION . $file_patch);
                    }
                } else {
                    $json['error'] = $this->language->get('error_code');
                }
            } else {
                $json['error'] = $this->language->get('error_file');
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'extension/modification/diff')) {
            $this->response->redirect($this->url->link('marketplace/modification', 'user_token=' . $this->session->data['user_token'], true));
        }
    }
}
