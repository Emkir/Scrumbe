<?php
namespace Scrumbe\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'email', array(
            'required' => true
        ));
        $builder->add('password', 'password', array(
            'required' => true
        ));
        $builder->add('firstname', 'text', array(
            'required' => true
        ));
        $builder->add('lastname', 'text', array(
            'required' => true
        ));
        $builder->add('avatar');
        $builder->add('domain', 'text', array());
        $builder->add('business', 'text', array());
        $builder->add('save', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Scrumbe\Models\User',
        ));
    }

    public function getName()
    {
        return 'user';
    }
} 