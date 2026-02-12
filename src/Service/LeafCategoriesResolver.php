<?php declare(strict_types=1);

namespace AlphaFoundation\Service;

use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class LeafCategoriesResolver
{
    /**
     * Liefert alle "tiefsten" Kategorien des Produkts im Navigationsbaum des aktuellen Sales-Channels.
     * Strategie:
     * 1) Betrachte nur Kategorien unterhalb der Navigation-Root des Sales-Channels.
     * 2) Bestimme das maximale Level der zugeordneten Kategorien.
     * 3) Nimm alle Kategorien mit diesem Level.
     * 4) Bevorzuge "echte" Blätter (childCount == 0); falls vorhanden, filtere auf diese.
     */
    public function resolveLeafCategoryIds(ProductEntity $product, SalesChannelContext $salesChannelContext): array
    {
        $navRootId = $salesChannelContext->getSalesChannel()->getNavigationCategoryId();
        if (!$navRootId) {
            return [];
        }

        $categories = $product->getCategoriesRo() ?? $product->getCategories();
        if (!$categories || $categories->count() === 0) {
            return [];
        }

        $inTree = [];
        foreach ($categories as $cat) {
            $path = (string) $cat->getPath();
            if ($cat->getId() !== $navRootId && !str_contains($path, '|' . $navRootId . '|')) {
                continue;
            }
            $inTree[] = $cat;
        }

        if (!$inTree) {
            return [];
        }

        $maxLevel = max(array_map(static fn (CategoryEntity $c) => (int) $c->getLevel(), $inTree));
        $deepest = array_filter($inTree, static fn (CategoryEntity $c) => (int) $c->getLevel() === $maxLevel);

        $hasLeaves = (bool) array_filter($deepest, static fn (CategoryEntity $c) => (int) $c->getChildCount() === 0);
        if ($hasLeaves) {
            $deepest = array_filter($deepest, static fn (CategoryEntity $c) => (int) $c->getChildCount() === 0);
        }

        return array_values(array_unique(array_map(static fn (CategoryEntity $c) => $c->getId(), $deepest)));
    }
}
