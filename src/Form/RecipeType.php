<?php

namespace App\Form;

use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'empty_data' => ''
            ])
            ->add('slug', TextType::class, [
                'required' => false,
                // Contraintes au niveau du formulaire, mais bonne pratique de le faire direct sur l'entité
                // 'constraints' => [
                //     new Length(min: 10),
                //     new Regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', message: "Ceci n'est pas un slug valide.")
                // ]
            ])
            ->add('content', TextareaType::class, [
                'empty_data' => ''
            ])
            ->add('duration')
            ->add('save', SubmitType::class, [
                'label' => 'Envoyer'
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->autoSlug(...));
        ;
    }

    public function autoSlug(PreSubmitEvent $event): void
    {
        $data = $event->getData();
        if(empty($data['slug'])){
            $slugger = new AsciiSlugger();
            $data['slug'] = strtolower($slugger -> slug($data['title']));
            $event->setData($data);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
            'validation_groups' => ['Default','Extra']
        ]);
    }
}
