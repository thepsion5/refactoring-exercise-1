<?php
class ProductsController extends Controller
{
    public function index()
    {

        // companies
        $company_sel = array();
        $company_sel['all'] = "All companies";

        foreach(Company::all() as $company) {
            $company_sel[$company->id] = $company->company_name;
        }

        // cities
        $city_sel = array();
        $city_sel['all'] = "All cities";

        foreach(City::all() as $city) {
            $city_sel[$city->id] = $city->company_name;
        }

        // states
        $state_sel = array();
        $state_sel['all'] = "All states";

        foreach(State::all() as $state) {
            $state_sel[$state->id] = $state->state_name;
        }


        // countries
        $country_sel = array();
        $country_sel['all'] = "All countries";

        foreach(Country::all() as $country) {
            $country_sel[$country->id] = $country->country_name;
        }

        // box designations
        $boxdes_sel = array();
        $boxdes_sel['all'] = "All box designations";

        foreach(BoxDesignation::all() as $boxdes) {
            $boxdes_sel[$boxdes->id] = $boxdes->code;
        }

        // Property Types
        $prop_type_sel = array();
        $prop_type_sel['all'] = "All property types";

        foreach(PropertyType::all() as $prop_type) {
            $prop_type_sel[$prop_type->id] = $prop_type->description;
        }


        if (Input::get('clear'))
        {
            $text_match = '';
            Session::forget('text_match_sess_var');

            $company = 'all';
            Session::forget('company_match_sess_var');

            $city = 'all';
            Session::forget('city_match_sess_var');

            $state = 'all';
            Session::forget('state_match_sess_var');

            $country = 'all';
            Session::forget('country_match_sess_var');

            $boxdes = 'all';
            Session::forget('boxdes_match_sess_var');

            $property_type = 'all';
            Session::forget('property_type_match_sess_var');
        }
        else
        {
            $text_match = Input::get('text_match');
            if (Session::has('text_match_sess_var') && !$text_match)
            {
                $text_match = Session::get('text_match_sess_var');
            } else {
                Session::put('text_match_sess_var', $text_match);
            }

            $company = Input::get('company');
            if (Session::has('company_match_sess_var') && !$company )
            {
                $company = Session::get('company_match_sess_var');
            } else {
                Session::put('company_match_sess_var', $company);
            }
            $company = $company ? $company : 'all';

            $city = Input::get('city');
            if (Session::has('city_match_sess_var') && !$city )
            {
                $city = Session::get('city_match_sess_var');
            } else {
                Session::put('city_match_sess_var', $city);
            }
            $city = $city ? $city : 'all';

            $state = Input::get('state');
            if (Session::has('state_match_sess_var') && !$state )
            {
                $state = Session::get('state_match_sess_var');
            } else {
                Session::put('state_match_sess_var', $state);
            }
            $state = $state ? $state : 'all';

            $country = Input::get('country');
            if (Session::has('country_match_sess_var') && !$country )
            {
                $country = Session::get('country_match_sess_var');
            } else {
                Session::put('country_match_sess_var', $country);
            }
            $country = $country ? $country : 'all';

            $boxdes = Input::get('boxdes');
            if (Session::has('boxdes_match_sess_var') && !$boxdes )
            {
                $boxdes = Session::get('boxdes_match_sess_var');
            } else {
                Session::put('boxdes_match_sess_var', $boxdes);
            }
            $boxdes = $boxdes ? $boxdes : 'all';

            $property_type = Input::get('property_type');
            if (Session::has('property_type_match_sess_var') && !$property_type )
            {
                $property_type = Session::get('property_type_match_sess_var');
            } else {
                Session::put('property_type_match_sess_var', $property_type);
            }
            $property_type = $property_type ? $property_type : 'all';
        }

        $query = Property::with('country')
            ->with('state')
            ->with('city')
            ->with('company')
            ->with('property_type')
            ->with('box_designation')
            ->with('map')
            ->with('details')
            ->with('amenities')
        ;
        if ($text_match)
        {
            $query->where('name', 'like', '%'. $text_match .'%');
        }
        if ($company != 'all')
        {
            $query->where('company_id', '=', $company);
        }
        if ($city != 'all')
        {
            $query->where('city_id', '=', $city);
        }
        if ($state != 'all')
        {
            $query->where('state_id', '=', $state);
        }
        if ($country != 'all')
        {
            $query->where('country_id', '=', $country);
        }
        if ($boxdes != 'all')
        {
            $query->where('boxdes_id', '=', $boxdes);
        }
        if ($property_type != 'all')
        {
            $query->where('property_type_id', '=', $property_type);
        }


        $query->orderBy('name', 'ASC');

        return View::make('properties/index')
            ->with('company_sel', $company_sel)
            ->with('city_sel', $city_sel)
            ->with('state_sel', $state_sel)
            ->with('country_sel', $country_sel)
            ->with('boxdes_sel', $boxdes_sel)
            ->with('property_type_sel', $property_type_sel)
            ->with('text_match', $text_match)
            ->with('properties', $query->paginate(10) );
    }
}
