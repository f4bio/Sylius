<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ProductBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Pagerfanta;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Product\Repository\ProductOptionRepositoryInterface;
use Sylius\Component\Search\Model\SearchQueryInterface;

class ProductOptionRepository extends EntityRepository implements ProductOptionRepositoryInterface
{
    public function createListQueryBuilder(string $locale): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.translations', 'translation')
            ->andWhere('translation.locale = :locale')
            ->setParameter('locale', $locale)
        ;
    }

    public function findByName(string $name, string $locale): array
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.translations', 'translation')
            ->andWhere('translation.name = :name')
            ->andWhere('translation.locale = :locale')
            ->setParameter('name', $name)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getResult()
        ;
    }

    public function searchWithoutTerms(): Pagerfanta
    {
        $queryBuilder = $this->createQueryBuilder('o');

        return $this->getPaginator($queryBuilder);
    }

    public function searchByTerms(SearchQueryInterface $query): Pagerfanta
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->where('MATCH_AGAINST(translation.name, :terms) > 0.2')
            ->orWhere('translation.name LIKE :likeTerms')
            ->setParameters([
                'locale' => $query->getLocaleCode(),
                'terms' => $query->getTerms(),
                'likeTerms' => sprintf('%%%s%%', str_replace(' ', '%', $query->getTerms())),
            ])
            ->orderBy('MATCH_AGAINST(translation.name, :terms \'IN BOOLEAN MODE\')', 'DESC')
        ;

        return $this->getPaginator($queryBuilder);
    }
}
