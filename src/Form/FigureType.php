<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form;

use App\Entity\Figure;
use App\Entity\Style;
use App\Form\Type\CategoriesInputType;
use App\Form\Type\DateTimePickerType;
use App\Form\Type\StylesInputType;
use App\Form\Type\TagsInputType;
use App\Form\Type\VideosInputType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class FigureType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // For the full reference of options defined by each form field type
        // see https://symfony.com/doc/current/reference/forms/types.html

        // By default, form fields include the 'required' attribute, which enables
        // the client-side form validation. This means that you can't test the
        // server-side validation errors from the browser. To temporarily disable
        // this validation, set the 'required' attribute to 'false':
        // $builder->add('title', null, ['required' => false, ...]);

        $builder


            ->add('title', null, [
                'attr' => ['autofocus' => true],
                'label' => 'label.figure.title',
            ])
            ->add('image', ImageType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.figure.image',
            ])
            ->add('videos', VideosInputType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.figure.videos',
            ])

            ->add('category', EntityType::class, array(
                'class'        => 'App:Category',
                'choice_label' => 'title',
                'multiple'     => false,
                'label' => 'label.figure.category',
                'required' => false,
            ))


            ->add('style', EntityType::class, array(
                'class'        => 'App:Style',
                'choice_label' => 'title',
                'multiple'     => false,
                'label' => 'label.figure.style',
                'required' => false,
            ))

            ->add('content', null, [
                'attr' => ['rows' => 20],
                'label' => 'label.figure.content',
            ])
            ->add('publishedAt', DateTimePickerType::class, [
                'label' => 'label.figure.published_at',
            ])

        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Figure::class,
        ]);
    }
}
