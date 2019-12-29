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

    private function validate() {
        if (!$this->user->hasPermission('modify', 'extension/modification/diff')) {
            $this->response->redirect($this->url->link('marketplace/modification', 'user_token=' . $this->session->data['user_token'], true));
        }
    }
}
