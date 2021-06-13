<?php
/**
 * Contact type.
 */

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ContactType.
 */
class ContactType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'required' => true,
                'label' => 'form.label.first_name',
                'attr' => ['max_length' => 50],
            ])
            ->add('lastName', TextType::class, [
                'required' => true,
                'label' => 'form.label.last_name',
                'attr' => ['max_length' => 50],
            ])
            ->add('address', TextType::class, [
                'required' => true,
                'label' => 'form.label.address',
                'attr' => ['max_length' => 255],
            ])
            ->add('phone', TelType::class, [
                'required' => true,
                'label' => 'form.label.phone',
                'attr' => ['max_length' => 32],
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix(): string
    {
        return 'contact';
    }
}
