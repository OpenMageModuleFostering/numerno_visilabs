<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the NUMERNO VISILABS EXTENSION FOR MAGENTO® License, which extends the
 * Open Software License (OSL 3.0).
 * The Visilabs Extension for Magento® License is available at this URL: http://numerno.com/licenses/visilabs-ce.txt
 * The Open Software License is available at this URL: http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * By adding to, editing, or in any way modifying this code, Numerno is not held liable for any inconsistencies or
 * abnormalities in the behaviour of this code. By adding to, editing, or in any way modifying this code, the Licensee
 * terminates any agreement of support offered by Numerno, outlined in the provided Visilabs Extension for Magento®
 * License. Upon discovery of modified code in the process of support, the Licensee is still held accountable for any
 * and all billable time Numerno spent during the support process. Numerno does not guarantee compatibility with
 * any other Magento® extension. Numerno is not responsbile for any inconsistencies or abnormalities in the
 * behaviour of this code if caused by other Magento® extension.
 *
 * If you did not receive a copy of the license, please send an email to info@numerno.com or call +90-212-223-5093,
 * so we can send you a copy immediately.
 *
 * @category   [Numerno]
 * @package    [Numerno_Visilabs]
 * @copyright  Copyright (c) 2016. Numerno Bilisim Hiz. Tic. Ltd. Sti. (http://numerno.com/)
 * @license    http://numerno.com/licenses/visilabs-ce.txt  Numerno Visilabs Extension for Magento® License
 */

/**
 * Customer Identifier Options
 *
 * @category   Numerno
 * @package    Numerno_Visilabs
 * @author     Numerno Bilisim Hiz. Tic. Ltd. Sti. <info@numerno.com>
 */
class Numerno_Visilabs_Model_System_Config_Source_Cid
{
    /**
     * Retrieve Customer Identifier Attributes to Option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array(
            'entity_id' => Mage::helper('visilabs')->__('Customer Id'),
            'email'     => Mage::helper('visilabs')->__('Customer Email')
        );
        return $options;
    }

}

