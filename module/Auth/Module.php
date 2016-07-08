<?php
namespace Auth;

use Auth\Model\AuthAdapter;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;

use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Auth\Interfaces\AuthServiceAwareInterface;

class Module implements AutoloaderProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
            // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/' , __NAMESPACE__),
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getControllerConfig()
    {
        return array(
            'initializers' => array(
                function ($instance, $sm) {
                    if ($instance instanceof AuthServiceAwareInterface) {
                        $authService  = $sm->getServiceLocator()->get('LocalAuthService');
                        $instance->setAuthService($authService);
                    }
                }
            )
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Auth\Model\AuthAdapter' => function ($sm) {
                    $adapter = new AuthAdapter();
                    $adapter->setServiceLocator($sm);

                    return $adapter;
                },
                'Auth\Model\AuthStorage' => function ($sm) {
                    return new \Auth\Model\AuthStorage('local_storage');
                },
                'LocalAuthService' => function ($sm) {
                    $dbAdapter      = $sm->get('Zend\Db\Adapter\Adapter');
                    $dbTableAuthAdapter  = new DbTableAuthAdapter($dbAdapter, 'users_table','username','password', 'PASSWORD(?)');

                    $authService = new AuthenticationService();
                    $authService->setAdapter($dbTableAuthAdapter);
                    $authService->setStorage($sm->get('Auth\Model\AuthStorage'));

                    return $authService;
                },
            ),
            'initializers' => array(
                function ($instance, $sm) {
                    if ($instance instanceof AuthServiceAwareInterface) {
                        $authService  = $this->getServiceLocator()->get('LocalAuthService');
                        $instance->setAuthService($authService);
                    }
                }
            )
        );
    }
}
