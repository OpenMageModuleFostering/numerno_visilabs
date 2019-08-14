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
 * Event observer
 *
 * @category   Numerno
 * @package    Numerno_Visilabs
 * @author     Numerno Bilisim Hiz. Tic. Ltd. Sti. <info@numerno.com>
 */
class Numerno_Visilabs_Model_Observer
{
    const VISILABS_CACHE_LIFETIME     = 86400;
    const VISILABS_CACHE_TAG          = 'VISILABS';
    const VISILABS_CACHE_PRODUCT_KEY  = 'VISILABS_P';
    const VISILABS_CACHE_CATEGORY_KEY = 'VISILABS_C';

    /**
     * Get customer identifier attribute
     *
     * @return string
     */
    private function _getCustomerIdentifier()
    {
        return Mage::getStoreConfig('visilabs/general/cid');
    }

    /**
     * Prepare category hierarcy array
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    private function _prepareCategoryHierarchy($product)
    {

        $categoryIds = $product->getCategoryIds();
        if (empty($categoryIds)) {
            return array();
        }

        $foundDeepestHierarchy = false;

        while (!$foundDeepestHierarchy) {

            //get deepest category by level
            $category = Mage::getModel('catalog/category')
                ->getCollection()
                ->addAttributeToSelect(array('entity_id', 'path'))
                ->addAttributeToFilter('is_active', 1)
                ->addFieldToFilter('entity_id', array('in' => $categoryIds))
                ->setOrder('level', 'DESC')
                ->setPageSize(1)
                ->setCurPage(1)
                ->getFirstItem();

            $pathIds = array_reverse(explode(',', $category->getPathInStore()));

            $categories = Mage::getModel('catalog/category')
                ->getCollection()
                ->addAttributeToSelect('name')
                ->addAttributeToFilter('is_active', 1)
                ->addFieldToFilter('entity_id', array('in' => $pathIds));

            $pathDiff = array_diff($pathIds, $categories->getAllIds());
            if (!empty($pathDiff) ) { //there is a disabled category in hierarcy
                $categoryIds = array_diff($categoryIds, array($category->getId()));
                if(empty($categoryIds)) {
                    return array(); //there is no category of this product that has a hierarcy with all categories active
                }
                $foundDeepestHierarchy = false;
            } else {
                $foundDeepestHierarchy = true;
            }
        }

        $hierarchy = array_flip($pathIds);
        foreach ($categories as $category) {
            $hierarchy[$category->getId()] = $category->getName();
        }

        return $hierarchy;
    }

    /**
     * Prepare customer parameters
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return array
     */
    private function _prepareCustomerParams($customer, $eventType)
    {

        Mage::log(array(get_class($customer), $customer->getData()), null, 'c.log');
        //get customer identifier
        switch ($this->_getCustomerIdentifier()) {
            case 'email':
                $exVisitorId = $customer->getEmail();
                break;
            default:
                $exVisitorId = $customer->getId();
        }

        //initial parameters
        $params = array(
            'OM.exVisitorID' => $exVisitorId,
            'OM.vn'          => $customer->getFirstname(),
            'OM.sn'          => $customer->getLastname(),
            'OM.em'          => $customer->getEmail(),
            $eventType       => $customer->getId(),
            'EventType'      => $eventType
        );

        //location
        $address = $customer->getPrimaryBillingAddress();
        if ($address) {
            $params['OM.loc'] = $address->getRegion();
        }

        //date of birth
        if(Zend_Date::isDate($customer->getDob(), Zend_Date::ISO_8601)) {
            $params['OM.bd'] = date("Y-m-d", strtotime($customer->getDob()));
        }

        //gender
        $genders = array(1 => 'Male', 2 => 'Female'); //static labels in Visilabs
        if($customer->getGender() && isset($genders[$customer->getGender()])){
            $params['OM.gn'] = $genders[$customer->getGender()];
        }

        //custom event parameters
        $customParams = unserialize(Mage::getStoreConfig('visilabs/general/cep'));
        foreach ($customParams as $param) {

            //get group name
            if ($param['attribute'] == '_group') {
                $groupName = Mage::getModel('customer/group')
                    ->load($customer->getGroupId())
                    ->getCustomerGroupCode();
                $params['OM.cep' . $param['param']] = $groupName;
                continue;
            }

            if (!$param['attribute'] || !$customer->getResource()->getAttribute($param['attribute'])) {
                continue;
            }
            $params['OM.vseg' . $param['param']] = $customer->getAttributeText($param['attribute']);
        }

        return $params;
    }

