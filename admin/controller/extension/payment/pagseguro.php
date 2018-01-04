<?php
/*
 ************************************************************************
 Copyright [2013] [PagSeguro Internet Ltda.]

 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.
 ************************************************************************
 */

/**
 * Controller Payment PagSeguro.
 * Class responsible for the configuration data of the user of PagSeguro (adm)
 */
class ControllerExtensionPaymentPagSeguro extends Controller
{

	/**
	 * Array Error
	 * @var array
	 */
	private $error = array();

	/**
	 *Regex validate e-mail
	 * @var regex
	 */
	private $pattern = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';

	/**
	 * Array of extensions
	 * @var string
	 */
	private $array_extension = array(".txt", ".log");

	private $tplData = array();

	public function index()
	{

		$this->_addPagSeguroLibrary();
		$this->language->load('extension/payment/pagseguro');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payment_pagseguro', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->_setPagSeguroConfiguration();
			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', 'SSL'));
		}

		$this->tplData['heading_title'] = $this->language->get('heading_title');

		$this->_createInput();
		$this->_createText();
		$this->_createRadio();
		$this->_createButtons();
		$this->_createBreadcrumbs();
		$this->_createLink();
		$this->_createError();

		$this->template = 'extension/payment/pagseguro';
		$this->tplData['header'] = $this->load->controller('common/header');
        $this->tplData['column_left'] = $this->load->controller('common/column_left');
        $this->tplData['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view($this->template, $this->tplData));
	}

	/**
	 * Add PagSeguro Libary
	 */
	private function _addPagSeguroLibrary()
	{
		include_once DIR_CATALOG . 'controller/extension/payment/PagSeguroLibrary/PagSeguroLibrary.php';
	}

	/**
	 * Create Input
	 */
	private function _createInput()
	{

		if (isset($this->request->post['payment_pagseguro_status']))
			$this->tplData['payment_pagseguro_status'] = $this->request->post['payment_pagseguro_status'];
		else
			$this->tplData['payment_pagseguro_status'] = $this->config->get('payment_pagseguro_status');

		if (isset($this->request->post['payment_pagseguro_sort_order']))
			$this->tplData['pagseguro_sort_order'] = $this->request->post['payment_pagseguro_sort_order'];
		else
			$this->tplData['pagseguro_sort_order'] = $this->config->get('payment_pagseguro_sort_order');

		if (isset($this->request->post['payment_pagseguro_email']))
			$this->tplData['pagseguro_email'] = $this->request->post['payment_pagseguro_email'];
		else
			$this->tplData['pagseguro_email'] = $this->config->get('payment_pagseguro_email');

		if (isset($this->request->post['payment_pagseguro_token']))
			$this->tplData['pagseguro_token'] = $this->request->post['payment_pagseguro_token'];
		else
			$this->tplData['pagseguro_token'] = $this->config->get('payment_pagseguro_token');

		if (isset($this->request->post['payment_pagseguro_environment']))
            $this->tplData['pagseguro_environment'] = $this->request->post['payment_pagseguro_environment'];
        else
            $this->tplData['pagseguro_environment'] = $this->config->get('payment_pagseguro_environment');

        if (isset($this->request->post['payment_pagseguro_checkout']))
            $this->tplData['pagseguro_checkout'] = $this->request->post['payment_pagseguro_checkout'];
        else
            $this->tplData['pagseguro_checkout'] = $this->config->get('payment_pagseguro_checkout');

		if (isset($this->request->post['payment_pagseguro_forwarding']))
			$this->tplData['pagseguro_forwarding'] = $this->request->post['payment_pagseguro_forwarding'];
		else
			$this->tplData['pagseguro_forwarding'] = $this->validateRedirectUrl();

		if (isset($this->request->post['payment_pagseguro_url_notification']))
			$this->tplData['pagseguro_url_notification'] = $this->request->post['payment_pagseguro_url_notification'];
		else
			$this->tplData['pagseguro_url_notification'] = $this->validateNotificationUrl();

		if (isset($this->request->post['payment_pagseguro_charset']))
			$this->tplData['pagseguro_charset'] = $this->request->post['payment_pagseguro_charset'];
		else
			$this->tplData['pagseguro_charset'] = $this->config->get('payment_pagseguro_charset');

		if (isset($this->request->post['payment_pagseguro_log']))
			$this->tplData['pagseguro_log'] = $this->request->post['payment_pagseguro_log'];
		else
			$this->tplData['pagseguro_log'] = $this->config->get('payment_pagseguro_log');

		if (isset($this->request->post['payment_pagseguro_directory']))
			$this->tplData['pagseguro_directory'] = $this->request->post['payment_pagseguro_directory'];
		else
			$this->tplData['pagseguro_directory'] = $this->config->get('payment_pagseguro_directory');
	}

