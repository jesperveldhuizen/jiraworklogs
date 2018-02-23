<?php

namespace AppBundle\Parser;

/**
 * Class JiraApiDataParser
 * @package AppBundle\Parser
 */
class JiraApiDataParser
{

    /**
     * Json data omzetten naar array.
     *
     * @param string $json
     * @return array
     */
    public function parse(string $json = '')
    {
        if (empty($json)) {
            return [];
        }

        $data = json_decode($json, true);
        if (isset($data['values'])) {
            $data = $data['values'];
        }

        return $data;
    }
}
