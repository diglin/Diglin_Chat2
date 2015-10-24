<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author      Sylvain Rayé <support at diglin.com>
 * @category    Diglin
 * @package     Indianershop_Base
 * @copyright   Copyright (c) 2011-2015 Diglin (http://www.diglin.com)
 */

namespace Diglin\Zopim\Block\Config\Source;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Heading extends \Magento\Config\Block\System\Config\Form\Field
{
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setValue($this->_cache->load('admin_notifications_lastcheck'));
        $format = $this->_localeDate->getDateTimeFormat(
            \IntlDateFormatter::MEDIUM
        );
        return \IntlDateFormatter::formatObject($this->_localeDate->date(intval($element->getValue())), $format);
    }
}