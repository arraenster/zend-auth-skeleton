<?php
namespace ApplicationTest\Controller;

use Application\Controller\IndexController;
use Application\Entity\UsersTable;
use Zend\Stdlib\ArrayUtils;
use Zend\Dom\Query;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Main IndexController Test
 * Class IndexControllerTest
 *
 * @coversDefaultClass Application\Controller\IndexController
 * @author Vladyslav Semerenko <vladyslav.semerenko@gmail.com>
 */
class IndexControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $configOverrides = [];

        $this->setApplicationConfig(ArrayUtils::merge(
            include __DIR__ . '/../../../../config/application.config.php',
            $configOverrides
        ));

        parent::setUp();
    }

    protected function mockUserLogin()
    {
        $userSessionModel = new UsersTable();
        $userSessionModel->setId(1);
        $userSessionModel->setUsername('Tester');
        $userSessionModel->setRole('admin');

        $authService = $this->getMock('Zend\Authentication\AuthenticationService');
        $authService->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($userSessionModel));

        $authService->expects($this->any())
            ->method('hasIdentity')
            ->will($this->returnValue(true));

        $this->getApplicationServiceLocator()->setAllowOverride(true);
        $this->getApplicationServiceLocator()->setService('LocalAuthService', $authService);
    }

    /**
     * Test home route.
     * Should return 302 status because of unauthorized access
     * @covers ::indexAction
     */
    public function testIndexActionCanBeAccessed()
    {

        $this->dispatch('/', 'GET');
        $this->assertResponseStatusCode(302);
        $this->assertModuleName('application');
        $this->assertControllerName(IndexController::class); // as specified in router's controller name alias
        $this->assertControllerClass('IndexController');
        $this->assertMatchedRouteName('home');
    }

    /**
     * Test redirect for invalid route
     */
    public function testInvalidRouteDoesNotCrash()
    {

        $this->dispatch('/invalid/route', 'GET');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::indexAction
     */
    public function testMainPageWithLogin()
    {

        $this->mockUserLogin();
        $this->dispatch('/', 'GET');
        $this->assertResponseStatusCode(200);
        $model = $this->getApplicationServiceLocator()->get('ViewManager')->getViewModel();
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $model);
    }

    /**
     * @covers ::addAction
     */
    public function testAddActionRedirectsAfterValidPost()
    {

        $this->mockUserLogin();
        $entityManagerMock = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $entityManagerMock->expects($this->once())
            ->method('persist')
            ->will($this->returnValue(null));

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Doctrine\ORM\EntityManager', $entityManagerMock);

        $this->dispatch('/user/add');

        // fetch content of the page
        $html = $this->getResponse()->getBody();
        ;
        // parse page content, find the hash value pre-filled to the hidden element
        $dom = new Query($html);
        $csrf = $dom->execute('input[name="security"]')->current()->getAttribute('value');

        $postData = array(
            'id'            => '',
            'security'      => $csrf,
            'username'      => 'testuser',
            'password'      => '123',
            'role'          => 'admin',
        );
        $this->dispatch('/user/add', 'POST', $postData);
        $this->assertResponseStatusCode(302);

        $this->assertRedirectTo('/');
    }
}
