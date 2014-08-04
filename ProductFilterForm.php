<?php

class ProductFilterForm
{
    public function getSelectData()
    {
        return array(
            'city_sel'          => array('All Cities') + City::lists('id', 'city_name'),
            'company_sel'       => array('All Companies') + Company::lists('id', 'company_name'),
            'country_sel'       => array('All Countries') + Country::lists('id', 'country_name'),
            'state_sel'         => array('All States') + State::lists('id', 'state_name'),
            'property_type_sel' => array('All Property Types') + PropertyTypes::lists('id', 'description'),
            'boxdes_sel'        => array('All Box Designations') + BoxDesignations::lists('id', 'code')
        );
    }
}
