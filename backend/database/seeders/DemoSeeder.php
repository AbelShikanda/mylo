<?php

namespace Database\Seeders;

use App\Models\Identity\Administrator;
use App\Models\Identity\AdminProfile;
use App\Models\Identity\Article;
use App\Models\Identity\ArticleLike;
use App\Models\Identity\Book;
use App\Models\Identity\Bookmark;
use App\Models\Identity\Category;
use App\Models\Identity\Comment;
use App\Models\Identity\Contribution;
use App\Models\Identity\DigitalAccess;
use App\Models\Identity\HomepageSection;
use App\Models\Identity\Order;
use App\Models\Identity\OrderItem;
use App\Models\Identity\ProductCategory;
use App\Models\Identity\Reader;
use App\Models\Identity\ReaderCategoryPreference;
use App\Models\Identity\ReaderProfile;
use App\Models\Identity\Writer;
use App\Models\Identity\WriterProfile;
use App\Services\Identity\IdentityService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAccounts();
        $categories = $this->seedCategories();
        $writers = $this->writers();
        $reader = $this->reader();
        $articles = $this->seedArticles($writers, $categories);
        $books = $this->seedBooks($writers);
        $this->seedEngagement($reader, $articles);
        $this->seedOrder($reader, $books);
        $this->seedHomepage();
    }

    protected function seedAccounts(): void
    {
        $identity = app(IdentityService::class);

        $admin = Administrator::firstOrCreate(
            ['email' => 'admin@mylo.test'],
            ['password' => bcrypt('password'), 'email_verified_at' => now()]
        );
        AdminProfile::firstOrCreate(
            ['administrator_id' => $admin->id],
            ['display_name' => 'Amina Okafor']
        );
        $identity->assignRole($admin, 'super-administrator');

        $writerOne = Writer::firstOrCreate(
            ['email' => 'writer@mylo.test'],
            ['password' => bcrypt('password'), 'email_verified_at' => now()]
        );
        WriterProfile::firstOrCreate(
            ['writer_id' => $writerOne->id],
            [
                'display_name' => 'Daniel Mensah',
                'bio' => 'Staff writer covering technology and African innovation.',
                'support_active' => true,
            ]
        );

        $writerTwo = Writer::firstOrCreate(
            ['email' => 'writer2@mylo.test'],
            ['password' => bcrypt('password'), 'email_verified_at' => now()]
        );
        WriterProfile::firstOrCreate(
            ['writer_id' => $writerTwo->id],
            [
                'display_name' => 'Lena Petrova',
                'bio' => 'Essayist writing about culture, cities and books.',
                'support_active' => true,
            ]
        );

        $reader = Reader::firstOrCreate(
            ['email' => 'reader@mylo.test'],
            ['password' => bcrypt('password'), 'email_verified_at' => now()]
        );
        ReaderProfile::firstOrCreate(
            ['reader_id' => $reader->id],
            ['display_name' => 'Test Reader']
        );
    }

    protected function reader(): Reader
    {
        return Reader::where('email', 'reader@mylo.test')->firstOrFail();
    }

    /** @return array<int, Writer> */
    protected function writers(): array
    {
        return [
            Writer::where('email', 'writer@mylo.test')->firstOrFail(),
            Writer::where('email', 'writer2@mylo.test')->firstOrFail(),
        ];
    }

    /** @return array<string, Category> */
    protected function seedCategories(): array
    {
        $definitions = [
            ['name' => 'Technology', 'slug' => 'technology', 'sort_order' => 1, 'is_featured' => true, 'description' => 'Software, AI and African tech ecosystems.'],
            ['name' => 'Culture', 'slug' => 'culture', 'sort_order' => 2, 'is_featured' => true, 'description' => 'Arts, society and everyday life.'],
            ['name' => 'Business', 'slug' => 'business', 'sort_order' => 3, 'is_featured' => true, 'description' => 'Markets, entrepreneurship and policy.'],
            ['name' => 'Books & Publishing', 'slug' => 'books-publishing', 'sort_order' => 4, 'is_featured' => true, 'description' => 'Literature, publishing and reading culture.'],
            ['name' => 'Environment', 'slug' => 'environment', 'sort_order' => 5, 'is_featured' => false, 'description' => 'Climate, conservation and sustainability.'],
            ['name' => 'Food', 'slug' => 'food', 'sort_order' => 6, 'is_featured' => false, 'description' => 'Culinary stories and food systems.'],
        ];

        $categories = [];
        foreach ($definitions as $definition) {
            $categories[$definition['slug']] = Category::firstOrCreate(
                ['slug' => $definition['slug']],
                array_merge($definition, ['is_active' => true])
            );
        }

        return $categories;
    }

    /**
     * @param  array<int, Writer>  $writers
     * @param  array<string, Category>  $categories
     * @return array<string, Article>
     */
    protected function seedArticles(array $writers, array $categories): array
    {
        [$daniel, $lena] = $writers;

        $definitions = [
            [
                'slug' => 'the-quiet-rise-of-african-ai',
                'title' => 'The Quiet Rise of African AI',
                'excerpt' => 'Across Lagos, Nairobi and Kigali, a generation of builders is rewriting the continent’s technology story.',
                'writer_id' => $daniel->id,
                'category_slug' => 'technology',
                'status' => 'published',
                'is_featured' => true,
                'reading_time' => 8,
                'published_at' => now()->subDays(2),
                'content' => [
                    ['id' => (string) Str::uuid(), 'type' => 'paragraph', 'content' => 'In a converted warehouse in Yaba, six engineers are training a speech model on eleven local languages. They are not waiting for permission from Silicon Valley.'],
                    ['id' => (string) Str::uuid(), 'type' => 'heading', 'content' => 'The talent is already here'],
                    ['id' => (string) Str::uuid(), 'type' => 'paragraph', 'content' => 'Nigeria alone graduates more than 100,000 software engineers a year. What changed is not ability, but capital, compute and confidence.'],
                    ['id' => (string) Str::uuid(), 'type' => 'pullquote', 'content' => 'The next AI labs will not be built in garages in California. They will be built in rooms like this one.', 'credit' => 'Daniel Mensah'],
                    ['id' => (string) Str::uuid(), 'type' => 'paragraph', 'content' => 'The question is whether the rest of the ecosystem — policy, infrastructure and patient money — can keep pace.'],
                ],
            ],
            [
                'slug' => 'nairobi-bookshops-are-back',
                'title' => 'Nairobi Bookshops Are Back',
                'excerpt' => 'Independent bookstores are thriving again, one annotated margin at a time.',
                'writer_id' => $lena->id,
                'category_slug' => 'books-publishing',
                'status' => 'published',
                'is_featured' => true,
                'reading_time' => 6,
                'published_at' => now()->subDays(4),
                'content' => [
                    ['id' => (string) Str::uuid(), 'type' => 'paragraph', 'content' => 'On a Saturday morning at Bookstop in Westlands, the queue for signed copies stretched past the coffee counter.'],
                    ['id' => (string) Str::uuid(), 'type' => 'heading', 'content' => 'The economics of browsing'],
                    ['id' => (string) Str::uuid(), 'type' => 'paragraph', 'content' => 'Discovery — not logistics — is the moat. Readers want curation, conversation and community, not just a SKU.'],
                    ['id' => (string) Str::uuid(), 'type' => 'pullquote', 'content' => 'A bookstore is a room where browsing is treated as an intellectual act.'],
                ],
            ],
            [
                'slug' => 'what-a-seed-round-teaches-you',
                'title' => 'What a Seed Round Teaches You About Your Product',
                'excerpt' => 'Fundraising is an interrogation your roadmap has to survive.',
                'writer_id' => $daniel->id,
                'category_slug' => 'business',
                'status' => 'published',
                'is_featured' => false,
                'reading_time' => 10,
                'published_at' => now()->subDays(6),
                'content' => [
                    ['id' => (string) Str::uuid(), 'type' => 'paragraph', 'content' => 'Every diligence question is a proxy for a deeper fear: can this team, this market, this product survive contact with reality?'],
                    ['id' => (string) Str::uuid(), 'type' => 'heading', 'content' => 'Answer the fear, not the question'],
                    ['id' => (string) Str::uuid(), 'type' => 'list', 'content' => "Show the retention curve, not the signup count\nPrice before you discount\nKill features in public"],
                    ['id' => (string) Str::uuid(), 'type' => 'paragraph', 'content' => 'Investors do not fund products; they underwrite teams who understand their own numbers.'],
                ],
            ],
            [
                'slug' => 'the-city-that-learned-to-walk',
                'title' => 'The City That Learned to Walk',
                'excerpt' => 'Accra’s pedestrianisation experiment is rewriting how planners think about streets.',
                'writer_id' => $lena->id,
                'category_slug' => 'culture',
                'status' => 'published',
                'is_featured' => false,
                'reading_time' => 7,
                'published_at' => now()->subDays(9),
                'content' => [
                    ['id' => (string) Str::uuid(), 'type' => 'paragraph', 'content' => 'For one weekend a month, a kilometre of Kanda Highway belongs to nobody in particular — which is to say, everybody.'],
                    ['id' => (string) Str::uuid(), 'type' => 'heading', 'content' => 'Streets as public health infrastructure'],
                    ['id' => (string) Str::uuid(), 'type' => 'paragraph', 'content' => 'Walking is not the absence of mobility; it is a mobility policy that costs almost nothing and compounds quietly.'],
                ],
            ],
            [
                'slug' => 'the-case-for-slow-journalism-online',
                'title' => 'The Case for Slow Journalism Online',
                'excerpt' => 'Speed was a strategy. Depth is one too.',
                'writer_id' => $lena->id,
                'category_slug' => 'culture',
                'status' => 'published',
                'is_featured' => false,
                'reading_time' => 9,
                'published_at' => now()->subDays(12),
                'content' => [
                    ['id' => (string) Str::uuid(), 'type' => 'paragraph', 'content' => 'The most read stories on this platform are not the fastest; they are the ones that hold a reader for nine minutes.'],
                    ['id' => (string) Str::uuid(), 'type' => 'pullquote', 'content' => 'Attention is not captured; it is earned, sentence by sentence.'],
                ],
            ],
            [
                'slug' => 'climate-adaptation-is-a-design-problem',
                'title' => 'Climate Adaptation Is a Design Problem',
                'excerpt' => 'Resilience will be won in floor plans and drainage grids, not communiqués.',
                'writer_id' => $daniel->id,
                'category_slug' => 'environment',
                'status' => 'published',
                'is_featured' => false,
                'reading_time' => 11,
                'published_at' => now()->subDays(15),
                'content' => [
                    ['id' => (string) Str::uuid(), 'type' => 'paragraph', 'content' => 'In Kinshasa’s Masina district, raised thresholds and sloped gardens have cut flood damage more cheaply than any wall.'],
                    ['id' => (string) Str::uuid(), 'type' => 'heading', 'content' => 'Design is destiny'],
                    ['id' => (string) Str::uuid(), 'type' => 'paragraph', 'content' => 'Adaptation budgets that ignore design become maintenance nightmares; the ones that embrace it become quiet revolutions.'],
                ],
            ],
            [
                'slug' => 'draft-notes-on-newsletters',
                'title' => 'Draft: Notes on the Newsletter Economy',
                'excerpt' => null,
                'writer_id' => $daniel->id,
                'category_slug' => 'business',
                'status' => 'draft',
                'is_featured' => false,
                'reading_time' => 4,
                'published_at' => null,
                'content' => [
                    ['id' => (string) Str::uuid(), 'type' => 'paragraph', 'content' => 'Everyone says email is dead until they need to reach two thousand people who actually asked to hear from them.'],
                ],
            ],
            [
                'slug' => 'scheduled-the-return-of-print-criticism',
                'title' => 'The Return of Print Criticism',
                'excerpt' => 'Why long-form literary criticism is migrating back to paper.',
                'writer_id' => $lena->id,
                'category_slug' => 'books-publishing',
                'status' => 'scheduled',
                'is_featured' => false,
                'reading_time' => 8,
                'published_at' => null,
                'scheduled_at' => now()->addDays(7),
                'content' => [
                    ['id' => (string) Str::uuid(), 'type' => 'paragraph', 'content' => 'Criticism is a slow art meeting a slow medium again, and readers are noticing.'],
                ],
            ],
        ];

        $articles = [];
        foreach ($definitions as $definition) {
            $categorySlug = $definition['category_slug'];
            unset($definition['category_slug']);

            $definition['content'] = json_encode($definition['content']);
            $definition['slug'] = (string) $definition['slug'];

            $article = Article::firstOrCreate(
                ['slug' => $definition['slug']],
                array_merge($definition, [
                    'category_id' => $categories[$categorySlug]->id,
                    'seo_title' => $definition['title'],
                    'seo_description' => $definition['excerpt'],
                ])
            );

            $articles[$definition['slug']] = $article;
        }

        return $articles;
    }

    /**
     * @param  array<int, Writer>  $writers
     * @return array<string, Book>
     */
    protected function seedBooks(array $writers): array
    {
        [$daniel, $lena] = $writers;

        $productCategories = [];
        foreach (['Ebooks' => 'ebooks', 'Physical Books' => 'physical-books'] as $name => $slug) {
            $productCategories[$slug] = ProductCategory::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            );
        }

        $definitions = [
            [
                'slug' => 'machines-of-the-monsoon',
                'title' => 'Machines of the Monsoon',
                'description' => 'An investigation into how coastal cities are using machine learning to predict floods — and who gets protected.',
                'author' => 'Daniel Mensah',
                'price' => 1500.00,
                'product_type' => 'ebook',
                'writer_id' => $daniel->id,
                'product_category_id' => $productCategories['ebooks']->id,
                'inventory_count' => 9999,
            ],
            [
                'slug' => 'the-reader-of-kampala',
                'title' => 'The Reader of Kampala',
                'description' => 'A novel about a lending library, a love letter, and a city that refuses to stop reading.',
                'author' => 'Lena Petrova',
                'price' => 2200.00,
                'product_type' => 'ebook',
                'writer_id' => $lena->id,
                'product_category_id' => $productCategories['ebooks']->id,
                'inventory_count' => 9999,
            ],
            [
                'slug' => 'machines-of-the-monsoon-print',
                'title' => 'Machines of the Monsoon (Print)',
                'description' => 'The print edition of the acclaimed flood-tech investigation.',
                'author' => 'Daniel Mensah',
                'price' => 3500.00,
                'product_type' => 'physical',
                'writer_id' => $daniel->id,
                'product_category_id' => $productCategories['physical-books']->id,
                'inventory_count' => 120,
            ],
            [
                'slug' => 'walking-as-policy',
                'title' => 'Walking as Policy',
                'description' => 'Essays on streets, public space and civic imagination.',
                'author' => 'Lena Petrova',
                'price' => 1800.00,
                'product_type' => 'physical',
                'writer_id' => $lena->id,
                'product_category_id' => $productCategories['physical-books']->id,
                'inventory_count' => 80,
            ],
        ];

        $books = [];
        foreach ($definitions as $definition) {
            $books[$definition['slug']] = Book::firstOrCreate(
                ['slug' => $definition['slug']],
                array_merge($definition, ['is_available' => true])
            );
        }

        return $books;
    }

    /**
     * @param  array<string, Article>  $articles
     */
    protected function seedEngagement(Reader $reader, array $articles): void
    {
        foreach ($articles as $article) {
            if ($article->status !== 'published') {
                continue;
            }

            ArticleLike::firstOrCreate([
                'article_id' => $article->id,
                'reader_id' => $reader->id,
            ]);

            Bookmark::firstOrCreate([
                'article_id' => $article->id,
                'reader_id' => $reader->id,
            ]);
        }

        $technology = Category::where('slug', 'technology')->first();
        $culture = Category::where('slug', 'culture')->first();
        if ($technology && $culture) {
            foreach ([$technology->id, $culture->id] as $categoryId) {
                ReaderCategoryPreference::firstOrCreate([
                    'reader_id' => $reader->id,
                    'category_id' => $categoryId,
                ]);
            }
        }

        $featured = $articles['the-quiet-rise-of-african-ai'] ?? null;
        if ($featured) {
            Comment::firstOrCreate([
                'article_id' => $featured->id,
                'reader_id' => $reader->id,
                'content' => 'This is exactly the reporting this story needed.',
            ], ['is_approved' => true]);
        }

        $bookshops = $articles['nairobi-bookshops-are-back'] ?? null;
        if ($bookshops) {
            Comment::firstOrCreate([
                'article_id' => $bookshops->id,
                'reader_id' => $reader->id,
                'content' => 'Saturday queues at Bookstop confirmed this for me last month.',
            ], ['is_approved' => true]);
        }
    }

    /**
     * @param  array<string, Book>  $books
     */
    protected function seedOrder(Reader $reader, array $books): void
    {
        if (Order::where('reader_id', $reader->id)->exists()) {
            return;
        }

        $ebook = $books['machines-of-the-monsoon'] ?? null;
        if (! $ebook) {
            return;
        }

        $order = Order::create([
            'reader_id' => $reader->id,
            'status' => 'completed',
            'total' => $ebook->price,
            'currency' => 'KES',
        ]);

        $item = OrderItem::create([
            'order_id' => $order->id,
            'book_id' => $ebook->id,
            'quantity' => 1,
            'price' => $ebook->price,
        ]);

        DigitalAccess::create([
            'order_item_id' => $item->id,
            'reader_id' => $reader->id,
            'book_id' => $ebook->id,
            'access_token' => (string) Str::uuid(),
            'expires_at' => now()->addYear(),
        ]);

        Contribution::firstOrCreate([
            'transaction_reference' => 'DEMO-CONTRIB-0001',
        ], [
            'reader_id' => $reader->id,
            'writer_id' => $ebook->writer_id,
            'amount' => 500.00,
            'currency' => 'KES',
            'type' => 'one-time',
            'status' => 'completed',
        ]);
    }

    protected function seedHomepage(): void
    {
        $sections = [
            ['name' => 'Featured Stories', 'type' => 'featured', 'sort_order' => 1],
            ['name' => 'Recently Published', 'type' => 'recent', 'sort_order' => 2],
            ['name' => 'Books and Marketplace', 'type' => 'marketplace', 'sort_order' => 3],
        ];

        foreach ($sections as $section) {
            HomepageSection::firstOrCreate(
                ['name' => $section['name']],
                array_merge($section, ['is_active' => true])
            );
        }
    }
}
