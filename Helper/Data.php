<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author      Sylvain Rayé <support at diglin.com>
 * @category    Diglin
 * @package     Diglin_Zopim
 * @copyright   Copyright (c) 2011-2016 Diglin (http://www.diglin.com)
 */

namespace Diglin\Zopim\Helper;

use Magento\Framework\HTTP\Client\Curl;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * @package Diglin\Zopim\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const ZOPIM_BASE_URL = 'https://www.zopim.com/';
    const ZOPIM_LOGIN_URL = 'https://www.zopim.com/plugins/login';
    const ZOPIM_SIGNUP_URL = 'https://www.zopim.com/plugins/createTrialAccount';
    const ZOPIM_GETACCOUNTDETAILS_URL = 'https://www.zopim.com/plugins/getAccountDetails';
    const ZOPIM_DASHBOARD_URL = 'https://dashboard.zopim.com';

    const CFG_CHATCONFIG_ENABLED         = 'zopim/chatconfig/enabled';
    const CFG_CHATCONFIG_ALLOW_NAME      = 'zopim/chatconfig/allow_name';
    const CFG_CHATCONFIG_ALLOW_EMAIL     = 'zopim/chatconfig/allow_email';
    const CFG_CHATCONFIG_LANGUAGE        = 'zopim/chatconfig/language';
    const CFG_CHATCONFIG_KEY             = 'zopim/chatconfig/key';
    const CFG_CHATCONFIG_DISABLE_SOUND   = 'zopim/widgetconfig/disable_sound';
    const CFG_CHATCONFIG_ONLINE_MESSAGE  = 'zopim/widgetconfig/online_message';
    const CFG_CHATCONFIG_OFFLINE_MESSAGE = 'zopim/widgetconfig/offline_message';

    const CFG_WIDGETCONFIG_TYPE            = 'zopim/widgetconfig/type_config';
    const CFG_WIDGETCONFIG_WINDOW_THEME    = 'zopim/widgetconfig/window_theme';
    const CFG_WIDGETCONFIG_WINDOW_TITLE    = 'zopim/widgetconfig/window_title';
    const CFG_WIDGETCONFIG_WINDOW_SIZE     = 'zopim/widgetconfig/window_size';
    const CFG_WIDGETCONFIG_WINDOW_ONSHOW   = 'zopim/widgetconfig/window_onshow';
    const CFG_WIDGETCONFIG_WINDOW_ONHIDE   = 'zopim/widgetconfig/window_onhide';
    const CFG_WIDGETCONFIG_WINDOW_POSITION = 'zopim/widgetconfig/button_position';
    const CFG_WIDGETCONFIG_WINDOW_POSITION_MOBILE = 'zopim/widgetconfig/button_position_mobile';

    const CFG_WIDGETCONFIG_BUBBLE_SHOW          = 'zopim/widgetconfig/bubble_show';
    const CFG_WIDGETCONFIG_BUBBLE_TITLE         = 'zopim/widgetconfig/bubble_title';
    const CFG_WIDGETCONFIG_BUBBLE_TEXT          = 'zopim/widgetconfig/bubble_text';
    const CFG_WIDGETCONFIG_BUBBLE_COLOR_PRIMARY = 'zopim/widgetconfig/theme_bubble_color_primary';
    const CFG_WIDGETCONFIG_BUBBLE_COLOR         = 'zopim/widgetconfig/theme_bubble_color';
    const CFG_WIDGETCONFIG_BUTTON_HIDE_OFFLINE  = 'zopim/widgetconfig/button_hide_offline';

    const CFG_DEPARTMENTS_FILTER            = 'zopim/departments/filter';

    const CFG_WIDGETCONFIG_COOKIE_COMPLY    = 'zopim/widgetconfig/cookielaw_comply';
    const CFG_WIDGETCONFIG_COOKIE_CONSENT   = 'zopim/widgetconfig/cookielaw_consent';
    const CFG_WIDGETCONFIG_CONCIERGE_AVATAR = 'zopim/widgetconfig/concierge_avatar';
    const CFG_WIDGETCONFIG_CONCIERGE_NAME   = 'zopim/widgetconfig/concierge_name';
    const CFG_WIDGETCONFIG_CONCIERGE_TITLE  = 'zopim/widgetconfig/concierge_title';

    const CFG_WIDGETCONFIG_BADGE_SHOW       = 'zopim/widgetconfig/badge_show';
    const CFG_WIDGETCONFIG_BADGE_LAYOUT     = 'zopim/widgetconfig/badge_layout';
    const CFG_WIDGETCONFIG_BADGE_IMAGE      = 'zopim/widgetconfig/badge_image';
    const CFG_WIDGETCONFIG_BADGE_TEXT       = 'zopim/widgetconfig/badge_text';
    const CFG_WIDGETCONFIG_BADGE_COLOR_PRIMARY       = 'zopim/widgetconfig/theme_badge_color_primary';
    const CFG_WIDGETCONFIG_BADGE_COLOR      = 'zopim/widgetconfig/theme_badge_color';

    const CFG_WIDGETCONFIG_THEME_COLOR_PRIMARY       = 'zopim/widgetconfig/theme_primary_color';

    const MEDIA_PATH = 'chat';

    /**
     * @return mixed|string
     */
    public function getCurrentPageURL()
    {
        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }

        return preg_replace("/\?.*$/", "", $pageURL);
    }

    /**
     * @param string $url
     * @param array $_data
     * @return mixed
     */
    public function doPostRequest($url, $_data)
    {
        $data = array();
        while (list($n, $v) = each($_data)) {
            $data[] = urlencode($n) . "=" . urlencode($v);
        }
        $data = implode('&', $data);

        $curl = new Curl();
        $curl->setOption(CURLOPT_RETURNTRANSFER, true);

        try {
            $curl->post($url, $data);
            $response = $curl->getBody();
        } catch (\Exception $e) {
            $this->_logger->log(\Zend\Log\Logger::ERR, 'Curl Error for Zopim Login - ' . $e->getMessage());
            $response = '';
        }

        return $response;
    }

    /**
     * @param array $url
     * @return array
     */
    public function getCurlOptions($url)
    {
        $curl = new Curl();
        $curl->setTimeout(5);
        $curl->get($url);
        $response = $curl->getBody();
        if ($response === false) {
            return false;
        }
        $response = preg_split('/^\r?$/m', $response, 2);
        $response = trim($response[1]);
        $options = explode("\n", $response);

        return $options;
    }

    /**
     * @return bool
     */
    public function allowName()
    {
        return $this->scopeConfig->isSetFlag(self::CFG_CHATCONFIG_ALLOW_NAME, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function allowEmail()
    {
        return $this->scopeConfig->isSetFlag(self::CFG_CHATCONFIG_ALLOW_EMAIL, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function getEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::CFG_CHATCONFIG_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->scopeConfig->getValue(self::CFG_CHATCONFIG_LANGUAGE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->scopeConfig->getValue(self::CFG_CHATCONFIG_KEY, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getDisableSound()
    {
        return $this->scopeConfig->getValue(self::CFG_CHATCONFIG_DISABLE_SOUND, ScopeInterface::SCOPE_STORE);
    }

    /* Greetings Config */

    /**
     * @return mixed
     */
    public function getOnlineMessage()
    {
        return $this->scopeConfig->getValue(self::CFG_CHATCONFIG_ONLINE_MESSAGE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getOfflineMessage()
    {
        return $this->scopeConfig->getValue(self::CFG_CHATCONFIG_OFFLINE_MESSAGE, ScopeInterface::SCOPE_STORE);
    }

    /* Widget Config */

    /**
     * If the shop owner use the dashboard.zopim.com or Magento config
     *
     * @return mixed
     */
    public function getConfigType()
    {
        return $this->scopeConfig->getValue(self::CFG_WIDGETCONFIG_TYPE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Simple or Classic Theme
     *
     * @return mixed
     */
    public function getWindowTheme()
    {
        return $this->scopeConfig->getValue(self::CFG_WIDGETCONFIG_WINDOW_THEME, ScopeInterface::SCOPE_STORE);
    }

    /* Bubble Config */

    /**
     * @return mixed
     */
    public function getBubbleShow()
    {
        return $this->scopeConfig->getValue(self::CFG_WIDGETCONFIG_BUBBLE_SHOW, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getBubbleTitle()
    {
        return $this->scopeConfig->getValue(self::CFG_WIDGETCONFIG_BUBBLE_TITLE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getBubbleText()
    {
        return $this->scopeConfig->getValue(self::CFG_WIDGETCONFIG_BUBBLE_TEXT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getBubbleColorPrimary()
    {
        return $this->scopeConfig->getValue(self::CFG_WIDGETCONFIG_BUBBLE_COLOR_PRIMARY, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getBubbleColor()
    {
        return $this->scopeConfig->getValue(self::CFG_WIDGETCONFIG_BUBBLE_COLOR, ScopeInterface::SCOPE_STORE);
    }

    /* Window Config */

    /**
     * @return mixed
     */
    public function getWindowTitle()
    {
        return $this->scopeConfig->getValue(self::CFG_WIDGETCONFIG_WINDOW_TITLE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @deprecated
     * @return mixed
     */
    public function getWindowColor()
    {
        return $this->getThemePrimaryColor();
    }

    /**
     * @return mixed
     */
    public function getWindowSize()
    {
        return $this->scopeConfig->getValue(self::CFG_WIDGETCONFIG_WINDOW_SIZE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getWindowOnShow()
    {
        return $this->scopeConfig->getValue(self::CFG_WIDGETCONFIG_WINDOW_ONSHOW, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getWindowOnHide()
    {
        return $this->scopeConfig->getValue(self::CFG_WIDGETCONFIG_WINDOW_ONHIDE, ScopeInterface::SCOPE_STORE);
    }

    /* Button Config */

    /**
     * @return mixed
     */
    public function getButtonPosition()
    {
        return $this->scopeConfig->getValue(self::CFG_WIDGETCONFIG_WINDOW_POSITION, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getButtonPositionMobile()
    {
        return $this->scopeConfig->getValue(self::CFG_WIDGETCONFIG_WINDOW_POSITION_MOBILE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getButtonHideOffline()
    {
        return $this->scopeConfig->getValue(self::CFG_WIDGETCONFIG_BUTTON_HIDE_OFFLINE, ScopeInterface::SCOPE_STORE);
    }

    /* Department Config */

    /**
     * @return mixed
     */
    public function getDepartmentsFilter()
    {
        return $this->scopeConfig->getValue(self::CFG_DEPARTMENTS_FILTER, ScopeInterface::SCOPE_STORE);
    }

    /* Cookie Law Config */

    /**
     * @return bool
     */
    public function getCookieLawComply()
    {
        return $this->scopeConfig->isSetFlag(self::CFG_WIDGETCONFIG_COOKIE_COMPLY, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function getCookieLawConsent()
    {
        return $this->scopeConfig->isSetFlag(self::CFG_WIDGETCONFIG_COOKIE_CONSENT, ScopeInterface::SCOPE_STORE);
    }

    /* Concierge Config */

    /**
     * @return mixed
     */
    public function getConciergeAvatar()
    {
        return $this->scopeConfig->getValue(self::CFG_WIDGETCONFIG_CONCIERGE_AVATAR, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getConciergeName()
    {
        return $this->scopeConfig->getValue(self::CFG_WIDGETCONFIG_CONCIERGE_NAME, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getConciergeTitle()
    {
        return $this->scopeConfig->getValue(self::CFG_WIDGETCONFIG_CONCIERGE_TITLE, ScopeInterface::SCOPE_STORE);
    }

    /* Badge Config */

    /**
     * @return mixed
     */
    public function getBadgeShow()
    {
        return $this->scopeConfig->getValue(self::CFG_WIDGETCONFIG_BADGE_SHOW, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getBadgeLayout()
    {
        return $this->scopeConfig->getValue(self::CFG_WIDGETCONFIG_BADGE_LAYOUT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getBadgeImage()
    {
        return $this->scopeConfig->getValue(self::CFG_WIDGETCONFIG_BADGE_IMAGE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getBadgeText()
    {
        return $this->scopeConfig->getValue(self::CFG_WIDGETCONFIG_BADGE_TEXT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getBadgeColorPrimary()
    {
        return $this->scopeConfig->getValue(self::CFG_WIDGETCONFIG_BADGE_COLOR_PRIMARY, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getBadgeColor()
    {
        return $this->scopeConfig->getValue(self::CFG_WIDGETCONFIG_BADGE_COLOR, ScopeInterface::SCOPE_STORE);
    }

    /* Theme Config */

    /**
     * @return mixed
     */
    public function getThemePrimaryColor()
    {
        return $this->scopeConfig->getValue(self::CFG_WIDGETCONFIG_THEME_COLOR_PRIMARY, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getBaseMediaPath()
    {
        return self::MEDIA_PATH;
    }
}