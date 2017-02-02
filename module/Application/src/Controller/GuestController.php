<?php
namespace Application\Controller;

use Application\Entity\LogTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use \Zend\Log\Logger;

/**
 * Manage guest options
 * Such as opening doors
 * @author: Vladyslav Semerenko <vladyslav.semerenko@gmail.com>
 */
class GuestController extends AbstractActionController
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */

    public $sm;

    public function guestAction()
    {

        $identity = $this->sm->get('LocalAuthService')->getIdentity();
        $objectManager = $this->sm->get('Doctrine\ORM\EntityManager');

        $doors = $objectManager->createQueryBuilder()
            ->select('dt.id, dt.alias, dt.description')
            ->from('\Application\Entity\PermissionsTable', 'pt')
            ->innerJoin('\Application\Entity\DoorsTable', 'dt', 'WITH', 'dt.id = pt.doorId')
            ->where('pt.userId = :userId')
            ->setParameter(':userId', $identity->getId())
            ->getQuery()
            ->getResult();

        $logs = $objectManager->createQueryBuilder()
            ->select('lt.logTime, lt.description, ut.username, dt.alias')
            ->from('\Application\Entity\LogTable', 'lt')
            ->innerJoin('\Application\Entity\UsersTable', 'ut', 'WITH', 'ut.id = lt.userId')
            ->innerJoin('\Application\Entity\DoorsTable', 'dt', 'WITH', 'dt.id = lt.doorId')
            ->where('lt.userId = :userId')
            ->setParameter(':userId', $identity->getId())
            ->getQuery()
            ->getResult();

        return new ViewModel([
            'username'  => $identity->getUsername(),
            'userId'    => $identity->getId(),
            'doors'     => $doors,
            'logs'      => $logs
        ]);
    }

    /**
     * Handler for opening door
     * TODO: Implement logging and checking logic
     *
     * @return \Zend\Http\Response
     */
    public function openAction()
    {

        $logger = $this->sm->get('Zend\Log');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $formParams = $request->getPost();
            $doorId = $formParams['doorId'];
            $userId = $formParams['userId'];

            $this->flashMessenger()->addMessage(sprintf("Door #%s has been opened", $doorId));

            // Text logging
            $logger->log(Logger::INFO, sprintf("Door #%s has been opened by user #%s", $doorId, $userId));

            // Database logging
            $this->_addNewOpenLog($doorId, $userId, sprintf("Door #%s has been opened by user #%s", $doorId, $userId));
        } else {
            $logger->log(Logger::ALERT, "Somebody has tried to open door");
        }

        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);

        return $viewModel;
    }

    /**
     * Add new record to logs table
     *
     * @param $doorId
     * @param $userId
     * @param $description
     */
    private function _addNewOpenLog($doorId, $userId, $description)
    {

        $objectManager = $this->sm->get('Doctrine\ORM\EntityManager');

        $newLog = new LogTable();
        $newLog->setDoorId($doorId)
            ->setUserId($userId)
            ->setLogTime(new \DateTime())
            ->setDescription($description);

        $objectManager->persist($newLog);
        $objectManager->flush();
    }
}
