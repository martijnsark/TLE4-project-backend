<?php

namespace Database\Seeders;

use App\Models\Article;
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
            ['name' => 'happy', 'category' => 'happy'],
            ['name' => 'Politiek', 'category' => 'politiek'],
            ['name' => 'Buitenland', 'category' => 'buitenland'],
            ['name' => 'Economie', 'category' => 'economie'],
            ['name' => 'Sport', 'category' => 'sport'],
            ['name' => 'Natuur', 'category' => 'natuur'],
            ['name' => 'Innovatie', 'category' => 'innovatie'],
            ['name' => 'Kunst', 'category' => 'kunst'],
            ['name' => 'Lokaal', 'category' => 'lokaal'],
        ])->map(fn($tag) => Tag::firstOrCreate(['name' => $tag['name']], $tag));

        $user->interestTags()->sync($tags->whereIn('name', ['Politiek', 'Innovatie', 'Natuur'])->pluck('id'));

        $nos = Source::firstOrCreate(
            ['name' => 'NOS'],
            ['url' => 'https://nos.nl', 'reliability_score' => 90]
        );

        $reuters = Source::firstOrCreate(
            ['name' => 'Reuters'],
            ['url' => 'https://www.reuters.com', 'reliability_score' => 95]
        );

//1
        $article1 = Article::create([
            'title' => 'Kinderen in vlogs krijgen meer bescherming',
            'summary' => 'Kabinet wil strengere regels voor kinder-vlogs met verdienmodel',
            'content' => 'Ouders die geld verdienen met video’s waarin hun kinderen (tot en met 15 jaar) te zien zijn, mogen dat straks mogelijk niet meer zomaar doen. Het kabinet wil dit onder de kinderarbeidswet laten vallen. Uit onderzoek blijkt dat kinderen die vaak in vlogs verschijnen daar negatieve gevolgen van kunnen ondervinden, zoals een slechter zelfbeeld of mentale klachten. De exacte regels worden nog uitgewerkt. Gewone gezinsvlogs zonder verdienmodel vallen er niet onder.',
            'image_url' => 'https://images.unsplash.com/photo-1529101091764-c3526daf38fe',
            'original_url' => 'https://example.com/kinderen-vlogs',
            'tone' => 'light',
            'status' => 'active',
            'author_id' => $admin->id,
            'published_at' => now(),
        ]);
//2
        $article2 = Article::create([
            'title' => 'Zware aardbeving treft de Filipijnen',
            'summary' => 'Aardbeving van 7,8 veroorzaakt doden en grote schade',
            'content' => 'De Filipijnen zijn getroffen door een zware aardbeving van 7,8. Er vielen tientallen doden en honderden gewonden. Meer dan 20.000 mensen moesten hun huis verlaten. Vooral het eiland Mindanao is zwaar getroffen met ingestorte gebouwen en infrastructuurschade.',
            'image_url' => 'https://images.unsplash.com/photo-1500674425229-f692875b0ab7',
            'original_url' => 'https://example.com/aardbeving-filipijnen',
            'tone' => 'light',
            'status' => 'active',
            'author_id' => $admin->id,
            'published_at' => now(),
        ]);
//3
        $article3 = Article::create([
            'title' => 'Amerikaans visum van 100.000 dollar van de baan',
            'summary' => 'Rechter blokkeert verhoging werkvisum in de VS',
            'content' => 'Een Amerikaanse rechter heeft een plan om de kosten van het H-1B werkvisum te verhogen naar 100.000 dollar tegengehouden. Volgens de rechter is daar toestemming van het Congres voor nodig. Het besluit kan nog via hoger beroep worden teruggedraaid.',
            'image_url' => 'https://images.unsplash.com/photo-1528747045269-390fe33c19d7',
            'original_url' => 'https://example.com/visum-vs',
            'tone' => 'light',
            'status' => 'active',
            'author_id' => $admin->id,
            'published_at' => now(),
        ]);
