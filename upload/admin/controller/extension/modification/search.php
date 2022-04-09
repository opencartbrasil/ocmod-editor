<?php
class ControllerExtensionModificationSearch extends Controller {
    public function index() {
        $this->validate();

        $this->load->language('extension/modification/search');

        $url = '';

        $data = array();

        if (isset($this->request->get['search_query'])) {
            $url .= '&search_query=' . $this->request->get['search_query'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        if (isset($this->request->get['search_query'])) {
            $search_query = $this->request->get['search_query'];
        } else {
            $search_query = '';
        }

        $data['search_query'] = $search_query;

        $filter_data = array(
            'search_query' => $search_query,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $this->load->model('extension/modification/editor');

        $modification_total = 0;

        $data['modifications'] = array();

        if (!empty($search_query)){
            $modification_total = $this->model_extension_modification_editor->getTotalSearchModificationElement($filter_data);

            $modifications = $this->model_extension_modification_editor->searchModificationElement($filter_data);

            foreach ($modifications as $modification) {
                $data['modifications'][] = array(
                    'modification_id' => $modification['modification_id'],
                    'extension_install_id' => $modification['extension_install_id'],
                    'name' => $modification['name'],
                    'code' => $modification['code'],
                    'author' => $modification['author'],
                    'version' => $modification['version'],
                    'date_added' => date($this->language->get('datetime_format'), strtotime($modification['date_added'])),
                    'link_editor' => $this->url->link('extension/modification/editor', 'user_token=' . $this->session->data['user_token'] . '&modification_id=' . $modification['modification_id'] . $url, true)
                );
            }
        }

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
            'text' => $this->language->get('text_search'),
            'href' => $this->url->link('marketplace/modification/search', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $url = '';

        if (isset($this->request->get['search_query'])) {
            $url .= '&search_query=' . $this->request->get['search_query'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $pagination = new Pagination();
        $pagination->total = $modification_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('extension/modification/search', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($modification_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($modification_total - $this->config->get('config_limit_admin'))) ? $modification_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $modification_total, ceil($modification_total / $this->config->get('config_limit_admin')));

        $data['return'] = $this->url->link('marketplace/modification', 'user_token=' . $this->session->data['user_token'], true);

        $data['user_token'] = $this->session->data['user_token'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/modification/search', $data));
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'extension/modification/search')) {
            $this->response->redirect($this->url->link('marketplace/modification', 'user_token=' . $this->session->data['user_token'], true));
        }
    }
}
