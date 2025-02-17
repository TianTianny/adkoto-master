<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Game;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;

class GamesController extends Controller
{
    public function index()
    {
        // List of open-source HTML5/WebGL games with iframe URLs
        // $games = [
        //     [
        //         'id' => 1,
        //         'name' => 'Meow',
        //         'icon' => asset('img/GameIcons/MeowIcon.jpeg'),
        //         'description' => 'Meow.',
        //         'iframe_url' => asset('Games/Meow/index.html'),
        //     ],
        //     [
        //         'id' => 2,
        //         'name' => 'Soldier Attack 2',
        //         'icon' => asset('img/GameIcons/soldierattack2300200.webp'),
        //         'description' => 'A Soldier Attack 2 game.',
        //         'iframe_url' => 'https://cdn.htmlgames.com/SoldierAttack2/',
        //     ],
        //     [
        //         'id' => 3,
        //         'name' => 'Galaxy Shooter',
        //         'icon' => asset('img/GameIcons/galaxyshooter300200.webp'),
        //         'description' => 'A Galaxy Shooter game.',
        //         'iframe_url' => 'https://cdn.htmlgames.com/GalaxyShooter/',
        //     ],
        //     [
        //         'id' => 4,
        //         'name' => 'Upside Down',
        //         'icon' => asset('img/GameIcons/upsidedown300200.webp'),
        //         'description' => 'Upside Down.',
        //         'iframe_url' => 'https://cdn.htmlgames.com/UpsideDown/',
        //     ],
        //     [
        //         'id' => 5,
        //         'name' => 'Number Search',
        //         'icon' => asset('img/GameIcons/numbersearch300.webp'),
        //         'description' => 'Number Search.',
        //         'iframe_url' => 'https://cdn.htmlgames.com/NumberSearch/',
        //     ],
        //     [
        //         'id' => 6,
        //         'name' => 'Airport Sniper',
        //         'icon' => asset('img/GameIcons/airportsniper300200.webp'),
        //         'description' => 'Airport Sniper.',
        //         'iframe_url' => 'https://cdn.htmlgames.com/AirportSniper/',
        //     ],
        //     [
        //         'id' => 7,
        //         'name' => '2048 Billiards',
        //         'icon' => asset('img/GameIcons/2048billiards300200.webp'),
        //         'description' => '2048 Billiards.',
        //         'iframe_url' => 'https://cdn.htmlgames.com/2048Billiards/',
        //     ],
        //     [
        //         'id' => 8,
        //         'name' => 'TRUCK SPACE 2',
        //         'icon' => asset('img/GameIcons/ts2-200x200.jpg'),
        //         'description' => 'TRUCK SPACE 2.',
        //         'iframe_url' => 'https://1000webgames.com/games/truckspace2/html5/',
        //     ],
        //     [
        //         'id' => 9,
        //         'name' => 'WARFARE AREA',
        //         'icon' => asset('img/GameIcons/warfarearea-200x200.jpg'),
        //         'description' => 'WARFARE AREA.',
        //         'iframe_url' => 'https://1000webgames.com/games/warfarearea/html5/',
        //     ],
        //     [
        //         'id' => 10,
        //         'name' => 'TRIALS RIDE 2',
        //         'icon' => asset('img/GameIcons/trialsride2-200x200.jpg'),
        //         'description' => 'TRIALS RIDE 2.',
        //         'iframe_url' => 'https://1000webgames.com/games/trialsride2/html5/',
        //     ],
        //     [
        //         'id' => 11,
        //         'name' => 'GTA Simulator',
        //         'icon' => asset('img/GameIcons/GTA-Simulator-lg.jpg'),
        //         'description' => 'GTA Simulator.',
        //         'iframe_url' => 'https://www.onlinegames.io/games/2023/unity2/gta-simulator/index.html',
        //     ],
        //     [
        //         'id' => 12,
        //         'name' => 'Highway Traffic',
        //         'icon' => asset('img/GameIcons/Highway-Traffic-2-lg.jpg'),
        //         'description' => 'Highway Traffic.',
        //         'iframe_url' => 'https://www.onlinegames.io/games/2022/unity/highway-traffic/index.html',
        //     ],
        //     [
        //         'id' => 13,
        //         'name' => 'Cat Simulator',
        //         'icon' => asset('img/GameIcons/Cat-Simulator-Online-lg.jpg'),
        //         'description' => 'Cat Simulator.',
        //         'iframe_url' => 'https://www.onlinegames.io/games/2022/unity4/cat-simulator/index.html',
        //     ],
        // ];

        $games = Game::all()->map(function ($game) {
            if (!preg_match('/^http/', $game->iframe_url)) {
                $game->iframe_url = asset($game->iframe_url);
            }

            return $game;
        });

        $featuredAds = Advertisement::with(['attachments', 'user', 'category'])
            ->where('featured', true)
            ->orderByDesc('created_at')
            ->get();

        $featuredAds->each(function ($ad) {
            $ad->attachments->each(function ($attachment) {
                $attachment->image_path = asset('storage/' . $attachment->image_path);
            });
        });

        return Inertia::render('Games/Index', [
            'games' => $games,
            'featuredAds' => $featuredAds,
        ]);
    }

