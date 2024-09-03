<?php

namespace App\Http\View\Composers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FakerComposer
{
    public function fakeUsers()
    {
        if (!Auth::guest()) {
            $users = collect([
                ['name' => Auth::user()->name, 'gender' => Auth::user()->karyawan->jenis_kelamin ?? 'Not Defined', 'email' => Auth::user()->email],
            ]);
        } else {
            $users = collect([
                ['name' => 'Not Defined', 'gender' =>  'Not Defined', 'email' => 'Not Defined'],
            ]);
        }

        return $users->random(1)->map(function ($user) {
            return [
                'name' => $user['name'],
                'gender' => $user['gender'],
                'email' => $user['email']
            ];
        });
    }

    public function fakePhotos()
    {
        $photos = [];
        for ($i = 0; $i < 15; $i++) {
            $photos[] = 'profile-' . rand(1, 15) . '.jpg';
        }
        return collect($photos)->random(10);
    }

    public function fakeImages()
    {
        $photos = [];
        for ($i = 0; $i < 15; $i++) {
            $photos[] = 'preview-' . rand(1, 15) . '.jpg';
        }
        return collect($photos)->random(10);
    }

    public function fakeDates()
    {
        $dates = [];
        for ($i = 0; $i < 5; $i++) {
            $dates[] = date("j F Y", mt_rand(1586584776897, 1672333200000) / 1000);
        }
        return collect($dates)->random(3);
    }

    public function fakeTimes()
    {
        $times = ['01:10 PM', '05:09 AM', '06:05 AM', '03:20 PM', '04:50 AM', '07:00 PM'];
        return collect($times)->random(3);
    }

    public function fakeFormattedTimes()
    {
        $times = collect([
            rand(10, 60) . ' seconds ago',
            rand(10, 60) . ' minutes ago',
            rand(10, 24) . ' hours ago',
            rand(10, 20) . ' days ago',
            rand(10, 12) . ' months ago'
        ]);

        return collect($times)->random(3);
    }

    public function fakeTotals()
    {
        return collect([
            rand(20, 220),
            rand(20, 120),
            rand(20, 50)
        ])->shuffle();
    }

    public function fakeTrueFalse()
    {
        return collect([0, 1, 1])->random(1);
    }

    public function fakeStocks()
    {
        return collect([
            rand(50, 220),
            rand(50, 120),
            rand(50, 50)
        ])->shuffle();
    }

    public function fakeProducts()
    {
        $products = collect([
            ['name' => 'Dell XPS 13', 'category' => 'PC & Laptop'],
            ['name' => 'Apple MacBook Pro 13', 'category' => 'PC & Laptop'],
            ['name' => 'Oppo Find X2 Pro', 'category' => 'Smartphone & Tablet'],
            ['name' => 'Samsung Galaxy S20 Ultra', 'category' => 'Smartphone & Tablet'],
            ['name' => 'Sony Master Series A9G', 'category' => 'Electronic'],
            ['name' => 'Samsung Q90 QLED TV', 'category' => 'Electronic'],
            ['name' => 'Nike Air Max 270', 'category' => 'Sport & Outdoor'],
            ['name' => 'Nike Tanjun', 'category' => 'Sport & Outdoor'],
            ['name' => 'Nikon Z6', 'category' => 'Photography'],
            ['name' => 'Sony A7 III', 'category' => 'Photography']
        ]);

        return $products->shuffle();
    }

