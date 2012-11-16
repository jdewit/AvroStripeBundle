<?php
namespace Avro\StripeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CouponFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'Name',
                'attr' => array(
                    'title' => 'Enter the name'
                )
            ))
            ->add('percentOff', 'number', array(
                'label' => 'Percent Off',
                'precision' => 0,
                'attr' => array(
                    'title' => 'Enter the percent off'
                )
            ))
            ->add('duration', 'choice', array(
                'label' => 'Duration',
                'choices' => array('forever' => 'Forever', 'once' => 'Once', 'repeating' => 'Multi-month'),
                'attr' => array(
                    'title' => 'Enter the duration'
                )
            ))
            ->add('durationInMonths', 'number', array(
                'label' => 'Duration (months)',
                'required' => false,
                'attr' => array(
                    'title' => 'Enter the duration in months'
                )
            ))
            ->add('maxRedemptions', 'number', array(
                'label' => 'Max Redemptions',
                'required' => false,
                'attr' => array(
                    'title' => 'Enter the maximum redemptions'
                )
            ))
            ->add('redeemBy', 'date', array(
                'label' => 'Redeem By',
                'widget' => 'single_text',
                'required' => false,
                'attr' => array(
                    'title' => 'Enter the expiry date',
                )
            ))

        ;

    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
        ));
    }

    public function getName()
    {
        return 'avro_stripe_coupon';
    }
}
