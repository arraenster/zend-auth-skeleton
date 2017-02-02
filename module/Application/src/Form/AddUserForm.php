<?php
namespace Application\Form;

use Application\Form\Filter\AddUserInputFilter;
use Zend\Form\Form;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;

/**
 * Add new flight form
 * Implements filters
 *
 * @author: Vladyslav Semerenko <vladyslav.semerenko@gmail.com>
 */
class AddUserForm extends Form
{

    public function __construct($name = null)
    {
        parent::__construct('adduser');
        $this->setAttribute('method', 'post');
        $this->setInputFilter(new AddUserInputFilter());
        $this->add(array(
            'name' => 'security',
            'type' => 'Zend\Form\Element\Csrf',
        ));
        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'username',
            'type' => 'Text',
            'options' => array(
                'min' => 3,
                'max' => 25,
                'label' => 'Username',
            ),
            'attributes' => array(
                'class' => 'form-control',
            )
        ));
        $this->add(array(
            'name' => 'password',
            'type' => 'Password',
            'options' => array(
                'min' => 3,
                'max' => 25,
                'label' => 'Password',
            ),
            'attributes' => array(
                'class' => 'form-control',
            )
        ));
        $this->add(array(
            'name' => 'role',
            'type' => 'Select',
            'options' => array(
                'label' => 'Role',
                'value_options' => [
                    'guest' => 'Guest',
                    'admin' => 'Admin',
                ]
            ),
            'attributes' => array(
                'class' => 'form-control',
            )
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Add',
                'id' => 'submitbutton',
                'class' => 'btn btn-default'
            ),
        ));
    }
}
