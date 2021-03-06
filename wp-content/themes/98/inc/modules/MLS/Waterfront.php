<?php

namespace Includes\Modules\MLS;

use GuzzleHttp\Client;

class Waterfront extends CuratedResults {

    public function __construct()
    {
        parent::set('endPoint', 'waterfront');
        parent::set('searchParams', [
            'sort'   => $this->getSort()
        ]);
    }

}