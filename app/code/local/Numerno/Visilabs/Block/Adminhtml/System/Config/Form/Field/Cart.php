<?php
/**
 * Numerno - Visilabs Magento Extension
 *
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
 * @copyright  Copyright (c) 2015 Numerno Bilisim Hiz. Tic. Ltd. Sti. (http://numerno.com/)
 * @license    http://numerno.com/licenses/visilabs-ce.txt  Numerno Visilabs Magento Extension License
 */

/**
 * Catalog Attributes Form Field
 *
 * @category   Numerno
 * @package    Numerno_Visilabs
 * @author     Numerno Bilisim Hiz. Tic. Ltd. Sti. <info@numerno.com>
 */
class Numerno_Visilabs_Block_Adminhtml_System_Config_Form_Field_Cart
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * Attribute renderer cache
     */
    protected $_attributeRenderer;

    /**
     * Parameter no renderer cache
     */
    protected $_noRenderer;

    /**
     * Retrieve attribute renderer
     */
    protected function _getAttributeRenderer()
    {
        if (!$this->_attributeRenderer) {
            $this->_attributeRenderer = $this->getLayout()->createBlock(
                'visilabs/adminhtml_system_config_form_field_renderer_quoteAttributes', '',
                array('is_render_to_js_template' => true)
            );
            $this->_attributeRenderer->setClass('attribute_select');
            $this->_attributeRenderer->setExtraParams('style="width:200px"');
        }
        return $this->_attributeRenderer;
    }

    /**
     * Retrieve attribute renderer
     */
    protected function _getNoRenderer()
    {
        if (!$this->_noRenderer) {
            $this->_noRenderer = $this->getLayout()->createBlock(
                'visilabs/adminhtml_system_config_form_field_renderer_countOut', '',
                array('is_render_to_js_template' => true)
            );
            $this->_noRenderer->setClass('attribute_select');
            $this->_noRenderer->setExtraParams('style="width:100px"');
        }
        return $this->_noRenderer;
    }

    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
        $this->addColumn('param', array(
            'label' => Mage::helper('visilabs')->__('Parameter'),
            'renderer' => $this->_getNoRenderer()
        ));
        $this->addColumn('attribute', array(
            'label' => Mage::helper('visilabs')->__('Cart Attribute'),
            'renderer' => $this->_getAttributeRenderer()
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('visilabs')->__('Add Parameter');
    }

    /**
     * Prepare existing row data object
     *
     * @param Varien_Object
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getAttributeRenderer()->calcOptionHash($row->getData('attribute')),
            'selected="selected"'
        );
        $row->setData(
            'option_extra_attr_' . $this->_getNoRenderer()->calcOptionHash($row->getData('param')),
            'selected="selected"'
        );
    }

    /**
     * Render cell template
     *
     * @param string
     */
    protected function _renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new Exception(Mage::helper('adminhtml')->__('Wrong column name specified.'));
        }
        $column     = $this->_columns[$columnName];
        $inputName  = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';

        if ($column['renderer']) {
            return $column['renderer']->setInputName($inputName)->setColumnName($columnName)->setColumn($column)
                ->toHtml();
        }

        return '<input type="text" name="' . $inputName . '" value="#{' . $columnName . '}" ' .
        ($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' .
        (isset($column['class']) ? $column['class'] : 'input-text') . '"'.
        (isset($column['style']) ? ' style="'.$column['style'] . '"' : '') . '/>';
    }
}