<?php

namespace AppBundle\Helper;

/**
 * Trait HelperTrait
 * @package AppBundle\Helper
 */
trait HelperTrait
{

    /**
     * Data sorteren bij datum.
     *
     * @param array $data
     * @return array
     */
    public function sortDataByDate(array $data)
    {
        usort($data, function($a, $b) {
            $a = strtotime($a['date']);
            $b = strtotime($b['date']);

            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        });

        return $data;
    }
}
