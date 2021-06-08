<?php
/**
 * LogIn type.
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LogInType.
 */
class LogInType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => 'form.label.email',
            ])
            ->add('password', PasswordType::class, [
                'required' => true,
                'label' => 'form.label.password',
            ])
            ->add('_remember_me', CheckboxType::class, [
                'required' => false,
                'data' => false,
                'label' => 'form.label.remember_me',
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_field_name' => '_csrf_token',
            'csrf_token_id' => 'authenticate',
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return '';
    }
}
