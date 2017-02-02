<?php

namespace Auth\Controller;

use Auth\Model\AuthAdapter;
use Zend\Crypt\Password\Bcrypt;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Form\Annotation\AnnotationBuilder;

use Auth\Model\User;
use Auth\Model\AuthStorage;
use Auth\Interfaces\AuthServiceAwareInterface;

class AuthController extends AbstractActionController implements AuthServiceAwareInterface
{

    protected $form;
    protected $storage;
    protected $authservice;

    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    public $sm;

    public function setServiceLocator($sm)
    {
        $this->sm = $sm;
    }

    public function setAuthService($authService)
    {

        $this->authservice = $authService;
    }

    public function getAuthService()
    {

        return $this->authservice;
    }

    public function getSessionStorage()
    {
        if (! $this->storage) {
            $this->storage = new AuthStorage('local_storage');
        }

        return $this->storage;
    }

    public function getForm()
    {
        if (! $this->form) {
            $user       = new User();
            $builder    = new AnnotationBuilder();
            $this->form = $builder->createForm($user);
        }

        return $this->form;
    }

    /**
     * Login
     *
     * @return array|\Zend\Http\Response
     */
    public function loginAction()
    {
        //if already login, redirect to success page
        if ($this->getAuthService()->hasIdentity()) {
            return $this->redirect()->toRoute('home');
        }

        $form = $this->getForm();

        return array(
            'form'      => $form,
            'messages'  => $this->flashmessenger()->getMessages()
        );
    }

    /**
     * @param $password
     * @return string
     */
    private function _getEncryptedPassword($password)
    {

        $bcrypt = new Bcrypt();
        return $bcrypt->create($password);
    }

    /**
     * Authenticate with credentials
     *
     * @return \Zend\Http\Response
     */
    public function authenticateAction()
    {
        $form = $this->getForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $authAdapter =  $this->sm->get('Auth\Model\AuthAdapter');
                $result = $authAdapter->setCredentials(
                    $request->getPost('username'),
                    $request->getPost('password')
                )->authenticate();

                foreach ($result->getMessages() as $message) {
                    //save message temporary into flashmessenger
                    $this->flashmessenger()->addMessage($message);
                }

                if ($result->isValid()) {
                    $resultRow = $result->getIdentity();

                    //check if it has rememberMe :
                    if ($request->getPost('rememberMe') == 1) {
                        $this->getSessionStorage()
                             ->setRememberMe(1);
                        //set storage again
                        $this->getAuthService()->setStorage($this->getSessionStorage());
                    }
                    $this->getAuthService()->setStorage($this->getSessionStorage());
                    $this->getAuthService()->getStorage()->write($resultRow);
                }
            } else {
                foreach ($form->getMessages() as $key => $message) {
                    $this->flashmessenger()->setNamespace('danger')->addMessage($key . " " . $message['isEmpty']);
                }
            }
        }

        return $this->redirect()->toRoute('home');
    }

    /**
     * Logout
     *
     * @return \Zend\Http\Response
     */
    public function logoutAction()
    {
        if ($this->getAuthService()->hasIdentity()) {
            $this->getSessionStorage()->forgetMe();
            $this->getAuthService()->clearIdentity();
            $this->flashmessenger()->setNamespace('success')->addMessage("You've been logged out");
        }

        return $this->redirect()->toRoute('home');
    }
}
