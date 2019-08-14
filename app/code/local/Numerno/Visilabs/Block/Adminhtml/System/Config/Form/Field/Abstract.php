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
 * Abstract Class for Attributes Form Field
 *
 * @category   Numerno
 * @package    Numerno_Visilabs
 * @author     Numerno Bilisim Hiz. Tic. Ltd. Sti. <info@numerno.com>
 */
class Numerno_Visilabs_Block_Adminhtml_System_Config_Form_Field_Abstract
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * Attribute renderer cache
     */
    protected $_attributeRenderer;

    /**
     * Custom Event Parameters renderer cache
     */
    protected $_cepRenderer;

    /**
     * Attribute column name
     */
    public $_columnName = 'Attribute';

    /**
     * Retrieve attribute renderer
     */
    protected function _getAttributeRenderer()
    {
        return $this->_attributeRenderer;
    }

    /**
     * Retrieve custom event parametera renderer
     */
    protected function _getCepRenderer()
    {
        if (!$this->_cepRenderer) {
            $this->_cepRenderer = $this->getLayout()->createBlock(
                'visilabs/adminhtml_system_config_form_field_renderer_countOut', '',
                array('is_render_to_js_template' => true)
            );
            $this->_cepRenderer->setClass('attribute_select');
            $this->_cepRenderer->setExtraParams('style="width:100px"');
        }
        return $this->_cepRenderer;
    }

    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
        $this->addColumn('param', array(
            'label' => Mage::helper('visilabs')->__('Parameter'),
            'renderer' => $this->_getCepRenderer()
        ));
        $this->addColumn('attribute', array(
            'label' => Mage::helper('visilabs')->__($this->_columnName),
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
            'option_extra_attr_' . $this->_getCepRenderer()->calcOptionHash($row->getData('param')),
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