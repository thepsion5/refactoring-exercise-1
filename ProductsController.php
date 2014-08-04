<?php
class ProductsController extends Controller
{
    /**
     * @var ProductFilterService
     */
    protected $filter;

    /**
     * @var ProductFilterForm
     */
    protected $form;

    public function __construct(ProductFilterService $filter, ProductFilterForm $form)
    {
        $this->filter = $filter;
        $this->form = $form;
    }

    public function index()
    {
        $query = Property::with('country')
            ->with('state')
            ->with('city')
            ->with('company')
            ->with('property_type')
            ->with('box_designation')
            ->with('map')
            ->with('details')
            ->with('amenities')
            ->orderBy('name', 'ASC');

        $this->filter->configureFromInput(Input::all())->apply($query);

        $viewData = $this->form->getSelectData();
        $viewData['properties'] = $query->paginate(10);

        return View::make('properties/index', $viewData);
    }
}
