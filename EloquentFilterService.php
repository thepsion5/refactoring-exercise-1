<?php

use Illuminate\Database\Eloquent\Builder,
    Illuminate\Session\Store;

/**
 * Stores current filter sessings in the session and allows
 * for custom filter functions when a simple match is
 * not sufficient
 */
class EloquentFilterService
{
    protected $sessionKey = 'eloquent_filter';

    protected $filters = array();

    protected $filterableFields = array();

    public function __construct(Store $session)
    {
        $this->session = $session;
    }

    /**
     * Sets the fields to filter by
     *
     * @param array $fields
     * @return $this
     */
    public function setFilters(array $fields)
    {
        foreach($fields as $key => $value) {
            $this->addFilter($key, $value);
        }
        return $this;
    }

    /**
     * Adds a field to filter by
     *
     * @param string $field
     * @param string $value
     * @return $this
     */
    public function addFilter($field, $value)
    {
        if(in_array($field, $this->filterableFields) && $value != '') {
            $this->filters[$field] = $value;
        }
        return $this;
    }

    /**
     * Removes a field to filter by
     *
     * @param $field
     * @return $this
     */
    public function clearFilter($field)
    {
        unset($this->filters[$field]);
        return $this;
    }

    /**
     * Removes all filters
     *
     * @return $this
     */
    public function clear()
    {
        $this->filters = array();
        return $this;
    }

    /**
     * Loads previously-saved filters from the session
     *
     * @return $this
     */
    public function loadFromSession()
    {
        $this->filters = $this->session->get($this->sessionKey, array());
        return $this;
    }

    /**
     * Applies the current filter settings to a query
     *
     * @param Builder $query
     * @param bool $saveToSession
     * @return $this
     */
    public function apply(Builder $query, $saveToSession = true)
    {
        foreach($this->filters as $field => $value) {
            $filterMethod = 'filterBy' . camel_case($field);
            if(method_exists($this, $filterMethod)) {
                $this->$filterMethod($query, $value);
            } else {
                $query->where($field, $value);
            }
        }
        if($saveToSession) {
            $this->saveToSession();
        }
        return $this;
    }

    /**
     * Saves the current filter settings to the session
     *
     * @return $this
     */
    public function saveToSession()
    {
        $this->session->put($this->sessionKey, $this->filters);

        return $this;
    }

}
