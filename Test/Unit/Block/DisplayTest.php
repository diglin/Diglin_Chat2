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

/**
 * Class DisplayTest
 * @package Diglin\Zopim\Block
 */
class DisplayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Diglin\Zopim\Block\Display
     */
    protected $block;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutMock;

    /**
     * @var \Diglin\Zopim\Helper\Data
     */
    protected $chatHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystem;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeResolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfig;


    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreFileStorageDatabase;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $helperContext;

    protected $blockContext;

    protected function setUp()
    {
        $this->_objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->mockContext();

        $this->block = $this->_objectManager->getObject(
            'Diglin\Zopim\Block\Display',
            [
                'context' => $this->blockContext,
                'customerSession' => $this->customerSessionMock,
                'filesystem' => $this->filesystem,
                'coreFileStorageDatabase' => $this->coreFileStorageDatabase,
            ]
        );
    }

    protected function mockContext()
    {
        $request = $this->getMock('\Magento\Framework\App\RequestInterface', [], [], '', false);
        $layout = $this->getMock('\Magento\Framework\View\LayoutInterface', [], [], '', false); ;
        $eventManager = $this->getMock('\Magento\Framework\Event\ManagerInterface', [], [], '', false);
        $urlBuilder = $this->getMock('\Magento\Framework\UrlInterface', [], [], '', false);
        $cache = $this->getMock('\Magento\Framework\App\CacheInterface', [], [], '', false);
        $design = $this->getMock('\Magento\Framework\View\DesignInterface', [], [], '', false);
        $session = $this->getMock('\Magento\Framework\Session\SessionManagerInterface', [], [], '', false);
        $sidResolver = $this->getMock('\Magento\Framework\Session\SidResolverInterface', [], [], '', false);
        $assetRepo = $this->getMock('\Magento\Framework\View\Asset\Repository', [], [], '', false);
        $viewConfig = $this->getMock('\Magento\Framework\View\ConfigInterface', [], [], '', false);
        $cacheState = $this->getMock('\Magento\Framework\App\Cache\StateInterface', [], [], '', false);
        $logger = $this->getMock('\Psr\Log\LoggerInterface', [], [], '', false);
        $escaper = $this->_objectManager->getObject('\Magento\Framework\Escaper');
        $filterManager = $this->getMock('\Magento\Framework\Filter\FilterManager', [], [], '', false);
        $localeDate = $this->getMock('\Magento\Framework\Stdlib\DateTime\TimezoneInterface', [], [], '', false);
        $inlineTranslation = $this->getMock('\Magento\Framework\Translate\Inline\StateInterface', [], [], '', false);
        $viewFileSystem = $this->getMock('\Magento\Framework\View\FileSystem',[],[],'',false);
        $templateEnginePool = $this->getMock('\Magento\Framework\View\TemplateEnginePool', [], [], '', false);
        $appState = $this->getMock('\Magento\Framework\App\State', [], [], '', false);
        $storeManager = $this->getMock('\Magento\Store\Model\StoreManagerInterface', [], [], '', false);
        $pageConfig = $this->getMock('\Magento\Framework\View\Page\Config', [], [], '', false);
        $resolver = $this->getMock('\Magento\Framework\View\Element\Template\File\Resolver', [], [], '', false);
        $validator = $this->getMock('\Magento\Framework\View\Element\Template\File\Validator', [], [], '', false);
        $localeResolver = $this->getMock('\Magento\Framework\Locale\ResolverInterface', [], [], '', false);

        $this->scopeConfig = $this->getMock('\Magento\Framework\App\Config\ScopeConfigInterface', [], [], '', false);

        $this->customerSessionMock = $this->getMock('\Magento\Customer\Model\Session', [], [], '', false);
        $this->customerSessionMock->expects($this->any())->method('getCustomer')->willReturn(new \Magento\Framework\DataObject(array('name' => 'Sylvain Rayé', 'email' => 'support@diglin.com')));
        $this->customerSessionMock->expects($this->any())->method('isLoggedIn')->willReturn(true);

        $this->coreFileStorageDatabase = $this->getMock('\Magento\MediaStorage\Helper\File\Storage\Database', [], [], '', false);
        $this->filesystem = $this->getMock('\Magento\Framework\Filesystem', [], [], '', false);
        $dirMock = $this->getMockForAbstractClass('Magento\Framework\Filesystem\Directory\ReadInterface');
        $this->filesystem->expects($this->any())->method('getDirectoryRead')->will($this->returnValue($dirMock));

        $this->helperContext = $this->getMockBuilder('Magento\Framework\App\Helper\Context')
            ->disableOriginalConstructor()
            ->getMock();

        $this->scopeConfig = $this->getMockBuilder('Magento\Framework\App\Config\ScopeConfigInterface')
            ->getMockForAbstractClass();

        $this->helperContext->expects($this->any())
            ->method('getScopeConfig')
            ->willReturn($this->scopeConfig);

        $this->chatHelper = new \Diglin\Zopim\Helper\Data($this->helperContext);

        $this->blockContext = $this->_objectManager->getObject(
            '\Diglin\Zopim\Block\Context',
            [
                'request' => $request,
                'layout' => $layout,
                'eventManager' => $eventManager,
                'urlBuilder' => $urlBuilder,
                'cache' => $cache,
                'design' => $design,
                'session' => $session,
                'sidResolver' => $sidResolver,
                'scopeConfig' => $this->scopeConfig,
                'assetRepo' => $assetRepo,
                'viewConfig' => $viewConfig,
                'cacheState' => $cacheState,
                'logger' => $logger,
                'escaper' => $escaper,
                'filterManager' => $filterManager,
                'localeDate' => $localeDate,
                'inlineTranslation' => $inlineTranslation,
                'filesystem' => $this->filesystem,
                'viewFileSystem' => $viewFileSystem,
                'templateEnginePool' => $templateEnginePool,
                'appState' => $appState,
                'storeManager' => $storeManager,
                'pageConfig' => $pageConfig,
                'resolver' => $resolver,
                'validator' => $validator,
                'dataHelper' => $this->chatHelper,
                'localeResolver' => $localeResolver,
            ]
        );
    }

    /**
     * @param $data
     * @dataProvider initDataProvider
     */
    public function testInit($data)
    {
        $this->prepareConfiguration($data);
    }

    /**
     * @param $data
     */
    protected function prepareConfiguration($data)
    {
        $map = [];
        foreach ($data as $key => $value) {
            $map[] = [
                $key,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                null,
                $value
            ];
        }

        $this->scopeConfig->expects($this->any())
            ->method('getValue')
            ->willReturnMap($map);

        $this->scopeConfig->expects($this->any())
            ->method('isSetFlag')
            ->willReturnMap($map);
    }

    /**
     * @return array
     */
    public function initDataProvider()
    {
        return
        [
            [
                [
                    \Diglin\Zopim\Helper\Data::CFG_CHATCONFIG_ALLOW_NAME => '1',
                    \Diglin\Zopim\Helper\Data::CFG_CHATCONFIG_ALLOW_EMAIL => '1',
                    \Diglin\Zopim\Helper\Data::CFG_CHATCONFIG_ENABLED => '1',
                    \Diglin\Zopim\Helper\Data::CFG_CHATCONFIG_LANGUAGE => 'de',
                    \Diglin\Zopim\Helper\Data::CFG_CHATCONFIG_KEY => '1234567890',
                    \Diglin\Zopim\Helper\Data::CFG_CHATCONFIG_DISABLE_SOUND => '0',
                    \Diglin\Zopim\Helper\Data::CFG_CHATCONFIG_ONLINE_MESSAGE => 'My Online Message',
                    \Diglin\Zopim\Helper\Data::CFG_CHATCONFIG_OFFLINE_MESSAGE => 'My Offline Message',

                    \Diglin\Zopim\Helper\Data::CFG_WIDGETCONFIG_TYPE => 'adv',
                    \Diglin\Zopim\Helper\Data::CFG_WIDGETCONFIG_WINDOW_THEME => 'simple',
                    \Diglin\Zopim\Helper\Data::CFG_WIDGETCONFIG_WINDOW_TITLE => 'My Window Title',
                    \Diglin\Zopim\Helper\Data::CFG_WIDGETCONFIG_WINDOW_SIZE => '200',
                    \Diglin\Zopim\Helper\Data::CFG_WIDGETCONFIG_WINDOW_ONSHOW => '',
                    \Diglin\Zopim\Helper\Data::CFG_WIDGETCONFIG_WINDOW_ONHIDE => '',
                    \Diglin\Zopim\Helper\Data::CFG_WIDGETCONFIG_WINDOW_POSITION => 'br',
                    \Diglin\Zopim\Helper\Data::CFG_WIDGETCONFIG_WINDOW_POSITION_MOBILE => 'br',

                    \Diglin\Zopim\Helper\Data::CFG_WIDGETCONFIG_BUBBLE_SHOW => '1',
                    \Diglin\Zopim\Helper\Data::CFG_WIDGETCONFIG_BUBBLE_TITLE => 'My Bubble Title',
                    \Diglin\Zopim\Helper\Data::CFG_WIDGETCONFIG_BUBBLE_TEXT => 'My Bubble Text',
                    \Diglin\Zopim\Helper\Data::CFG_WIDGETCONFIG_BUBBLE_COLOR_PRIMARY => '#E8E8E8',
                    \Diglin\Zopim\Helper\Data::CFG_WIDGETCONFIG_BUBBLE_COLOR => '#E3E3E3',
                    \Diglin\Zopim\Helper\Data::CFG_WIDGETCONFIG_BUTTON_HIDE_OFFLINE => '0',

                    \Diglin\Zopim\Helper\Data::CFG_DEPARTMENTS_FILTER => '',

                    \Diglin\Zopim\Helper\Data::CFG_WIDGETCONFIG_COOKIE_COMPLY => '1',
                    \Diglin\Zopim\Helper\Data::CFG_WIDGETCONFIG_COOKIE_CONSENT => '1',

                    // Concierge only for simple theme
                    \Diglin\Zopim\Helper\Data::CFG_WIDGETCONFIG_CONCIERGE_AVATAR => 'http://lorempixel.com/100/100/food/',
                    \Diglin\Zopim\Helper\Data::CFG_WIDGETCONFIG_CONCIERGE_NAME => 'My Concierge Name',
                    \Diglin\Zopim\Helper\Data::CFG_WIDGETCONFIG_CONCIERGE_TITLE => 'My Concierge Title',

                    \Diglin\Zopim\Helper\Data::CFG_WIDGETCONFIG_BADGE_COLOR_PRIMARY => '#AEAEAE',
                    \Diglin\Zopim\Helper\Data::CFG_WIDGETCONFIG_BADGE_COLOR => '',

                    \Diglin\Zopim\Helper\Data::CFG_WIDGETCONFIG_THEME_COLOR_PRIMARY => '#A1A1A1',

                    // Badge options only for simple theme
                    \Diglin\Zopim\Helper\Data::CFG_WIDGETCONFIG_BADGE_SHOW => '1',
                    \Diglin\Zopim\Helper\Data::CFG_WIDGETCONFIG_BADGE_LAYOUT => '',
                    \Diglin\Zopim\Helper\Data::CFG_WIDGETCONFIG_BADGE_IMAGE => 'http://lorempixel.com/100/100/fashion/',
                    \Diglin\Zopim\Helper\Data::CFG_WIDGETCONFIG_BADGE_TEXT => 'My Badge Text',
                ],
            ],
        ];
    }

    /**
     * @param array $data
     * @dataProvider initDataProvider
     */
    public function testZopimWidgetSimpleHtml($data)
    {
        $this->prepareConfiguration($data);

        $options = $this->block->getOptions();

        $this->assertContains('setGreetings', $options, 'Greetings missing');
        $this->assertContains('My Online Message', $options, 'Online message missing');
        $this->assertContains('My Offline Message', $options, 'Offline message missing');

        $this->assertContains('setLanguage(\'de\'', $options, 'Language missing');

        $this->assertContains('setName(\'Sylvain Rayé\'', $options, 'Name is missing');
        $this->assertContains('setEmail(\'support@diglin.com\'', $options, 'Email is missing');

        $this->assertContains('setDisableSound(false);', $options, 'Sound is not disabled');

        $this->assertContains('theme.setTheme(\'simple\');', $options, 'Simple theme is not set');

        $this->assertContains('concierge.setName(\'My Concierge Name\');', $options, 'Concierge Name is not set');
        $this->assertContains('concierge.setTitle(\'My Concierge Title\');', $options, 'Concierge Title is not set');

        $this->assertContains('badge.show()', $options, 'Badge is not shown');
        $this->assertContains('badge.setText(\'My Badge Text\')', $options, 'Badge Text is not set');
        $this->assertContains('badge.setImage(\'http://lorempixel.com/100/100/fashion/\')', $options, 'Badge Image is not set');

        $this->assertContains('window.setSize(\'200\');', $options, 'Window size is not 200');
        $this->assertContains('window.setPosition(\'br\');', $options, 'Window position is not br');

        $this->assertContains('button.setPositionMobile(\'br\');', $options, 'Button Mobile position is not br');
        $this->assertContains('button.setPosition(\'br\');', $options, 'Button position is not br');

        $this->assertContains('theme.setColor(\'#A1A1A1\'', $options, 'Theme Set Color ');

        $this->assertContains('http://lorempixel.com/100/100/food/', $options, 'Concierge Avatar Image is missing');
    }

    /**
     * @return array
     */
    public function initDataClassicProvider()
    {
        $data = $this->initDataProvider();
        $data[0][0][\Diglin\Zopim\Helper\Data::CFG_WIDGETCONFIG_WINDOW_THEME] = 'classic';

        return $data;
    }

    /**
     * @param array $data
     * @dataProvider initDataClassicProvider
     */
    public function testZopimWidgetClassicHtml($data)
    {
        $this->prepareConfiguration($data);

        $options = $this->block->getOptions();

        $this->assertNotContains('badge.', $options, 'Badge options are not possible with classic theme');

        if ($this->chatHelper->getBadgeColorPrimary()) {
            $this->assertContains('theme.setColor', $options, 'Theme Set Color is missing');

        }
    }
}