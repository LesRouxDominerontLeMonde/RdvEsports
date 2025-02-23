<?php

namespace App\Form;

use App\Entity\Rdv;
use App\Entity\Cat;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Text;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\String\Slugger\AsciiSlugger;

class RdvType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'empty_data' => '',
            ])
            ->add('category', EntityType::class, [
                'class' => Cat::class,
                'choice_label' => 'title',
                'multiple' => true,
                'autocomplete' => true,
            ])
            ->add('content', TextType::class, [
                'empty_data' => '',
            ])
            ->add('place', TextType::class, [
                'empty_data' => '',
            ])
            ->add('date', null, [
                'widget' => 'single_text',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->autoSlug(...))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->attachTimestamps(...))
        ;
    }

    public function autoSlug(PreSubmitEvent $event): void
    {
        $data = $event->getData();
        if (empty($data['slug'])) {
            $slugger = new AsciiSlugger();
            $data['slug'] = strtolower($slugger->slug($data['title']));
            $event->setData($data);
        }
    }

    public function attachTimestamps(PostSubmitEvent $event): void
    {
        $data = $event->getData();
        if (!($data instanceof Rdv)) {
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
            'data_class' => Rdv::class,
        ]);
    }
}
