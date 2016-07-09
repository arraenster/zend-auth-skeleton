<?php
namespace Application\Controller;

use Application\Entity\PermissionsTable;
use Zend\Crypt\Password\Bcrypt;
use Application\Entity\UsersTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Fat default controller. Implements also work with database
 * Class IndexController
 *
 * @author: Vladyslav Semerenko <vladyslav.semerenko@gmail.com>
 */
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

        $users = $objectManager->createQueryBuilder()
            ->select('ut.id, ut.username, ut.role, dt.alias, dt.description')
            ->from('\Application\Entity\UsersTable', 'ut')
            ->leftJoin('\Application\Entity\PermissionsTable', 'pt', 'WITH', 'pt.userId = ut.id')
            ->leftJoin('\Application\Entity\DoorsTable', 'dt', 'WITH', 'pt.doorId = dt.id')
            ->orderBy('ut.id')
            ->getQuery()
            ->getResult();

        $grouppedUsers = [];
        foreach ($users as $user) {

            $grouppedUsers[$user['id']]['id'] = $user['id'];
            $grouppedUsers[$user['id']]['username'] = $user['username'];
            $grouppedUsers[$user['id']]['role'] = $user['role'];
            if (isset($user['alias'])) {
                $grouppedUsers[$user['id']]['doors'][] = $user['alias'];
            } else {
                $grouppedUsers[$user['id']]['doors'] = [];
            }
        }

        return new ViewModel([
            'users' => $grouppedUsers,
            'doors' => $this->_getDoors(),
        ]);
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

    public function editAction()
    {

        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            $this->flashMessenger()->addErrorMessage('User id doesn\'t set');
            return $this->redirect()->toRoute('home');
        }

        $objectManager = $this->sm->get('Doctrine\ORM\EntityManager');

        $user = $objectManager->createQueryBuilder()
            ->select('u.id, u.username, u.role')
            ->from('\Application\Entity\UsersTable', 'u')
            ->where('u.id = :id')
            ->setParameter(':id',$id)
            ->getQuery()
            ->getOneOrNullResult();



        return new ViewModel([
            'user' => $user,
            'doors' => $this->_getDoors()
        ]);
    }

    /**
     * Save user permissions
     * @return \Zend\Http\Response
     */
    public function saveAction()
    {

        $request = $this->getRequest();
        if ($request->isPost()) {
            $formParams = $request->getPost();
            $userId = $formParams['userId'];

            // Remove all user permissions before saving new
            $objectManager = $this->sm->get('Doctrine\ORM\EntityManager');
            $permissionsObj = $objectManager->createQuery('DELETE Application\Entity\PermissionsTable p WHERE p.userId = ' . $userId);

            $permissionsObj->getResult();

            // Save new permissions
            foreach ($formParams['doorIds'] as $doorId) {

                $newPermission = new PermissionsTable();
                $newPermission->setDoorId($doorId)->setUserId($formParams['userId']);
                $newPermission->setId(0);

                $objectManager->persist($newPermission);
                $objectManager->flush();

                $this->flashMessenger()->addMessage(
                    sprintf("New permissions for user #%s successfully added", $formParams['userId'])
                );
            }
        } else {
            $this->flashMessenger()->addErrorMessage("Error while saving permissions");
        }
        return $this->redirect()->toRoute('home');
    }

    /**
     * Get all users from DB
     * @return mixed
     */
    private function _getUsers()
    {

        $objectManager = $this->sm->get('Doctrine\ORM\EntityManager');

        $usersObj = $objectManager
            ->getRepository('\Application\Entity\UsersTable')
            ->findAll();

        $users = [];
        foreach ($usersObj as $user) {
            $users[] = $user->getArrayCopy();
        }

        return $users;
    }

    /**
     * Get all doors from DB
     * @return array
     */
    private function _getDoors()
    {

        $objectManager = $this->sm->get('Doctrine\ORM\EntityManager');
        $doorsObj = $objectManager
            ->getRepository('\Application\Entity\DoorsTable')
            ->findAll();

        $doors = [];
        foreach ($doorsObj as $door) {
            $doors[] = $door->getArrayCopy();
        }

        return $doors;
    }
}
