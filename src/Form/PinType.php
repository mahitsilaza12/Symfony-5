<?php

namespace App\Form;

use App\Entity\Pin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class PinType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imageFile', VichImageType::class, [
                'label'=>'sary',
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Supprimer',
                'download_label' => '...',
                'download_uri' => true,
                'image_uri' => true,
                'asset_helper' => true,
                // 'imagine_pattern'=>'squared_thumbnail_small'
            ])
            ->add('title')
            ->add('description')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pin::class,
        ]);
    }
}

//rah pdf s excel dia
//  public function buildForm(FormBuilderInterface $builder, array $options): void
//{
//    // ...
//
//    $builder->add('genericFile', VichFileType::class, [
//        'required' => false,
//        'allow_delete' => true,
//        'delete_label' => '...',
//        'download_uri' => '...',
//        'download_label' => '...',
//        'asset_helper' => true,
//    ]);
//}