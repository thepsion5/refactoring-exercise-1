<?php


class ProductsController extends Controller
{
    public function index()
    {
        $service        = new ProductsServiceProvider();
        
        // Get the 'text_match' from session or input
        $text_match     = $service->getTextMatch();

        if (Input::get('clear')) {
            $service->clearSession();
            $query      = $service->getQuery($text_match);
        }
        else {
            $query      = $service->getQueryFromSession($text_match);
        }

        return View::make('properties/index')->with( $service->getRepositoryViewData() + [
            'properties'    => $query->orderBy('name', 'ASC')->paginate(10),
            'text_match'    => $text_match,
        ]);
    }
}