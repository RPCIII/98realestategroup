<?php

namespace Includes\Modules\MLS;

use GuzzleHttp\Client;

class QuickSearch
{
    protected $approvedParams;
    protected $searchRequested;
    protected $searchParams;
    protected $searchResults;

    /**
     * QuickSearch constructor.
     * configure any options here
     */
    public function __construct ()
    {
        $this->approvedParams = [
            'omni',
            'area',
            'status',
            'propertyType',
            'forclosure',
            'minPrice',
            'maxPrice',
            'beds',
            'baths',
            'sqft',
            'acreage',
            'waterfront',
            'waterview',
            'sort',
            'page'
        ];
        $this->searchParams = [];
        $this->searchResults = [];
        $this->searchRequested = (isset($_GET['q']) && $_GET['q'] == 'search' ? $_GET : null);

        if($this->searchRequested){
            $this->contactTheMothership();
        }

    }

    public function getSearchResults()
    {
        return $this->searchResults;
    }

    public function getResultMeta()
    {
        return isset($this->searchResults->meta) ? $this->searchResults->meta : null;
    }

    public function getCurrentRequest()
    {
        return json_encode($this->searchParams);
    }

    public function getSort()
    {
        return isset($this->searchRequested['sort']) ? $this->searchRequested['sort'] : 'price|desc';
    }

    public function filterRequest()
    {
        foreach($_GET as $key => $var){
            if(in_array($key, $this->approvedParams)){
                $this->searchParams[$key] = $var;
            }
        }
    }

    public function makeRequest()
    {
        $this->filterRequest();

        $request = '?q=search';
        foreach($this->searchParams as $key => $var){
            if(is_array($var)){
                $request .= '&' . $key . '=';
                $i = 1;
                foreach($var as $k => $v){
                    $request .= $v . ($i < count($var) ? '|' : '');
                    $i++;
                }
            }else{
                if($var != '' && $var != 'Any'){
                    $request .= '&' . $key . '=' . $var;
                }
            }
        }

        return $request;
    }

    public function contactTheMothership()
    {
        $client     = new Client(['base_uri' => 'https://rafgc.kerigan.com/api/v1/']);
        $apiCall = $client->request(
            'GET', 'search' . $this->makeRequest()
        );

        $this->searchResults = json_decode($apiCall->getBody());
    }
}