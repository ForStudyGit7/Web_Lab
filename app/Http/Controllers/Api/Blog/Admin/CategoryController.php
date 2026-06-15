<?php

namespace App\Http\Controllers\Api\Blog\Admin;

use Illuminate\Http\Request;
use App\Models\BlogCategory;
use Illuminate\Support\Str;
use App\Http\Requests\BlogCategoryUpdateRequest;
use App\Http\Requests\BlogCategoryCreateRequest;
use App\Repositories\BlogCategoryRepository;
use App\Http\Resources\Api\Blog\Admin\CategoryResource;

class CategoryController extends BaseController
{
    public function __construct(private BlogCategoryRepository $blogCategoryRepository)
    {
        // parent::__construct();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paginator = $this->blogCategoryRepository->getAllWithPaginate(5);
        return CategoryResource::collection($paginator);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BlogCategoryCreateRequest $request)
    {
        $data = $request->input();
        $item = (new BlogCategory())->create($data);

        if ($item) {
            return response()->json([
                'success' => true,
                'message' => 'Успішно збережено',
                'data' => new CategoryResource($item)
            ], 201);
        } else {
            return response()->json(['message' => 'Помилка збереження'], 400);
        }
    }

    /**
     * Display the specified resource.
     * ФІКС: Додано метод для віддачі однієї категорії на фронтенд для редагування
     */
    public function show($id)
    {
        $item = $this->blogCategoryRepository->getEdit($id);

        if (empty($item)) {
            return response()->json([
                'success' => false,
                'message' => "Запис id=[{$id}] не знайдено"
            ], 404);
        }

        return new CategoryResource($item);
    }

    /**
     * Update the specified resource in storage.
     */
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
                'data' => new CategoryResource($item->refresh())
            ], 200);
        } else {
            return response()->json(['message' => 'Помилка збереження'], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     * ФІКС: Додано логіку видалення категорії з бази даних
     */
    public function destroy($id)
    {
        $item = $this->blogCategoryRepository->getEdit($id);

        if (empty($item)) {
            return response()->json([
                'success' => false,
                'message' => "Запис id=[{$id}] не знайдено"
            ], 404);
        }


        $hasChildren = BlogCategory::where('parent_id', $id)->exists();
        if ($hasChildren) {
            return response()->json([
                'success' => false,
                'message' => 'Не вдалося видалити. Ця категорія має підкатегорії!'
            ], 422);
        }

        $result = $item->delete();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Успішно видалено'
            ], 200);
        }

        return response()->json(['message' => 'Помилка видалення'], 400);
    }
}
