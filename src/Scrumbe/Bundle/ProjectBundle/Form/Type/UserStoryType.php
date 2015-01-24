<?php
namespace Scrumbe\Bundle\ProjectBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserStoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('project_id','hidden');
        $builder->add('numero');
        $builder->add('description');
        $builder->add('value');
        $builder->add('complexity');
        $builder->add('ratio');
        $builder->add('save','submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Scrumbe\Models\UserStory',
        ));
    }

    public function getName()
    {
        return 'user_story';
    }
} 