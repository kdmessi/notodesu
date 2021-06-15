<?php
/**
 * Event type.
 */

namespace App\Form;

use App\Entity\Category;
use App\Entity\Contact;
use App\Entity\Event;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class EventType.
 */
class EventType extends AbstractType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'form.label.title',
                    'required' => true,
                    'attr' => ['max_length' => 255],
                ]
            )
            ->add(
                'location',
                TextType::class,
                [
                    'label' => 'form.label.location',
                    'required' => true,
                    'attr' => ['max_length' => 255],
                ]
            )
            ->add(
                'date',
                DateTimeType::class,
                [
                    'label' => 'form.label.date',
                    'required' => true,
                ]
            )
            ->add(
                'category',
                EntityType::class,
                [
                    'class' => Category::class,
                    'choice_label' => function ($category) {
                        return $category->getTitle();
                    },
                    'label' => 'form.label.category',
                    'placeholder' => 'form.label.none_select',
                    'required' => true,
                ]
            )
            ->add(
                'contact',
                EntityType::class,
                [
                    'class' => Contact::class,
                    'choice_label' => function (Contact $contact) {
                        return $contact->getFirstName().' '.$contact->getLastName();
                    },
                    'label' => 'form.label.contact',
                    'placeholder' => 'form.label.none_select',
                    'required' => true,
                    'multiple' => true,
                ]
            )
        ;
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Event::class]);
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
        return 'event';
    }
}
