<?php
class ControllerExtensionModificationEditor extends Controller {
	public function index() {
		if (!$this->user->hasPermission('modify', 'extension/modification')) {
			$this->response->redirect($this->url->link('extension/modification', 'token=' . $this->session->data['token'], 'SSL'));
		}

		if (isset($this->request->get['modification_id'])) {
			$modification_id = $this->request->get['modification_id'];
		} else {
			$modification_id = 0;
		}

		$this->load->model('extension/modification_editor');
		$modification_info = $this->model_extension_modification_editor->getModification($modification_id);

		if ($modification_info) {
			$this->load->language('extension/modification_editor');

			$this->document->setTitle($this->language->get('heading_title'));

			$this->document->addScript('view/javascript/ace/ace.js');

			$data['modification_id'] = $modification_id;
			$data['name'] = $modification_info['name'];
			$data['xml'] = $modification_info['xml'];

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_modifications'),
				'href' => $this->url->link('extension/modification', 'token=' . $this->session->data['token'], 'SSL')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/modification_editor', 'token=' . $this->session->data['token'] . '&modification_id=' . $modification_id, 'SSL')
			);

			$data['heading_title'] = $this->language->get('heading_title');

			$data['entry_xml_code'] = $this->language->get('entry_xml_code');

			$data['text_loading'] = $this->language->get('text_loading');

			$data['button_save'] = $this->language->get('button_save');
			$data['button_return'] = $this->language->get('button_return');

			$data['return'] = $this->url->link('extension/modification', 'token=' . $this->session->data['token'], 'SSL');
			
			$data['token'] = $this->session->data['token'];

			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('extension/modification_editor.tpl', $data));
		} else {
			$this->response->redirect($this->url->link('extension/modification', 'token=' . $this->session->data['token'], 'SSL'));
		}
	}

	public function save() {
		$json = array();

		$this->load->language('extension/modification_editor');

		if (!$this->user->hasPermission('modify', 'extension/modification')) {
			$json['error'] = $this->language->get('error_permission');
		} else {	
			if (isset($this->request->post['modification_id'])) {
				$modification_id = $this->request->post['modification_id'];
			} else {
				$modification_id = 0;
			}

			$this->load->model('extension/modification_editor');
			$modification_info = $this->model_extension_modification_editor->getModification($modification_id);

			if ($modification_info) {
				$xml = html_entity_decode($this->request->post['xml']);

				if ($xml) {
					try {
						$dom = new DOMDocument('1.0', 'UTF-8');
						$dom->loadXml($xml);
						
						$name = $dom->getElementsByTagName('name')->item(0);

						if ($name) {
							$name = $name->nodeValue;
						} else {
							$name = '';
						}
						
						$author = $dom->getElementsByTagName('author')->item(0);

						if ($author) {
							$author = $author->nodeValue;
						} else {
							$author = '';
						}

						$version = $dom->getElementsByTagName('version')->item(0);

						if ($version) {
							$version = $version->nodeValue;
						} else {
							$version = '';
						}

						$link = $dom->getElementsByTagName('link')->item(0);

						if ($link) {
							$link = $link->nodeValue;
						} else {
							$link = '';
						}

						$modification_data = array(
							'modification_id' => $modification_id,
							'name'            => $name,
							'author'          => $author,
							'version'         => $version,
							'link'            => $link,
							'xml'             => $xml
						);

						if (!$json) {
							$this->model_extension_modification_editor->editModification($modification_data);
							$json['success'] = $this->language->get('text_success');
						}
					} catch(Exception $exception) {
						$json['error'] = sprintf($this->language->get('error_exception'), $exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine());
					}
				}
			} else {
				$json['error'] = $this->language->get('error_file');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function download() {
		if (!$this->user->hasPermission('modify', 'extension/modification')) {
			$this->response->redirect($this->url->link('extension/modification', 'token=' . $this->session->data['token'], 'SSL'));
		}

		if (isset($this->request->get['modification_id'])) {
			$modification_id = $this->request->get['modification_id'];
		} else {
			$modification_id = 0;
		}

		$this->load->model('extension/modification_editor');
		$modification_info = $this->model_extension_modification_editor->getModification($modification_id);

		if ($modification_info) {
			$dom = new DOMDocument('1.0', 'UTF-8');

			$dom->loadXml($modification_info['xml']);
			$dom->formatOutput = true;

			$filename = $modification_info['code'] . '.ocmod.xml';
			
			$content = html_entity_decode($dom->saveXML());

			if (!headers_sent()) {
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="' . $filename . '"');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				header('Content-Length: ' . filesize($content));

				if (ob_get_level()) {
					ob_end_clean();
				}

				echo $content;

				exit();
			}
		} else {
			$this->response->redirect($this->url->link('extension/modification', 'token=' . $this->session->data['token'], 'SSL'));
		}
	}
}
