<?php

namespace App\Form;

use App\Entity\Cat;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Rdv;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'empty_data' => '',
            ])
            ->add('content', TextType::class, [
                'empty_data' => '',
            ])
            ->add('rdvs', EntityType::class, [
                'class' => Rdv::class,
                'choice_label' => 'title',
                'multiple' => true,
                'by_reference' => false,
                'expanded' => true,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, $this->attachtimestamps(...))
        ;
    }

    public function attachtimestamps(PostSubmitEvent $event): void
    {
        $data = $event->getData();
        if (!($data instanceof Cat)) {
            return;
        }
        if ($data->getCreatedAt() === null) {
            $data->setCreatedAt(new \DateTimeImmutable());
        }
        $data->setUpdatedAt(new \DateTimeImmutable());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cat::class,
        ]);
    }
}
