<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Artwork;
use App\Models\SellerDescription;
use App\Models\Address;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Tworzenie kategorii
        $categories = [
            'Malarstwo',
            'Rzeźba',
            'Grafika',
            'Fotografia',
            'Sztuka cyfrowa',
            'Inne'
        ];

        foreach ($categories as $categoryName) {
            Category::create([
                'name' => $categoryName,
            ]);
        }

        // Tworzenie 3 zwykłych użytkowników
        $users = [
            [
                'username' => 'anna_k',
                'first_name' => 'Anna',
                'last_name' => 'Kowalska',
                'email' => 'anna@example.com',
                'password' => Hash::make('12345678'),
                'role' => 'user',
                'balance' => 5000.00,
                'email_verified_at' => now(),
            ],
            [
                'username' => 'piotr_n',
                'first_name' => 'Piotr',
                'last_name' => 'Nowak',
                'email' => 'piotr@example.com',
                'password' => Hash::make('12345678'),
                'role' => 'user',
                'balance' => 3000.00,
                'email_verified_at' => now(),
            ],
            [
                'username' => 'maria_w',
                'first_name' => 'Maria',
                'last_name' => 'Wiśniewska',
                'email' => 'maria@example.com',
                'password' => Hash::make('12345678'),
                'role' => 'user',
                'balance' => 7500.00,
                'email_verified_at' => now(),
            ]
        ];

        $regularUsers = [];
        foreach ($users as $userData) {
            $regularUsers[] = User::create($userData);
        }

        // Tworzenie 2 sprzedawców
        $sellers = [
            [
                'username' => 'jakub_art',
                'first_name' => 'Jakub',
                'last_name' => 'Artystowski',
                'email' => 'jakub@gallery.com',
                'password' => Hash::make('12345678'),
                'role' => 'seller',
                'balance' => 10000.00,
                'email_verified_at' => now(),
            ],
            [
                'username' => 'magda_rzezba',
                'first_name' => 'Magdalena',
                'last_name' => 'Rzeźbiarska',
                'email' => 'magdalena@gallery.com',
                'password' => Hash::make('12345678'),
                'role' => 'seller',
                'balance' => 8500.00,
                'email_verified_at' => now(),
            ]
        ];

        $sellerUsers = [];
        foreach ($sellers as $sellerData) {
            $sellerUsers[] = User::create($sellerData);
        }

        // Łączenie wszystkich użytkowników (zwykli + sprzedawcy)
        $allUsers = array_merge($regularUsers, $sellerUsers);

        // Tworzenie adresów dla wszystkich użytkowników
        $addresses = [
            // Adresy dla zwykłych użytkowników
            [
                'user_id' => $regularUsers[0]->id, // Anna
                'city' => 'Warszawa',
                'postal_code' => '00-001',
                'street' => 'Marszałkowska',
                'house_number' => '15',
                'apartment_number' => '23',
            ],
            [
                'user_id' => $regularUsers[1]->id, // Piotr
                'city' => 'Kraków',
                'postal_code' => '30-001',
                'street' => 'Floriańska',
                'house_number' => '42',
                'apartment_number' => null,
            ],
            [
                'user_id' => $regularUsers[2]->id, // Maria
                'city' => 'Gdańsk',
                'postal_code' => '80-001',
                'street' => 'Długa',
                'house_number' => '7',
                'apartment_number' => '5',
            ],
            // Adresy dla sprzedawców
            [
                'user_id' => $sellerUsers[0]->id, // Jakub
                'city' => 'Wrocław',
                'postal_code' => '50-001',
                'street' => 'Rynek',
                'house_number' => '12',
                'apartment_number' => '8',
            ],
            [
                'user_id' => $sellerUsers[1]->id, // Magdalena
                'city' => 'Poznań',
                'postal_code' => '60-001',
                'street' => 'Stary Rynek',
                'house_number' => '25',
                'apartment_number' => null,
            ]
        ];

        foreach ($addresses as $addressData) {
            Address::create($addressData);
        }

        // Tworzenie opisów sprzedawców w tabeli seller_descriptions
        $sellerDescriptions = [
            [
                'user_id' => $sellerUsers[0]->id,
                'short_description' => 'Artysta malarz specjalizujący się w malarstwie olejnym i akwarelach. Tworzę od ponad 15 lat.',
            ],
            [
                'user_id' => $sellerUsers[1]->id,
                'short_description' => 'Rzeźbiarka tworząca nowoczesne instalacje i rzeźby abstrakcyjne. Pasjonuje mnie sztuka współczesna.',
            ]
        ];

        foreach ($sellerDescriptions as $descriptionData) {
            SellerDescription::create($descriptionData);
        }

        // Lista zdjęć z folderu public/artworksImage
        $images = [
            'artwork1.jpg',
            'artwork2.jpg',
            'artwork3.jpg',
            'artwork4.jpg',
            'artwork5.jpg',
            'artwork6.jpg',
        ];

        // Pobieranie kategorii
        $paintingCategory = Category::where('name', 'Malarstwo')->first();
        $sculptureCategory = Category::where('name', 'Rzeźba')->first();
        $otherCategory = Category::where('name', 'Inne')->first();

        // Tworzenie dzieł sztuki zgodnie ze strukturą tabeli artworks
        $artworks = [
            [
                'user_id' => $sellerUsers[0]->id,
                'title' => 'Zachód słońca nad morzem',
                'description' => 'Piękny obraz przedstawiający zachód słońca nad spokojnym morzem. Wykonany techniką olejną na płótnie.',
                'price' => 1500.00,
                'artist' => $sellerUsers[0]->first_name . ' ' . $sellerUsers[0]->last_name,
                'image_path' => 'artworksImage/' . $images[0],
                'width' => 60.00,
                'height' => 40.00,
                'depth' => 3.00,
                'category_id' => $paintingCategory->id,
                'is_priceless' => false,
            ],
            [
                'user_id' => $sellerUsers[1]->id,
                'title' => 'Abstrakcyjna kompozycja #1',
                'description' => 'Nowoczesna rzeźba abstrakcyjna wykonana z brązu. Reprezentuje dynamikę ruchu i emocji.',
                'price' => 3200.00,
                'artist' => $sellerUsers[1]->first_name . ' ' . $sellerUsers[1]->last_name,
                'image_path' => 'artworksImage/' . $images[1],
                'width' => 30.00,
                'height' => 45.00,
                'depth' => 25.00,
                'category_id' => $sculptureCategory->id,
                'is_priceless' => false,
            ],
            [
                'user_id' => $sellerUsers[0]->id,
                'title' => 'Portret kobiety w kapeluszu',
                'description' => 'Klasyczny portret wykonany akwarelą. Delikatne kolory i precyzyjne detale.',
                'price' => 850.00,
                'artist' => $sellerUsers[0]->first_name . ' ' . $sellerUsers[0]->last_name,
                'image_path' => 'artworksImage/' . $images[2],
                'width' => 35.00,
                'height' => 50.00,
                'depth' => 2.00,
                'category_id' => $paintingCategory->id,
                'is_priceless' => false,
            ],
            [
                'user_id' => $sellerUsers[1]->id,
                'title' => 'Geometryczne formy',
                'description' => 'Eksperymentalne dzieło łączące różne techniki i materiały. Unikalna kompozycja.',
                'price' => 1200.00,
                'artist' => $sellerUsers[1]->first_name . ' ' . $sellerUsers[1]->last_name,
                'image_path' => 'artworksImage/' . $images[3],
                'width' => 40.00,
                'height' => 40.00,
                'depth' => 5.00,
                'category_id' => $otherCategory->id,
                'is_priceless' => false,
            ],
            [
                'user_id' => $sellerUsers[0]->id,
                'title' => 'Miejski pejzaż nocą',
                'description' => 'Dynamiczny obraz przedstawiający miasto w nocnych światłach. Technika mieszana.',
                'price' => 1800.00,
                'artist' => $sellerUsers[0]->first_name . ' ' . $sellerUsers[0]->last_name,
                'image_path' => 'artworksImage/' . $images[4],
                'width' => 70.00,
                'height' => 50.00,
                'depth' => 3.00,
                'category_id' => $paintingCategory->id,
                'is_priceless' => false,
            ],
            [
                'user_id' => $sellerUsers[1]->id,
                'title' => 'Bezcenne dzieło',
                'description' => 'Unikalna rzeźba o nieocenionej wartości artystycznej. Część ekskluzywnej kolekcji galerii.',
                'price' => 0.00,
                'artist' => $sellerUsers[1]->first_name . ' ' . $sellerUsers[1]->last_name,
                'image_path' => 'artworksImage/' . $images[5],
                'width' => 25.00,
                'height' => 60.00,
                'depth' => 20.00,
                'category_id' => $sculptureCategory->id,
                'is_priceless' => true,
            ]
        ];

        foreach ($artworks as $artworkData) {
            Artwork::create($artworkData);
        }

        $this->command->info('Seeder wykonany pomyślnie!');
        $this->command->info('Utworzono:');
        $this->command->info('- 6 kategorii (w tym "Inne")');
        $this->command->info('- 3 użytkowników z adresami');
        $this->command->info('- 2 sprzedawców z adresami i opisami');
        $this->command->info('- 6 dzieł sztuki (w tym 1 bezcenne)');
        $this->command->info('');
        $this->command->info('Dane logowania:');
        $this->command->info('Użytkownicy: anna@example.com, piotr@example.com, maria@example.com');
        $this->command->info('Sprzedawcy: jakub@gallery.com, magdalena@gallery.com');
        $this->command->info('Hasło dla wszystkich: 12345678');
        $this->command->info('');
        $this->command->info('Adresy:');
        $this->command->info('Anna: Warszawa, Marszałkowska 15/23');
        $this->command->info('Piotr: Kraków, Floriańska 42');
        $this->command->info('Maria: Gdańsk, Długa 7/5');
        $this->command->info('Jakub: Wrocław, Rynek 12/8');
        $this->command->info('Magdalena: Poznań, Stary Rynek 25');
    }
}