//4
        $article4 = Article::create([
            'title' => 'Iraanse fans mogen niet naar het WK',
            'summary' => 'Tickets voor Iraanse supporters ingetrokken',
            'content' => 'Iraanse fans krijgen geen toegang meer tot hun eerder toegewezen WK-tickets. De beslissing zorgt voor politieke spanningen tussen landen. Veel supporters hadden al reisplannen gemaakt.',
            'image_url' => 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2',
            'original_url' => 'https://example.com/wk-iran-fans',
            'tone' => 'light',
            'status' => 'active',
            'author_id' => $admin->id,
            'published_at' => now(),
        ]);
//5
        $article5 = Article::create([
            'title' => 'Kans op natuurbranden in Nederland blijft hoog',
            'summary' => 'Droogte zorgt voor verhoogd brandrisico',
            'content' => 'Door aanhoudende droogte is het risico op natuurbranden in Nederland hoog. Hulpdiensten staan in verhoogde paraatheid en waarschuwen mensen om geen vuur te maken in natuurgebieden.',
            'image_url' => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470',
            'original_url' => 'https://example.com/natuurbranden-nl',
            'tone' => 'light',
            'status' => 'active',
            'author_id' => $admin->id,
            'published_at' => now(),
        ]);
//6
        $article6 = Article::create([
            'title' => 'AI op de werkvloer: handig, maar niet zonder risico',
            'summary' => 'Waarschuwing voor datalekken door AI-gebruik',
            'content' => 'Het gebruik van AI op de werkvloer neemt toe, maar brengt risico’s met zich mee. Werknemers kunnen onbedoeld gevoelige informatie delen met externe AI-systemen. Organisaties worden geadviseerd duidelijke regels op te stellen.',
            'image_url' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995',
            'original_url' => 'https://example.com/ai-werkvloer',
            'tone' => 'light',
            'status' => 'active',
            'author_id' => $admin->id,
            'published_at' => now(),
        ]);
//7
        $article7 = Article::create([
            'title' => 'Ye treedt op in Arnhem ondanks alle ophef',
            'summary' => 'Omstreden rapper geeft concert in GelreDome',
            'content' => 'Rapper Ye treedt op in Arnhem ondanks eerdere ophef. Zijn shows zorgden in Europa voor discussie, maar in Nederland is het concert doorgegaan. Fans reageren verdeeld.',
            'image_url' => 'https://images.unsplash.com/photo-1501386761578-eac5c94b800a',
            'original_url' => 'https://example.com/ye-arnhem',
            'tone' => 'light',
            'status' => 'active',
            'author_id' => $admin->id,
            'published_at' => now(),
        ]);
//8
        $article8 = Article::create([
            'title' => 'Megafestatie is terug na 24 jaar',
            'summary' => 'Groot jeugdevenement keert terug in Utrecht',
            'content' => 'Na 24 jaar keert de Megafestatie terug naar de Jaarbeurs in Utrecht. Het evenement richt zich op live entertainment, eten en activiteiten. Het moet een offline ervaring bieden voor jongeren.',
            'image_url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee',
            'original_url' => 'https://example.com/megafestatie',
            'tone' => 'light',
            'status' => 'active',
            'author_id' => $admin->id,
            'published_at' => now(),
        ]);

