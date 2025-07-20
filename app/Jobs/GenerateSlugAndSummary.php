<?php

// app/Jobs/GenerateSlugAndSummary.php

namespace App\Jobs;

use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class GenerateSlugAndSummary implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $article;

    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    public function handle(): void
    {
        $apiKey = env('GROQ_API_KEY');
        $endpoint = 'https://api.groq.com/openai/v1/chat/completions';

        // 1. Generate Slug
        $slugPrompt = [
            'model' => 'llama3-8b-8192', // or gpt-3.5-turbo
            'messages' => [[
                'role' => 'user',
                'content' => "Generate a URL-friendly slug for this title: '{$this->article->title}'. Only return the slug."
            ]]
        ];

        $slugResponse = Http::withToken($apiKey)
            ->post($endpoint, $slugPrompt);

        $slug = trim($slugResponse['choices'][0]['message']['content']);

        // 2. Generate Summary
        $summaryPrompt = [
            'model' => 'llama3-8b-8192',
            'messages' => [[
                'role' => 'user',
                'content' => "Summarize the following article in 2-3 sentences:\n\n{$this->article->content}"
            ]]
        ];

        $summaryResponse = Http::withToken($apiKey)
            ->post($endpoint, $summaryPrompt);

        $summary = trim($summaryResponse['choices'][0]['message']['content']);

        // 3. Update article
        $this->article->update([
            'slug' => $slug,
            'summary' => $summary,
        ]);
    }
}
