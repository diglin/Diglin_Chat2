<?php
/**
 * Diglin
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Diglin
 * @package     Diglin_Chat
 * @copyright   Copyright (c) 2011-2015 Diglin (http://www.diglin.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Diglin\Zopim\Model\Config\Source;

/**
 * Class Simpleadvanced
 * @package Diglin\Zopim\Model\Config\Source
 */
class Simpleadvanced implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            // we use spl as value to prevent conflict with the simple theme
            ['value' => 'spl', 'label' => __('Simple')],
            ['value' => 'adv', 'label' => __('Advanced')],
        ];
    }
}
