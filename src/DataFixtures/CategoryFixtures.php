<?php
/**
 * Category fixture.
 */

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;

/**
 * Class CategoryFixtures.
 */
class CategoryFixtures extends AbstractBaseFixture
{
    /**
     * Load data.
     *
     * @param \Doctrine\Persistence\ObjectManager $manager Object manager
     */
    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(10, Category::class, function () {
            $category = new Category();
            $category->setTitle($this->faker->unique()->words(3, true));
            $category->setCreatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            $category->setUpdatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));

            return $category;
        });

        $manager->flush();
    }
}
