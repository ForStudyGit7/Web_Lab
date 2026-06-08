<?php

namespace App\Http\Controllers\Api\Blog\Admin;

use Illuminate\Http\Request;
use App\Models\BlogCategory;
use Illuminate\Support\Str;
use App\Http\Requests\BlogCategoryUpdateRequest;
use App\Http\Requests\BlogCategoryCreateRequest;
use App\Repositories\BlogCategoryRepository;

class CategoryController extends BaseController
{

    public function __construct(private BlogCategoryRepository $blogCategoryRepository)
    {
        // parent::__construct();
    }

    public function index()
    {

        $paginator = $this->blogCategoryRepository->getAllWithPaginate(5);

        return $paginator;
    }

    public function store(BlogCategoryCreateRequest $request)
    {
        $data = $request->input();



        $item = (new BlogCategory())->create($data);

        if ($item) {
            return response()->json([
                'success' => true,
                'message' => 'Успішно збережено',
                'data' => $item
            ], 201);
        } else {
            return response()->json(['message' => 'Помилка збереження'], 400);
        }
    }

    public function update(BlogCategoryUpdateRequest $request, $id)
    {
        $item = $this->blogCategoryRepository->getEdit($id);

        if (empty($item)) {
            return response()->json([
                'success' => false,
                'message' => "Запис id=[{$id}] не знайдено"
            ], 404);
        }

        $data = $request->all();



        $result = $item->update($data);

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Успішно збережено',
                'data' => $item->refresh()
            ], 200);
        } else {
            return response()->json(['message' => 'Помилка збереження'], 400);
        }
    }
}
