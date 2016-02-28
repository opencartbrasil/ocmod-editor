<?php
class ControllerExtensionModificationEditor extends Controller {
	public function index() {
		if (!$this->user->hasPermission('modify', 'extension/modification_editor')) {
			$this->response->redirect($this->url->link('extension/modification', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->load->language('extension/modification_editor');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addScript('view/javascript/ace/ace.js');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_modifications'),
			'href' => $this->url->link('extension/modification', 'token=' . $this->session->data['token'], 'SSL')
		);

		if (isset($this->request->get['modification_id'])) {
			$modification_id = $this->request->get['modification_id'];

			$this->load->model('extension/modification_editor');
			$modification_info = $this->model_extension_modification_editor->getModification($modification_id);

			if ($modification_info) {
				$data['modification_id'] = $modification_id;
				$data['name'] = $modification_info['name'];
				$data['xml'] = $modification_info['xml'];

				$data['breadcrumbs'][] = array(
					'text' => $this->language->get('heading_title'),
					'href' => $this->url->link('extension/modification_editor', 'token=' . $this->session->data['token'] . '&modification_id=' . $modification_id, 'SSL')
				);
			} else {
				$this->response->redirect($this->url->link('extension/modification', 'token=' . $this->session->data['token'], 'SSL'));
			}
		} else {
			$data['modification_id'] = 0;
			$data['name'] = $this->language->get('text_new');
			$data['xml'] = '';

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/modification_editor', 'token=' . $this->session->data['token'], 'SSL')
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['entry_xml_code'] = $this->language->get('entry_xml_code');

		$data['text_loading'] = $this->language->get('text_loading');
		$data['text_erasing'] = $this->language->get('text_erasing');

		$data['button_clear_data']  = $this->language->get('button_clear_data');
		$data['button_clear_image'] = $this->language->get('button_clear_image');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_return'] = $this->language->get('button_return');

		$data['return'] = $this->url->link('extension/modification', 'token=' . $this->session->data['token'], 'SSL');
		
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
				try {
					$dom = new DOMDocument('1.0', 'UTF-8');
					$dom->loadXml($xml);
					
					$name = $dom->getElementsByTagName('name')->item(0);

					if ($name) {
						$name = $name->nodeValue;
					} else {
						$name = '';
					}

					$code = $dom->getElementsByTagName('code')->item(0);

					if ($code) {
						$code = $code->nodeValue;
					} else {
						$json['error'] = $this->language->get('error_code');
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
						'name'    => $name,
						'code'    => $code,
						'author'  => $author,
						'version' => $version,
						'link'    => $link,
						'xml'     => $xml,
						'status'  => 1
					);

					if (isset($this->request->post['modification_id'])) {
						$modification_id = $this->request->post['modification_id'];

						$this->load->model('extension/modification_editor');

						if ($modification_id > 0) {
							$modification_info = $this->model_extension_modification_editor->getModification($modification_id);

							if ($modification_info) {
								$this->model_extension_modification_editor->editModification($modification_id, $modification_data);
								$json['success'] = $this->language->get('text_success_edit');
							} else {
								$json['error'] = $this->language->get('error_modification');
							}
						} else {
							$this->load->model('extension/modification');
							$modification_info = $this->model_extension_modification->getModificationByCode($code);

							if ($modification_info) {
								$json['error'] = sprintf($this->language->get('error_exists'), $modification_info['name']);
							} else {
								$this->model_extension_modification_editor->addModification($modification_data);
								$json['success'] = $this->language->get('text_success_add');
							}
						}

						if (!$json) {
							$this->refresh();
						}
					} else {
						$json['error'] = $this->language->get('error_modification');
					}
				} catch(Exception $exception) {
					$json['error'] = sprintf($this->language->get('error_exception'), $exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine());
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
			$this->response->redirect($this->url->link('extension/modification', 'token=' . $this->session->data['token'], 'SSL'));
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
			$this->response->redirect($this->url->link('extension/modification', 'token=' . $this->session->data['token'], 'SSL'));
		}
	}

	public function refresh() {
		if (!$this->user->hasPermission('modify', 'extension/modification_editor')) {
			$this->response->redirect($this->url->link('extension/modification', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$maintenance = $this->config->get('config_maintenance');

		$this->load->model('setting/setting');

		$this->model_setting_setting->editSettingValue('config', 'config_maintenance', true);

		$log = array();

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
					unlink($file);

				} elseif (is_dir($file)) {
					rmdir($file);
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

		$this->load->model('extension/modification');
		$results = $this->model_extension_modification->getModifications();

		foreach ($results as $result) {
			if ($result['status']) {
				$xml[] = $result['xml'];
			}
		}

		$modification = array();

		foreach ($xml as $xml) {
			$dom = new DOMDocument('1.0', 'UTF-8');
			$dom->preserveWhiteSpace = false;
			$dom->loadXml($xml);

			$log[] = 'MOD: ' . $dom->getElementsByTagName('name')->item(0)->textContent;

			$recovery = array();

			if (isset($modification)) {
				$recovery = $modification;
			}

			$files = $dom->getElementsByTagName('modification')->item(0)->getElementsByTagName('file');

			foreach ($files as $file) {
				$operations = $file->getElementsByTagName('operation');

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

									$log[] = 'FILE: ' . $key;
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
										$log[] = 'NOT FOUND!';

										if ($error == 'skip') {
											break;
										}

										if ($error == 'abort') {
											$modification = $recovery;

											$log[] = 'ABORTING!';

											break 5;
										}
									}
								}
							}
						}
					}
				}
			}

			$log[] = '----------------------------------------------------------------';
		}

		$ocmod = new Log('ocmod.log');
		$ocmod->write(implode("\n", $log));

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

	private function delTree($dir) {
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
			 (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : @unlink("$dir/$file");
		}
		return @rmdir($dir);
	}

	public function clearCacheData() {
		if (!$this->user->hasPermission('modify', 'extension/modification_editor')) {
			$this->response->redirect($this->url->link('extension/modification', 'token=' . $this->session->data['token'], 'SSL'));
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
			$this->response->redirect($this->url->link('extension/modification', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->delTree(DIR_IMAGE . 'cache/');
		@mkdir(DIR_IMAGE . 'cache/');

		$this->load->language('extension/modification_editor');
		$json['success'] = $this->language->get('text_clear_image');

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}