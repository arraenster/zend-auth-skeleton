<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Crypt\Password\Bcrypt;
use Application\Entity\UsersTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{

    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    public $sm;

    /**
     * Show list of users
     *
     * @return ViewModel
     */
    public function indexAction()
    {

        $objectManager = $this->sm->get('Doctrine\ORM\EntityManager');

        $usersObj = $objectManager
            ->getRepository('\Application\Entity\UsersTable')
            ->findAll();

        $users = [];
        foreach ($usersObj as $user) {
            $users[] = $user->getArrayCopy();
        }

        $doorsObj = $objectManager
            ->getRepository('\Application\Entity\DoorsTable')
            ->findAll();

        $doors = [];
        foreach ($doorsObj as $door) {
            $doors[] = $door->getArrayCopy();
        }

        return new ViewModel(array(
            'users' => $users,
            'doors' => $doors,
        ));
    }

    /**
     * Add new user
     * @return ViewModel
     */
    public function addAction()
    {

        $form = $this->sm->get('FormElementManager')
            ->get('AddUserForm');

        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $formParams = $request->getPost();
            $form->setData($formParams);

            if ($form->isValid()) {

                $objectManager = $this->sm->get('Doctrine\ORM\EntityManager');
                $newUser = new UsersTable();

                $newUser->exchangeArray($form->getData());
                $bcrypt = new Bcrypt();
                $newUser->setPassword($bcrypt->create($formParams['password']));

                $newUser->setId(0);

                $objectManager->persist($newUser);
                $objectManager->flush();

                $this->flashMessenger()->addMessage("New user successfully added");

                return $this->redirect()->toRoute('home');
            } else {
                $this->flashMessenger()->addErrorMessage("Error while adding user");
            }
        }

        return new ViewModel([
            'form' => $form,
        ]);
    }
}
