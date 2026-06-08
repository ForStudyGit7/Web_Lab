<?php

namespace App\Http\Controllers\Api\Blog\Admin;

use App\Http\Controllers\Api\Blog\Admin\BaseController;
use App\Repositories\BlogPostRepository;
use App\Repositories\BlogCategoryRepository; // Додано
use App\Http\Requests\BlogPostUpdateRequest; // Додано
use Illuminate\Support\Str; // Додано для генерації slug

class PostController extends BaseController
{

    public function __construct(
        private BlogPostRepository $blogPostRepository,
        private BlogCategoryRepository $blogCategoryRepository
    ) {
        // parent::__construct();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paginator = $this->blogPostRepository->getAllWithPaginate();

        return $paginator;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BlogPostUpdateRequest $request, $id)
    {
        $item = $this->blogPostRepository->getEdit($id);

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
            return response()->json([
                'success' => false,
                'message' => 'Помилка збереження'
            ], 400);
        }
    }
}
