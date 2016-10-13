<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author      Sylvain Rayé <support at diglin.com>
 * @category    Diglin
 * @package     Diglin_
 * @copyright   Copyright (c) 2011-2016 Diglin (http://www.diglin.com)
 */

namespace Diglin\Zopim;


use Magento\TestFramework\ObjectManager;

class IntegrationDisplayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var \Diglin\Zopim\Block\Display
     */
    private $block;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerSession;

    /**
     * @var \Diglin\Zopim\Helper\Data
     */
    private $helper;

    protected function setUp()
    {
        $this->objectManager = ObjectManager::getInstance();
        $this->block = $this->objectManager->create(\Diglin\Zopim\Block\Display::class);

        $this->customerSession = $this->getMock(\Magento\Customer\Model\Session::class, ['isLoggedIn', 'getCustomer'], [], '', false);

        $this->helper = $this->objectManager->get(\Diglin\Zopim\Helper\Data::class);
    }

    public function testCacheKeyHasTemplate()
    {
        $this->block->setTemplate('foo');

        $this->assertContains('foo', $this->block->getCacheKeyInfo());
    }

}