<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\MvcEvent;
use Zend\Mvc\ModuleRouteListener;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{
    const VERSION = '0.1';

    public function onBootstrap(MvcEvent $e)
    {

        $sm = $e->getApplication()->getServiceManager();

        $router = $sm->get('router');
        $request = $sm->get('request');

        // Check auth only for web requests

        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'authPreDispatch'), 1);
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $this->initAcl($e);
        $e->getApplication()->getEventManager()->attach('route', array($this, 'checkAcl'));

        $matchedRoute = $router->match($request);
        if ($matchedRoute) {
            $params = $matchedRoute->getParams();

            $controller = $params['controller'];
            $action = $params['action'];

            $module_array = explode('\\', $controller);
            $module = array_pop($module_array);

            $route = $matchedRoute->getMatchedRouteName();

            $e->getViewModel()->setVariables(
                array(
                    'CURRENT_MODULE_NAME' => $module,
                    'CURRENT_CONTROLLER_NAME' => $controller,
                    'CURRENT_ACTION_NAME' => $action,
                    'CURRENT_ROUTE_NAME' => $route,
                )
            );
        }
    }

    /**
     * Start ACL system
     * Load roles
     *
     * @param MvcEvent $e
     */
    public function initAcl(MvcEvent $e)
    {

        $acl = new \Zend\Permissions\Acl\Acl();
        $roles = include __DIR__ . '/config/module.acl.roles.php';
        $allResources = array();
        foreach ($roles as $role => $resources) {

            $role = new \Zend\Permissions\Acl\Role\GenericRole($role);
            $acl->addRole($role);

            $allResources = array_merge($resources, $allResources);

            //adding resources
            foreach ($resources as $resource) {
                if(!$acl->hasResource($resource))
                    $acl->addResource(new \Zend\Permissions\Acl\Resource\GenericResource($resource));
            }
            //adding restrictions
            foreach ($allResources as $resource) {
                $acl->allow($role, $resource);
            }
        }

        //setting to view
        $e->getViewModel()->acl = $acl;
    }

    /**
     * Check role of current user
     * Redirect to 403 if current resource is not allowed
     *
     * @param MvcEvent $e
     * @return Response
     */
    public function checkAcl(MvcEvent $e)
    {

        $route = $e->getRouteMatch()->getMatchedRouteName();
        if ($e->getApplication()->getServiceManager()->get("LocalAuthService")->hasIdentity()) {
            $userRole = $e->getApplication()->getServiceManager()->get("LocalAuthService")->getIdentity()->getRole();
        } else {
            return;
        }

        if (!$e->getViewModel()->acl->isAllowed($userRole, $route)) {
            $router   = $e->getRouter();
            $url      = $router->assemble(array(), array(
                'name' => 'guest'
            ));

            $response = $e->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $url);
            $response->setStatusCode(302);

            return $response;
        }
    }

    /**
     * Authenticate user or redirect to log in
     */
    public function authPreDispatch($e) {

        $auth   = $e->getApplication()->getServiceManager()->get("LocalAuthService");
        $match  = $e->getRouteMatch();

        $controller = $match->getParam('controller');
        if (strstr($controller, "Auth"))
            return;

        //if already login, redirect to success page
        if (!$auth->hasIdentity()) {

            $router   = $e->getRouter();
            $url      = $router->assemble(array(), array(
                'name' => 'auth'
            ));

            $response = $e->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $url);
            $response->setStatusCode(302);

            return $response;
        } else {
            $e->getViewModel()->identityModel = $auth->getIdentity();
        }
    }

    /**
     * Dependency injection to controllers
     *
     * @return array
     */
    public function getControllerConfig()
    {
        return array(
            'initializers' => array(
                function ($instance, $sm) {

                    $locator = $sm->getServiceLocator();
                    $instance->sm = $locator;
                }
            )
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Zend\Log' => function($sm) {
                    $logger = new \Zend\Log\Logger();
                    $writer = new \Zend\Log\Writer\Stream('./data/log/app.log');
                    $logger->addWriter($writer);

                    return $logger;
                },
            ),
        );
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src',
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}
