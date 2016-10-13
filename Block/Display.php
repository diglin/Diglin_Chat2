<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author      Sylvain Rayé <support at diglin.com>
 * @category    Diglin
 * @package     Diglin_Zopim
 * @copyright   Copyright (c) 2011-2016 Diglin (http://www.diglin.com)
 */

namespace Diglin\Zopim\Block;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\View\Element\Template;

/**
 * Class Display
 * @package Diglin\Zopim\Block
 *
 * @method string getKey()
 * @method string getZopimOptions()
 */
class Display extends Template
{
    /**
     * @var array
     */
    private $options = [];

    /**
     * @var \Diglin\Zopim\Helper\Data
     */
    protected $chatHelper;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $localeResolver;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * Core file storage database
     *
     * @var \Magento\MediaStorage\Helper\File\Storage\Database
     */
    protected $coreFileStorageDatabase = null;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var bool
     */
    protected $_isScopePrivate = true;

    /**
     * Display constructor.
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase,
        array $data = []
    ) {
        $this->chatHelper = $context->getChatHelper();
        $this->localeResolver = $context->getLocaleResolver();
        $this->customerSession = $customerSession;
        $this->filesystem = $context->getFilesystem();
        $this->coreFileStorageDatabase = $coreFileStorageDatabase;

        parent::__construct($context, $data);

//        $this->setCacheLifetime(86400);
    }

    /**
     * First check this file on FS
     * If it doesn't exist - try to download it from DB
     *
     * @param string $filename
     * @return bool
     */
    protected function _fileExists($filename)
    {
        if ($this->mediaDirectory->isFile($filename)) {
            return true;
        } else {
            return $this->coreFileStorageDatabase->saveFileToFilesystem(
                $this->mediaDirectory->getAbsolutePath($filename)
            );
        }
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
            'template' => $this->getTemplate(),
        ];
    }

    /**
     * Set to force the button display
     *
     * @param bool $value
     * @return $this
     */
    public function setForceButtonDisplay($value = false)
    {
        $this->options['force_button_display'] = (bool)$value;

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
        $this->options['force_bubble_display'] = (bool)$value;

        return $this;
    }

    /**
     * get if we force the button display or not
     *
     * @return bool
     */
    public function getForceButtonDisplay()
    {
        return (isset($this->options['force_button_display'])) ? $this->options['force_button_display'] : false;
    }

    /**
     * get if we force the button display or not (only API v1 or V2 with classic theme)
     *
     * @return bool
     */
    public function getForceBubbleDisplay()
    {
        return (isset($this->options['force_bubble_display'])) ? $this->options['force_bubble_display'] : false;
    }

    /**
     * Get the list of greetings options
     *
     * @return string
     */
    public function getGreetingsOptions()
    {
        $offlineMessage = $this->escapeJsQuote($this->escapeHtml($this->chatHelper->getOfflineMessage()));
        $onlineMessage = $this->escapeJsQuote($this->escapeHtml($this->chatHelper->getOnlineMessage()));

        $data = array();
        (!empty($onlineMessage)) ? $data[] = "'online': '" . $onlineMessage . "'" : null;
        (!empty($offlineMessage)) ? $data[] = "'offline': '" . $offlineMessage . "'" : null;

        if (count($data) > 0) {
            $data = implode(',', $data);

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
        if ($this->chatHelper->getLanguage() == 'auto') {
            return null;
        }

        if ($this->chatHelper->getLanguage() == 'md') {
            return "\$zopim.livechat.setLanguage('" . substr($this->localeResolver->getLocale(), 0, 2) . "');" . "\n";
        }

        return "\$zopim.livechat.setLanguage('" . $this->chatHelper->getLanguage() . "');" . "\n";
    }

    /**
     * Get the name to display
     *
     * @return null|string
     */
    public function getName()
    {
        if ($this->chatHelper->allowName() && $this->customerSession->isLoggedIn()) {
            return "\$zopim.livechat.setName('" . $this->escapeJsQuote($this->customerSession->getCustomer()->getName()) . "');" . "\n";
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
        if ($this->chatHelper->allowEmail() && $this->customerSession->isLoggedIn()) {
            return "\$zopim.livechat.setEmail('" . $this->escapeJsQuote($this->customerSession->getCustomer()->getEmail()) . "');" . "\n";
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
        if ($this->chatHelper->getDisableSound()) {
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

        if (strlen($this->chatHelper->getWindowTheme()) > 0) {
            $out[] = "\$zopim.livechat.theme.setTheme('" . $this->chatHelper->getWindowTheme() . "')";
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

        if ($this->chatHelper->getWindowTheme() == 'simple') {
            return null;
        }

        if (strlen($this->chatHelper->getBubbleTitle()) > 0) {
            $out[] = "\$zopim.livechat.bubble.setTitle('" . $this->chatHelper->getBubbleTitle() . "')";
        }

        if (strlen($this->chatHelper->getBubbleText()) > 0) {
            $out[] = "\$zopim.livechat.bubble.setText('" . $this->chatHelper->getBubbleText() . "')";
        }

        if ($this->chatHelper->getBubbleShow() == 'show' || $this->getForceBubbleDisplay()) {
            $out[] = "\$zopim.livechat.bubble.show()";
        } elseif ($this->chatHelper->getBubbleShow() == 'hide') {
            $out[] = "\$zopim.livechat.bubble.hide()";
        } elseif ($this->chatHelper->getBubbleShow() == 'reset') { // reset on each page reload
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

        if (strlen($this->chatHelper->getWindowTitle()) > 0) {
            $out[] = "\$zopim.livechat.window.setTitle('" . $this->escapeJsQuote($this->chatHelper->getWindowTitle()) . "')";
        }
        if (strlen($this->chatHelper->getWindowSize()) > 0) {
            $out[] = "\$zopim.livechat.window.setSize('" . $this->chatHelper->getWindowSize() . "')";
        }

        if (strlen($this->chatHelper->getWindowOnShow())) {
            $out[] = "\$zopim.livechat.window.onShow('" . $this->chatHelper->getWindowOnShow() . "')";
        }

        if (strlen($this->chatHelper->getWindowOnHide())) {
            $out[] = "\$zopim.livechat.window.onHide('" . $this->chatHelper->getWindowOnHide() . "')";
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

        if (strlen($this->chatHelper->getButtonPosition()) > 0) {
            $out[] = "\$zopim.livechat.button.setPosition('" . $this->chatHelper->getButtonPosition() . "')";
            $out[] = "\$zopim.livechat.window.setPosition('" . $this->chatHelper->getButtonPosition() . "')";
        }

        if (strlen($this->chatHelper->getButtonPositionMobile()) > 0) {
            $out[] = "\$zopim.livechat.button.setPositionMobile('" . $this->chatHelper->getButtonPositionMobile() . "')";
        }

        if ($this->chatHelper->getButtonHideOffline()) {
            $out[] = "\$zopim.livechat.button.setHideWhenOffline(1)";
        }

        if (count($out) > 0) {
            return implode(';' . "\n", $out) . ';' . "\n";
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

        if ($this->chatHelper->getDepartmentsFilter()) {
            $departments = explode(',', $this->chatHelper->getDepartmentsFilter());
            $out[] = "\$zopim.livechat.departments.filter('" . $this->escapeJsQuote(implode("','", $departments)) . "')";
        }

        if (count($out) > 0) {
            return implode(';' . "\n", $out) . ';' . "\n";
        }

        return null;
    }

    /**
     * Get cookie law options
     *
     * @return string
     */
    public function getCookieLawOptions()
    {
        $out = array();

        if ($this->chatHelper->getCookieLawComply()) {
            $out [] = "\$zopim.livechat.cookieLaw.comply()";

            if ($this->chatHelper->getCookieLawConsent()) {
                $out[] = "\$zopim.livechat.cookieLaw.setDefaultImplicitConsent()";
            }
        }

        if (count($out) > 0) {
            return implode(';' . "\n", $out) . ';' . "\n";
        }

        return null;
    }

    /**
     * @return string
     */
    public function getImageUrl($fileName = '')
    {
        if (strpos($fileName, 'http') === false) {
            $uploadDir = $this->chatHelper->getBaseMediaPath();
            $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
            if ($mediaDirectory->isFile($uploadDir . '/' . $fileName)) {
                return $this->_storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . $uploadDir . '/' . $fileName;
            }
        } elseif (!empty($fileName) && strpos($fileName, 'http') !== false) {
            return $fileName;
        }

        return false;
    }

    /**
     * Get concierge options
     *
     * @return string
     */
    public function getConciergeOptions()
    {
        $out = array();

        if ($this->chatHelper->getWindowTheme() == 'classic') {
            return null;
        }

        $conciergeAvatarImage = $this->getImageUrl($this->chatHelper->getConciergeAvatar());
        if ($conciergeAvatarImage) {
            $out[] = "\$zopim.livechat.concierge.setAvatar('" . $conciergeAvatarImage . "')";
        }

        if (strlen($this->chatHelper->getConciergeName()) > 0) {
            $out[] = "\$zopim.livechat.concierge.setName('" . $this->escapeJsQuote($this->chatHelper->getConciergeName()) . "')";
        }

        if (strlen($this->chatHelper->getConciergeTitle()) > 0) {
            $out[] = "\$zopim.livechat.concierge.setTitle('" . $this->escapeJsQuote($this->chatHelper->getConciergeTitle()) . "')";
        }

        if (!empty($out)) {
            return implode(';' . "\n", $out) . ';' . "\n";
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
        if ($this->chatHelper->getWindowTheme() != 'simple') {
            return null;
        }
        $out = array();

        if (strlen($this->chatHelper->getBadgeLayout()) > 0) {
            $out[] = "\$zopim.livechat.badge.setLayout('" . $this->chatHelper->getBadgeLayout() . "')";
        }

        if (strlen($this->chatHelper->getBadgeText()) > 0) {
            $out[] = "\$zopim.livechat.badge.setText('" . $this->escapeJsQuote($this->chatHelper->getBadgeText()) . "')";
        }

        $imageBadge = $this->getImageUrl($this->chatHelper->getBadgeImage());
        if ($imageBadge) {
            $out[] = "\$zopim.livechat.badge.setImage('" . $imageBadge . "')";
        }

        if (!$this->chatHelper->getButtonHideOffline()) {
            if ($this->chatHelper->getBadgeShow() == 'hide') {
                $out[] = "\$zopim.livechat.badge.hide()";
            } else {
                $out[] = "\$zopim.livechat.badge.show()";
            }
        }

        if (!empty($out)) {
            return implode(';' . "\n", $out) . ';' . "\n";
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

        if (strlen($this->chatHelper->getThemePrimaryColor()) > 0) {
            $out[] = "\$zopim.livechat.theme.setColor('#" . ltrim($this->chatHelper->getThemePrimaryColor(), '#') . "', 'primary')";
        }

        // Specify Badge Color
        if ($this->chatHelper->getWindowTheme() == 'simple' && $this->chatHelper->getBadgeColorPrimary()) {
            switch ($this->chatHelper->getBadgeColorPrimary()) {
                case 'badge_color_primary':
                    $color = $this->chatHelper->getThemePrimaryColor();
                    break;
                case 'badge_color_customized':
                default:
                    $color = $this->chatHelper->getBadgeColor();
                    if (empty($color)) {
                        $color = $this->chatHelper->getThemePrimaryColor();
                    }
                    break;

            }
            if (!empty($color)) {
                $out[] = "\$zopim.livechat.theme.setColor('#" . ltrim($color, '#') . "', 'badge')";
            }
        }

        // Specify Bubble Color
        if ($this->chatHelper->getWindowTheme() == 'classic' && $this->chatHelper->getBubbleColorPrimary()) {
            switch ($this->chatHelper->getBubbleColorPrimary()) {
                case 'bubble_color_primary':
                    $color = $this->chatHelper->getThemePrimaryColor();
                    break;
                case 'bubble_color_customized':
                default:
                    $color = $this->chatHelper->getBubbleColor();
                    if (empty($color)) {
                        $color = $this->chatHelper->getThemePrimaryColor();
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
            return implode(';' . "\n", $out) . ';' . "\n";
        }

        return null;
    }

    public function getOptions()
    {
        $zopimOptions = '';

        if ($this->chatHelper->getConfigType() == 'adv') {
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

        return $zopimOptions;
    }

    /**
     * Generate the Zopim output
     *
     * @return string
     */
    public function _toHtml()
    {
        if ($this->chatHelper->getEnabled()) {

            $this
                ->setTemplate('chat/widget.phtml')
                ->setKey($this->chatHelper->getKey())
                ->setZopimOptions($this->getOptions());

            return parent::_toHtml();
        }

        return '';
    }
}