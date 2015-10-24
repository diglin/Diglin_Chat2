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
 * Class Layout
 * @package Diglin\Zopim\Model\Config\Source
 */
class Layout implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'image_right', 'label' => __('Image Right')],
            ['value' => 'image_left', 'label' => __('Image Left')],
            ['value' => 'image_only', 'label' => __('Image Only')],
            ['value' => 'text_only', 'label' => __('Text Only')],
        ];
    }
}