//        $article->tags()->sync($tags->whereIn('name', ['Politiek', 'Natuur'])->pluck('id'));
//        $article->sources()->syncWithoutDetaching([
//            $nos->id => [
//                'source_url' => 'https://nos.nl/example-klimaat',
//                'is_primary' => true,
//            ],
//            $reuters->id => [
//                'source_url' => 'https://www.reuters.com/example-climate',
//                'is_primary' => false,
//            ],
//        ]);
//1
        $article1->tags()->sync(
            $tags->whereIn('name', ['Politiek', 'Media'])->pluck('id')
        );

        $article1->sources()->syncWithoutDetaching([
            $nos->id => [
                'source_url' => 'https://example.com/kinderen-vlogs',
                'is_primary' => true,
            ],
        ]);

        CallToAction::create([
            'article_id' => $article1->id,
            'title' => 'Lees meer over online veiligheid voor kinderen',
            'context_text' => 'Kinderen verschijnen steeds vaker online.',
            'goal_text' => 'Ontdek hoe je kinderen kunt beschermen op sociale media.',
            'target_url' => 'https://www.veiliginternetten.nl',
        ]);

        $poll = Poll::create([
            'article_id' => $article1->id,
            'question' => 'Vind jij dat kinderen beter beschermd moeten worden in commerciële vlogs?',
        ]);

        $optionOne = PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Ja, absoluut',
        ]);

        PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Alleen bij grote inkomsten',
        ]);

        PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Nee',
        ]);

        //2
        $article2->tags()->sync(
            $tags->whereIn('name', ['Buitenland', 'Ramp'])->pluck('id')
        );

        CallToAction::create([
            'article_id' => $article2->id,
            'title' => 'Steun slachtoffers van natuurrampen',
            'context_text' => 'Duizenden mensen zijn hun huis kwijtgeraakt.',
            'goal_text' => 'Hulporganisaties bieden noodhulp aan getroffen gezinnen.',
            'target_url' => 'https://www.rodekruis.nl',
        ]);

        $poll = Poll::create([
            'article_id' => $article2->id,
            'question' => 'Doneer jij soms aan noodhulp na een natuurramp?',
        ]);

        $optionOne = PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Ja regelmatig',
        ]);

        PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Soms',
        ]);

        PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Nee',
        ]);
        //3
        $article3->tags()->sync(
            $tags->whereIn('name', ['Economie', 'Buitenland'])->pluck('id')
        );

        $poll = Poll::create([
            'article_id' => $article3->id,
            'question' => 'Moeten landen buitenlandse kenniswerkers actief aantrekken?',
        ]);

        $optionOne = PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Ja',
        ]);

        PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Alleen bij tekorten',
        ]);

        PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Nee',
        ]);
        //4
        $article4->tags()->sync(
            $tags->whereIn('name', ['Sport', 'Buitenland'])->pluck('id')
        );

        $poll = Poll::create([
            'article_id' => $article4->id,
            'question' => 'Moet sport losstaan van politieke conflicten?',
        ]);

        $optionOne = PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Ja',
        ]);

        PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Soms',
        ]);

        PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Nee',
        ]);
        //5
        $article5->tags()->sync(
            $tags->whereIn('name', ['Natuur', 'Nederland'])->pluck('id')
        );

        $poll = Poll::create([
            'article_id' => $article5->id,
            'question' => 'Maak jij je zorgen over natuurbranden in Nederland?',
        ]);

        $optionOne = PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Ja',
        ]);

        PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Een beetje',
        ]);

        PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Nee',
        ]);
        //6
        $article6->tags()->sync(
            $tags->whereIn('name', ['Natuur', 'Nederland'])->pluck('id')
        );

        $poll = Poll::create([
            'article_id' => $article6->id,
            'question' => 'Maak jij je zorgen over natuurbranden in Nederland?',
        ]);

        $optionOne = PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Ja',
        ]);

        PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Een beetje',
        ]);

        PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Nee',
        ]);
        //7
        $article7->tags()->sync(
            $tags->whereIn('name', ['Innovatie', 'Technologie'])->pluck('id')
        );

        $poll = Poll::create([
            'article_id' => $article7->id,
            'question' => 'Gebruik jij AI-tools voor school of werk?',
        ]);

        $optionOne = PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Dagelijks',
        ]);

        PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Soms',
        ]);

        PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Nooit',
        ]);

        $meme = Meme::create([
            'article_id' => $article7->id,
            'title' => 'Wanneer ChatGPT je deadline redt',
            'image_url' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995',
            'caption' => 'Ik: schrijf 2000 woorden. AI: zeg minder.',
        ]);
        //8
        $article8->tags()->sync(
            $tags->whereIn('name', ['Muziek', 'Kunst'])->pluck('id')
        );

        $poll = Poll::create([
            'article_id' => $article8->id,
            'question' => 'Kun je muziek los zien van de artiest?',
        ]);

        $optionOne = PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Ja',
        ]);

        PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Soms',
        ]);

        PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Nee',
        ]);

    }
}
