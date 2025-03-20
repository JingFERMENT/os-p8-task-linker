<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\Project;
<<<<<<< HEAD
use App\Entity\Statut;
=======
>>>>>>> 2d46599397054f3da280fe39ea3a8d7fa9da37d5
use App\Entity\Tag;
use App\Entity\Task;
use App\Enum\StatutName;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la tâche',
<<<<<<< HEAD
                'required' => true,

            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
=======
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
>>>>>>> 2d46599397054f3da280fe39ea3a8d7fa9da37d5
            ])
            ->add('deadline', null, [
                'label' => 'Date',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker', 'placeholder' => 'jj/mm/aaaa'],
<<<<<<< HEAD
                'required' => false,
=======
>>>>>>> 2d46599397054f3da280fe39ea3a8d7fa9da37d5
            ])
            ->add('employee', EntityType::class, [
                'class' => Employee::class,
                'choice_label' => 'firstname',
                'label' => 'Membre',
<<<<<<< HEAD
                'required' => false,
            ])
            ->add('statut', EntityType::class, [
                'class' => Statut::class,
                'choice_label' => function ($statut) {
                    return $statut->getStatutName()->getLabel();}, // statutName is an Enum so we need to get the label
                'label' => 'Statut',
                'required' => true,
=======
            ])
            ->add('statut', ChoiceType::class, [
                'choices' =>
                [
                    'To do' => StatutName::ToDo,
                    'Doing' => StatutName::Doing,
                    'Done' => StatutName::Done,
                ],
                'label' => 'Statut',
>>>>>>> 2d46599397054f3da280fe39ea3a8d7fa9da37d5
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
