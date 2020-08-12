<?php

namespace Markenzoo\ContaoExtendedFaqBundle;

use Contao\Collection;
use Contao\FaqModel;

/**
 * Class FaqModelExtended.
 *
 * @author Felix Kästner <https://felix-kaestner.com>
 */
class FaqModelExtended
{
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
    public static function findPublishedByIds($arrIds, array $arrOptions = [])
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

class_alias(FaqModelExtended::class, 'FaqModelExtended');