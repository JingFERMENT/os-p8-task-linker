<?php 

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class Enable2FAType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // add in french 
        $builder->add('enable', SubmitType::class, [
            'label' => 'Activer l’authentification à deux facteurs',
            'attr' => [
                'class' => 'button button-submit',
            ],
    ]);
    }
}