    public function show($id)
    {
        // $games = [
        //     [
        //         'id' => 1,
        //         'name' => 'Meow',
        //         'description' => 'Meow.',
        //         'iframe_url' => asset('Games/Meow/index.html'),
        //     ],
        //     [
        //         'id' => 2,
        //         'name' => 'Soldier Attack 2',
        //         'description' => 'A Soldier Attack 2 game.',
        //         'iframe_url' => 'https://cdn.htmlgames.com/SoldierAttack2/',
        //     ],
        //     [
        //         'id' => 3,
        //         'name' => 'Galaxy Shooter',
        //         'description' => 'A Galaxy Shooter game.',
        //         'iframe_url' => 'https://cdn.htmlgames.com/GalaxyShooter/',
        //     ],
        //     [
        //         'id' => 4,
        //         'name' => 'Upside Down',
        //         'description' => 'Upside Down.',
        //         'iframe_url' => 'https://cdn.htmlgames.com/UpsideDown/',
        //     ],
        //     [
        //         'id' => 5,
        //         'name' => 'Number Search',
        //         'description' => 'Number Search.',
        //         'iframe_url' => 'https://cdn.htmlgames.com/NumberSearch/',
        //     ],
        //     [
        //         'id' => 6,
        //         'name' => 'Airport Sniper',
        //         'description' => 'Airport Sniper.',
        //         'iframe_url' => 'https://cdn.htmlgames.com/AirportSniper/',
        //     ],
        //     [
        //         'id' => 7,
        //         'name' => '2048 Billiards',
        //         'description' => '2048 Billiards.',
        //         'iframe_url' => 'https://cdn.htmlgames.com/2048Billiards/',
        //     ],
        //     [
        //         'id' => 8,
        //         'name' => 'TRUCK SPACE 2',
        //         'description' => 'TRUCK SPACE 2.',
        //         'iframe_url' => 'https://1000webgames.com/games/truckspace2/html5/',
        //     ],
        //     [
        //         'id' => 9,
        //         'name' => 'WARFARE AREA',
        //         'description' => 'WARFARE AREA.',
        //         'iframe_url' => 'https://1000webgames.com/games/warfarearea/html5/',
        //     ],
        //     [
        //         'id' => 10,
        //         'name' => 'TRIALS RIDE 2',
        //         'description' => 'TRIALS RIDE 2.',
        //         'iframe_url' => 'https://1000webgames.com/games/trialsride2/html5/',
        //     ],
        //     [
        //         'id' => 11,
        //         'name' => 'GTA Simulator',
        //         'description' => 'GTA Simulator.',
        //         'iframe_url' => 'https://www.onlinegames.io/games/2023/unity2/gta-simulator/index.html',
        //     ],
        //     [
        //         'id' => 12,
        //         'name' => 'Highway Traffic',
        //         'description' => 'Highway Traffic.',
        //         'iframe_url' => 'https://www.onlinegames.io/games/2022/unity/highway-traffic/index.html',
        //     ],
        // ];

        // $game = collect($games)->firstWhere('id', $id);

        $game = Game::findOrFail($id);

        if (!preg_match('/^http/', $game->iframe_url)) {
            $game->iframe_url = asset($game->iframe_url);
        }

        $featuredAds = Advertisement::with(['attachments', 'user', 'category'])
            ->where('featured', true)
            ->orderByDesc('created_at')
            ->get();

        $featuredAds->each(function ($ad) {
            $ad->attachments->each(function ($attachment) {
                $attachment->image_path = asset('storage/' . $attachment->image_path);
            });
        });

        return Inertia::render('Games/Detail', [
            'game' => $game,
            'featuredAds' => $featuredAds,
        ]);
    }
}
