<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\Project;
use App\Enum\ContractName;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prenom',
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('contract', ChoiceType::class, [
                'choices' => [
                    'CDI' => ContractName::PermanentContract,
                    'CDD' => ContractName::FixedTermContract,
                    'Freelance' => ContractName::Freelancer,
                ],
                'label' => 'Statut',
            ])
            ->add('startDate', null, [
                'widget' => 'single_text',
                'label' => 'Date d\'entrée',
            ])
            // ->add('role', ChoiceType::class, [
            //     'label' => 'role',
            // ] )
            // ->add('isActif')
            // ->add('project', EntityType::class, [
            //     'class' => Project::class,
            //     'choice_label' => 'id',
            //     'multiple' => true,
            // ])
            // ->add('password', PasswordType::class, [
            //     'label' => 'Mot de passe',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employee::class,
        ]);
    }
}
