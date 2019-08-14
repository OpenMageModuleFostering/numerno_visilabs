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
 * Custom Event Parameters (OM.cep1-10) Form Field Renderer
 *
 * @category   Numerno
 * @package    Numerno_Euromsg
 * @author     Numerno Bilisim Hiz. Tic. Ltd. Sti. <info@numerno.com>
 */
class Numerno_Visilabs_Block_Adminhtml_System_Config_Form_Field_Renderer_CountOut
    extends Mage_Core_Block_Html_Select
{

    /**
     * Set form element input name
     *
     * @param string $value
     * @return string
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            for ($i = 1; $i <= 10; $i++) {
                $this->addOption($i, "OM.cep$i");
            }
        }

        return parent::_toHtml();
    }
}