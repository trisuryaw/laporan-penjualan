<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    private Client $client;
    public function __construct()
    {
        $this->client = new Client();
    }

    public function index(Request $request)
    {
        $year = $request->get('tahun');
        $transactions = $this->getTransactions($year);
        $menu = $this->getMenu();
        $menuFoods = $this->filterMenuByCategory($menu, 'makanan');
        $menuDrinks = $this->filterMenuByCategory($menu, 'minuman');
        $total = $transactions->sum('total');

        $data = [
            'menu' => ['Makanan' => $menuFoods, 'Minuman' => $menuDrinks],
            'transactions' => $this->groupTransactionsByMonth($transactions),
            'year' => $year,
            'total' => $total,
        ];

        return view('home', compact('data'));
    }

    private function getTransactions($tahun): Collection
    {
        $response = $this->client->get("https://tes-web.landa.id/intermediate/transaksi?tahun=$tahun");
        return collect(json_decode($response->getBody()->getContents()));
    }

    private function getMenu(): Collection
    {
        $response = $this->client->get('https://tes-web.landa.id/intermediate/menu');
        return collect(json_decode($response->getBody()->getContents()));
    }

    private function filterMenuByCategory($menu, $category): Collection
    {
        return $menu->filter(function ($item) use ($category) {
            return $item->kategori == $category;
        });
    }

    private function groupTransactionsByMonth($transactions): Collection
    {
        return $transactions->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->tanggal)->format('m');
        });
    }
}
