<?php

namespace App\Http\Controllers\Api\Blog\Admin;


use Illuminate\Http\Request;
use App\Models\BlogCategory;
use Illuminate\Support\Str;

class CategoryController extends BaseController
{
    public function index()
    {
        $paginator = BlogCategory::paginate(5);

        return $paginator;
    }

    public function store(Request $request)
    {
        $data = $request->all();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }


        $item = BlogCategory::create($data);

        if ($item) {
            return ['success' => 'Успішно створено'];
        } else {
            return ['msg' => 'Помилка створення'];
        }
    }

    public function update(Request $request, string $id)
    {
        $item = BlogCategory::find($id);

        if (empty($item)) {
            return back()
                ->withErrors(['msg' => "Запис id=[{$id}] не знайдено"])
                ->withInput();
        }

        $data = $request->all();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $result = $item->update($data);

        if ($result) {
            return ['success' => 'Успішно збережено'];
        } else {
            return ['msg' => 'Помилка збереження'];
        }
    }
}
