<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author      Sylvain Rayé <support at diglin.com>
 * @category    Diglin
 * @package     Diglin_Zopim
 * @copyright   Copyright (c) 2011-2015 Diglin (http://www.diglin.com)
 */

namespace Diglin\Zopim\Block;

use Magento\Framework\View\Element\Template;

/**
 * Class Display
 * @package Diglin\Zopim\Block
 */
class Display extends \Magento\Framework\View\Element\Template
{
    /**
     * @var array
     */
    private $_options = [];

    protected $_chatHelper;

    /**
     * Display constructor.
     * @param Context $context
     * @param array $data
     */
    public function __construct(Context $context, array $data = [])
    {
        $this->_chatHelper = $context->getChatHelper();
        parent::__construct($context, $data);

//        $this->setCacheLifetime(86400);
    }

    /**
     * Get Cache Key Info
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return [
            'ZOPIM_CHAT',
            $this->_storeManager->getStore()->getCode(),
            $this->getTemplateFile(),
            'template' => $this->getTemplate(),
            // @todo get current customer id
//            Mage::helper('customer')->getCurrentCustomer()->getId()
        ];

//        return array(
//            'ZOPIM_CHAT',
//            $this->getNameInLayout(),
//            Mage::app()->getStore()->getId(),
//            Mage::helper('customer')->getCurrentCustomer()->getId()
//        );
    }

    /**
     * Set to force the button display
     *
     * @param bool $value
     * @return $this
     */
    public function setForceButtonDisplay($value = false)
    {
        $this->_options['force_button_display'] = (bool) $value;
        return $this;
    }

    /**
     * Set to force the bubble display (only API v1 or V2 with classic theme)
     *
     * @param bool $value
     * @return $this
     */
    public function setForceBubbleDisplay($value = false)
    {
        $this->_options['force_bubble_display'] = (bool) $value;
        return $this;
    }

    /**
     * get if we force the button display or not
     *
     * @return bool
     */
    public function getForceButtonDisplay()
    {
        return (isset($this->_options['force_button_display'])) ? $this->_options['force_button_display'] : false;
    }

    /**
     * get if we force the button display or not (only API v1 or V2 with classic theme)
     *
     * @return bool
     */
    public function getForceBubbleDisplay()
    {
        return (isset($this->_options['force_bubble_display'])) ? $this->_options['force_bubble_display'] : false;
    }

    /**
     * Get the list of greetings options
     *
     * @return string
     */
    public function getGreetingsOptions()
    {
        $offlineMessage = $this->escapeJsQuote($this->escapeHtml($this->_chatHelper->getOfflineMessage()));
        $onlineMessage = $this->escapeJsQuote($this->escapeHtml($this->_chatHelper->getOnlineMessage()));

        $data = array();
        (!empty($onlineMessage )) ? $data[] = "'online': '" . $onlineMessage  . "'" : null;
        (!empty($offlineMessage)) ? $data[] = "'offline': '" . $offlineMessage . "'" : null;

        if (count($data) > 0) {
            $data = implode(',',$data);
            return "\$zopim.livechat.setGreetings({" . $data . "});" . "\n";
        }
        return null;
    }

    /**
     * Get the language option
     *
     * @return null|string
     */
    public function getLanguage()
    {
        if ($this->_chatHelper->getLanguage() == 'auto') {
            return null;
        }

        if ($this->_chatHelper->getLanguage() == 'md') {
            return "\$zopim.livechat.setLanguage('" . substr(Mage::app()->getLocale()->getLocale(),0,2)."');" . "\n";
        }
        return "\$zopim.livechat.setLanguage('" . $this->_chatHelper->getLanguage() . "');" . "\n";
    }

    /**
     * Get the name to display
     *
     * @return null|string
     */
    public function getName()
    {
        if ($this->_chatHelper->allowName() && Mage::getSingleton('customer/session')->isLoggedIn()) {
            return "\$zopim.livechat.setName('" . $this->escapeJsQuote(Mage::getSingleton('customer/session')->getCustomer()->getName()) . "');" . "\n";
        }
        return null;
    }

    /**
     * Get the email to link
     *
     * @return null|string
     */
    public function getEmail()
    {
        if ($this->_chatHelper->allowEmail() && Mage::getSingleton('customer/session')->isLoggedIn()) {
            return  "\$zopim.livechat.setEmail('" . $this->escapeJsQuote(Mage::getSingleton('customer/session')->getCustomer()->getEmail()) . "');" . "\n";
        }
        return null;
    }

    /**
     * Disable or not sound notification
     *
     * @return string
     */
    public function getDisableSound()
    {
        if ($this->_chatHelper->getDisableSound()) {
            return "\$zopim.livechat.setDisableSound(true);" . "\n";
        }

        return "\$zopim.livechat.setDisableSound(false);" . "\n";
    }

    /**
     *
     *
     * @return string
     */
    public function getTheme()
    {
        $out = array();

        if (strlen($this->_chatHelper->getWindowTheme()) > 0) {
            $out[] = "\$zopim.livechat.theme.setTheme('" . $this->_chatHelper->getWindowTheme() . "')";
        }

        if (count($out) > 0) {
            return implode(';' . "\n", $out) . ';' . "\n";
        }

        return null;
    }

    /**
     * get the Bubble options
     *
     * @return string
     */
    public function getBubbleOptions()
    {
        $out = array();

        if ($this->_chatHelper->getWindowTheme() == 'simple') {
            return null;
        }

        if (strlen($this->_chatHelper->getBubbleTitle()) > 0) {
            $out[] = "\$zopim.livechat.bubble.setTitle('" . $this->_chatHelper->getBubbleTitle() . "')";
        }

        if (strlen($this->_chatHelper->getBubbleText()) > 0) {
            $out[] = "\$zopim.livechat.bubble.setText('" . $this->_chatHelper->getBubbleText() . "')";
        }

        if ($this->_chatHelper->getBubbleShow() == 'show' || $this->getForceBubbleDisplay()) {
            $out[] = "\$zopim.livechat.bubble.show()";
        } elseif ($this->_chatHelper->getBubbleShow() == 'hide') {
            $out[] = "\$zopim.livechat.bubble.hide()";
        } elseif ($this->_chatHelper->getBubbleShow() == 'reset') { // reset on each page reload
            $out[] = "\$zopim.livechat.bubble.reset()";
        }

        if (count($out) > 0) {
            return implode(';' . "\n", $out) . ';' . "\n";
        }

        return null;
    }

    /**
     * Get the options to define for the window
     *
     * @return string
     */
    public function getWindowOptions()
    {
        $out = array();

        if (strlen($this->_chatHelper->getWindowTitle()) > 0) {
            $out[] = "\$zopim.livechat.window.setTitle('" . $this->escapeJsQuote($this->_chatHelper->getWindowTitle()) . "')";
        }
        if (strlen($this->_chatHelper->getWindowSize()) > 0) {
            $out[] = "\$zopim.livechat.window.setSize('" . $this->_chatHelper->getWindowSize() . "')";
        }

        if (strlen($this->_chatHelper->getWindowOnShow())) {
            $out[] = "\$zopim.livechat.window.onShow('" . $this->_chatHelper->getWindowOnShow() . "')";
        }

        if (strlen($this->_chatHelper->getWindowOnHide())) {
            $out[] = "\$zopim.livechat.window.onHide('" . $this->_chatHelper->getWindowOnHide() . "')";
        }

        if (count($out) > 0) {
            return implode(';' . "\n", $out) . ';' . "\n";
        }

        return null;
    }

    /**
     * Get the options to define the button
     *
     * @return string
     */
    public function getButtonOptions()
    {
        $out = array();

        if (strlen($this->_chatHelper->getButtonPosition()) > 0) {
            $out[] = "\$zopim.livechat.button.setPosition('" . $this->_chatHelper->getButtonPosition() . "')";
            $out[] = "\$zopim.livechat.window.setPosition('" . $this->_chatHelper->getButtonPosition() . "')";
        }

        if (strlen($this->_chatHelper->getButtonPositionMobile()) > 0) {
            $out[] = "\$zopim.livechat.button.setPositionMobile('" . $this->_chatHelper->getButtonPositionMobile() . "')";
        }

        if ($this->_chatHelper->getButtonHideOffline()) {
            $out[] = "\$zopim.livechat.button.setHideWhenOffline(1)";
        }

        if (count($out) > 0) {
            return implode(';' . "\n", $out). ';' . "\n";
        }
        return null;
    }

    /**
     * Get the option for the department feature
     *
     * @return string
     */
    public function getDepartmentsOptions()
    {
        $out = array();

        if ($this->_chatHelper->getDepartmentsFilter()) {
            $departments = explode(',', $this->_chatHelper->getDepartmentsFilter());
            $out[] = "\$zopim.livechat.departments.filter('" . $this->escapeJsQuote(implode("','", $departments)) . "')";
        }

        if (count($out) > 0) {
            return implode(';' . "\n", $out). ';' . "\n";
        }
        return null;
    }

    /**
     * Get cookie law options
     *
     * @return string
     */
    public function getCookieLawOptions ()
    {
        $out = array();

        if ($this->_chatHelper->getCookieLawComply()) {
            $out [] = "\$zopim.livechat.cookieLaw.comply()";

            if ($this->_chatHelper->getCookieLawConsent()) {
                $out[] = "\$zopim.livechat.cookieLaw.setDefaultImplicitConsent()";
            }
        }

        if (count($out) > 0) {
            return implode(';' . "\n", $out). ';' . "\n";
        }
        return null;
    }

    /**
     * Get concierge options
     *
     * @return string
     */
    public function getConciergeOptions ()
    {
        $out = array();

        if ($this->_chatHelper->getWindowTheme() == 'classic') {
            return null;
        }

        if (strlen($this->_chatHelper->getConciergeAvatar()) > 0) {
            $out[] = "\$zopim.livechat.concierge.setAvatar('" . Mage::getBaseUrl('media') . 'chat/' . $this->_chatHelper->getConciergeAvatar() . "')";
        }

        if (strlen($this->_chatHelper->getConciergeName()) > 0) {
            $out[] = "\$zopim.livechat.concierge.setName('" . $this->escapeJsQuote($this->_chatHelper->getConciergeName()) . "')";
        }

        if (strlen($this->_chatHelper->getConciergeTitle()) > 0) {
            $out[] = "\$zopim.livechat.concierge.setTitle('" . $this->escapeJsQuote($this->_chatHelper->getConciergeTitle()) . "')";
        }

        if (!empty($out)) {
            return implode(';' . "\n", $out). ';' . "\n";
        }
        return null;
    }

    /**
     * Get the Badge options
     *
     * @return string
     */
    public function getBadgeOptions()
    {
        if ($this->_chatHelper->getWindowTheme() != 'simple') {
            return null;
        }
        $out = array();

        if (strlen($this->_chatHelper->getBadgeLayout()) > 0) {
            $out[] = "\$zopim.livechat.badge.setLayout('" . $this->_chatHelper->getBadgeLayout() . "')";
        }

        if (strlen($this->_chatHelper->getBadgeText()) > 0) {
            $out[] = "\$zopim.livechat.badge.setText('" . $this->escapeJsQuote($this->_chatHelper->getBadgeText()) . "')";
        }

        if (strlen($this->_chatHelper->getBadgeImage()) > 0) {
            $out[] = "\$zopim.livechat.badge.setImage('" . Mage::getBaseUrl('media') . 'chat/' . $this->_chatHelper->getBadgeImage() . "')";
        }

        if (!$this->_chatHelper->getButtonHideOffline()) {
            if ($this->_chatHelper->getBadgeShow() == 'hide') {
                $out[] = "\$zopim.livechat.badge.hide()";
            } else {
                $out[] = "\$zopim.livechat.badge.show()";
            }
        }

        if (!empty($out)) {
            return implode(';' . "\n", $out). ';' . "\n";
        }
        return null;
    }

    /**
     * Get the color options for window, bubble and the theme
     *
     * @return string
     */
    public function getColor()
    {
        $out = array();

        if (strlen($this->_chatHelper->getThemePrimaryColor()) > 0) {
            $out[] = "\$zopim.livechat.theme.setColor('#" . ltrim($this->_chatHelper->getThemePrimaryColor(), '#') . "', 'primary')";
        }

        // Specify Badge Color
        if ($this->_chatHelper->getWindowTheme() == 'simple' && $this->_chatHelper->getBadgeColorPrimary()) {
            switch ($this->_chatHelper->getBadgeColorPrimary()){
                case 'badge_color_primary':
                    $color = $this->_chatHelper->getThemePrimaryColor();
                    break;
                case 'badge_color_customized':
                default:
                    $color = $this->_chatHelper->getBadgeColor();
                    if (empty($color)) {
                        $color = $this->_chatHelper->getThemePrimaryColor();
                    }
                    break;

            }
            if (!empty($color)) {
                $out[] = "\$zopim.livechat.theme.setColor('#" . ltrim($color, '#') . "', 'badge')";
            }
        }

        // Specify Bubble Color
        if ($this->_chatHelper->getWindowTheme() == 'classic' && $this->_chatHelper->getBubbleColorPrimary()) {
            switch ($this->_chatHelper->getBubbleColorPrimary()) {
                case 'bubble_color_primary':
                    $color = $this->_chatHelper->getThemePrimaryColor();
                    break;
                case 'bubble_color_customized':
                default:
                    $color = $this->_chatHelper->getBubbleColor();
                    if (empty($color)) {
                        $color = $this->_chatHelper->getThemePrimaryColor();
                    }
                    break;
            }
            if (!empty($color)) {
                $out[] = "\$zopim.livechat.theme.setColor('#" . ltrim($color, '#') . "', 'bubble')";
            }
        }

        if (count($out) > 0) {
            $out[] = "\$zopim.livechat.theme.reload()";
        }

        if (!empty($out)) {
            return implode(';' . "\n", $out). ';' . "\n";
        }
        return null;
    }

    /**
     * Generate the Zopim output
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_chatHelper->getEnabled()) {

            $zopimOptions = '';

            if ($this->_chatHelper->getConfigType() == 'adv') {
                $zopimOptions .= $this->getCookieLawOptions(); // Must be in first place
                $zopimOptions .= $this->getDisableSound();
                $zopimOptions .= $this->getTheme(); // should be set after setColor/setColors js methods but works better here

                $zopimOptions .= $this->getConciergeOptions();
                $zopimOptions .= $this->getBadgeOptions();

                $zopimOptions .= $this->getWindowOptions();
                $zopimOptions .= $this->getGreetingsOptions();
                $zopimOptions .= $this->getButtonOptions();
                $zopimOptions .= $this->getBubbleOptions();
                $zopimOptions .= $this->getColor();
            }

            if (strlen($this->getName()) > 0) {
                $zopimOptions .= $this->getName();
            }
            if (strlen($this->getEmail()) > 0) {
                $zopimOptions .= $this->getEmail();
            }
            if (strlen($this->getLanguage()) > 0) {
                $zopimOptions .= $this->getLanguage();
            }

            $zopimOptions .= $this->getDepartmentsOptions();

            /* @var $block Mage_Core_Block_Template */
            $block = $this->getLayout()->createBlock(
                'core/template',
                'zopim_chat',
                array(
                    'template' => 'chat/widget.phtml',
                    'key' => $this->_chatHelper->getKey(),
                    'zopim_options' => $zopimOptions
                )
            );

            return $block->toHtml();
        }

        return null;
    }
}