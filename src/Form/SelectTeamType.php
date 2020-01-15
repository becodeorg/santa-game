<?php

namespace App\Form;

use App\Entity\Team;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SelectTeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('team', EntityType::class, [
            'class' => Team::class,
            'choice_label' => 'name',
            'placeholder' => 'Select a team',
        ]);
        $builder->add('password', TextType::class, [
            'label' => false,
            'attr' => ['placeholder' => 'Password', 'class' => 'mt-2'],
        ]);
        $builder->add('submit', SubmitType::class, [
            'label' => 'Select team',
            'attr' => ['class' => 'my-3 btn btn-secondary'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
