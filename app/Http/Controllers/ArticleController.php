<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Jobs\GenerateSlugAndSummary;

class ArticleController extends Controller
{
    /**
     * GET /api/articles
     * List articles with optional filters: status, category_id, published_from, published_to
     */
    public function index(Request $request)
    {
        $query = Article::with(['author', 'categories']);

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($categoryId = $request->query('category_id')) {
            $query->whereHas('categories', fn($q) => $q->where('categories.id', $categoryId));
        }

        if ($from = $request->query('published_from')) {
            $query->where('published_at', '>=', $from);
        }

        if ($to = $request->query('published_to')) {
            $query->where('published_at', '<=', $to);
        }

        return response()->json($query->get());
    }

    /**
     * POST /api/articles
     * Create a new article (Admin or Author)
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'title'        => 'required|string|max:255',
                'content'      => 'required|string',
                'status'       => 'required|in:Draft,Published,Archived',
                'published_at' => 'nullable|date',
                'categories'   => 'array',
                'categories.*' => 'exists:categories,id',
            ]);

            $data['user_id'] = $request->user()->id;

            // Create article without slug and summary (they'll be filled asynchronously)
            $article = Article::create($data);

            if (!empty($data['categories'])) {
                $article->categories()->sync($data['categories']);
            }

            // Dispatch async slug & summary generation job
            GenerateSlugAndSummary::dispatch($article);

            return response()->json([
                'message' => 'Article created. Slug and summary will be generated shortly.',
                'data' => $article->load(['author', 'categories']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create article.',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * GET /api/articles/{id}
     * View a single article
     */
    public function show($id)
    {
        $article = Article::with(['author', 'categories'])->findOrFail($id);
        return response()->json($article);
    }

    /**
     * PUT/PATCH /api/articles/{id}
     * Update an article (only by Admin or its Author)
     */
    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        $user    = $request->user();

        if ($user->role !== 'admin' && $article->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'title'        => 'sometimes|required|string|max:255',
            'content'      => 'sometimes|required|string',
            'status'       => 'sometimes|required|in:Draft,Published,Archived',
            'published_at' => 'nullable|date',
            'categories'   => 'array',
            'categories.*' => 'exists:categories,id',
        ]);

        $article->update($data);

        if (array_key_exists('categories', $data)) {
            $article->categories()->sync($data['categories']);
        }

        // Dispatch slug & summary regeneration if title or content changed
        if (isset($data['title']) || isset($data['content'])) {
            GenerateSlugAndSummary::dispatch($article);
        }

        return response()->json($article->load(['author', 'categories']));
    }

    /**
     * DELETE /api/articles/{id}
     * Delete an article (only by Admin or its Author)
     */
    public function destroy(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        $user    = $request->user();

        if ($user->role !== 'admin' && $article->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $article->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
