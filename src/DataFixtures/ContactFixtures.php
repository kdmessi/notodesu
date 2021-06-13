<?php
/**
 * Contact fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Contact;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;

/**
 * Class ContactFixtures.
 */
class ContactFixtures extends AbstractBaseFixture
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

        $this->createMany(50, 'contacts', function () use ($user) {
            $contact = new Contact();
            $contact->setFirstName($this->faker->word);
            $contact->setLastName($this->faker->word);
            $contact->setAddress($this->faker->address);
            $contact->setPhone($this->faker->phoneNumber);
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

    /**
     * Get order.
     *
     * @return int
     */
    public function getOrder(): int
    {
        return 1;
    }
}