	/**
	 * Create Text
	 */
	private function _createText()
	{

		$this->tplData['enable_module'] = $this->language->get('enable_module');
		$this->tplData['text_module'] = $this->language->get('text_module');

		$this->tplData['display_order'] = $this->language->get('display_order');
		$this->tplData['text_order'] = $this->language->get('text_order');

		$this->tplData['ps_email'] = $this->language->get('ps_email');
		$this->tplData['text_email'] = $this->language->get('text_email');

		$this->tplData['ps_token'] = $this->language->get('ps_token');
		$this->tplData['text_token'] = $this->language->get('text_token');

		$this->tplData['ps_environment'] = $this->language->get('ps_environment');
        $this->tplData['text_environment'] = $this->language->get('text_environment');

        $this->tplData['ps_checkout'] = $this->language->get('ps_checkout');
        $this->tplData['text_checkoutPadrao'] = $this->language->get('text_checkoutPadrao');
        $this->tplData['text_checkoutLightbox'] = $this->language->get('text_checkoutLightbox');

		$this->tplData['url_forwarding'] = $this->language->get('url_forwarding');
		$this->tplData['text_url_forwarding'] = $this->language->get('text_url_forwarding');

		$this->tplData['url_notification'] = $this->language->get('url_notification');
		$this->tplData['text_url_notification'] = $this->language->get('text_url_notification');

		$this->tplData['charset'] = $this->language->get('charset');
		$this->tplData['text_charset'] = $this->language->get('text_charset');

		$this->tplData['log'] = $this->language->get('log');
		$this->tplData['text_log'] = $this->language->get('text_log');

		$this->tplData['directory'] = $this->language->get('directory');
		$this->tplData['text_directory'] = $this->language->get('text_directory');
	}

	/**
	 * Create Radio Button
	 */
	private function _createRadio()
	{
		$this->tplData['text_yes'] = $this->language->get('text_yes');
		$this->tplData['text_no'] = $this->language->get('text_no');

		$this->tplData['iso'] = $this->language->get('iso');
		$this->tplData['utf'] = $this->language->get('utf');
	}

	/**
	 * Create Buttons
	 */
	private function _createButtons()
	{
		$this->tplData['button_save'] = $this->language->get('button_save');
		$this->tplData['button_cancel'] = $this->language->get('button_cancel');
	}

