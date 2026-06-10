<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleView;
use App\Models\CallToAction;
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
            // navigation tags — de 6 RN-mock categorieën
            ['name' => 'Voor jou',   'category' => 'navigation'],
            ['name' => 'Klimaat',    'category' => 'navigation'],
            ['name' => 'Politiek',   'category' => 'navigation'],
            ['name' => 'Sport',      'category' => 'navigation'],
            ['name' => 'Tech',       'category' => 'navigation'],
            ['name' => 'Wereld',     'category' => 'navigation'],
            // topic tags
            ['name' => 'Technologie', 'category' => 'topic'],
            ['name' => 'Gezondheid',  'category' => 'topic'],
            ['name' => 'Economie',    'category' => 'topic'],
            // flag tags
            ['name' => 'Goed nieuws', 'category' => 'flag'],
            ['name' => 'Trending',    'category' => 'flag'],
        ])->map(fn ($tag) => Tag::firstOrCreate(['name' => $tag['name']], $tag));

        $user->interestTags()->sync($tags->whereIn('name', ['Politiek', 'Innovatie', 'Natuur'])->pluck('id'));

        $klimaatTag = $tags->firstWhere('name', 'Klimaat');

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
            'content' => 'Ouders die geld verdienen met video`s waarin hun kinderen (tot en met 15 jaar) te zien zijn, mogen dat straks mogelijk niet meer zomaar doen. Het kabinet wil dit onder de kinderarbeidswet laten vallen.
    Uit onderzoek blijkt dat kinderen die vaak in vlogs verschijnen daar negatieve gevolgen van kunnen ondervinden, zoals een slechter zelfbeeld of mentale klachten.
    De exacte regels worden nog uitgewerkt. Denk aan: hoe vaak een kind in beeld komt en of er echt geld mee verdiend wordt. Gewone gezinsvlogs zonder verdienmodel vallen er niet onder. Ook YouTube, Instagram en TikTok worden bij de plannen betrokken. Eind dit jaar moet alles duidelijk zijn.
    ',
            'image_url' => 'https://images.unsplash.com/photo-1529101091764-c3526daf38fe',
            'original_url' => 'https://example.com/kinderen-vlogs',
            'tone' => 'light',
            'status' => 'active',
            'author_id' => $admin->id,
            'published_at' => now(),
        ]);
