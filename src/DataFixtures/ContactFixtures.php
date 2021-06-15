<?php
/**
 * Contact fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Contact;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class ContactFixtures.
 */
class ContactFixtures extends AbstractBaseFixture implements DependentFixtureInterface
{
    /**
     * Load data.
     *
     * @param ObjectManager $manager Persistence object manager
     */
    public function loadData(ObjectManager $manager): void
    {
        /** @var User $user */
        $user = $this->getReference(sprintf('%s_%d', UserFixtures::SIMPLE_USER_REFERENCE, 0));

        $this->createMany(50, Contact::class, function () use ($user) {
            $contact = new Contact();
            $contact->setFirstName($this->faker->firstName);
            $contact->setLastName($this->faker->unique()->lastName);
            $contact->setAddress($this->faker->unique()->address);
            $contact->setPhone($this->faker->unique()->phoneNumber);
            $contact->setUser($user);

            return $contact;
        });

        $manager->flush();
    }

    /**
     * Get dependencies to load it first.
     *
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
