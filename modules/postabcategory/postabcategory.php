
<?php

	class postabcategory extends Module {
		public function __construct() {
			$this->name 		= 'postabcategory';
			$this->tab 			= 'front_office_features';
			$this->version 		= '1.5';
			$this->author 		= 'posthemes';
			$this->spacer_size ='5';
			$this->_postErrors =array();
			$this->displayName 	= $this->l('Category Tabs');
			$this->description 	= $this->l('Category Tabs');
			parent :: __construct();
		}

		public function install() {
			Configuration::updateValue($this->name . '_list_cate',implode(',',array(3,4,5)));
			Configuration::updateValue($this->name . '_p_on_row', 4);
			Configuration::updateValue($this->name . '_p_limit', 100);
			Configuration::updateValue($this->name . '_tab_effect', 'wiggle');
			Configuration::updateValue($this->name . '_title', $this->l('Cateogry Tabs'));
			return parent :: install()
                && $this->registerHook('blockPosition2')
                && $this->registerHook('header');
		}

		public function uninstall() {
			$this->_clearCache('postabcategory.tpl');
			return parent::uninstall();
		}
		public function hookHeader($params){
				$this->context->controller->addCSS(($this->_path).'postabcategory.css', 'all');
		}
		// Hook Home
		public function hookblockPosition2($params) {
            $nb = Configuration::get($this->name . '_p_limit');
            $product_on_row = Configuration::get($this->name . '_p_on_row');
            $arrayCategory = array();
            $catSelected = Configuration::get($this->name . '_list_cate');
            $cateArray = explode(',', $catSelected);
            $id_lang = (int) Context::getContext()->language->id;
            $id_shop = (int) Context::getContext()->shop->id;
            $arrayProductCate = array();
            foreach($cateArray as $id_category) {
                $category = new Category((int) $id_category, (int) $id_lang, (int) $id_shop);
                $child_cate = Category::getChildren($id_category,$id_lang);
                $html = '';
                $files = scandir(_PS_CAT_IMG_DIR_);
                if (count($files) > 0)
                {
                    
                    foreach ($files as $value=>$file)
                        if (preg_match('/'.$id_category.'-([0-9])?_thumb.jpg/i',substr($file,0)) === 1)
                             if (preg_match('/'.$id_category.'-([0-9])?_thumb.jpg/i',substr($file,1))!=1)
                            $html .= '<span class ="thumb"><img alt="thumb" src="'.$this->context->link->getMediaLink(_THEME_CAT_DIR_.$file).'"
                            class="imgm"/></span>';
                    
                }
                $categoryProducts = $category->getProducts($this->context->language->id, 0,($nb ? $nb : 5));
                $arrayProductCate[] = array('id' => $id_category, 'name'=> $category->name, 'product' => $categoryProducts, 'child_cate'=>$child_cate,'html'=>$html);
            }

            $this->smarty->assign(array(
                'productCates' => $arrayProductCate,
                'child_cate' =>$child_cate,
                'html'=>$html,
                'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
                'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
                'product_on_row' => $product_on_row,
                'tab_effect' => Configuration::get($this->name . '_tab_effect'),
                'title' => Configuration::get($this->name . '_title'),
            ));
            return $this->display(__FILE__, 'postabcategory.tpl');
		}
		public function hookblockPosition1($params) {
            $nb = Configuration::get($this->name . '_p_limit');
            $product_on_row = Configuration::get($this->name . '_p_on_row');
            $arrayCategory = array();
            $catSelected = Configuration::get($this->name . '_list_cate');
            $cateArray = explode(',', $catSelected);
            $id_lang = (int) Context::getContext()->language->id;
            $id_shop = (int) Context::getContext()->shop->id;
            $arrayProductCate = array();
            foreach($cateArray as $id_category) {
                $category = new Category((int) $id_category, (int) $id_lang, (int) $id_shop);
                $child_cate = Category::getChildren($id_category,$id_lang);
                $html = '';
                $files = scandir(_PS_CAT_IMG_DIR_);
                if (count($files) > 0)
                {
                    
                    foreach ($files as $value=>$file)
                        if (preg_match('/'.$id_category.'-([0-9])?_thumb.jpg/i',substr($file,0)) === 1)
                             if (preg_match('/'.$id_category.'-([0-9])?_thumb.jpg/i',substr($file,1))!=1)
                            $html .= '<span class ="thumb"><img alt="thumb" src="'.$this->context->link->getMediaLink(_THEME_CAT_DIR_.$file).'"
                            class="imgm"/></span>';
                    
                }
                $categoryProducts = $category->getProducts($this->context->language->id, 0,($nb ? $nb : 5));
                $arrayProductCate[] = array('id' => $id_category, 'name'=> $category->name, 'product' => $categoryProducts, 'child_cate'=>$child_cate,'html'=>$html);
            }

            $this->smarty->assign(array(
                'productCates' => $arrayProductCate,
                'child_cate' =>$child_cate,
                'html'=>$html,
                'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
                'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
                'product_on_row' => $product_on_row,
                'tab_effect' => Configuration::get($this->name . '_tab_effect'),
                'title' => Configuration::get($this->name . '_title'),
            ));
            return $this->display(__FILE__, 'postabcategory.tpl');
		}
		private function _installHookCustomer(){
			$hookspos = array(
				'tabCategory',
			);
			foreach( $hookspos as $hook ){
				if( Hook::getIdByName($hook) ){

				} else {
					$new_hook = new Hook();
					$new_hook->name = pSQL($hook);
					$new_hook->title = pSQL($hook);
					$new_hook->add();
					$id_hook = $new_hook->id;
				}
			}
			return true;
		}

		public function getContent() {
			$output = '<h2>' . $this->displayName . '</h2>';
			if (Tools::isSubmit('submitPosTabCate')) {
				if (!sizeof($this->_postErrors))
					$this->_postProcess();
				else {
					foreach ($this->_postErrors AS $err) {
						$this->_html .= '<div class="alert error">' . $err . '</div>';
					}
				}
			}
			return $output . $this->_displayForm();
		}

		public function getSelectOptionsHtml($options = NULL, $name = NULL, $selected = NULL) {
			$html = "";
			$html .='<select name =' . $name . ' style="width:130px">';
			if (count($options) > 0) {
				foreach ($options as $key => $val) {
					if (trim($key) == trim($selected)) {
						$html .='<option value=' . $key . ' selected="selected">' . $val . '</option>';
					} else {
						$html .='<option value=' . $key . '>' . $val . '</option>';
					}
				}
			}
			$html .= '</select>';
			return $html;
		}

		private function _postProcess() {
			if(Tools::isSubmit('submitPosTabCate')){
			Configuration::updateValue($this->name . '_list_cate', implode(',', Tools::getValue('list_cate')));
			Configuration::updateValue($this->name . '_p_on_row', Tools::getValue('p_on_row'));
			Configuration::updateValue($this->name . '_p_limit', Tools::getValue('p_limit'));
			Configuration::updateValue($this->name . '_tab_effect', Tools::getValue('tab_effect'));
			Configuration::updateValue($this->name . '_title', Tools::getValue('title'));
			$this->_html.= $this->displayConfirmation($this->l('Settings updated successfully.'));
			}
			return $this->_html;
		}

		private function _displayForm(){

			$tabEffect = array();
			$tabEffect = array(
				'none' => 'None',
				'hinge' => 'Hinge',
				'flash' => 'Flash',
				'shake' => 'Shake',
				'bounce' => 'Bounce',
				'tada' => 'Tada' ,
				'swing' => 'Swing',
				'wobble' => 'Wobble',
				'pulse' => 'Pulse',
				'flip' => 'Flip',
				'flipInX' => 'FlipInX',
				'flipInY' => 'FlipInY',
				'fadeIn' => 'FadeIn',
				'bounceInUp' => 'BounceInUp',
				'fadeInLeft' => 'FadeInLeft',
				'rollIn' => 'RollIn',
				'lightSpeedIn' => 'LightSpeedIn',
				'wiggle' => 'Wiggle',
				'rotateIn' => 'RotateIn',
				'rotateInUpLeft' => 'RotateInUpLeft',
				'rotateInUpRight' => 'RotateInUpRight'

			);
			$this->_html .= '
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
                  <fieldset>
                    <legend><img src="../img/admin/cog.gif" alt="" class="middle" />' . $this->l('Settings') . '</legend>
					<label>'.$this->l('Title: ').'</label>
                    <div class="margin-form">
                            <input type = "text"  name="title" value ='.(Tools::getValue('title')?Tools::getValue('title'): Configuration::get($this->name.'_title')).' ></input>
                    </div>
					<label>'.$this->l('Effect Tab: ').'</label>
                    <div class="margin-form">';
			$this->_html .= $this->getSelectOptionsHtml($tabEffect,'tab_effect',   (Tools::getValue('tab_effect') ? Tools::getValue('tab_effect') : Configuration::get($this->name . '_tab_effect')));
			$this->_html .='
                    </div>';

			$this->_html .= '<label>' . $this->l('Show Link/Label Category: ') . '</label>';
			$this->_html .= '<div class="margin-form">';
			$this->_html .= '<select multiple="multiple" name ="list_cate[]" style="width: 200px; height: 160px;">';
			// BEGIN Categories
			$id_lang = (int) Context::getContext()->language->id;
			$this->getCategoryOption(1, (int) $id_lang, (int) Shop::getContextShopID());
			$this->_html .= '</select>
					</div>';
			$this->_html .='<label>'.$this->l('Products on Row: ').'</label>
                    <div class="margin-form">
                            <input type = "text"  name="p_on_row" value ='. (Tools::getValue('p_on_row')?Tools::getValue('p_on_row'): Configuration::get($this->name.'_p_on_row')).' ></input>
                    </div>
                     <label>'.$this->l('Products Limit: ').'</label>
                    <div class="margin-form">
                            <input type = "text"  name="p_limit" value ='.(Tools::getValue('p_limit')?Tools::getValue('p_limit'): Configuration::get($this->name.'_p_limit')).' ></input>
                    </div>
                    <input type="submit" name="submitPosTabCate" value="'.$this->l('Update').'" class="button" />
                     </fieldset>
		</form>';
			return $this->_html;
		}


		private function getCategoryOption($id_category = 1, $id_lang = false, $id_shop = false, $recursive = true) {
			$id_lang = $id_lang ? (int) $id_lang : (int) Context::getContext()->language->id;
			$category = new Category((int) $id_category, (int) $id_lang, (int) $id_shop);
			if (is_null($category->id))
				return;
			$spacer = NULL;
			if ($recursive) {
				$children = Category::getChildren((int) $id_category, (int) $id_lang, true, (int) $id_shop);
				$spacer = str_repeat('&nbsp;', $this->spacer_size * (int) $category->level_depth);
			}

			$shop = (object) Shop::getShop((int) $category->getShopID());
			$cateCurrent = Configuration::get($this->name . '_list_cate');
			$cateCurrent = explode(',', $cateCurrent);
			$value = (int) $category->id;
			if (in_array($value, $cateCurrent)) {
				$this->_html .= '<option value="' . (int) $category->id . '"  selected ="selected">' . (isset($spacer) ? $spacer : '') . $category->name . '</option>';
			} else {
				$this->_html .= '<option value="' . (int) $category->id . '">' . (isset($spacer) ? $spacer : '') . $category->name . '</option>';
			}
			if (isset($children) && count($children))
				foreach ($children as $child) {
					$this->getCategoryOption((int) $child['id_category'], (int) $id_lang, (int) $child['id_shop']);
				}
		}

	}

?>




