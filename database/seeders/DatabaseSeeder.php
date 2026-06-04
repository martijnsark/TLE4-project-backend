<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\ArticleView;
use App\Models\CallToAction;
use App\Models\ContentReview;
use App\Models\GeneratedContent;
use App\Models\Meme;
use App\Models\MemeView;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use App\Models\Reaction;
use App\Models\Source;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CategorySeeder::class);

        $klimaatCategory = Category::where('name', 'Klimaat')->first();

        $admin = User::updateOrCreate(
            ['email' => 'admin@impakt.test'],
            [
                'username' => 'impakt_admin',
                'name' => 'IMPAKT Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $user = User::updateOrCreate(
            ['email' => 'user@impakt.test'],
            [
                'username' => 'nieuwsfan',
                'name' => 'Nieuws Fan',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );

        $tags = collect([
            ['name' => 'Klimaat', 'category' => 'natuur'],
            ['name' => 'Politiek', 'category' => 'maatschappij'],
            ['name' => 'Technologie', 'category' => 'innovatie'],
            ['name' => 'Gezondheid', 'category' => 'maatschappij'],
            ['name' => 'Economie', 'category' => 'maatschappij'],
            ['name' => 'Sport', 'category' => 'cultuur'],
        ])->map(fn ($tag) => Tag::firstOrCreate(['name' => $tag['name']], $tag));

        $user->interestTags()->sync($tags->whereIn('name', ['Klimaat', 'Technologie', 'Gezondheid'])->pluck('id'));

        $nos = Source::firstOrCreate(
            ['name' => 'NOS'],
            ['url' => 'https://nos.nl', 'reliability_score' => 90]
        );

        $reuters = Source::firstOrCreate(
            ['name' => 'Reuters'],
            ['url' => 'https://www.reuters.com', 'reliability_score' => 95]
        );

        $article = Article::create([
            'title'            => 'Jongeren maken zich zorgen over klimaat, maar willen vooral praktische tips',
            'summary'          => 'Een kort en helder artikel over hoe jongeren naar klimaatproblemen kijken.',
            'content'          => 'Veel jongvolwassenen willen duurzamer leven, maar weten niet altijd waar ze moeten beginnen.',
            'body_paragraphs'  => [
                'Veel jongvolwassenen willen duurzamer leven, maar weten niet altijd waar ze moeten beginnen.',
                'Kleine keuzes zoals minder voedsel verspillen, vaker fietsen en bewuster kopen kunnen samen veel verschil maken.',
            ],
            'image_url'        => 'https://images.unsplash.com/photo-1497436072909-60f360e1d4b1',
            'original_url'     => 'https://example.com/klimaat-jongeren',
            'tone'             => 'Achtergrond',
            'status'           => 'active',
            'author_id'        => $admin->id,
            'category_id'      => $klimaatCategory?->id,
            'is_good_news'     => false,
            'is_trending'      => true,
            'published_at'     => now(),
        ]);

        // extra articles for testing home feed
        Article::create([
            'title'       => 'Artikel 2',
            'summary'     => 'Samenvatting 2',
            'content'     => 'Content 2',
            'image_url'   => 'https://images.unsplash.com/photo-2',
            'original_url' => 'https://example.com/2',
            'tone'        => 'Live',
            'status'      => 'active',
            'author_id'   => $admin->id,
            'published_at' => now()->subDays(1),
        ]);

        Article::create([
            'title'       => 'Artikel 3',
            'summary'     => 'Samenvatting 3',
            'content'     => 'Content 3',
            'image_url'   => 'https://images.unsplash.com/photo-3',
            'original_url' => 'https://example.com/3',
            'tone'        => 'Reportage',
            'status'      => 'inactive',
            'author_id'   => $admin->id,
            'published_at' => now()->subDays(1),
        ]);

        $article->tags()->sync($tags->whereIn('name', ['Klimaat', 'Politiek'])->pluck('id'));
        $article->sources()->syncWithoutDetaching([
            $nos->id => [
                'source_url' => 'https://nos.nl/example-klimaat',
                'is_primary' => true,
            ],
            $reuters->id => [
                'source_url' => 'https://www.reuters.com/example-climate',
                'is_primary' => false,
            ],
        ]);

        CallToAction::create([
            'article_id' => $article->id,
            'title' => 'Help voedselverspilling verminderen',
            'context_text' => 'In dit artikel las je dat kleine dagelijkse keuzes veel invloed kunnen hebben.',
            'goal_text' => 'Deze organisatie wil voedselverspilling tegengaan door eten te redden en te delen met mensen die het nodig hebben.',
            'target_url' => 'https://www.voedselbankennederland.nl',
        ]);

        $poll = Poll::create([
            'article_id' => $article->id,
            'question' => 'Welke klimaat-actie zou jij deze week willen proberen?',
        ]);

        $optionOne = PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Minder voedsel verspillen',
        ]);

        PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Vaker fietsen of lopen',
        ]);

        PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Minder fast fashion kopen',
        ]);

        PollVote::create([
            'poll_id' => $poll->id,
            'option_id' => $optionOne->id,
            'user_id' => $user->id,
            'voted_at' => now(),
        ]);

        Reaction::create([
            'user_id' => $user->id,
            'article_id' => $article->id,
            'reaction' => 'happy',
        ]);

        $user->savedArticles()->syncWithoutDetaching([
            $article->id => ['saved_at' => now()],
        ]);

        ArticleView::create([
            'user_id' => $user->id,
            'article_id' => $article->id,
            'viewed_at' => now(),
            'reading_time_seconds' => 95,
        ]);

        $meme = Meme::create([
            'article_id' => $article->id,
            'title' => 'Wanneer je “vandaag duurzaam” zegt',
            'image_url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee',
            'caption' => 'Ik op de fiets terwijl het regent: Character Development.',
        ]);

        $user->savedMemes()->syncWithoutDetaching([
            $meme->id => ['saved_at' => now()],
        ]);

        MemeView::create([
            'user_id' => $user->id,
            'meme_id' => $meme->id,
            'viewed_at' => now(),
            'viewing_time_seconds' => 8,
        ]);

        $generatedContent = GeneratedContent::create([
            'admin_id' => $admin->id,
            'title' => 'AI concept: Waarom lokale initiatieven steeds populairder worden',
            'generated_text' => 'Concepttekst die eerst door een admin beoordeeld moet worden voordat deze live gaat.',
            'original_news_url' => 'https://example.com/lokale-initiatieven',
            'status' => 'draft',
        ]);

        ContentReview::create([
            'generated_content_id' => $generatedContent->id,
            'admin_id' => $admin->id,
            'feedback' => 'Goede basis, maar voeg nog betrouwbare bronnen en een lichtere toon toe.',
            'approved' => false,
            'reviewed_at' => now(),
        ]);
    }
}
