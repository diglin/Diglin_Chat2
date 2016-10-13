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

/**
 * Class Dashboard
 * @package Diglin\Zopim\Controller\Adminhtml\Account
 */
class Dashboard extends Chat
{
    public function execute()
    {
        // @deprecated for security reason on Zopim side, we forward to ZOPIM_DASHBOARD_URL
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setUrl(\Diglin\Zopim\Helper\Data::ZOPIM_DASHBOARD_URL);
    }
}