<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\Project;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Titre du projet',
            ])
            // ->add('createdAt', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('deadline', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('isArchived', CheckboxType::class, [
            //     'required' => false,  // Allows the value to be null
            //     'label' => 'Archived',
            // ])
            ->add('employees', EntityType::class, [
                'class' => Employee::class,
                'label' => 'Inviter des membres',
                'choice_label' => function ($employee) {
                    return $employee->getFirstName() . ' ' . $employee->getLastName();
                },
                'multiple' => true,
                'required' => false, // <--- This allows the field to be empty
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
