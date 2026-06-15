<?php

namespace App\Http\Resources\Api\Blog\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon; // Додано імпорт Carbon для роботи з датами

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'slug'           => $this->slug,
            'is_published'   => (bool) $this->is_published,

            // ФІКС: Безпечно перетворюємо рядок в об'єкт Carbon перед викликом format()
            'published_at'   => $this->published_at ? Carbon::parse($this->published_at)->format('Y-m-d H:i:s') : null,

            'user_id'        => $this->user_id,
            'category_id'    => $this->category_id,

            // Залишаємо супербезпечний вивід об'єктів для Nuxt
            'user'           => $this->user ? [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ] : [
                'id'   => $this->user_id,
                'name' => 'Невідомий автор'
            ],

            'category'       => $this->category ? [
                'id'    => $this->category->id,
                'title' => $this->category->title,
            ] : [
                'id'    => $this->category_id,
                'title' => 'Без категорії'
            ],

            'content'        => $this->content ?? $this->text,
        ];
    }
}
