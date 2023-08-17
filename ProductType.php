<?php

namespace App\Form;

use App\Entity\Brand;
use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,
            [
                'label'=>"Product name",
                'required'=> true,
                'attr' =>
                [
                    'minlength' => 5,
                    'maxlength' => 50
                ]
            ])
            ->add('price', MoneyType::class,
            [
                'required'=> true,
                'currency'=>'USD',
                'attr' =>
                [
                    'min' => 0
                ]
            ])
            ->add('image',FileType::class,
            [
                'data_class' => null,
                'required' => is_null($builder->getData()->getImage()),

            ])
            ->add('description')
            ->add('date', DateType::class,
            [
                'widget' => 'single_text',
                'attr' =>
                [
                    'max' => date("Y-m-d")
                ]
            ])
            ->add('quantity', IntegerType::class,
            [
                'attr' =>
                [
                    'min' => 0,
                    'max' => 1000
                ]
            ])
            ->add('category', EntityType::class,
            [
                'class' => Category::class,
                'choice_label' => 'name',
                'expanded' => true 
            ])
            ->add('brand', EntityType::class,
            [
                'class' => Brand::class,
                'choice_label' => 'name',
            ])
            ->add('Submit', SubmitType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
