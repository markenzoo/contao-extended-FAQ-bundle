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

namespace Markenzoo\ContaoExtendedFaqBundle;

use Contao\BackendTemplate;
use Contao\FaqCategoryModel;
use Contao\FaqModel;
use Contao\ModuleFaqList;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use Patchwork\Utf8;

/**
 * Class ModuleFaqTeaser.
 *
 * @property array $faq_categories
 * @property int   $faq_readerModule
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 * @author Felix Kästner <https://felix-kaestner.com>
 */
class ModuleFaqTeaser extends ModuleFaqList
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'mod_faqteaser';

    /**
     * Display a wildcard in the back end.
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE === 'BE') {
            $objTemplate = new BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### '.Utf8::strtoupper($GLOBALS['TL_LANG']['FMD']['faqteaser'][0]).' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id='.$this->id;

            return $objTemplate->parse();
        }

        return parent::generate();
    }

    /**
     * Generate the module.
     *
     * @see https://github.com/contao/contao/blob/4.9/faq-bundle/src/Resources/contao/modules/ModuleFaqList.php
     *
     * @return void
     */
    protected function compile(): void
    {
        $this->faq_questions = StringUtil::deserialize($this->faq_questions);

        $objFaq = static::findPublishedByIds($this->faq_questions);

        if (null === $objFaq) {
            $this->Template->faq = [];

            return;
        }

        $arrFaq = array_fill_keys($this->faq_categories, []);

        // Add FAQs
        while ($objFaq->next()) {
            $arrTemp = $objFaq->row();
            $arrTemp['title'] = StringUtil::specialchars($objFaq->question, true);
            $arrTemp['href'] = parent::generateFaqLink($objFaq);

            /** @var FaqCategoryModel $objPid */
            $objPid = $objFaq->getRelated('pid');

            $arrFaq[$objFaq->pid]['items'][] = $arrTemp;
            $arrFaq[$objFaq->pid]['headline'] = $objPid->headline;
            $arrFaq[$objFaq->pid]['title'] = $objPid->title;
        }

        $arrFaq = array_values(array_filter($arrFaq));

        $cat_count = 0;
        $cat_limit = \count($arrFaq);

        // Add classes
        foreach ($arrFaq as $k => $v) {
            $count = 0;
            $limit = \count($v['items']);

            for ($i = 0; $i < $limit; ++$i) {
                $arrFaq[$k]['items'][$i]['class'] = trim(((1 === ++$count) ? ' first' : '').(($count >= $limit) ? ' last' : '').((0 === ($count % 2)) ? ' odd' : ' even'));
            }

            $arrFaq[$k]['class'] = trim(((1 === ++$cat_count) ? ' first' : '').(($cat_count >= $cat_limit) ? ' last' : '').((0 === ($cat_count % 2)) ? ' odd' : ' even'));
        }

        $this->Template->faq = $arrFaq;

        $jumpTo = (int) $this->jumpTo;

        // Generate link to FAQ Page
        if ($jumpTo > 0 && null !== ($objTarget = PageModel::findByPk($jumpTo))) {
            /* @var PageModel $objTarget */
            $this->Template->href = $objTarget->getFrontendUrl();
            $this->Template->linkTitle = $objTarget->title;

            System::loadLanguageFile('tl_faq');
            $this->Template->link = $GLOBALS['TL_LANG']['tl_faq']['more'];
        }
    }

    /**
     * Find all published FAQs by their IDs.
     *
     * @param array $arrPids    An array of FAQ IDs
     * @param array $arrOptions An optional options array
     * @param mixed $arrIds
     *
     * @see https://github.com/contao/contao/blob/4.9/faq-bundle/src/Resources/contao/models/FaqModel.php#L183
     *
     * @return Collection|FaqModel[]|FaqModel|null A collection of models or null if there are no FAQs
     */
    protected static function findPublishedByIds($arrIds, array $arrOptions = [])
    {
        if (empty($arrIds) || !\is_array($arrIds)) {
            return null;
        }

        $t = 'tl_faq';
        $arrColumns = ["$t.id IN(".implode(',', array_map('\intval', $arrIds)).')'];

        if (!static::isPreviewMode($arrOptions)) {
            $arrColumns[] = "$t.published='1'";
        }

        if (!isset($arrOptions['order'])) {
            $arrOptions['order'] = "$t.id, $t.sorting";
        }

        return FaqModel::findBy($arrColumns, null, $arrOptions);
    }

    /**
     * Check if the preview mode is enabled.
     *
     * @param array $arrOptions The options array
     *
     * @see https://github.com/contao/contao/blob/4.9/core-bundle/src/Resources/contao/library/Contao/Model.php#L1276
     *
     * @return bool
     */
    protected static function isPreviewMode(array $arrOptions)
    {
        if (isset($arrOptions['ignoreFePreview'])) {
            return false;
        }

        return \defined('BE_USER_LOGGED_IN') && BE_USER_LOGGED_IN === true;
    }
}

class_alias(ModuleFaqTeaser::class, 'ModuleFaqTeaser');
