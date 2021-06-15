<?php
/**
 * Event fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Contact;
use App\Entity\Event;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class EventFixtures.
 */
class EventFixtures extends AbstractBaseFixture implements DependentFixtureInterface
{
    /**
     * Load data.
     *
     * @param ObjectManager $manager Persistence object manager
     */
    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(1000, 'events', function () {
            $event = new Event();
            $event->setTitle($this->faker->sentence);
            $event->setLocation($this->faker->streetAddress);
            $event->setDate($this->faker->dateTimeBetween('-100 days', '-1 days'));
            /** @var Category $category */
            $category = $this->getRandomReference(Category::class);
            $event->setCategory($category);

            /** @var Contact $contact */
            $contact = $this->getRandomReference(Contact::class);
            $event->addContact($contact);

            /** @var User $user */
            $user = $this->getRandomReference(UserFixtures::SIMPLE_USER_REFERENCE);
            $event->setUser($user);

            return $event;
        });

        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return array Array of dependencies
     */
    public function getDependencies(): array
    {
        return [CategoryFixtures::class, ContactFixtures::class];
    }
}