    /**
     * Format price
     *
     * @param float $price
     * @return string
     */
    private function _formatPrice($price)
    {
        return number_format($price, 2, ',', '');
    }

    /**
     * Get visitor session
     *
     * @param array $params
     * @return void
     */
    protected function getSession()
    {
        return Mage::getSingleton('core/session',  array("name" => "frontend"));
    }

    /**
     * Add visilabs params to visitor session
     *
     * @param array $params
     * @return void
     */
    public function addVisilabsParams($params)
    {
        $session = $this->getSession();
        if($session->hasVisilabsParams()) {
            $params = array_merge($session->getVisilabsParams(), $params);
        }
        $session->setVisilabsParams($params);
    }

    /**
     * Login Event
     *
     * @param Varien_Event_Observer $observer
     * @return Numerno_Visilabs_Model_Observer
     */
    public function eventLogin(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('visilabs')->isTaggingEnabled()) {
            return $this;
        }

        $customer = $observer->getCustomer();
        $params   = $this->_prepareCustomerParams($customer, 'Login');

        $this->addVisilabsParams($params);

        return $this;
    }

    /**
     * Signup Event
     *
     * @param Varien_Event_Observer $observer
     * @return Numerno_Visilabs_Model_Observer
     */
    public function eventSignup(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('visilabs')->isTaggingEnabled()) {
            return $this;
        }
        $event    = $observer->getEvent();
        $customer = $event->getCustomer();

        $params = $this->_prepareCustomerParams($customer, 'Signup');

        $this->addVisilabsParams($params);

        return $this;
    }

    /**
     * Product View Event
     *
     * @param Varien_Event_Observer $observer
     * @return Numerno_Visilabs_Model_Observer
     */
    public function eventProductView(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('visilabs')->isTaggingEnabled('catalog')) {
            return $this;
        }

        $event   = $observer->getEvent();
        $product = $event->getProduct();
        $cache   = Mage::app()->getCache();
        $params  = json_decode($cache->load(self::VISILABS_CACHE_PRODUCT_KEY . $product->getId()));

        if (!$params) {

            $categoryHierarcy = $this->_prepareCategoryHierarchy($product);

            //initial parameters
            $params = array(
                'OM.pv'   => $product->getId(),
                'OM.pn'   => $product->getName(),
                'OM.cat'  => implode('|', array_keys($categoryHierarcy)),
                'OM.catn' => implode('|', $categoryHierarcy),
                'OM.inv'  => $product->getStockItem()->getIsInStock(),
                'OM.ppr'  => $this->_formatPrice($product->getFinalPrice())
            );

            //custom event parameters
            $customParams = unserialize(Mage::getStoreConfig('visilabs/catalog/cep'));
            foreach ($customParams as $param) {
                if ($param['attribute'] && $product->getResource()->getAttribute($param['attribute'])) {
                    $params['OM.pv' . $param['param']] = $product->getAttributeText($param['attribute']);
                }
            }

            $cache->save(
                json_encode($params),
                self::VISILABS_CACHE_PRODUCT_KEY . $product->getId(),
                array(self::VISILABS_CACHE_TAG),
                self::VISILABS_CACHE_LIFETIME
            );
        }

        $this->addVisilabsParams($params);

        return $this;
    }

    /**
     * Refresh Product Cache
     *
     * @param Varien_Event_Observer $observer
     * @return Numerno_Visilabs_Model_Observer
     */
    public function refreshProduct(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('visilabs')->isTaggingEnabled('catalog')) {
            return $this;
        }

        $event   = $observer->getEvent();
        $product = $event->getProduct();

        if ($product->hasDataChanges()) {
            Mage::app()->getCache()->remove(self::VISILABS_CACHE_PRODUCT_KEY . $product->getId());
        }

        return $this;
    }

    /**
     * Category View Event
     *
     * @param Varien_Event_Observer $observer
     * @return Numerno_Visilabs_Model_Observer
     */
    public function eventCategoryView(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('visilabs')->isTaggingEnabled('catalog')) {
            return $this;
        }

        $event    = $observer->getEvent();
        $category = $event->getCategory();
        $params   = array('OM.clist' => $category->getId());

        $this->addVisilabsParams($params);

        return $this;
    }

    /**
     * Add to Cart Event
     *
     * @return Numerno_Visilabs_Model_Observer
     */
    public function eventBasket()
    {
        if (!Mage::helper('visilabs')->isTaggingEnabled('cart')) {
            return $this;
        }

        $quote    = Mage::getModel('checkout/cart')->getQuote();
        $items    = $quote->getAllVisibleItems();
        $products = array();
        $totals   = array();
        foreach ($items as $item) {
            $pid = $item->getProductId();

            if (isset($products[$pid])) {
                $products[$pid] += $item->getQty();
                $totals[$pid]   += round($item->getRowTotalInclTax(), 2);
            } else {
                $products[$pid] = $item->getQty();
                $totals[$pid]   = round($item->getRowTotalInclTax(), 2);
            }
        }

        $params = array(
            'OM.pbid' => $quote->getId(),
            'OM.pb'   => implode(';', array_keys($products)),
            'OM.pu'   => implode(';', $products),
            'OM.ppr'  => str_ireplace('.', ',', implode(';', $totals))
        );

        //custom event parameters
        $customParams = unserialize(Mage::getStoreConfig('visilabs/cart/cep'));
        foreach ($customParams as $param) {
            if ($param['attribute'] && $quote->hasData($param['attribute'])) {
                $params['OM.cep' . $param['param']] = (string) $quote->getData($param['attribute']);
            }
        }

        $this->addVisilabsParams($params);

        return $this;
    }

    /**
     * Purchase Event
     *
     * @param Varien_Event_Observer $observer
     * @return Numerno_Visilabs_Model_Observer
     */
    public function eventPurchase(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('visilabs')->isTaggingEnabled('order')) {
            return $this;
        }

        $event    = $observer->getEvent();
        $order    = $event->getOrder();
        $items    = $order->getAllVisibleItems();
        $products = array();
        $totals   = array();

        foreach ($items as $item) {
            $pid = $item->getProductId();

            if (isset($products[$pid])) {
                $products[$pid] += $item->getQtyOrdered();
                $totals[$pid]   += round($item->getRowTotalInclTax(), 2);
            } else {
                $products[$pid] = $item->getQtyOrdered();
                $totals[$pid]   = round($item->getRowTotalInclTax(), 2);
            }
        }

        $params = array(
            'OM.tid' => $order->getIncrementId(),
            'OM.pp'   => implode(';', array_keys($products)),
            'OM.pu'   => implode(';', $products),
            'OM.ppr'  => str_ireplace('.', ',', implode(';', $totals))
        );

        //customer identifier
        switch ($this->_getCustomerIdentifier()) {
            case 'email':
                $params['OM.exVisitorID'] = $order->getCustomerEmail();
                break;
            default:
                $params['OM.exVisitorID'] = $order->getCustomerId();
        }

        //custom event parameters
        $customParams = unserialize(Mage::getStoreConfig('visilabs/order/cep'));
        foreach ($customParams as $param) {
            if ($param['attribute'] && $order->hasData($param['attribute'])) {
                $params['OM.cep' . $param['param']] = (string) $order->getData($param['attribute']);
            }
        }

        $this->addVisilabsParams($params);

        return $this;
    }

    /**
     * On Site Search Event
     *
     * @param Varien_Event_Observer $observer
     * @return Numerno_Visilabs_Model_Observer
     */
    public function eventOnSiteSearch()
    {
        if (!Mage::helper('visilabs')->isTaggingEnabled('search')) {
            return $this;
        }

        $params = array('OM.OSS' => Mage::helper('catalogsearch')->getQueryText());

        $this->addVisilabsParams($params);

        return $this;
    }

}
