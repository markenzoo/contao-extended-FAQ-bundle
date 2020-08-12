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
use Contao\ModuleFaqList;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use Patchwork\Utf8;
use Markenzoo\ContaoExtendedFaqBundle\FaqModelExtended;

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

        $objFaq = FaqModelExtended::findPublishedByIds($this->faq_questions);

        // START: Copied from source 
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
        // END: Copied from source 
        
        // Generate link to FAQ Page
        $jumpTo = (int) $this->jumpTo;
        $this->Template->addLink = false;
        if ($jumpTo > 0 && null !== ($objTarget = PageModel::findByPk($jumpTo))) {
            /* @var PageModel $objTarget */
            $this->Template->href = $objTarget->getFrontendUrl();
            $this->Template->linkTitle = $objTarget->title;
            
            System::loadLanguageFile('tl_faq');
            $this->Template->link = $GLOBALS['TL_LANG']['tl_faq']['more'];
            $this->Template->addLink = true;
        }
    }
    
}

class_alias(ModuleFaqTeaser::class, 'ModuleFaqTeaser');