	/**
	 * Create Breadcrumbs to view.
	 */
	private function _createBreadcrumbs()
	{

		$this->tplData['breadcrumbs'] = array();

		$this->tplData['breadcrumbs'][] = array(
			'text'		 => $this->language->get('text_home'),
			'href'		 => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], 'SSL'),
			'separator'	 => false
		);

		$this->tplData['breadcrumbs'][] = array(
			'text'		 => $this->language->get('text_payment'),
			'href'		 => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], 'SSL'),
			'separator'	 => ' :: '
		);

		$this->tplData['breadcrumbs'][] = array(
			'text'		 => $this->language->get('heading_title'),
			'href'		 => $this->url->link('extension/payment/pagseguro', 'user_token=' . $this->session->data['user_token'], 'SSL'),
			'separator'	 => ' :: '
		);
	}

	/**
	 * Create Link's
	 */
	private function _createLink()
	{
		$this->tplData['action'] = $this->url->link('extension/payment/pagseguro', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$this->tplData['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], 'SSL');
	}

	/**
	 * Create Error
	 */
	private function _createError()
	{

		if (isset($this->error['warning'])) {
			$this->tplData['error_warning'] = $this->error['warning'];
		} else {
			$this->tplData['error_warning'] = '';
		}

		if (isset($this->error['email'])) {
			$this->tplData['error_email'] = $this->error['email'];
		} else {
			$this->tplData['error_email'] = '';
		}

		if (isset($this->error['token'])) {
			$this->tplData['error_token'] = $this->error['token'];
		} else {
			$this->tplData['error_token'] = '';
		}
	}

	/**
	 * Validate
	 * @return boolean
	 */
	protected function validate()
	{

		$this->_permission();
		$this->_validateEmail();
		$this->_validateToken();
		$this->_notificationUrl();
		$this->_redirectUrl();

		return (empty($this->error )) ? true : false;
	}

	/**
	 * Validate Permisson
	 */
	private function _permission()
	{
		if (!$this->user->hasPermission('modify', 'extension/payment/pp_standard'))
			$this->error['warning'] = $this->language->get('error_permission');
	}

	/**
	 * Validate E-mail
	 */
	private function _validateEmail()
	{

		if (empty($this->request->post['payment_pagseguro_email']))
			$this->error['email'] = $this->language->get('error_email_required');

		if (!empty($this->request->post['payment_pagseguro_email'])) {
			$valid = preg_match($this->pattern, $this->request->post['payment_pagseguro_email']);

			if ($valid != 1) {
				$this->error['email'] = $this->language->get('error_email_invalid');
			}
		}
	}

	/**
	 * Validate Token
	 */
	private function _validateToken()
	{

		if (empty($this->request->post['payment_pagseguro_token']))
			$this->error['token'] = $this->language->get('error_token_required');

		if (strlen(trim($this->request->post['payment_pagseguro_token'])) != 32 && !(empty($this->request->post['payment_pagseguro_token'])))
			$this->error['token'] = $this->language->get('error_token_invalid');
	}

	/**
	 * Retrieve PagSeguro data configuration from database
	 */
	private function _setPagSeguroConfiguration()
	{
		$charset = ($this->request->post['payment_pagseguro_charset'] == 1) ? $this->language->get('iso') : $this->language->get('utf');

		// setting configurated default charset
		PagSeguroConfig::setApplicationCharset($charset);

		$activeLog = ($this->request->post['payment_pagseguro_log'] == 1) ? TRUE : FALSE;

		// setting configurated default log info
		if ($activeLog) {
			$directory = $this->_getDirectoryLog();
			$this->_verifyLogFile($directory);
			PagSeguroConfig::activeLog($directory);
		}
	}

	/**
	 * Verify if PagSeguro log file exists.
	 * Case log file not exists, try create
	 * else, log will be created as name as PagSeguro.log as name into PagseguroLibrary folder into module
	 */
	private function _verifyLogFile($file)
	{

		try
		{
			$f = fopen($file, "a");
			fclose($f);
		}
		catch (Exception $e)
		{
			die($e);
		}
	}

	/**
	 * Validate Notification Url
	 * @return url
	 */
	private function validateNotificationUrl()
	{

		$value = $this->config->get('payment_pagseguro_url_notification');

		if (empty($value))
			return $this->_generateNotificationUrl();

		return $value;
	}

	/**
	 * Validate Redirect Url
	 * @return url
	 */
	private function validateRedirectUrl()
	{

		$value = $this->config->get('payment_pagseguro_forwarding');

		if (empty($value))
			return $this->_generationRedirectUrl();

		return $value;
	}

	/**
	 * Notification Url
	 */
	private function _notificationUrl()
	{

		if (empty($this->request->post['payment_pagseguro_url_notification']))
			$this->tplData['pagseguro_url_notification'] = $this->_generateNotificationUrl();
	}

	/**
	 * Redirect Url
	 */
	private function _redirectUrl()
	{

		if (empty($this->request->post['payment_pagseguro_forwarding']))
			$this->tplData['pagseguro_forwarding'] = $this->_generationRedirectUrl();
	}

	/**
	 * Url Notification
	 * @return url notification
	 */
	private function _generateNotificationUrl()
	{
		return HTTP_CATALOG . "index.php?route=extension/payment/pagseguro_notification";
	}

	/**
	 * Redirect Url
	 * @return url redirect
	 */
	private function _generationRedirectUrl()
	{
		return HTTP_CATALOG . "index.php?route=checkout/success";
	}

	/**
	 * Validate if value is not null
	 * @param type $value
	 * @return boolean
	 */
	private function _isNotNull($value)
	{

		if ($value != null && $value != "")
			return TRUE;

		return false;
	}

	/**
	 * Return directory log
	 */
	private function _getDirectoryLog()
	{
		$_dir = str_replace('catalog/', '', DIR_CATALOG);
		$directory = NULL;
		$validate_extension = FALSE;

		if ($this->_isNotNull($this->request->post['payment_pagseguro_directory'])) {
			$directory = $this->request->post['payment_pagseguro_directory'];

			foreach ($this->array_extension as $extension) {
				if (stripos($directory, $extension))
					$validate_extension = TRUE;
			}
		}

		if ($directory != NULL && $validate_extension == FALSE)
			$directory = $this->_createFileDirectory($directory);

		return ($directory != NULL) ? $_dir . $directory : null;
	}

	/**
	 * Create file
	 * @param type $directory
	 * @return string
	 */
	private function _createFileDirectory($directory)
	{

		$directory = explode('/', $directory);
		$path = '';

		foreach ($directory as $char) {
			$path = $path . $char . DIRECTORY_SEPARATOR;
		}

		return $path . 'PagSeguro.log';
	}
}
?>