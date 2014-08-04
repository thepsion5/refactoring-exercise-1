<?php

/**
 * Filter specifically for products
 */
class ProductFilterService extends EloquentFilterService
{
    protected $sessionKey = 'product_filter';

    protected $filterableFields = array(
        'boxdes_id',
        'company_id',
        'city_id',
        'country_id',
        'property_type',
        'state_id',
        'name',
    );

    /**
     * Clears filters if the clear field is true, updates filters if
     * any are supplied, and loads them from the session otherwise
     *
     * @param array $input
     * @return $this
     */
    public function configureFromInput(array $input = array())
    {
        if (isset($input['clear']) && $input['clear']) {
            $this->clear();
        } elseif(count($input) > 0) {
            $this->setFilters($input);
        } else {
            $this->loadFromSession();
        }
        return $this;
    }

    public function filterByName(Builder $query, $value)
    {
        $query->where('name', 'LIKE', "%$value%");
    }
}
