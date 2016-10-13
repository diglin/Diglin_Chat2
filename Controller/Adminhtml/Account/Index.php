<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author      Sylvain Rayé <support at diglin.com>
 * @category    Diglin
 * @package     Diglin_
 * @copyright   Copyright (c) 2011-2016 Diglin (http://www.diglin.com)
 */

namespace Diglin\Zopim\Controller\Adminhtml\Account;

use Diglin\Zopim\Controller\Adminhtml\Chat;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Account
 * @package Diglin\Zopim\Controller\Adminhtml\Account
 */
class Index extends Chat
{
    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Diglin\Zopim\Helper\Data
     */
    protected $chatHelper;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var bool
     */
    protected $goToLogin = false;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Diglin\Zopim\Helper\Data $chatHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
    ) {
        parent::__construct($context);

        $this->layoutFactory = $layoutFactory;
        $this->scopeConfig = $scopeConfig;
        $this->chatHelper = $chatHelper;
        $this->jsonHelper = $jsonHelper;
        $this->cacheTypeList = $cacheTypeList;
    }

    public function execute()
    {
        $chatAccountBlock = $this->layoutFactory->create()->getBlock('zopim_account');

        $key = $this->scopeConfig->getValue(\Diglin\Zopim\Helper\Data::CFG_CHATCONFIG_KEY);
        $username = $this->scopeConfig->getValue('chat/chatconfig/username');
        $salt = $this->scopeConfig->getValue('chat/chatconfig/salt');

        $zopimObject = new \Magento\Framework\DataObject(array(
            'key' => $key,
            'username' => $username,
            'salt' => $salt
        ));

        $error = array();

        if ($this->getRequest()->getParam('deactivate') == "yes") {
            $zopimObject->setSalt(null);
            $zopimObject->setKey('zopim');
        } else if ($this->getRequest()->getParam('zopimusername') != "") {
            // logging in
            $zopimusername = $this->getRequest()->getParam('zopimusername');
            $zopimpassword = $this->getRequest()->getParam('zopimpassword');

            $logindata = array(
                "email"     => $zopimusername,
                "password"  => $zopimpassword
            );

            $loginresult = $this->jsonHelper->jsonDecode($this->chatHelper->doPostRequest(\Diglin\Zopim\Helper\Data::ZOPIM_LOGIN_URL, $logindata));

            if (isset($loginresult["error"])) {
                $error["login"] = __("<b>Could not log in to Zopim. Please check your login details. If problem persists, try connecting without SSL enabled.</b>");
                $this->goToLogin = 1;
                $zopimObject->setSalt(null);
            } elseif (isset($loginresult["salt"])) {

                $zopimObject->setUsername($zopimusername);
                $zopimObject->setSalt($loginresult["salt"]);

                $account = $this->jsonHelper->jsonDecode($this->chatHelper->doPostRequest(\Diglin\Zopim\Helper\Data::ZOPIM_GETACCOUNTDETAILS_URL, array("salt" => $loginresult["salt"])));

                if (isset($account)) {
                    $zopimObject->setKey($account["account_key"]);
                }
            } else {
                $zopimObject->setSalt(null);
                $error["login"] = __("<b>Could not log in to Zopim. We were unable to contact Zopim servers. Please check with your server administrator to ensure that <a href='http://www.php.net/manual/en/book.curl.php'>PHP Curl</a> is installed and permissions are set correctly.</b>");
            }
        } else if ($this->getRequest()->getParam('zopimfirstname') != "") {

            $signupResult = $this->jsonHelper->jsonDecode($this->chatHelper->doPostRequest(\Diglin\Zopim\Helper\Data::ZOPIM_SIGNUP_URL, $this->getCreateRequest()));

            if (isset($signupResult["error"])) {
                $error["auth"] = __("Error during activation: <b>" . $signupResult["error"] . "</b> Please try again.");
            } else if (isset($signupResult["account_key"])) {
                $message = __("<b>Thank you for signing up. Please check your mail for your password to complete the process. </b>");
                $this->goToLogin = 1;
            } else if (isset($signupResult['url'])) {
                $message = __('<b>Thank you for signing up. Please click on <button onclick="%s"><span><span>this button</span></span></button> to complete the process.</b>', "window.open('" . $signupResult['url'] . "')");
                $this->goToLogin = 1;
            } else {
                $error["auth"] = __("<b>Could not activate account. The Magento installation was unable to contact Zopim servers. Please check with your server administrator to ensure that <a href='http://www.php.net/manual/en/book.curl.php'>PHP Curl</a> is installed and permissions are set correctly.</b>");
            }
        }

        if ($zopimObject->getKey() != "" && $zopimObject->getKey() != "zopim") {

            if (isset($account)) {
                $accountDetails = $account;
            } else {
                $accountDetails = $this->jsonHelper->jsonDecode($this->chatHelper->doPostRequest(
                    \Diglin\Zopim\Helper\Data::ZOPIM_GETACCOUNTDETAILS_URL,
                    array("salt" => $zopimObject->getSalt())
                    )
                );
            }

            if (!isset($accountDetails) || isset($accountDetails["error"])) {
                $this->goToLogin = 1;
                $error["auth"] = __('Account no longer linked! We could not verify your Zopim account. Please check your password and try again.');
            } else {
                $chatAccountBlock->setIsAuthenticated(true);
            }
        }

        if (isset($error["auth"])) {
            $this->messageManager->addError($error["auth"]);
        } else if (isset($error["login"])) {
            $this->messageManager->addError($error["login"]);
        } else if (isset($message)) {
            $this->messageManager->addSuccess($message);
        }

        if ($chatAccountBlock->getIsAuthenticated()) {
            if ($accountDetails["package_id"] == "trial") {
                $accountDetails["package_id"] = "Free Lite Package + 14 Days Full-features";
            } else {
                $accountDetails["package_id"] .= " Package";
            }
        } else {
            if ($this->getRequest()->getParam('zopimfirstname')) {
                $chatAccountBlock->setWasChecked('checked');
            }

            if (!$chatAccountBlock->getIsAuthenticated() && !$this->goToLogin) {
                $chatAccountBlock->setShowSignup('showSignup(1);');
            } else {
                $chatAccountBlock->setShowSignup('showSignup(0);');
            }
        }

        $chatAccountBlock->setUsername($zopimObject->getUsername());

        if (isset($accountDetails)) {
            $chatAccountBlock->setAccountDetails($accountDetails["package_id"]);
        }
    }

    /**
     * @return array
     */
    protected function getCreateRequest()
    {
        return [
            "email" => $this->getRequest()->getParam('zopimnewemail'),
            "first_name" => $this->getRequest()->getParam('zopimfirstname'),
            "last_name" => $this->getRequest()->getParam('zopimlastname'),
            "display_name" => $this->getRequest()->getParam('zopimfirstname') . " " . $this->getRequest()->getParam('zopimlastname'),
            "aref_id" => '477070', # Diglin
            "eref" => "",
            "source" => "magento",
            "recaptcha_challenge_field" => $this->getRequest()->getParam('recaptcha_challenge_field'),
            "recaptcha_response_field" => $this->getRequest()->getParam('recaptcha_response_field')
        ];
    }

    /**
     * @param \Magento\Framework\DataObject $zopimObject
     * @return $this
     */
    protected function saveConfiguration (\Magento\Framework\DataObject $zopimObject)
    {
        /* @var $config \Magento\Config\Model\ResourceModel\Config */
        $config = $this->_objectManager->get('Magento\Config\Model\ResourceModel\Config');

        if ($zopimObject->getKey()) {
            if ($zopimObject->getKey() != 'zopim') {
                $config->saveConfig(\Diglin\Zopim\Helper\Data::CFG_CHATCONFIG_ENABLED, 1, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
            } else {
                $zopimObject->setKey(null);
                $config->saveConfig(\Diglin\Zopim\Helper\Data::CFG_CHATCONFIG_ENABLED, 0, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
            }
        }

        $config->saveConfig(\Diglin\Zopim\Helper\Data::CFG_CHATCONFIG_KEY, $zopimObject->getKey(), ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $config->saveConfig('chat/chatconfig/username', $zopimObject->getUsername(), ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $config->saveConfig('chat/chatconfig/salt', $zopimObject->getSalt(), ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);

        $this->cacheTypeList->cleanType(\Magento\Framework\App\Cache\Type\Config::TYPE_IDENTIFIER);
        return $this;
    }
}