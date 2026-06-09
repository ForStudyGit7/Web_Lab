<?php

namespace App\Http\Controllers\Api\Blog\Admin;

use App\Models\BlogPost;
use App\Http\Controllers\Api\Blog\Admin\BaseController;
use App\Repositories\BlogPostRepository;
use App\Repositories\BlogCategoryRepository;
use App\Http\Requests\BlogPostUpdateRequest;
use App\Http\Requests\BlogPostCreateRequest;
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
     * Store a newly created resource in storage.
     */
    public function store(BlogPostCreateRequest $request)
    {
        $data = $request->input();

        $item = (new BlogPost())->create($data);

        if ($item) {
            return response()->json([
                'success' => true,
                'message' => 'Успішно збережено',
                'data' => $item
            ], 201); // 201 Created — статус успішного створення в API
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Помилка збереження'
            ], 400);
        }
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $result = BlogPost::destroy($id); // софт деліт, запис лишається в БД, але заповнюється deleted_at

        // $result = BlogPost::find($id)->forceDelete(); (повне видалення з БД):

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => "Запис id=[{$id}] успішно видалено"
            ], 200); // 200 OK
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Помилка видалення або запис не знайдено'
            ], 400); // 400 Bad Request
        }
    }
}
