<?php

declare(strict_types=1);

/*
 * This file is part of markenzoo/contao-extended-faq-bundle.
 *
 * Copyright (c) Felix Kästner
 *
 * @package   markenzoo/contao-extended-faq-bundle
 * @author    Felix Kästner <hello@felix-kaestner.com>
 * @copyright 2020 Felix Kästner
 * @license   https://github.com/markenzoo/contao-extended-faq-bundle/blob/master/LICENSE LGPL-3.0-or-later
 */

namespace Markenzoo\ContaoExtendedFaqBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\FaqBundle\ContaoFaqBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Markenzoo\ContaoExtendedFaqBundle\ContaoExtendedFaqBundle;

class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(ContaoExtendedFaqBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class, ContaoFaqBundle::class]),
        ];
    }
}
