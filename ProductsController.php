<?php
class ProductsController extends Controller
{

    private function select($first, $array, $key)
    {
        $select = array();
        $select['all'] = $first
        foreach($array as $element) {
            $select[$element->id] = $element->[$key];
        }
    }

    public function index()
    {

        $company_sel    = $this->select("All companies",        Company::all(),         'company_name');
        $city_sel       = $this->select("All cities",           City::all(),            'city_name');
        $state_sel      = $this->select("All states",           State::all(),           'state_name');
        $country_sel    = $this->select("All countries",        Country::all(),         'country_name');
        $boxdes_sel     = $this->select("All box designations", BoxDesignation::all(),  'code');
        $prop_type_sel  = $this->select("All property types",   PropertyType::all(),    'description');

        if (Input::get('clear'))
        {
            Session::flush();
        }
        else
        {
            Input::flash();

            $text_match     = Session::get('text_match');
            $company        = Session::get('company');
            $city           = Session::get('city');
            $state          = Session::get('state');
            $country        = Session::get('country');
            $boxdes         = Session::get('boxdes');
            $property_type  = Session::get('property_type');
        }

        $query = Property::with('country')
            ->with('state')
            ->with('city')
            ->with('company')
            ->with('property_type')
            ->with('box_designation')
            ->with('map')
            ->with('details')
            ->with('amenities');

        if (!is_null($text_match))
        {
            $query->where('name', 'like', '%'. $text_match .'%');
        }
        if (!is_null($company))
        {
            $query->where('company_id', '=', $company);
        }
        if (!is_null($city))
        {
            $query->where('city_id', '=', $city);
        }
        if (!is_null($state))
        {
            $query->where('state_id', '=', $state);
        }
        if (!is_null($country))
        {
            $query->where('country_id', '=', $country);
        }
        if (!is_null($boxdes))
        {
            $query->where('boxdes_id', '=', $boxdes);
        }
        if (!is_null($property_type))
        {
            $query->where('property_type_id', '=', $property_type);
        }


        $query->orderBy('name', 'ASC');

        return View::make('properties/index')->with(array(
            'company_sel'       => $company_sel,
            'city_sel'          => $city_sel,
            'state_sel'         => $state_sel,
            'country_sel'       => $country_sel,
            'boxdes_sel'        => $boxdes_sel,
            'property_type_sel' => $property_type_sel,
            'text_match'        => $text_match,
            'properties'        => $query->paginate(10) ));
    }
}
