<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Tests\Functional\Bundle\CsrfFormLoginBundle\Form\UserLoginType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => 'label.register.fullName',
                'required' => true,
            ])

            ->add('email', EmailType::class, [
                'label' => 'label.register.email',
                'required' => true,
            ])

            ->add('username', TextType::class, [
                'label' => 'label.register.username',
                'required' => true,
            ])

            ->add('profilePictureFile')

            ->add('plainPassword', RepeatedType::class, array(
                'required' => true,
                'type' => PasswordType::class,
                'first_options'  => array('label' => 'label.register.password'),
                'second_options' => array('label' => 'label.register.plainPassword'),
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // uncomment if you want to bind to a class
            'data_class' => User::class,
        ]);
    }
}
