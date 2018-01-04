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
 * Class Controller Payment PagSeguro Redirect
 */
class ControllerExtensionPaymentPagSeguroRedirect extends Controller
{

	private $_urlPagSeguro;

	/**
	 * The first method to be called by the redirect PagSeguro treatment.
	 */
	public function index()
	{

		if ($_POST) {
            if ($this->config->get('payment_pagseguro_checkout') == 'lightbox') {
                $this->tplData['breadcrumbs'] = array();

                $this->tplData['breadcrumbs'][] = array(
                    'text'      => $this->language->get('text_home'),
                    'href'      => $this->url->link('common/home'),
                    'separator' => false
                );

                $this->tplData['breadcrumbs'][] = array(
                    'text'      => 'Checkout',
                    'href'      => $this->url->link('information/static'),
                    'separator' => $this->language->get('text_separator')
                );

                $this->template = 'default/template/extension/payment/pagseguro_lightbox';
    			$this->tplData['column_left'] = $this->load->controller('common/column_left');
    			$this->tplData['column_right'] = $this->load->controller('common/column_right');
    			$this->tplData['header'] = $this->load->controller('common/header');
    			$this->tplData['footer'] = $this->load->controller('common/footer');
    			$this->tplData['content_top'] = $this->load->controller('common/content_top');
    			$this->tplData['content_bottom'] = $this->load->controller('common/content_bottom');

                $this->tplData['code'] = '';
                $this->tplData['environment'] = '';
            }
            $this->_redirect();
        }
        $this->response->setOutput($this->load->view($this->template, $this->tplData));
	}

	/**
	 * Redirect Store for PagSeguro and Clean Cart
	 */
	private function _redirect()
	{

		$this->_urlPagSeguro = $this->request->post['url_ps'];

		if (!empty($this->_urlPagSeguro )) {
		    if($this->config->get('payment_pagseguro_checkout') == 'lightbox'){
		        $this->tplData['code'] = $this->_urlPagSeguro;
		        $this->tplData['environment'] = $this->config->get('payment_pagseguro_environment');

                $this->cart->clear();
            }else{
                header('Location: ' . $this->_urlPagSeguro);
                $this->cart->clear();
            }

		}
	}
}
?>