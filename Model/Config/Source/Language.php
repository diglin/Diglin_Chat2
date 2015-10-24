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
 * Class Language
 * @package Diglin\Zopim\Model\Config\Source
 */
class Language implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     * @see https://zopim.zendesk.com/entries/23886593-Changing-Widget-language-using-API
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'auto', 'label' => __('- Auto Detect -')],
            ['value' => 'md', 'label' => __('- Magento Locale Detection -')],
            ['value' => 'ar', 'label' => __("Arabic")],
            ['value' => 'bg', 'label' => __("Bulgarian")],
            ['value' => 'cs', 'label' => __("Czech")],
            ['value' => 'da', 'label' => __("Danish")],
            ['value' => 'de', 'label' => __("German")],
            ['value' => 'en', 'label' => __("English")],
            ['value' => 'es', 'label' => __("Spanish; Castilian")],
            ['value' => 'fa', 'label' => __("Persian")],
            ['value' => 'fo', 'label' => __("Faroese")],
            ['value' => 'fr', 'label' => __("French")],
            ['value' => 'he', 'label' => __("Hebrew")],
            ['value' => 'hr', 'label' => __("Croatian")],
            ['value' => 'id', 'label' => __("Indonesian")],
            ['value' => 'it', 'label' => __("Italian")],
            ['value' => 'ja', 'label' => __("Japanese")],
            ['value' => 'ko', 'label' => __("Korean")],
            ['value' => 'ms', 'label' => __("Malay")],
            ['value' => 'nb', 'label' => __("Norwegian Bokmal")],
            ['value' => 'nl', 'label' => __("Dutch; Flemish")],
            ['value' => 'pl', 'label' => __("Polish")],
            ['value' => 'pt', 'label' => __("Portuguese")],
            ['value' => 'ru', 'label' => __("Russian")],
            ['value' => 'sk', 'label' => __("Slovak")],
            ['value' => 'sl', 'label' => __("Slovenian")],
            ['value' => 'sv', 'label' => __("Swedish")],
            ['value' => 'th', 'label' => __("Thai")],
            ['value' => 'tr', 'label' => __("Turkish")],
            ['value' => 'ur', 'label' => __("Urdu")],
            ['value' => 'vi', 'label' => __("Vietnamese")],
            ['value' => 'zh_CN', 'label' => __("Chinese (China)")],
        ];
    }
}
