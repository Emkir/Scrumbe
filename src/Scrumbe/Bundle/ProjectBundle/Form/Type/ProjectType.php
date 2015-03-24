<?php
namespace Scrumbe\Bundle\ProjectBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
        $builder->add('description');
        $builder->add('cover_project','text',array('required' => false, 'mapped' => false));
        //$builder->add('own_cover','file',array('required' => false, 'mapped' => false));
        $builder->add('start_date');
        $builder->add('end_date');
        $builder->add('save','submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Scrumbe\Models\Project',
        ));
    }

    public function getName()
    {
        return 'project';
    }
} 