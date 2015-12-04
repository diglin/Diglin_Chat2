<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author      Sylvain Rayé <support at diglin.com>
 * @category    Diglin
 * @package     Diglin_Zopim
 * @copyright   Copyright (c) 2011-2015 Diglin (http://www.diglin.com)
 */

namespace Diglin\Zopim\Helper;

use Magento\Framework\HTTP\Client\Curl;

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
        return $this->scopeConfig->isSetFlag('chat/chatconfig/allow_name');
    }

    /**
     * @return bool
     */
    public function allowEmail()
    {
        return $this->scopeConfig->isSetFlag('chat/chatconfig/allow_email');
    }

    /**
     * @return bool
     */
    public function getEnabled()
    {
        return $this->scopeConfig->isSetFlag('chat/chatconfig/enabled');
    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->scopeConfig->getValue('chat/chatconfig/language');
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->scopeConfig->getValue('chat/chatconfig/key');
    }

    /**
     * @return mixed
     */
    public function getDisableSound()
    {
        return $this->scopeConfig->getValue('chat/widgetconfig/disable_sound');
    }

    /* Greetings Config */

    /**
     * @return mixed
     */
    public function getOnlineMessage()
    {
        return $this->scopeConfig->getValue('chat/widgetconfig/online_message');
    }

    /**
     * @return mixed
     */
    public function getOfflineMessage()
    {
        return $this->scopeConfig->getValue('chat/widgetconfig/offline_message');
    }

    /* Widget Config */

    /**
     * If the shop owner use the dashboard.zopim.com or Magento config
     *
     * @return mixed
     */
    public function getConfigType()
    {
        return $this->scopeConfig->getValue('chat/widgetconfig/type_config');
    }

    /**
     * Simple or Classic Theme
     *
     * @return mixed
     */
    public function getWindowTheme()
    {
        return $this->scopeConfig->getValue('chat/widgetconfig/window_theme');
    }

    /* Bubble Config */

    /**
     * @return mixed
     */
    public function getBubbleShow()
    {
        return $this->scopeConfig->getValue('chat/widgetconfig/bubble_show');
    }

    /**
     * @return mixed
     */
    public function getBubbleTitle()
    {
        return $this->scopeConfig->getValue('chat/widgetconfig/bubble_title');
    }

    /**
     * @return mixed
     */
    public function getBubbleText()
    {
        return $this->scopeConfig->getValue('chat/widgetconfig/bubble_text');
    }

    /**
     * @return mixed
     */
    public function getBubbleColorPrimary()
    {
        return $this->scopeConfig->getValue('chat/widgetconfig/theme_bubble_color_primary');
    }

    /**
     * @return mixed
     */
    public function getBubbleColor()
    {
        return $this->scopeConfig->getValue('chat/widgetconfig/theme_bubble_color');
    }

    /* Window Config */

    /**
     * @return mixed
     */
    public function getWindowTitle()
    {
        return $this->scopeConfig->getValue('chat/widgetconfig/window_title');
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
        return $this->scopeConfig->getValue('chat/widgetconfig/window_size');
    }

    /**
     * @return mixed
     */
    public function getWindowOnShow()
    {
        return $this->scopeConfig->getValue('chat/widgetconfig/window_onshow');
    }

    /**
     * @return mixed
     */
    public function getWindowOnHide()
    {
        return $this->scopeConfig->getValue('chat/widgetconfig/window_onhide');
    }

    /* Button Config */

    /**
     * @return mixed
     */
    public function getButtonPosition()
    {
        return $this->scopeConfig->getValue('chat/widgetconfig/button_position');
    }

    /**
     * @return mixed
     */
    public function getButtonPositionMobile()
    {
        return $this->scopeConfig->getValue('chat/widgetconfig/button_position_mobile');
    }

    /**
     * @return mixed
     */
    public function getButtonHideOffline()
    {
        return $this->scopeConfig->getValue('chat/widgetconfig/button_hide_offline');
    }

    /* Department Config */

    /**
     * @return mixed
     */
    public function getDepartmentsFilter()
    {
        return $this->scopeConfig->getValue('chat/departments/filter');
    }

    /* Cookie Law Config */

    /**
     * @return bool
     */
    public function getCookieLawComply()
    {
        return $this->scopeConfig->isSetFlag('chat/widgetconfig/cookielaw_comply');
    }

    /**
     * @return bool
     */
    public function getCookieLawConsent()
    {
        return $this->scopeConfig->isSetFlag('chat/widgetconfig/cookielaw_consent');
    }

    /* Concierge Config */

    /**
     * @return mixed
     */
    public function getConciergeAvatar()
    {
        return $this->scopeConfig->getValue('chat/widgetconfig/concierge_avatar');
    }

    /**
     * @return mixed
     */
    public function getConciergeName()
    {
        return $this->scopeConfig->getValue('chat/widgetconfig/concierge_name');
    }

    /**
     * @return mixed
     */
    public function getConciergeTitle()
    {
        return $this->scopeConfig->getValue('chat/widgetconfig/concierge_title');
    }

    /* Badge Config */

    /**
     * @return mixed
     */
    public function getBadgeShow()
    {
        return $this->scopeConfig->getValue('chat/widgetconfig/badge_show');
    }

    /**
     * @return mixed
     */
    public function getBadgeLayout()
    {
        return $this->scopeConfig->getValue('chat/widgetconfig/badge_layout');
    }

    /**
     * @return mixed
     */
    public function getBadgeImage()
    {
        return $this->scopeConfig->getValue('chat/widgetconfig/badge_image');
    }

    /**
     * @return mixed
     */
    public function getBadgeText()
    {
        return $this->scopeConfig->getValue('chat/widgetconfig/badge_text');
    }

    /**
     * @return mixed
     */
    public function getBadgeColorPrimary()
    {
        return $this->scopeConfig->getValue('chat/widgetconfig/theme_badge_color_primary');
    }

    /**
     * @return mixed
     */
    public function getBadgeColor()
    {
        return $this->scopeConfig->getValue('chat/widgetconfig/theme_badge_color');
    }

    /* Theme Config */

    /**
     * @return mixed
     */
    public function getThemePrimaryColor()
    {
        return $this->scopeConfig->getValue('chat/widgetconfig/theme_primary_color');
    }
}