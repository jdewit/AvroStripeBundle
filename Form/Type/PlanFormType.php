<?php
namespace Avro\StripeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PlanFormType extends AbstractType
{
    public function __construct($class)
    {
        $this->class = $class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'Name',
                'attr' => array(
                    'title' => 'Enter the name'
                )
            ))
            ->add('id', 'text', array(
                'label' => 'Id',
                'attr' => array(
                    'title' => 'Enter the id'
                )
            ))
            ->add('amount', 'text', array(
                'label' => 'Amount',
                'attr' => array(
                    'title' => 'Enter the amount'
                )
            ))
            ->add('interval', 'choice', array(
                'label' => 'Interval',
                'choices' => array('month' => 'Monthly', 'year' => 'Yearly'),
                'attr' => array(
                    'title' => 'Select the interval'
                )
            ))
            ->add('currency', 'choice', array(
                'label' => 'Currency',
                'choices' => array('CAD' => 'CAD', 'USD' => 'USD'),
                'attr' => array(
                    'title' => 'Select the currency'
                )
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
        ));
    }

    public function getName()
    {
        return 'avro_stripe_plan';
    }
}
