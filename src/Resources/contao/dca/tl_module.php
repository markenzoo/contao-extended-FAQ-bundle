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

// Make changing faq_categories submit, so the faq_questions get generated correctly
$GLOBALS['TL_DCA']['tl_module']['fields']['faq_categories']['eval']['submitOnChange'] = true;

// Add palettes to tl_module
$GLOBALS['TL_DCA']['tl_module']['palettes']['faqteaser'] = str_replace('faq_readerModule;', 'faq_questions,jumpTo,faq_readerModule;', $GLOBALS['TL_DCA']['tl_module']['palettes']['faqlist']);
$GLOBALS['TL_DCA']['tl_module']['palettes']['faqselection'] = str_replace('faq_categories;', 'faq_categories,faq_questions,jumpTo;', $GLOBALS['TL_DCA']['tl_module']['palettes']['faqpage']);

// Add fields to tl_module
$GLOBALS['TL_DCA']['tl_module']['fields']['faq_questions'] = [
    'exclude' => true,
    'inputType' => 'checkboxWizard',
    'foreignKey' => 'tl_faq.question',
    'options_callback' => ['tl_module_faq_extended', 'getQuestions'],
    'eval' => ['multiple' => true, 'mandatory' => true],
    'sql' => 'blob NULL',
];

/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @author Felix Kästner <https://felix-kaestner.com>
 */
class tl_module_faq_extended extends Backend
{
    /**
     * Get all FAQ questions from all selected categories of the DataContainer.
     *
     * @param Contao\DataContainer $dc
     *
     * @return array
     */
    public function getQuestions(Contao\DataContainer $dc): array
    {
        $categories = Contao\StringUtil::deserialize($dc->activeRecord->faq_categories);
        $arrQuestions = [];
        $objQuestions = $this->Database->execute('SELECT f.id, f.question, c.title AS category FROM tl_faq f LEFT JOIN tl_faq_category c ON f.pid=c.id WHERE f.pid IN ('.implode(',', array_map('\intval', $categories)).')');

        while ($objQuestions->next()) {
            $arrQuestions[$objQuestions->id] = \count($categories) > 1 ? $objQuestions->question.' ['.$objQuestions->category.']' : $objQuestions->question;
        }

        return $arrQuestions;
    }
}
