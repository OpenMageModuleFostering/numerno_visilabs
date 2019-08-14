<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the NUMERNO VISILABS MAGENTO EXTENSION License, which extends the Open Software
 * License (OSL 3.0). The Visilabs Magento Extension License is available at this URL:
 * http://numerno.com/licenses/visilabs-ce.txt The Open Software License is available at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * By adding to, editing, or in any way modifying this code, Numerno is not held liable for any inconsistencies or
 * abnormalities in the behaviour of this code. By adding to, editing, or in any way modifying this code, the Licensee
 * terminates any agreement of support offered by Numerno, outlined in the provided Visilabs Magento Extension License.
 * Upon discovery of modified code in the process of support, the Licensee is still held accountable for any and all
 * billable time Numerno spent during the support process. Numerno does not guarantee compatibility with any other
 * Magento extension. Numerno is not responsbile for any inconsistencies or abnormalities in the behaviour of this
 * code if caused by other Magento extension.
 * If you did not receive a copy of the license, please send an email to info@numerno.com or call +90-212-223-5093,
 * so we can send you a copy immediately.
 *
 * @category   [Numerno]
 * @package    [Numerno_Visilabs]
 * @copyright  Copyright (c) 2016. Numerno Bilisim Hiz. Tic. Ltd. Sti. (http://numerno.com/)
 * @license    http://numerno.com/licenses/visilabs-ce.txt  Numerno Visilabs Magento Extension License
 */

/**
 * Product Attributes Form Field
 *
 * @category   Numerno
 * @package    Numerno_Visilabs
 * @author     Numerno Bilisim Hiz. Tic. Ltd. Sti. <info@numerno.com>
 */
class Numerno_Visilabs_Block_Adminhtml_System_Config_Form_Field_Product
    extends Numerno_Visilabs_Block_Adminhtml_System_Config_Form_Field_Abstract
{
    /**
     * Attribute column name
     */
    public $_columnName = 'Product Attribute';

    /**
     * Retrieve attribute renderer
     */
    protected function _getAttributeRenderer()
    {
        if (!$this->_attributeRenderer) {
            $this->_attributeRenderer = $this->getLayout()->createBlock(
                'visilabs/adminhtml_system_config_form_field_renderer_visibleProductAttributes', '',
                array('is_render_to_js_template' => true)
            );
            $this->_attributeRenderer->setClass('attribute_select');
            $this->_attributeRenderer->setExtraParams('style="width:200px"');
        }

        return parent::_getAttributeRenderer();
    }
}