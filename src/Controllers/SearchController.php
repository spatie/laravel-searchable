<?php

namespace Spatie\Searchable\Controllers;

use Spatie\Searchable\Search;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Spatie\Searchable\Requests\SearchRequest;

class SearchController extends Controller
{
    public function __invoke(SearchRequest $request): JsonResponse
    {
        $input_search = config('searchable.input_search', 'search');
        $limit_aspect_results = config('searchable.limit_aspect_results', null);
        $models = config('searchable.models', []);

        $searchResults = new Search();

        foreach ($models as $model => $attributes) {
            $searchResults = $searchResults->registerModel($model, $attributes);
        }

        if ($limit_aspect_results) {
            $searchResults = $searchResults->limitAspectResults($limit_aspect_results);
        }

        $searchResults = $searchResults
            ->search($request->$input_search)
            ->groupByType();

        return response()->json($searchResults);
    }
}
