<?php

use \Illuminate\Eloquent\Database\Model;

class ProductsServiceProvider
{
    $query                  = null;
    const ALL_ITEMS_STRING  = 'all';

    public function __construct(
        $CompanyRepository          = null,
        $CityRepository             = null,
        $StateRepository            = null,
        $CountryRepository          = null,
        $BoxDesignationRepository   = null,
        $PropertyTypeRepository     = null,
        Model $Property = null
    ){
        $this->CompanyRepository          = $CompanyRepository           ?: new Company();
        $this->CityRepository             = $CityRepository              ?: new City();
        $this->StateRepository            = $StateRepository             ?: new State();
        $this->CountryRepository          = $CountryRepository           ?: new Country();
        $this->BoxDesignationRepository   = $BoxDesignationRepository    ?: new BoxDesignation();
        $this->PropertyTypeRepository     = $PropertyTypeRepository      ?: new PropertyType();
        $this->Property                   = $Property          ?: new Property();   
    }

    /**
    *   Get all items from each repository in an array indexed by the item id. Add an 'All' index to each array.
    *
    *   @return An array containing arrays of each repository's items.
    **/
    public function getRepositoryViewData()
    {
        return array(
            'company_sel'       => 
                [ $this->ALL_ITEMS_STRING => 'All Companies' ] + $CompanyRepository->lists('company_name', 'id'),

            'city_sel'          => 
                [ $this->ALL_ITEMS_STRING => 'All Cities' ] + $CityRepository->lists('company_name', 'id'),

            'state_sel'         => 
                [$this->ALL_ITEMS_STRING => 'All States'] + $StateRepository->lists('state_name', 'id'),

            'country_sel'       => 
                [$this->ALL_ITEMS_STRING => 'All Countries'] + $CountryRepository->lists('country_name', 'id'),

            'boxdes_sel'        => 
                [$this->ALL_ITEMS_STRING  => 'All Box Designations'] + $BoxDesignationRepository->lists('code', 'id'),

            'property_type_sel' => 
                [$this->ALL_ITEMS_STRING => 'All Property Types'] + $PropertyTypeRepository->all('description' ,'id'),
        );
    }

    /**
    *   Return a built query from a search string and use input or session for conditions
    *   @param $text_match The search string
    *   @return 
    **/
    public function getQueryFromSession($text_match)
    {              
        return $this->makeQuery($text_match, true);  
    }

    /**
    *   Return a built query from a search string *without* looking into the session or input.
    *   @param $text_match The search string
    *   @return 
    **/
    public function getQuery($text_match)
    {
        return $this->makeQuery($text_match);
    }

    /**
    *   Clear specific session keys
    *   @return void
    **/
    public function clearSession()
    {
        $keysToClear = array(
            'text_match_sess_var',
            'company_match_sess_var',
            'city_match_sess_var',
            'state_match_sess_var',
            'country_match_sess_var',
            'boxdes_match_sess_var',
            'property_type_match_sess_var',
        );
        foreach($keysToClear as $key)
            Session::forget($key);        
    }

    /**
    *   Get the 'text_match' from Input or Session. Update the session if in Input
    *   @return string The 'text_match'
    **/
    public function getTextMatch()
    {
        return $this->getValueFromInputNameOrSessionKeyAndMaybeUpdateSession('text_match', 'text_match_sess_var');
    }    

    /** 
    *   Make a query from a search string, optionally use session or input data as conditions
    *   @param $text_match The search string
    *   @param $useInput On true use session or input data as conditions
    *
    *   @return The built query.
    **/
    private function makeQuery($text_match, $useInput = false)
    {
        $query                  = $this->getPropertyWithEagerLoading();
        $query                  = $this->buildQueryConditionFromTextMatch($text_match, $query);
        if($useInput)
            $query            = $this->buildQueryConditionsFromInput($query);        

        return $query;
    }    

    /** 
    *   Eager load the property and return it.
    *   @return The eager loaded property.
    **/
    private function getPropertyWithEagerLoading()
    {
        return $this->Property->with([
            'country'
            'state'
            'city'
            'company'
            'property_type'
            'box_designation'
            'map'
            'details'
            'amenities'
        ]);
    }

    /**
    *   Add the text_match search string onto the query condition
    *   @param $text_match The search string
    *   @param $query The query to build upon
    *
    *   @return $query
    **/
    private function buildQueryConditionFromTextMatch(
        $text_match = null, 
        Model $query
    )
    {
        if($text_match)
            $query = $query->where('name', 'like', "%$text_match%");

        return $query;
    }

    /**
    *   Build the query with conidtions from input if they don't match ALL_ITEMS_STRING
    *   @param $query The query to build upon
    *   @return The built query
    **/
    private function buildQueryConditionsFromInput(Model $query)
    {
        $inputAndTableNames = array(
            [
                'input_name'    =>  'company',
                'session_key'   =>  'company_match_sess_var',
                'table_name'    =>  'company_id',
            ],
            [
                'input_name'    =>  'city',
                'session_key'   =>  'city_match_sess_var',
                'table_name'    =>  'city_id',
            ],
            [
                'input_name'    =>  'state',
                'session_key'   =>  'state_match_sess_var'.
                'table_name'    =>  'state_id',
            ],
            [
                'input_name'    =>  'country',
                'session_key'   =>  'country_match_sess_var',
                'table_name'    =>  'country_id',
            ],
            [
                'input_name'    =>  'boxdes',
                'session_key'   =>  'boxdes_match_sess_var',
                'table_name'    =>  'boxdes_id',
            ],
            [
                'input_name'    =>  'property_type',
                'session_key'   =>  'property_type_match_sess_var',
                'table_name'    =>  'property_type_id',
            ],
        );

        foreach($inputAndTableNames as $entity)
        {
            $value      = $this->getValueFromInputNameOrSessionKeyAndMaybeUpdateSession($entity['input_name'], $entity['session_key']);
            if($value != $this->ALL_ITEMS_STRING)
                $query = $query->where($entity['table_name'], $value);
        }

        return $query;
    }

    /**
    *   Get a value from input or session. If retrieved from input, update the session
    *   @param $inputname The name of the key in Input
    *   @param $sessionkey The name of the key to retrieve or update from Session
    *   @return The value if found, null if not
    **/
    private function getValueFromInputNameOrSessionKeyAndMaybeUpdateSession($inputname, $sessionkey)
    {
        if(Input::has($inputname) && Input::get($inputname)){
            $value = Input::get($inputname);
            Session::put($sessionkey, $value);
            return $value;
        }

        if(Session::has($sessionkey))
            return Session::get($sessionkey);

        // This makes Laravel's behaviour more implicit: Input or Session will return null if not found.
        return null;
    }
}