    public function fakeNews()
    {
        $news = collect([
            [
                'title' => 'Desktop publishing software like Aldus PageMaker',
                'super_short_content' => substr('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 0, 30),
                'short_content' => substr('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 0, 150),
                'content' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.'
            ],
            [
                'title' => 'Dummy text of the printing and typesetting industry',
                'super_short_content' => substr('It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).', 0, 30),
                'short_content' => substr('It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).', 0, 150),
                'content' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).'
            ],
            [
                'title' => 'Popularised in the 1960s with the release of Letraset',
                'super_short_content' => substr('Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32. The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from "de Finibus Bonorum et Malorum" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.', 0, 30),
                'short_content' => substr('Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32. The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from "de Finibus Bonorum et Malorum" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.', 0, 150),
                'content' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32. The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from "de Finibus Bonorum et Malorum" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.'
            ],
            [
                'title' => '200 Latin words, combined with a handful of model sentences',
                'super_short_content' => substr('There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn\'t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.', 0, 50),
                'short_content' => substr('There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn\'t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.', 0, 150),
                'content' => 'There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn\'t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.'
            ]
        ]);

        return $news->shuffle();
    }

    public function fakeFiles()
    {
        $files = collect([
            ['file_name' => 'Celine Dion - Ashes.mp4', 'type' => 'MP4', 'size' => '20 MB'],
            ['file_name' => 'Laravel 7', 'type' => 'Empty Folder', 'size' => '120 MB'],
            ['file_name' => $this->fakeImages()->first(), 'type' => 'Image', 'size' => '1.2 MB'],
            ['file_name' => 'Repository', 'type' => 'Folder', 'size' => '20 KB'],
            ['file_name' => 'Resources.txt', 'type' => 'TXT', 'size' => '2.2 MB'],
            ['file_name' => 'Routes.php', 'type' => 'PHP', 'size' => '1 KB'],
            ['file_name' => 'Dota 2', 'type' => 'Folder', 'size' => '112 GB'],
            ['file_name' => 'Documentation', 'type' => 'Empty Folder', 'size' => '4 MB'],
            ['file_name' => $this->fakeImages()->first(), 'type' => 'Image', 'size' => '1.4 MB'],
            ['file_name' => $this->fakeImages()->first(), 'type' => 'Image', 'size' => '1 MB']
        ]);

        return $files->shuffle();
    }

    public function fakeJobs()
    {
        if (!Auth::guest()) {
            $jobs = collect([
                Auth::user()->Role->name,
            ]);
        } else {
            $jobs = collect([
                'Not Defined'
            ]);
        }


        return $jobs->shuffle();
    }

    public function fakeNotificationCount()
    {
        return rand(1, 7);
    }

    public function fakeFoods()
    {
        $foods = collect([
            ['name' => 'Vanilla Latte', 'image' => 'food-beverage-1.jpg'],
            ['name' => 'Milkshake', 'image' => 'food-beverage-2.jpg'],
            ['name' => 'Soft Drink', 'image' => 'food-beverage-3.jpg'],
            ['name' => 'Root Beer', 'image' => 'food-beverage-4.jpg'],
            ['name' => 'Pocari', 'image' => 'food-beverage-5.jpg'],
            ['name' => 'Ultimate Burger', 'image' => 'food-beverage-6.jpg'],
            ['name' => 'Hotdog', 'image' => 'food-beverage-7.jpg'],
            ['name' => 'Avocado Burger', 'image' => 'food-beverage-8.jpg'],
            ['name' => 'Spaghetti Fettucine Aglio with Beef Bacon', 'image' => 'food-beverage-9.jpg'],
            ['name' => 'Spaghetti Fettucine Aglio with Smoked Salmon', 'image' => 'food-beverage-10.jpg'],
            ['name' => 'Curry Penne and Cheese', 'image' => 'food-beverage-11.jpg'],
            ['name' => 'French Fries', 'image' => 'food-beverage-12.jpg'],
            ['name' => 'Virginia Cheese Fries', 'image' => 'food-beverage-13.jpg'],
            ['name' => 'Virginia Cheese Wedges', 'image' => 'food-beverage-14.jpg'],
            ['name' => 'Fried/Grilled Banana', 'image' => 'food-beverage-15.jpg'],
            ['name' => 'Crispy Mushroom', 'image' => 'food-beverage-16.jpg'],
            ['name' => 'Fried Calamari', 'image' => 'food-beverage-17.jpg'],
            ['name' => 'Chicken Wings', 'image' => 'food-beverage-18.jpg'],
            ['name' => 'Snack Platter', 'image' => 'food-beverage-19.jpg']
        ]);

        return $foods->shuffle();
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */

    public function title()
    {
        $url = request();
        if ($url->segment(3)) {
            return $url->segment(2);
        } elseif ($url->segment(2)) {
            return $url->segment(1);
        } else {
            return 'Dashboard';
        }
    }

    public function compose(View $view)
    {
        $fakerData = [];
        $globalVariable = [];
        for ($i = 0; $i < 20; $i++) {
            $fakerData[] = [
                'users' => $this->fakeUsers(),
            ];
        }

        $globalVariable = [
            'title' => $this->title()
        ];

        $view->with([
            'fakers' => $fakerData,
            'global' => $globalVariable,
        ]);
    }
}
