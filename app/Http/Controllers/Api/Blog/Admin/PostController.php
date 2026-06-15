<?php

namespace App\Http\Controllers\Api\Blog\Admin;

use App\Models\BlogPost;
use App\Jobs\BlogPostAfterCreateJob;
use App\Jobs\BlogPostAfterDeleteJob;
use App\Http\Controllers\Api\Blog\Admin\BaseController;
use App\Repositories\BlogPostRepository;
use App\Repositories\BlogCategoryRepository;
use App\Http\Requests\BlogPostUpdateRequest;
use App\Http\Requests\BlogPostCreateRequest;
use App\Http\Resources\Api\Blog\Admin\PostResource;
use Illuminate\Support\Str;

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


        return PostResource::collection($paginator);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $item = BlogPost::with(['user', 'category'])->find($id);

        if (empty($item)) {
            return response()->json([
                'success' => false,
                'message' => "Запис id=[{$id}] не знайдено"
            ], 404);
        }

        // ОБГОРТАЄМО ОДИН ПОСТ В РЕСУРС
        return new PostResource($item);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BlogPostCreateRequest $request)
    {
        $data = $request->input();

        $item = (new BlogPost())->create($data);

        if ($item) {
            $job = new BlogPostAfterCreateJob($item);
            dispatch($job);

            return response()->json([
                'success' => true,
                'message' => 'Успішно збережено',
                'data' => new PostResource($item)
            ], 201);
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
                'data' => new PostResource($item->refresh())
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
        $result = BlogPost::destroy($id);

        if ($result) {
            BlogPostAfterDeleteJob::dispatch($id)->delay(20);

            return response()->json([
                'success' => true,
                'message' => "Запис id=[{$id}] успішно видалено"
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Помилка видалення або запис не існує'
            ], 400);
        }
    }
}