//2
        $article2 = Article::create([
            'title' => 'Aardbeving treft de Filipijnen',
            'summary' => 'Aardbeving van 7,8 veroorzaakt grote schade',
            'content' => 'Gisteren werd de Filipijnen getroffen door een zware aardbeving van 7,8, de krachtigste in vijftig jaar. Inmiddels zijn er zeker 37 mensen omgekomen, raakten bijna 500 mensen gewond en zijn er nog vier vermisten. Meer dan 20.000 mensen moesten hun huis verlaten, onder andere uit voorzorg vanwege een mogelijke tsunami. Die bleef uiteindelijk uit.De meeste schade is op het zuidelijke eiland Mindanao. Zo een 1500 huizen zijn volledig verwoest, scholen en overheidsgebouwen zijn beschadigd en honderdduizenden mensen zitten zonder stroom. In de provincie Sarangani werden huizen in een bergdorpje bedekt door een aardverschuiving. Ook zijn veel wegen onbegaanbaar. Bij een school stortte een dak in, net op de dag dat kinderen terugkwamen van zomervakantie. Hierdoor blijven veel scholen blijven voorlopig dicht. De autoriteiten zijn nog bezig met hulpverlening en zoeken naar mensen onder het puin. Wanneer iedereen weer veilig naar huis kan, is nog niet bekend.',
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
            'content' => 'De Amerikaanse regering wilde het tarief voor een werkvisum voor buitenlandse werknemers verhogen van 1.500 dollar naar 100.000 dollar. Dit visum, het H-1B-visum, is bedoeld voor bedrijven die specifieke kennis zoeken die ze in de VS zelf niet kunnen vinden. Twintig staten en de Amerikaanse Kamer van Koophandel waren het er niet mee eens en stapten naar de rechter.
Een federale rechter in Boston heeft de verhoging nu ongeldig verklaard. Volgens de rechter had de regering hiervoor toestemming van het Congres nodig, en die ontbrak. Het Witte Huis laat weten te verwachten dat de uitspraak later alsnog wordt teruggedraaid via een hoger beroep.
Of het tarief uiteindelijk toch wordt ingevoerd, wordt duidelijk zodra het hoger beroep is behandeld.
',
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
            'content' => 'De tickets voor Iraanse supporters op het WK voetbal in de VS, Canada en Mexico zijn op het laatste moment ingetrokken. Normaal krijgt elk deelnemend land 8 procent van de tickets per wedstrijd, die de bond dan verdeelt onder fans. Die tickets zijn Iran nu afgenomen.
De Iraanse voetbalbond is boos en geeft de VS de schuld, omdat de twee landen in conflict zijn met elkaar. Veel Iraanse fans hadden al reisplannen gemaakt op basis van officieel aangekondigde procedures. Zowel de FIFA als de VS hebben nog niet gereageerd op de situatie.
Iran speelt op het WK tegen Nieuw-Zeeland (16 juni), België (21 juni) en Egypte (27 juni). Of de tickets alsnog worden teruggegeven, is nog niet duidelijk.
',
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
            'content' => 'Door de aanhoudende droogte waarschuwt de brandweer voor meer natuurbranden de komende tijd. Afgelopen week waren er al branden in Den Haag, Malden en Helden. Die zijn inmiddels onder controle, maar bijna alle veiligheidsregio`s staan op het hoogste waarschuwingsniveau. Dat betekent dat hulpdiensten extra alert zijn en meer materieel klaarstaan.
    Volgens Edwin Kok, natuurbrandexpert bij het NIPV, worden branden niet alleen vaker maar ook heviger. Dat maakt ze moeilijker te blussen, zeker als er meerdere tegelijk uitbreken. Hij geeft aan dat alleen water gebruiken soms niet meer genoeg is. Een van de nieuwe aanpakken waar de brandweer naar kijkt, is het gecontroleerd aansteken van een stuk natuur, zodat een grote brand zich niet verder kan verspreiden.
    De brandweer roept iedereen op om geen vuur te maken in de natuur en bewust te zijn van de risico`s. Hoe de situatie zich ontwikkelt, hangt af van hoe lang de droogte aanhoudt.
',
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
            'content' => 'Steeds meer mensen gebruiken AI-tools zoals ChatGPT, Gemini en Claude op het werk. Dat kan veel tijd besparen, maar er kleven ook risico`s aan. Want als werknemers zomaar documenten uploaden zonder na te denken, kunnen persoonlijke gegevens of bedrijfsinformatie onbedoeld in een openbaar AI-systeem belanden. Dit gebeurt vaak zonder dat de werknemer het doorheeft.
Een bekend voorbeeld is de gemeente Eindhoven, waar medewerkers in een maand meer dan duizend documenten met gevoelige informatie hadden geüpload naar externe AI-tools. Denk aan BSN-nummers en medische gegevens. Ook Amazon had hier last van: interne informatie dook op in ChatGPT. Experts noemen dit soort situaties "Shadow AI", waarbij werknemers AI gebruiken buiten het zicht van hun werkgever.
Organisaties kunnen dit niet blijven negeren, zeggen deskundigen. Helemaal verbieden werkt ook niet. Beter is het om duidelijke regels te maken en eventueel software te gebruiken die automatisch bijhoudt wat er wordt gedeeld. Wanneer meer organisaties zulke regels invoeren, is nog onduidelijk.
',
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
            'content' => 'Rapper Ye, voorheen bekend als Kanye West, staat vanavond en morgen op het podium in het Gelredome in Arnhem. Het is zijn eerste optreden in Europa sinds 2014. Andere Europese concerten, onder andere in Frankrijk, Italie en op een groot festival in Londen, gingen niet door vanwege zijn omstreden uitspraken. Zo zei hij in 2022 dat hij van Hitler hield, noemde hij zichzelf een nazi en bracht hij een nummer uit genaamd Heil Hitler.
Ook in Nederland was er veel discussie over zijn komst. Een meerderheid van de Tweede Kamer wilde hem weren en een Joodse organisatie stapte naar de rechter. De Arnhemse burgemeester kon het concert niet verbieden, maar wil morgen wel samen met Ye naar het Nationaal Holocaustmuseum in Amsterdam. Ye heeft inmiddels meerdere keren zijn excuses aangeboden voor zijn uitspraken en zegt dat die voortkwamen uit een bipolaire stoornis.
Veel fans zitten met gemengde gevoelens. Ze houden van zijn muziek, maar keuren zijn gedrag af. "Ik ga voor de muziek, niet voor hem als persoon", zegt een van hen. Of Ye zich tijdens de shows aan zijn woord houdt, wordt vanavond duidelijk.
',
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
            'content' => 'Na 24 jaar komt de Megafestatie terug. Op 10 en 11 juli volgend jaar vindt het evenement plaats in de Jaarbeurs in Utrecht. In de jaren negentig trok het jaarlijks zo`n 75.000 jongeren, met op het hoogtepunt zelfs 180.000 bezoekers. Er was van alles te doen, van klimwanden en stormbanen tot gamen en attracties.
De nieuwe editie wordt wel anders dan vroeger. Wat er precies te doen is, wordt later bekendgemaakt, maar de organisatie belooft live entertainment, eten en drinken, winkelen en ruimte voor bedrijven om zich te presenteren. Een van de initiatiefnemers legt uit waarom dit een goed moment is: jongeren brengen steeds meer tijd online door, en de behoefte aan echte, offline-ervaringen groeit juist daardoor.
Meer details over het programma worden de komende maanden bekendgemaakt.
',
            'image_url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee',
            'original_url' => 'https://example.com/megafestatie',
            'tone' => 'light',
            'status' => 'active',
            'author_id' => $admin->id,
            'published_at' => now(),
        ]);
        $article3->sources()->syncWithoutDetaching([
            $reuters->id => ['source_url' => 'https://www.reuters.com/example-3', 'is_primary' => true],
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
            'context_text' => 'Veel mensen zijn hun huis kwijtgeraakt.',
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

        $article3->reactions()->create([
            'user_id' => $user->id,
            'article_id' => $article->id,
            'reaction' => 'smile',
            'reaction' => 'happy',
        ]);
      
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
          'editor_id' => $admin->id,
          'title' => 'Wanneer ChatGPT je deadline redt',
          'image_url' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995',
          'caption' => 'Ik: schrijf 2000 woorden. AI: zeg minder.',
          'author' => '@klimaatkat',
          'author_name' => 'Sara de Vries',
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
