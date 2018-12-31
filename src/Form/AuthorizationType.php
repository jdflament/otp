<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Jean-David Flament <flamentjeandavid@yahoo.fr>
 * @author Thomas Debacker <dbkr.thomas@gmail.com>
 */
class AuthorizationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, [
                'required' => true,
                'attr'     => [
                    'placeholder' => 'Saisissez votre code de validation'
                ]
            ])
        ;
    }
}
