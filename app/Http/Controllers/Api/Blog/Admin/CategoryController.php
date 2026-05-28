<?php

namespace App\Http\Controllers\Api\Blog\Admin;


use App\Models\BlogCategory;
use Illuminate\Support\Str;
use App\Http\Requests\BlogCategoryUpdateRequest;
use App\Http\Requests\BlogCategoryCreateRequest;

class CategoryController extends BaseController
{
    public function index()
    {
        $paginator = BlogCategory::paginate(5);

        return $paginator;
    }

    public function store(BlogCategoryCreateRequest $request)
    {
        $data = $request->input(); // отримаємо масив даних, які надійшли з форми

        if (empty($data['slug'])) { // якщо псевдонім порожній
            $data['slug'] = Str::slug($data['title']); // генеруємо псевдонім
        }

        // Створюємо об'єкт і додаємо в БД
        $item = (new BlogCategory())->create($data);

        if ($item) {
            // Повертаємо статус успіху разом із самим створеним об'єктом
            return response()->json([
                'success' => true,
                'message' => 'Успішно збережено',
                'data' => $item
            ], 201); // 201 Created — стандартний HTTP-статус для успішного створення
        } else {
            return response()->json(['message' => 'Помилка збереження'], 400);
        }
    }

    public function update(BlogCategoryUpdateRequest $request, $id)
    {
        $item = BlogCategory::find($id);

        if (empty($item)) {
            return response()->json([
                'success' => false,
                'message' => "Запис id=[{$id}] не знайдено"
            ], 404);
        }

        $data = $request->all();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $result = $item->update($data);

        if ($result) {
            // Повертаємо оновлений об'єкт (refresh() підтягне свіжі дані з БД)
            return response()->json([
                'success' => true,
                'message' => 'Успішно збережено',
                'data' => $item->refresh()
            ], 200); // 200 OK
        } else {
            return response()->json(['message' => 'Помилка збереження'], 400);
        }
    }
}
