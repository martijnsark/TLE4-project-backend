<?php

namespace App\Http\Resources;

use App\Enums\TagCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $poll = $this->polls?->first();

        return [
            'id'       => $this->id,
            'title'    => $this->title,
            'sub'      => $this->summary,
            'body'     => $this->mapBodyParagraphs(),
            'img'      => $this->resolveImageUrl(),
            'cat'      => $this->primaryTag()?->name,
            'tags'     => $this->tags
                ->where('category', '!=', TagCategory::Flag)
                ->map(fn ($tag) => [
                    'name' => $tag->name,
                    'icon' => $tag->icon?->value,
                ])
                ->values()
                ->toArray(),
            'tone'     => $this->tone,
            'trending' => (bool) $this->is_trending,
            'date'     => $this->published_at
                ? Carbon::parse($this->published_at)->locale('nl')->isoFormat('D MMMM YYYY')
                : null,
            'time'     => $this->published_at
                ? Carbon::parse($this->published_at)->format('H:i')
                : null,
            'views'   => $this->formatCount($this->views?->count() ?? 0),
            'readers' => $this->formatCount($this->views?->count() ?? 0),
            'reactions' => [
                'smile' => $this->reactions->where('reaction', 'smile')->count(),
                'meh'   => $this->reactions->where('reaction', 'meh')->count(),
                'frown' => $this->reactions->where('reaction', 'frown')->count(),
            ],
            'poll' => $poll ? [
                'q'       => $poll->question,
                'options' => $poll->options->map(fn ($option, $i) => [
                    'id'    => chr(97 + $i),
                    'label' => $option->option_text,
                    'votes' => $option->votes->count(),
                ])->values()->toArray(),
            ] : null,
            'actions' => ($this->callToAction && filled($this->callToAction->title))
                ? [[
                    'label' => $this->callToAction->title,
                    'sub'   => $this->callToAction->goal_text,
                ]]
                : [],
            'sources' => $this->sources->map(fn ($source) => [
                'label' => $source->name,
                'sub'   => $source->pivot->source_url ?? $source->url,
            ])->values()->toArray(),
            $this->mergeWhen($this->is_good_news, ['goodNews' => true]),
        ];
    }

    private function mapBodyParagraphs(): array
    {
        return collect($this->body_paragraphs ?? [])->map(function ($paragraph) {
            return is_array($paragraph) ? ($paragraph['value'] ?? '') : (string) $paragraph;
        })->filter()->values()->toArray();
    }

    private function resolveImageUrl(): ?string
    {
        if (empty($this->image_url)) {
            return null;
        }

        if (str_starts_with($this->image_url, 'http')) {
            return $this->image_url;
        }

        return Storage::disk('public')->url($this->image_url);
    }

    private function formatCount(int $count): string
    {
        if ($count >= 1000) {
            return round($count / 1000, 1) . 'k';
        }

        return (string) $count;
    }
}
